<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/app',
        __DIR__ . '/database',
        __DIR__ . '/routes',
    ]);

    $rectorConfig->phpVersion(PhpVersion::PHP_82);

    $rectorConfig->sets([
        SetList::CODE_QUALITY,
        SetList::TYPE_DECLARATION,
        SetList::DEAD_CODE,
        SetList::NAMING,
        SetList::EARLY_RETURN,
        SetList::PRIVATIZATION,
    ]);

    $rectorConfig->importNames();
};
