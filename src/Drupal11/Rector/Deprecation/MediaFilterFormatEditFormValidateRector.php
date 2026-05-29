<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated media_filter_format_edit_form_validate() with MediaHooks::formatEditFormValidate().
 *
 * Deprecated in drupal:11.4.0 and removed in drupal:12.0.0.
 *
 * @see https://www.drupal.org/node/3568124
 * @see https://www.drupal.org/node/3566774
 */
final class MediaFilterFormatEditFormValidateRector extends AbstractDrupalCoreRector
{
    private const DEPRECATED_FUNCTION = 'media_filter_format_edit_form_validate';

    private const MEDIA_HOOKS_CLASS = 'Drupal\media\Hook\MediaHooks';

    /**
     * @var array|DrupalIntroducedVersionConfiguration[]
     */
    protected array $configuration;

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof DrupalIntroducedVersionConfiguration) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', DrupalIntroducedVersionConfiguration::class));
            }
        }
        parent::configure($configuration);
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [FuncCall::class, String_::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        if ($node instanceof FuncCall) {
            if (!$node->name instanceof Name || $node->name->toString() !== self::DEPRECATED_FUNCTION) {
                return null;
            }

            $serviceCall = new StaticCall(
                new FullyQualified('Drupal'),
                'service',
                [new Arg(new ClassConstFetch(new FullyQualified(self::MEDIA_HOOKS_CLASS), 'class'))]
            );

            return new MethodCall($serviceCall, 'formatEditFormValidate', $node->args);
        }

        if ($node instanceof String_) {
            if ($node->value !== self::DEPRECATED_FUNCTION) {
                return null;
            }

            return new Array_([
                new ArrayItem(new ClassConstFetch(new FullyQualified(self::MEDIA_HOOKS_CLASS), 'class')),
                new ArrayItem(new String_('formatEditFormValidate')),
            ]);
        }

        return null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated media_filter_format_edit_form_validate() with \Drupal\media\Hook\MediaHooks::formatEditFormValidate().',
            [
                new ConfiguredCodeSample(
                    'media_filter_format_edit_form_validate($form, $form_state);',
                    '\Drupal::service(\Drupal\media\Hook\MediaHooks::class)->formatEditFormValidate($form, $form_state);',
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
                new ConfiguredCodeSample(
                    "\$form['#validate'][] = 'media_filter_format_edit_form_validate';",
                    "\$form['#validate'][] = [\\Drupal\\media\\Hook\\MediaHooks::class, 'formatEditFormValidate'];",
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
            ]
        );
    }
}
