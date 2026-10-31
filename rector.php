<?php

declare(strict_types=1);

use Pest\Rector\Set\PestSetList;
use Rector\CodeQuality\Rector\FunctionLike\SimplifyUselessVariableRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        earlyReturn: true,
    )
    ->withSets([
        PestSetList::CODING_STYLE,
    ])
    ->withImportNames(removeUnusedImports: true)
    ->withSkip([
        // A docs sample ends on the variable it shows; inlining it into the
        // return moves the code out of the region the page imports.
        SimplifyUselessVariableRector::class => [__DIR__.'/tests/Docs/samples'],
    ]);
