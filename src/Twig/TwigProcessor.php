<?php

declare(strict_types=1);

namespace DrupalRector\Twig;

use DrupalRector\Twig\Transformer;
use Rector\Core\Contract\Processor\FileProcessorInterface;
use Rector\Core\Provider\CurrentFileProvider;
use Rector\Core\ValueObject\Application\File;

final class TwigProcessor implements FileProcessorInterface
{
    /**
     * @var string[]
     */
    private const ALLOWED_FILE_EXTENSIONS = ['twig'];

    /**
     * @var string[]
     */
    private $transformerClasses = [
        Transformer\TwigReplaceTransformer::class,
    ];

    /**
     * @param File[] $files
     */
    public function process(array $files): void
    {
        foreach ($files as $file) {
            $this->processFile($file);
        }
    }

    public function supports(File $file): bool
    {
        $smartFileInfo = $file->getSmartFileInfo();
        return $smartFileInfo->hasSuffixes($this->getSupportedFileExtensions());
    }

    public function getSupportedFileExtensions(): array
    {
        return self::ALLOWED_FILE_EXTENSIONS;
    }

    private function processFile(File $file): void
    {
        $fileContent = $file->getFileContent();

        foreach ($this->transformerClasses as $transformerClass) {
            $transformer = new $transformerClass;
            $changedFileContent = $transformer->transform($fileContent);
        }

        $file->changeFileContent($changedFileContent);
    }
}
