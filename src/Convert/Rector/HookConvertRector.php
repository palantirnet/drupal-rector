<?php

declare(strict_types=1);

namespace DrupalRector\Convert\Rector;

use DrupalRector\Services\AddCommentService;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use Rector\Doctrine\CodeQuality\Utils\CaseStringHelper;
use Rector\PhpParser\Printer\BetterStandardPrinter;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class HookConvertRector extends AbstractRector
{

    protected string $inputFilename = '';

    /**
     * @var Node\Stmt\Use_[]
     */
    protected array $useStmts = [];

    /**
     * @var \PhpParser\Node\Stmt\Class_
     *
     * The hook class itself.
     */
    protected Node\Stmt\Class_ $hookClass;

    /**
     * @var string
     *
     * The name of the module, used to guess which functions are hooks.
     */
    protected string $module = '';

    /**
     * @var string
     *
     * THe module directory, used to write services.yml
     */
    protected string $moduleDir = '';

    /**
     * The Drupal service call.
     *
     * For example \Drupal::service(UserHooks::CLASS)
     */
    protected Node\Expr\StaticCall $drupalServiceCall;

    public function __construct(protected AddCommentService $addCommentService) {
    }



    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Hook conversion script', [
            new CodeSample(
                <<<'CODE_SAMPLE'
Drupal Hook Implementation
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
https://www.drupal.org/node/3442349
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Function_::class, Node\Stmt\Use_::class];
    }

    public function refactor(Node $node): ?Node
    {
        $filePath = $this->file->getFilePath();
        $ext = pathinfo($filePath, \PATHINFO_EXTENSION);
        if (!in_array($ext, ['inc', 'module']))
        {
            return $node;
        }
        if ($filePath !== $this->inputFilename)
        {
            $this->initializeHookClass();
        }
        if ($node instanceof Node\Stmt\Use_) {
            $this->useStmts[] = $node;
        }

        if ($node instanceof Function_ && $this->module && ($method = $this->createMethodFromFunction($node)))
        {
            $this->hookClass->stmts[] = $method;
            // See the note in ::getMethod() about how it's important to not
            // change any object property of $node here.
            // Rewrite the function body to be a single service call.
            $node->stmts = [$this->getServiceCall($node)];
            // Mark this function as a legacy hook.
            $node->attrGroups[] = new Node\AttributeGroup([new Node\Attribute(new Node\Name\FullyQualified('Drupal\Core\Hook\Attribute\LegacyHook'))]);
        }
        return $node;
    }


    protected function initializeHookClass(): void
    {
        $this->__destruct();
        $this->moduleDir = $this->file->getFilePath();
        $this->inputFilename = $this->moduleDir;
        // Find the relevant info.yml: it's either in the current directory or
        // one of the parents.
        while (($this->moduleDir = dirname($this->moduleDir)) && !($info = glob("$this->moduleDir/*.info.yml")));
        if ($infoFile = reset($info)) {
            $this->module = basename($infoFile, '.info.yml');
            $filename = pathinfo($this->file->getFilePath(), \PATHINFO_FILENAME);
            $hookClassName = ucfirst(CaseStringHelper::camelCase(str_replace('.', '_', $filename) . '_hooks'));
            $namespace = implode('\\', ['Drupal', $this->module, 'Hook']);
            $this->hookClass = new Node\Stmt\Class_(new Node\Name($hookClassName));
            // Using $this->nodeFactory->createStaticCall() results in
            // use \Drupal; on top which is not desirable.
            $classConst = new Node\Expr\ClassConstFetch(new Node\Name\FullyQualified("$namespace\\$hookClassName"), 'CLASS');
            $this->drupalServiceCall = new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal'), 'service', [new Node\Arg($classConst)]);
        }
    }

    public function __destruct()
    {
        if ($this->module && $this->hookClass->stmts)
        {
            $className = $this->hookClass->name->toString();
            $namespace = "Drupal\\$this->module\\Hook";
            // Create the statement use Drupal\Core\Hook\Hook;
            $name = new Node\Name('Drupal\Core\Hook\Hook');
            // UseItem contains an unguarded class_alias to the deprecated
            // UseUse class. However, due to some version mix-up it seems the
            // old class is used for parsing so only use the UseItem class if
            // it is already loaded or the UseUse class is completely gone.
            $useHook = class_exists('PhpParser\Node\UseItem', FALSE) || !class_exists('PhpParser\Node\Stmt\UseUse') ? new Node\UseItem($name) : new Node\Stmt\UseUse($name);
            // Put the class together.
            $hookClassStmts = [
                new Node\Stmt\Namespace_(new Node\Name($namespace)),
                ... $this->useStmts,
                new Node\Stmt\Use_([$useHook]),
                $this->hookClass,
            ];
            // Write it out.
            @mkdir("$this->moduleDir/src");
            @mkdir("$this->moduleDir/src/Hook");
            $hookClassFilename = "$this->moduleDir/src/Hook/$className.php";
            $printer = new BetterStandardPrinter();
            file_put_contents($hookClassFilename, $printer->prettyPrintFile($hookClassStmts));
            static::writeServicesYml("$this->moduleDir/$this->module.services.yml", "$namespace\\$className");
            $this->module = '';
        }
    }

    protected function createMethodFromFunction(Function_ $node): ?ClassMethod
    {
        $name = $node->name->toString();
        if (str_starts_with($name, '_'))
        {
            return NULL;
        }
        $hook = '';
        // If the function name starts with the module name, presume it's a hook.
        if (preg_match("/^{$this->module}_(.*)/", $name, $matches))
        {
            $hook = $matches[1];
        }
        // If there is doxygen but there's no hook yet then try to find the
        // hook and module by parsing the doxygen for "Implements hook_foo()."
        if (($doc = $node->getDocComment()) && !$hook)
        {
            $implementsModule = static::getImplementsModule($hook, $name, $doc->getReformattedText());
        }
        if ($hook)
        {
            // I prefer static because it shows what the method actually
            // depends on.
            return static::getMethod($node, $doc, $hook, $implementsModule ?? '', fn($name) => $this->nodeFactory->createPublicMethod($name));
        }
        elseif (!str_starts_with($name, 'template_preprocess_'))
        {
            $this->addCommentService->addDrupalRectorComment($node, 'If this is a hook then convert it according to https://www.drupal.org/node/3442349');
        }
        return NULL;
    }

    protected static function getImplementsModule(string &$hook, string $functionName, string $doxygen): string {
        // If the doxygen contains "Implements hook_foo()" then parse the hook
        // name. A difficulty here is "Implements hook_form_FORM_ID_alter".
        // Find these by looking for an optional part starting with an
        // uppercase letter.
        if (preg_match('/^ \* Implements hook_([a-z0-9_]+)(([A-Z][A-Z0-9_]+)(_[a-z0-9_]*))?/m', $doxygen, $matches))
        {
            $preg = $matches[1];
            // If the optional part is present then replace the uppercase
            // portions with an appropriate regex.
            if (isset($matches[4]))
            {
                $preg .= '[a-z0-9_]+' . $matches[4];
            }
            // And now find the module and the hook.
            if (preg_match("/^(.*?)_($preg)$/", $functionName, $matches))
            {
                $hook = $matches[2];
                return $matches[1];
            }
        }
        return '';
    }

    protected static function getMethod(Function_ $node, ?Doc $doc, string $hook, string $implementsModule, callable $methodFactory): ClassMethod {
        $method = $methodFactory(static::getMethodName($node));
        assert($method instanceof ClassMethod);
        if ($doc)
        {
            $method->setDocComment($doc);
        }
        // Do the actual copying.
        foreach ($node->getSubNodeNames() as $subNodeName)
        {
            // The name is set up in the constructor.
            if ($subNodeName !== 'name')
            {
                // Copying an object property could be a problem as those
                // are copied by handle so changing it would change it in
                // both places. But ::refactor() only changes stmts and
                // attrGroups and both are arrays. This function also only
                // changes attrGroups.
                $method->$subNodeName = $node->$subNodeName;
            }
        }
        // Assemble the arguments for the #[Hook] attribute.
        $arguments = [new Node\Arg(new Node\Scalar\String_($hook))];
        if ($implementsModule)
        {
            $arguments[] = new Node\Arg(new Node\Scalar\String_($implementsModule), name: new Node\Identifier('module'));
        }
        $hookAttribute = new Node\Attribute(new Node\Name('Hook'), $arguments);
        $method->attrGroups[] = new Node\AttributeGroup([$hookAttribute]);
        return $method;
    }

    /**
     * @param \PhpParser\Node\Stmt\Function_ $node
     *   E.g.: user_entity_operation(EntityInterface $entity)
     *
     * @return \PhpParser\Node\Stmt\Expression
     *   E.g.: Drupal::service('Drupal\user\Hook\UserHooks')->userEntityOperation($entity);
     *
     */
    protected function getServiceCall(Function_ $node): Node\Stmt\Expression
    {
        $args = array_map(fn($param) => $this->nodeFactory->createArg($param->var), $node->getParams());
        return new Node\Stmt\Expression($this->nodeFactory->createMethodCall($this->drupalServiceCall, static::getMethodName($node), $args));
    }

    /**
     * @param \PhpParser\Node\Stmt\Function_ $node
     *   A function declaration for example the entire user_user_role_insert()
     *   function.
     *
     * @return string
     *   The function name converted to camelCase for e.g. userUserRoleInsert.
     */
    public static function getMethodName(Function_ $node): string
    {
        return CaseStringHelper::camelCase($node->name->toString());
    }

    protected static function writeServicesYml(string $fileName, string $fullyClassifiedClassName): void
    {
        $services = is_file($fileName) ? file_get_contents($fileName) : '';
        $id = "\n  $fullyClassifiedClassName:\n";
        if (!str_contains($services, $id))
        {
            if (!str_contains($services, 'services:'))
            {
                $services .= "\nservices:";
            }
            $services .= "$id    class: $fullyClassifiedClassName\n    autowire: true\n";
            file_put_contents($fileName, $services);
        }
    }

}
