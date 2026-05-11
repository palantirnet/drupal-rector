<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FunctionToFirstArgMethodRector;
use DrupalRector\Rector\ValueObject\FunctionToFirstArgMethodConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(FunctionToFirstArgMethodRector::class, $rectorConfig, false, [
        new FunctionToFirstArgMethodConfiguration('11.3.0', 'comment_uri', 'permalink'),
        new FunctionToFirstArgMethodConfiguration('11.3.0', 'node_type_get_description', 'getDescription'),
        new FunctionToFirstArgMethodConfiguration('11.2.0', 'file_get_content_headers', 'getDownloadHeaders'),
    ]);
};
