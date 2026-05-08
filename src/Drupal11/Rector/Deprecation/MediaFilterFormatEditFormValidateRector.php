<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

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
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated media_filter_format_edit_form_validate() with MediaHooks::formatEditFormValidate().
 *
 * Deprecated in drupal:11.4.0 and removed in drupal:12.0.0.
 *
 * @see https://www.drupal.org/node/3568124
 * @see https://www.drupal.org/node/3566774
 */
class MediaFilterFormatEditFormValidateRector extends AbstractRector
{
    private const DEPRECATED_FUNCTION = 'media_filter_format_edit_form_validate';

    private const MEDIA_HOOKS_CLASS = 'Drupal\media\Hook\MediaHooks';

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [FuncCall::class, String_::class];
    }

    public function refactor(Node $node): ?Node
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
                new CodeSample(
                    'media_filter_format_edit_form_validate($form, $form_state);',
                    '\Drupal::service(\Drupal\media\Hook\MediaHooks::class)->formatEditFormValidate($form, $form_state);'
                ),
                new CodeSample(
                    "\$form['#validate'][] = 'media_filter_format_edit_form_validate';",
                    "\$form['#validate'][] = [\\Drupal\\media\\Hook\\MediaHooks::class, 'formatEditFormValidate'];"
                ),
            ]
        );
    }
}
