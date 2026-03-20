<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Illuminate\View\Component;

class Button extends Component
{
    public function render()
    {
        return <<<'blade'
            <button type="button" {{ $attributes->withoutTwMergeClasses()->merge(['class' => 'p-4']) }}>
                <svg {{ $attributes->twMergeFor('icon', 'h-5 w-5 text-gray-500') }}></svg>
            </button>
        blade;
    }
}
