<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated user one-time authentication functions with the OneTimeAuthentication service.
 *
 * Handles user_pass_rehash(), user_pass_reset_url(), and user_cancel_url().
 * The two URL-generating functions returned a string, while the new service
 * methods return a \Drupal\Core\Url object, so the rewrite chains ->toString().
 *
 * Deprecated in drupal:11.4.0 and removed in drupal:13.0.0.
 *
 * @see https://www.drupal.org/node/3581056
 * @see https://www.drupal.org/node/3581062
 */
class ReplaceUserOneTimeAuthFunctionsRector extends AbstractDrupalCoreRector
{
    private const SERVICE_CLASS = 'Drupal\user\OneTimeAuthentication';

    /** @var DrupalIntroducedVersionConfiguration[] */
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
        return [FuncCall::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof FuncCall);

        if (!$node->name instanceof Name) {
            return null;
        }

        return match ($node->name->toString()) {
            'user_pass_rehash' => $this->buildServiceCall('generateHmac', $node->args),
            'user_pass_reset_url' => $this->wrapToString($this->buildServiceCall('generateOneTimeLoginUrl', $node->args)),
            'user_cancel_url' => $this->wrapToString($this->buildServiceCall('generateCancelConfirmUrl', $node->args)),
            default => null,
        };
    }

    /** @param Arg[] $args */
    private function buildServiceCall(string $method, array $args): MethodCall
    {
        $serviceCall = new StaticCall(
            new FullyQualified('Drupal'),
            'service',
            [new Arg(new ClassConstFetch(new FullyQualified(self::SERVICE_CLASS), 'class'))]
        );

        return new MethodCall($serviceCall, $method, $args);
    }

    private function wrapToString(MethodCall $methodCall): MethodCall
    {
        return new MethodCall($methodCall, 'toString', []);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated user one-time authentication functions with the \Drupal\user\OneTimeAuthentication service.',
            [
                new ConfiguredCodeSample(
                    'user_pass_rehash($account, $timestamp);',
                    '\Drupal::service(\Drupal\user\OneTimeAuthentication::class)->generateHmac($account, $timestamp);',
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
                new ConfiguredCodeSample(
                    'user_pass_reset_url($account, $options);',
                    '\Drupal::service(\Drupal\user\OneTimeAuthentication::class)->generateOneTimeLoginUrl($account, $options)->toString();',
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
                new ConfiguredCodeSample(
                    'user_cancel_url($account, $options);',
                    '\Drupal::service(\Drupal\user\OneTimeAuthentication::class)->generateCancelConfirmUrl($account, $options)->toString();',
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
            ]
        );
    }
}
