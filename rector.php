<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\ValueObject\PhpVersion;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $rectorConfig->phpVersion(PhpVersion::PHP_82);

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_82,
        SetList::CODE_QUALITY,
        SetList::TYPE_DECLARATION,
        SetList::DEAD_CODE,
        SetList::STRICT_BOOLEANS,
        SetList::PHP_82,
    ]);

    $rectorConfig->skip([DisallowedEmptyRuleFixerRector::class]);

};
