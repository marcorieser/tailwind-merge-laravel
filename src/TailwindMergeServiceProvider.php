<?php

declare(strict_types=1);

namespace MarcoRieser\TailwindMergeLaravel;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\ComponentAttributeBag;
use TalesFromADev\TailwindMerge\TailwindMerge;
use TalesFromADev\TailwindMerge\TailwindMergeInterface;

class TailwindMergeServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TailwindMergeInterface::class, static fn (): TailwindMerge => new TailwindMerge(
            additionalConfiguration: config('tailwind-merge', []),
            cache: app('cache')->store(),
        ));

        $this->app->alias(TailwindMergeInterface::class, 'tailwind-merge');
        $this->app->alias(TailwindMergeInterface::class, TailwindMerge::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/tailwind-merge.php' => config_path('tailwind-merge.php'),
            ]);
        }

        $this->registerBladeDirectives();
        $this->registerAttributesBagMacros();
    }

    protected function registerBladeDirectives(): void
    {
        $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler): void {
            $name = config('tailwind-merge.blade_directive', 'twMerge');

            if ($name === null) {
                return;
            }

            $bladeCompiler->directive($name, fn (?string $expression): string => "<?php echo twMerge($expression); ?>");
        });
    }

    protected function registerAttributesBagMacros(): void
    {
        ComponentAttributeBag::macro('twMerge', function (...$args): ComponentAttributeBag {
            /** @var ComponentAttributeBag $this */
            $this->offsetSet('class', resolve(TailwindMergeInterface::class)->merge($args, ($this->get('class', ''))));

            return $this;
        });

        ComponentAttributeBag::macro('twMergeFor', function (string $for, ...$args): ComponentAttributeBag {
            /** @var ComponentAttributeBag $this */

            /** @var TailwindMergeInterface $instance */
            $instance = resolve(TailwindMergeInterface::class);

            $attribute = 'class'.($for !== '' ? ':'.$for : '');

            /** @var string $classes */
            $classes = $this->get($attribute, '');

            $this->offsetSet('class', $instance->merge($args, $classes));

            return $this->only('class');
        });

        ComponentAttributeBag::macro('withoutTwMergeClasses', function (): ComponentAttributeBag {
            /** @var ComponentAttributeBag $this */
            return $this->whereDoesntStartWith('class:');
        });
    }

    /**
     * @return array<class-string<TailwindMergeInterface>>|string[]
     */
    public function provides(): array
    {
        return [
            TailwindMerge::class,
            TailwindMergeInterface::class,
            'tailwind-merge',
        ];
    }
}
