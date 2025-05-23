<?php

declare(strict_types=1);

namespace DrupalRector\Rector\Convert;

use Composer\InstalledVersions;
use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitorAbstract;
use Rector\Configuration\Option;
use Rector\Doctrine\CodeQuality\Utils\CaseStringHelper;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\PhpParser\Printer\BetterStandardPrinter;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class HookConvertRector extends AbstractRector
{
    protected string $inputFilename = '';

    /**
     * @var Use_[]
     */
    protected array $useStmts = [];

    protected Class_ $hookClass;

    protected string $module = '';

    protected string $moduleDir = '';

    /**
     * The Drupal service call.
     *
     * For example \Drupal::service(UserHooks::CLASS)
     */
    protected Node\Expr\StaticCall $drupalServiceCall;

    private string $drupalCorePath = "\0";

    /**
     * @var BetterStandardPrinter
     */
    private BetterStandardPrinter $printer;

    private bool $isDryRun;

    public function __construct(BetterStandardPrinter $printer)
    {
        $this->isDryRun = in_array('--'.Option::DRY_RUN, $_SERVER['argv'] ?? []) || in_array('-'.Option::DRY_RUN_SHORT, $_SERVER['argv'] ?? []);
        $this->printer = $printer;

        try {
            if (class_exists(InstalledVersions::class) && ($corePath = InstalledVersions::getInstallPath('drupal/core'))) {
                $this->drupalCorePath = realpath($corePath);
            }
        } catch (\OutOfBoundsException $e) {
        }
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Hook conversion script', [
            new CodeSample(
                <<<'CODE_SAMPLE'
/**
 * Implements hook_user_cancel().
 */
function hookconvertrector_user_cancel($edit, UserInterface $account, $method) {
    $red = 'red';
    $method = ['red', 'green', 'blue'];
    $edit = [
        'red' => 'red',
        'green' => 'green',
        'blue' => 'blue',
    ];
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
/**
 * Hook implementations for hookconvertrector.
 */
class HookconvertrectorHooks
{
    /**
     * Implements hook_user_cancel().
     */
    #[Hook('user_cancel')]
    public function userCancel($edit, \UserInterface $account, $method)
    {
        $red = 'red';
        $method = [
            'red',
            'green',
            'blue',
        ];
        $edit = [
            'red' => 'red',
            'green' => 'green',
            'blue' => 'blue',
        ];
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Function_::class, Use_::class];
    }

    public function refactor(Node $node): Node|int|null
    {
        $filePath = $this->file->getFilePath();
        $ext = pathinfo($filePath, \PATHINFO_EXTENSION);
        if (!in_array($ext, ['inc', 'module'])) {
            return null;
        }
        if ($filePath !== $this->inputFilename) {
            $this->initializeHookClass();
        }
        if ($node instanceof Use_) {
            // For some unknown reason some Use_ statements are passed twice
            // to this method.
            $newNode = new Use_($node->uses, $node->type, ['comments' => []] + $node->getAttributes());
            $this->useStmts[$this->printer->prettyPrint([$newNode])] = $newNode;
        }

        if ($node instanceof Function_) {
            if ($node->name->toString() === 'system_theme') {
                return null;
            }
            // Skip already converted hooks marked with the LegacyHook attribute.
            foreach ($node->attrGroups as $attrGroup) {
                foreach ($attrGroup->attrs as $attribute) {
                    if ($this->getName($attribute->name) == 'Drupal\\Core\\Hook\\Attribute\\LegacyHook') {
                        return null;
                    }
                }
            }

            if ($this->module && ($method = $this->createMethodFromFunction($node))) {
                $this->hookClass->stmts[] = $method;
                if ($node->name->toString() === 'system_page_attachments') {
                    $method->stmts = [new Node\Stmt\Expression(new Node\Expr\FuncCall(new Node\Name('_system_page_attachments'), self::convertParamsToArgs($node)))];
                    $node->name = new Node\Identifier('_system_page_attachments');

                    return $node;
                }

                if (defined('\PhpParser\NodeVisitor::REMOVE_NODE')) {
                    $remove_node = NodeVisitor::REMOVE_NODE;
                } else {
                    /* @phpstan-ignore-next-line */
                    $remove_node = NodeTraverser::REMOVE_NODE;
                }

                return str_starts_with($filePath, $this->drupalCorePath) ? $remove_node : $this->getLegacyHookFunction($node);
            }
        }

        return null;
    }

    protected function initializeHookClass(): void
    {
        $this->__destruct();
        $this->moduleDir = $this->file->getFilePath();
        $this->inputFilename = $this->moduleDir;
        // Find the relevant info.yml: it's either in the current directory or
        // one of the parents.
        while (($this->moduleDir = dirname($this->moduleDir)) && !($info = glob("$this->moduleDir/*.info.yml"))) {
        }
        if (!empty($info)) {
            $infoFile = reset($info);
            $this->module = basename($infoFile, '.info.yml');
            $filename = pathinfo($this->file->getFilePath(), \PATHINFO_FILENAME);
            $hookClassName = ucfirst(CaseStringHelper::camelCase(str_replace('.', '_', $filename).'_hooks'));
            $counter = '';
            do {
                $candidate = "$hookClassName$counter";
                $hookClassFilename = "$this->moduleDir/src/Hook/$candidate.php";
                $counter = $counter ? $counter + 1 : 1;
            } while (file_exists($hookClassFilename));
            $namespace = implode('\\', ['Drupal', $this->module, 'Hook']);
            $this->hookClass = new Class_(new Node\Identifier($candidate));
            // Using $this->nodeFactory->createStaticCall() results in
            // use \Drupal; on top which is not desirable.
            $classConst = new Node\Expr\ClassConstFetch(new FullyQualified("$namespace\\$candidate"), 'class');
            $this->drupalServiceCall = new Node\Expr\StaticCall(new FullyQualified('Drupal'), 'service', [new Node\Arg($classConst)]);
            $this->useStmts = [];
        }
    }

    public function __destruct()
    {
        if ($this->module && $this->hookClass->stmts) {
            $className = $this->hookClass->name->toString();
            // Put the file together.
            $namespace = "Drupal\\$this->module\\Hook";
            $hookClassStmts = [
                new Node\Stmt\Namespace_(new Node\Name($namespace)),
                ...$this->useStmts,
                new Use_([new Node\Stmt\UseUse(new Node\Name('Drupal\Core\Hook\Attribute\Hook'))]),
                $this->hookClass,
            ];
            $this->hookClass->setDocComment(new \PhpParser\Comment\Doc("/**\n * Hook implementations for $this->module.\n */"));
            // Write it out if not a dry run
            if ($this->isDryRun === false) {
                @mkdir("$this->moduleDir/src");
                @mkdir("$this->moduleDir/src/Hook");

                file_put_contents("$this->moduleDir/src/Hook/$className.php", $this->printer->prettyPrintFile($hookClassStmts));
                if (!str_starts_with($this->moduleDir, $this->drupalCorePath)) {
                    static::writeServicesYml("$this->moduleDir/$this->module.services.yml", "$namespace\\$className");
                }
            }
        }
        $this->module = '';
    }

    protected function createMethodFromFunction(Function_ $node): ?ClassMethod
    {
        if ($info = $this->getHookAndModuleName($node)) {
            ['hook' => $hook, 'module' => $implementsModule] = $info;
            $procOnly = [
                'install',
                'requirements',
                'schema',
                'uninstall',
                'update_last_removed',
                'module_implements_alter',
                'hook_info',
                'install_tasks',
                'install_tasks_alter',
            ];
            if (in_array($hook, $procOnly) || str_starts_with($hook, 'preprocess') || str_starts_with($hook, 'process')) {
                return null;
            }
            // Resolve __FUNCTION__ and unqualify things so TRUE doesn't
            // become \TRUE.
            $visitor = new class(new String_($node->name->toString())) extends NodeVisitorAbstract {
                public function __construct(protected String_ $functionName)
                {
                }

                public function leaveNode(Node $node)
                {
                    if (isset($node->name) && $node->name instanceof FullyQualified) {
                        $name = new Node\Name($node->name);
                        if ($name->isUnqualified()) {
                            $node->name = $name;

                            return $node;
                        }
                    }

                    if ($node instanceof Node\Expr\Array_) {
                        $node->setAttribute(AttributeKey::NEWLINED_ARRAY_PRINT, true);
                    }

                    return $node instanceof Node\Scalar\MagicConst\Function_ ? $this->functionName : parent::leaveNode($node);
                }
            };
            $traverser = new NodeTraverser();
            $traverser->addVisitor($visitor);
            $traverser->traverse([$node]);
            // Convert the function to a method.
            $method = new ClassMethod($this->getMethodName($node), get_object_vars($node), $node->getAttributes());
            $method->flags = Modifiers::PUBLIC;
            // Assemble the arguments for the #[Hook] attribute.
            $arguments = [new Node\Arg(new String_($hook))];
            if ($implementsModule !== $this->module) {
                $arguments[] = new Node\Arg(new String_($implementsModule), name: new Node\Identifier('module'));
            }
            $method->attrGroups[] = new Node\AttributeGroup([new Node\Attribute(new Node\Name('Hook'), $arguments)]);

            return $method;
        }

        return null;
    }

    /**
     * Get the hook and module name from a function name and doxygen.
     *
     * If the doxygen has Implements hook_foo() in it then this method attempts
     * to find a matching module name and hook. Function names like
     * user_access_test_user_access() are ambiguous: it could be the user module
     * implementing the hook_ENTITY_TYPE_access hook for the access_test_user
     * entity type or it could be the user_access_test module implementing it for
     * the user entity type. The current module name is preferred by the method
     * then the shortest possible module name producing a match is returned.
     *
     * @param Function_ $node
     *                        A function node
     *
     * @return array<string, string>
     *                               If a match was found then an associative array with keys hook and module
     *                               with corresponding values. Otherwise, the array is empty.
     */
    protected function getHookAndModuleName(Function_ $node): array
    {
        // If the doxygen contains "Implements hook_foo()" then parse the hook
        // name. A difficulty here is "Implements hook_form_FORM_ID_alter".
        // Find these by looking for an optional part starting with an
        // uppercase letter.
        if (preg_match('/^ \* Implements hook_([a-zA-Z0-9_]+)/m', (string) $node->getDocComment()?->getReformattedText(), $matches)) {
            $parts = explode('_', $matches[1]);
            $isUppercase = false;
            foreach ($parts as &$part) {
                if (!$part) {
                    continue;
                }
                if ($part === strtoupper($part)) {
                    if (!$isUppercase) {
                        $isUppercase = true;
                        $part = '[a-z0-9_]+';
                    }
                } else {
                    $isUpperCase = false;
                }
            }
            $hookRegex = implode('_', $parts);
            $hookRegex = "_(?<hook>$hookRegex)";
            $functionName = $node->name->toString();
            // And now find the module and the hook.
            foreach ([$this->module, '.+?'] as $module) {
                if (preg_match("/^(?<module>$module)$hookRegex$/", $functionName, $matches)) {
                    return $matches;
                }
            }
        }

        return [];
    }

    /**
     * @param Function_ $node
     *                        A function declaration for example the entire user_user_role_insert()
     *                        function
     *
     * @return string
     *                The function name converted to camelCase for e.g. userRoleInsert. The
     *                current module name is removed from the beginning.
     */
    protected function getMethodName(Function_ $node): string
    {
        $name = preg_replace("/^{$this->module}_/", '', $node->name->toString());

        return CaseStringHelper::camelCase($name);
    }

    public function getLegacyHookFunction(Function_ $node): Function_
    {
        $methodCall = new Node\Expr\MethodCall($this->drupalServiceCall, $this->getMethodName($node), self::convertParamsToArgs($node));
        $hasReturn = (new NodeFinder())->findFirstInstanceOf([$node], Node\Stmt\Return_::class);
        $node->stmts = [$hasReturn ? new Node\Stmt\Return_($methodCall) : new Node\Stmt\Expression($methodCall)];
        // Mark this function as a legacy hook.
        $node->attrGroups[] = new Node\AttributeGroup([new Node\Attribute(new FullyQualified('Drupal\Core\Hook\Attribute\LegacyHook'))]);

        return $node;
    }

    protected static function writeServicesYml(string $fileName, string $fullyClassifiedClassName): void
    {
        $services = is_file($fileName) ? file_get_contents($fileName) : '';
        $id = "\n  $fullyClassifiedClassName:\n";
        if (!str_contains($services, $id)) {
            if (!str_contains($services, 'services:')) {
                $services .= "\nservices:";
            }
            $services .= "$id    class: $fullyClassifiedClassName\n    autowire: true\n";
            file_put_contents($fileName, $services);
        }
    }

    /**
     * @param Function_ $node
     *
     * @return Node\Arg[]
     */
    protected static function convertParamsToArgs(Function_ $node): array
    {
        return array_map(fn (Node\Param $param) => new Node\Arg($param->var), $node->getParams());
    }
}
