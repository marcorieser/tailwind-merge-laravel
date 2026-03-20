<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use MarcoRieser\TailwindMergeLaravel\Facades\TailwindMerge;
use MarcoRieser\TailwindMergeLaravel\TailwindMergeServiceProvider;

it('resolves resources', function () {
    $app = app();

    $app->bind('config', fn () => new Repository([
        'tailwind-merge' => [
        ],
    ]));

    (new TailwindMergeServiceProvider($app))->register();

    TailwindMerge::setFacadeApplication($app);

    expect(TailwindMerge::merge('h-4 h-6'))
        ->toBe('h-6');
});
