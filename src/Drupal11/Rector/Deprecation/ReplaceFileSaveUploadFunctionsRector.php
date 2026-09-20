<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces the deprecated form file upload functions with their services.
 *
 * Handles file_save_upload(), file_managed_file_save_upload() and
 * _file_save_upload_from_form(). Core moved each function body into
 * \Drupal\file\Upload\FormFileUploader or
 * \Drupal\file\Upload\ManagedFileElementHelper and left the function as a
 * delegating wrapper, so the argument order carries over unchanged.
 *
 * The one thing the wrapper did that the service method does not is normalise
 * its arguments: file_save_upload() turned a FALSE/NULL $destination into
 * 'temporary://' before delegating, and both it and
 * _file_save_upload_from_form() ran a legacy integer $fileExists through
 * \Drupal\Core\File\FileExists::fromLegacyInt(). The service parameters are
 * typed string and FileExists, so this rector rewrites a literal FALSE/NULL
 * destination and skips a call that passes an integer literal for $fileExists.
 *
 * Deprecated in drupal:11.5.0 and removed in drupal:13.0.0.
 *
 * @see https://www.drupal.org/node/3375423
 * @see https://www.drupal.org/node/3382414
 */
class ReplaceFileSaveUploadFunctionsRector extends AbstractDrupalCoreRector
{
    /**
     * The PHPStan deprecation messages this rector covers.
     *
     * Captured against drupal/core 11.x-dev via tmgmt, file_history and
     * gin_login, normalised with scripts/normalize-phpstan-message.php.
     */
    public const PHPSTAN_MESSAGES = [
        'Call to deprecated function file_save_upload(). Deprecated in drupal:11.5.0 and is removed from drupal:13.0.0. Use Drupal::service(FormFileUploader::class)->saveFormUploadedFiles() instead.',
        'Call to deprecated function file_managed_file_save_upload(). Deprecated in drupal:11.5.0 and is removed from drupal:13.0.0. Use Drupal::service(ManagedFileElementHelper::class)->managedFileSaveUpload() instead.',
        'Call to deprecated function _file_save_upload_from_form(). Deprecated in drupal:11.5.0 and is removed from drupal:13.0.0. Use Drupal::service(ManagedFileElementHelper::class)->saveFileUploads() instead.',
    ];

    private const FORM_FILE_UPLOADER = 'Drupal\file\Upload\FormFileUploader';

    private const MANAGED_FILE_ELEMENT_HELPER = 'Drupal\file\Upload\ManagedFileElementHelper';

    /**
     * Maps each deprecated function to its service class, method and arity.
     *
     * The argument counts are core's own: anything outside that range is not a
     * call to the deprecated function as it was ever declared.
     *
     * @var array<string, array{class: string, method: string, minArgs: int, maxArgs: int, fileExistsPosition: int|null, destinationPosition: int|null}>
     */
    private const FUNCTION_MAP = [
        'file_save_upload' => [
            'class' => self::FORM_FILE_UPLOADER,
            'method' => 'saveFormUploadedFiles',
            'minArgs' => 1,
            'maxArgs' => 5,
            'fileExistsPosition' => 4,
            'destinationPosition' => 2,
        ],
        'file_managed_file_save_upload' => [
            'class' => self::MANAGED_FILE_ELEMENT_HELPER,
            'method' => 'managedFileSaveUpload',
            'minArgs' => 2,
            'maxArgs' => 2,
            'fileExistsPosition' => null,
            'destinationPosition' => null,
        ],
        '_file_save_upload_from_form' => [
            'class' => self::MANAGED_FILE_ELEMENT_HELPER,
            'method' => 'saveFileUploads',
            'minArgs' => 2,
            'maxArgs' => 4,
            'fileExistsPosition' => 3,
            'destinationPosition' => null,
        ],
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

        $replacement = self::FUNCTION_MAP[$node->name->toString()] ?? null;
        if ($replacement === null) {
            return null;
        }

        $args = $node->args;
        $argCount = count($args);
        if ($argCount < $replacement['minArgs'] || $argCount > $replacement['maxArgs']) {
            return null;
        }

        // Named arguments and argument unpacking are too rare here to be worth
        // resolving into positional order.
        foreach ($args as $arg) {
            if (!$arg instanceof Arg || $arg->name !== null || $arg->unpack) {
                return null;
            }
        }

        // The deprecated functions accepted FileExists|int and converted a
        // legacy integer with FileExists::fromLegacyInt(); the service methods
        // only accept the enum. A FileSystemInterface::EXISTS_* constant is
        // rewritten to its enum case by the drupal-10.3 set, so the only shape
        // left is a bare integer literal, which is left alone for a human.
        $fileExistsPosition = $replacement['fileExistsPosition'];
        if ($fileExistsPosition !== null && isset($args[$fileExistsPosition]) && $args[$fileExistsPosition]->value instanceof Int_) {
            return null;
        }

        // file_save_upload() normalised a FALSE/NULL $destination to
        // 'temporary://'; saveFormUploadedFiles() types the parameter string.
        $destinationPosition = $replacement['destinationPosition'];
        if ($destinationPosition !== null && isset($args[$destinationPosition]) && $this->isFalseOrNull($args[$destinationPosition]->value)) {
            $args[$destinationPosition] = new Arg(new String_('temporary://'));
        }

        $serviceCall = new StaticCall(
            new FullyQualified('Drupal'),
            'service',
            [new Arg(new ClassConstFetch(new FullyQualified($replacement['class']), 'class'))]
        );

        return new MethodCall($serviceCall, $replacement['method'], $args);
    }

    private function isFalseOrNull(Node\Expr $expr): bool
    {
        if (!$expr instanceof ConstFetch) {
            return false;
        }

        return in_array(strtolower($expr->name->toString()), ['false', 'null'], true);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace file_save_upload(), file_managed_file_save_upload() and _file_save_upload_from_form() with the \Drupal\file\Upload\FormFileUploader and \Drupal\file\Upload\ManagedFileElementHelper services.',
            [
                new ConfiguredCodeSample(
                    "\$file = file_save_upload('upload', \$validators, FALSE, 0);",
                    "\$file = \\Drupal::service(\\Drupal\\file\\Upload\\FormFileUploader::class)->saveFormUploadedFiles('upload', \$validators, 'temporary://', 0);",
                    [new DrupalIntroducedVersionConfiguration('11.5.0')]
                ),
                new ConfiguredCodeSample(
                    '$files = file_managed_file_save_upload($element, $form_state);',
                    '$files = \Drupal::service(\Drupal\file\Upload\ManagedFileElementHelper::class)->managedFileSaveUpload($element, $form_state);',
                    [new DrupalIntroducedVersionConfiguration('11.5.0')]
                ),
                new ConfiguredCodeSample(
                    '$result = _file_save_upload_from_form($element, $form_state, 0);',
                    '$result = \Drupal::service(\Drupal\file\Upload\ManagedFileElementHelper::class)->saveFileUploads($element, $form_state, 0);',
                    [new DrupalIntroducedVersionConfiguration('11.5.0')]
                ),
            ]
        );
    }
}
