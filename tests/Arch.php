<?php

declare(strict_types=1);

test('facades')
    ->expect('MarcoRieser\TailwindMergeLaravel\Facades\TailwindMerge')
    ->toOnlyUse([
        'Illuminate\Support\Facades\Facade',
    ]);

test('service providers')
    ->expect('MarcoRieser\TailwindMergeLaravel\TailwindMergeServiceProvider')
    ->toOnlyUse([
        'Illuminate\Contracts\Support\DeferrableProvider',
        'Illuminate\Support\ServiceProvider',
        'Illuminate\View\Compilers\BladeCompiler',
        'Illuminate\View\ComponentAttributeBag',
        'TailwindMerge',

        // helpers...
        'app',
        'config',
        'config_path',
        'resolve',
    ]);
