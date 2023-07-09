<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\ValueObject\AssertLegacyTraitConfiguration;
use DrupalRector\Utility\AddCommentTrait;
use DrupalRector\Utility\GetDeclaringSourceTrait;
use PhpParser\Node;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class AssertLegacyTraitRector extends AbstractRector implements ConfigurableRectorInterface
{

    use AddCommentTrait;
    use GetDeclaringSourceTrait;

    /**
     * @var AssertLegacyTraitConfiguration[]
     */
    private array $assertLegacyTraitMethods;

    public function configure(array $configuration): void
    {
        $this->configureNoticesAsComments($configuration);


        foreach ($configuration as $value) {
            if (!($value instanceof AssertLegacyTraitConfiguration)) {
                throw new \InvalidArgumentException(sprintf(
                    'Each configuration item must be an instance of "%s"',
                    AssertLegacyTraitConfiguration::class
                ));
            }
        }

        $this->assertLegacyTraitMethods = $configuration;
    }

    public function getNodeTypes(): array
    {
        return [
            Node\Expr\MethodCall::class,
        ];
    }

    protected function createAssertSessionMethodCall(string $method, array $args): Node\Expr\MethodCall
    {
        $assertSessionNode = $this->nodeFactory->createLocalMethodCall('assertSession');
        return $this->nodeFactory->createMethodCall($assertSessionNode, $method, $args);
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\MethodCall);

        foreach ($this->assertLegacyTraitMethods as $configuration) {
            if ($this->getName($node->name) !== $configuration->getDeprecatedMethodName()) {
                return null;
            }
            if ($this->getDeclaringSource($node) !== $configuration->getDeclaringSource()) {
                return null;
            }

            if ($this->comment !== '') {
                $this->addDrupalRectorComment($node, $configuration->getComment());
            }

            $args = $node->args;
            if ($configuration->isProcessFirstArgumentOnly()) {
                $args = [$node->args[0]];
            }

            if ($configuration->getPrependArgument() !== NULL) {
                $args = array_unshift($args, $this->nodeFactory->createArg('X-Drupal-Cache-Tags'));
            }

            if ($configuration->isAssertSessionMethod()) {
                return $this->createAssertSessionMethodCall($configuration->getMethodName(), $args);
            }
            return $this->nodeFactory->createLocalMethodCall($configuration->getMethodName(), $args);
        }
        return null;
    }

    protected function processArgs(array $args): array
    {
        return $args;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::METHOD() calls', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$this->assertLinkByHref('user/1/translations');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->linkByHrefExists('user/1/translations');
CODE_AFTER
                ,
                [
                    new AssertLegacyTraitConfiguration('assertLinkByHref', 'linkByHrefExists'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$this->assertLink('Anonymous comment title');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->linkExists('Anonymous comment title');
CODE_AFTER
                ,
                [
                    new AssertLegacyTraitConfiguration('assertLink', 'linkExists'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$this->assertNoEscaped('<div class="escaped">');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->assertNoEscaped('<div class="escaped">');
CODE_AFTER
                ,
                [
                    new AssertLegacyTraitConfiguration('assertNoEscaped', 'assertNoEscaped'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$this->assertNoFieldChecked('edit-settings-view-mode', 'default');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->checkboxNotChecked('edit-settings-view-mode', 'default');
CODE_AFTER
                ,
                [
                    new AssertLegacyTraitConfiguration('assertNoFieldChecked', 'checkboxNotChecked'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
    $this->assertNoField('files[upload]', 'Found file upload field.');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
    $this->assertSession()->fieldNotExists('files[upload]', 'Found file upload field.');
CODE_AFTER
                ,
                [
                    new AssertLegacyTraitConfiguration('assertNoField', 'fieldNotExists', 'Change assertion to buttonExists() if checking for a button.'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$this->assertNoLinkByHref('user/2/translations');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->linkByHrefNotExists('user/2/translations');
CODE_AFTER
                ,
                [
                    new AssertLegacyTraitConfiguration('assertNoLinkByHref', 'linkByHrefNotExists'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$this->assertNoLink('Anonymous comment title');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->linkNotExists('Anonymous comment title');
CODE_AFTER
                ,
                [
                    new AssertLegacyTraitConfiguration('assertNoLink', 'linkNotExists'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$this->assertNoOption('edit-settings-view-mode', 'default');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->optionNotExists('edit-settings-view-mode', 'default');
CODE_AFTER
                ,
                [
                    new AssertLegacyTraitConfiguration('assertNoOption', 'optionNotExists'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$this->assertNoPattern('|<h4[^>]*></h4>|', 'No empty H4 element found.');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->responseNotMatches('|<h4[^>]*></h4>|', 'No empty H4 element found.');
CODE_AFTER
                ,
                [
                    new AssertLegacyTraitConfiguration('assertNoPattern', 'responseNotMatches'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$this->assertPattern('|<h4[^>]*></h4>|', 'No empty H4 element found.');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->responseMatches('|<h4[^>]*></h4>|', 'No empty H4 element found.');
CODE_AFTER
                ,
                [
                    new AssertLegacyTraitConfiguration('assertPattern', 'responseMatches'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$this->assertNoRaw('bartik/logo.svg');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->responseNotContains('bartik/logo.svg');
CODE_AFTER
                ,
                [
                    new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertNoRaw', methodName: 'responseNotContains', processFirstArgumentOnly: true),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$this->assertRaw('bartik/logo.svg');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->responseContains('bartik/logo.svg');
CODE_AFTER
                ,
                [
                    new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertRaw', methodName: 'responseContains', processFirstArgumentOnly: true),
                ]
            ),
        ]);
    }
}

