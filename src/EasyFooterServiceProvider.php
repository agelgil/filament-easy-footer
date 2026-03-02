<?php

namespace Agelgil\FilamentEasyFooter;

use Agelgil\FilamentEasyFooter\Livewire\GitHubVersion;
use Agelgil\FilamentEasyFooter\Services\GitHubService;
use Agelgil\FilamentEasyFooter\Testing\TestsEasyFooter;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EasyFooterServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-easy-footer';

    public static string $viewNamespace = 'filament-easy-footer';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('agelgil/filament-easy-footer');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../resources/dists/'))) {
            $package->hasAssets();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        $this->app->singleton(GitHubService::class, function ($app) {
            return new GitHubService(
                repository: config('filament-easy-footer.github.repository'),
                token: config('filament-easy-footer.github.token'),
                cacheTtl: config('filament-easy-footer.github.cache_ttl', 3600),
            );
        });

        Livewire::component('agelgil.filament-easy-footer.github-version', GitHubVersion::class);

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament-easy-footer/{$file->getFilename()}"),
                ], 'filament-easy-footer-stubs');
            }
        }

        Testable::mixin(new TestsEasyFooter);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'agelgil/filament-easy-footer';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }
}
