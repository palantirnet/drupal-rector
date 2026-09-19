<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces the deprecated user role permission functions with RoleInterface methods.
 *
 * Handles user_role_grant_permissions(), user_role_revoke_permissions() and
 * user_role_change_permissions(). The role is loaded with the static
 * \Drupal\user\Entity\Role::loadOverrideFree(), which returns NULL when the role does
 * not exist, so the chain is built from nullsafe calls — the pattern core itself
 * adopted in media_install() and node_install().
 *
 * Deprecated in drupal:11.5.0 and removed in drupal:13.0.0.
 *
 * @see https://www.drupal.org/node/2025089
 * @see https://www.drupal.org/node/3348027
 */
class ReplaceUserRolePermissionFunctionsRector extends AbstractDrupalCoreRector
{
    /**
     * The PHPStan deprecation messages this rector covers.
     *
     * Captured against drupal/core 11.x-dev via matomo, pwa and poll.
     */
    public const PHPSTAN_MESSAGES = [
        'Call to deprecated function user_role_grant_permissions(). Deprecated in drupal:11.5.0 and is removed from drupal:13.0.0. Use Drupal\user\RoleInterface::grantPermissions() instead.',
        'Call to deprecated function user_role_revoke_permissions(). Deprecated in drupal:11.5.0 and is removed from drupal:13.0.0. Use Drupal\user\RoleInterface::revokePermissions() instead.',
        'Call to deprecated function user_role_change_permissions(). Deprecated in drupal:11.5.0 and is removed from drupal:13.0.0. Use Drupal\user\RoleInterface::changePermissions() instead.',
    ];

    private const ROLE_CLASS = 'Drupal\user\Entity\Role';

    /**
     * Maps each deprecated function name to the RoleInterface method replacing it.
     *
     * @var array<string, string>
     */
    private const FUNCTION_TO_METHOD = [
        'user_role_grant_permissions' => 'grantPermissions',
        'user_role_revoke_permissions' => 'revokePermissions',
        'user_role_change_permissions' => 'changePermissions',
    ];

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

        $methodName = self::FUNCTION_TO_METHOD[$node->name->toString()] ?? null;
        if ($methodName === null) {
            return null;
        }

        // The deprecated functions take ($rid, array $permissions = []) and never
        // had a third parameter, so anything outside that shape is not a call we
        // know how to rewrite.
        if (count($node->args) < 1 || count($node->args) > 2) {
            return null;
        }

        // Named arguments and argument unpacking are too rare here to be worth
        // resolving into positional order.
        foreach ($node->args as $arg) {
            if (!$arg instanceof Arg || $arg->name !== null || $arg->unpack) {
                return null;
            }
        }

        $roleIdArg = $node->args[0];
        $permissionsArg = $node->args[1] ?? new Arg(new Array_([]));

        $loadCall = new StaticCall(
            new FullyQualified(self::ROLE_CLASS),
            'loadOverrideFree',
            [$roleIdArg]
        );

        return new NullsafeMethodCall(
            new NullsafeMethodCall($loadCall, $methodName, [$permissionsArg]),
            'save'
        );
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace user_role_grant_permissions(), user_role_revoke_permissions() and user_role_change_permissions() with the matching \Drupal\user\RoleInterface method on a role loaded with \Drupal\user\Entity\Role::loadOverrideFree().',
            [
                new ConfiguredCodeSample(
                    "user_role_grant_permissions('anonymous', ['access content']);",
                    "\\Drupal\\user\\Entity\\Role::loadOverrideFree('anonymous')?->grantPermissions(['access content'])?->save();",
                    [new DrupalIntroducedVersionConfiguration('11.5.0')]
                ),
                new ConfiguredCodeSample(
                    "user_role_revoke_permissions('anonymous', ['access content']);",
                    "\\Drupal\\user\\Entity\\Role::loadOverrideFree('anonymous')?->revokePermissions(['access content'])?->save();",
                    [new DrupalIntroducedVersionConfiguration('11.5.0')]
                ),
                new ConfiguredCodeSample(
                    "user_role_change_permissions('anonymous', ['access content' => TRUE]);",
                    "\\Drupal\\user\\Entity\\Role::loadOverrideFree('anonymous')?->changePermissions(['access content' => TRUE])?->save();",
                    [new DrupalIntroducedVersionConfiguration('11.5.0')]
                ),
            ]
        );
    }
}
