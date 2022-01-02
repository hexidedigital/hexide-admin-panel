<?php

namespace HexideDigital\HexideAdmin\Providers;

use HexideDigital\HexideAdmin\Classes\DBTranslations\DBLoader;
use HexideDigital\HexideAdmin\Classes\DBTranslations\MixedLoader;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\TranslationServiceProvider as LaravelTranslationServiceProvider;
use Illuminate\Translation\Translator;

class TranslationServiceProvider extends LaravelTranslationServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerLoaders();

        $this->app->singleton(
            'translator',
            function ($app) {
                $loader = $app['translation.loader'];

                // When registering the translator component, we'll need to set the default
                // locale as well as the fallback locale. So, we'll grab the application
                // configuration so we can easily get both of these values from there.
                $locale = $app['config']['app.locale'];

                $trans = new Translator($loader, $locale);

                $trans->setFallback($app['config']['app.fallback_locale']);

                return $trans;
            }
        );
    }

    /**
     * Register the translation line loader.
     *
     * @return void
     */
    protected function registerLoaders()
    {
        $this->app->singleton(
            'translation.loader',
            function ($app) {
                $databaseLoader = new DBLoader();
                $fileLoader = new FileLoader($app['files'], $app['path.lang']);

                return new MixedLoader($databaseLoader, $fileLoader);
            }
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return ['translator', 'translation.loader'];
    }
}
