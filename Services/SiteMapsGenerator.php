<?php

namespace Modules\SiteMap\Services;

use Illuminate\Contracts\Foundation\Application;
    use Modules\Workshop\Manager\ModuleManager;
    use LaravelLocalization;

    class SiteMapsGenerator
    {
        /**
         * @var Application
         */
        private $app;

        private $moduleManager;

        /**
         * @var SiteMaps
         */
        private $sitemap;

        public function __construct(Application $app, ModuleManager $moduleManager)
        {
            $this->app = $app;
            $this->sitemap = $this->app->make('sitemap');
            $this->availableLanguage = $this->getAllLanguageAvailable();
            $this->moduleManager = $moduleManager->enabled();
        }

        public function generateSiteMaps()
        {
            $this->itemSiteMaps();

            $this->sitemap->store('xml', 'mysitemap');

            return $this->sitemap->render('xml');
        }

        private function itemSiteMaps()
        {
            // For Pages
            $this->allPages();

            // For Blog, all Posts
            $this->allPosts();
        }

        private function getTranslation($collection, $route, $priority, $frequency)
        {
            $allLanguage = $this->availableLanguage;
            $firstLanguage = $this->availableLanguage[0];
            unset($allLanguage[0]);

            $defaultPriority = $priority;
            $defaultFrequency = $frequency;

            foreach ($collection as $item) {
                $translations = [];

                if (count($allLanguage)) {
                    foreach ($allLanguage as $key => $language) {
                        $translations[] = ['language' => $language, 'url' => $this->getRouteParams($item->translate($language), $route, $language)];
                    }
                }

                /* If home page, else default Priority */
                $priority = isset($item->is_home) && $item->is_home === 1 ? '1.0' : $defaultPriority;
                $frequency = isset($item->is_home) && $item->is_home === 1 ? 'weekly' : $defaultFrequency;

                $this->sitemap->add($this->getRouteParams($item->translate($firstLanguage), $route, $firstLanguage), $item->updated_at, $priority, $frequency, [], null, $translations);
            }
        }

        private function getRouteParams($item, $route, $language)
        {
            $params = [];

            foreach ($route['params'] as $key => $value) {
                $params = array_merge($params, ["$key" => $item->{$value}]);
            }

            $routeParams = $route['route'];
            $locale = locale();

            if (array_key_exists('locale', $route)) {
                $routeParams = $locale.'.'.$routeParams;
            }

            if ($locale !== $language) {
                $defaultUrl = route($routeParams, $params);

                return LaravelLocalization::getLocalizedURL($language, $defaultUrl);
            }

            return route($routeParams, $params);
        }

        private function getAllLanguageAvailable()
        {
            return LaravelLocalization::getSupportedLanguagesKeys();
        }

        private function allPages()
        {
            $value = ['name' => 'Page', 'entities' => 'Modules\Page\Entities\Page'];
            $query = $this->moduleIsPresentAndEnable($value);

            if ($query) {
                $collection = $query->translated()->get();
                $this->getTranslation($collection, ['route' => 'page', 'params' => ['uri' => 'slug']], '0.8', 'monthly');
            }
        }

        private function allPosts()
        {
            $value = ['name' => 'Blog', 'entities' => 'Modules\Page\Entities\Blog'];
            $query = $this->moduleIsPresentAndEnable($value);

            if ($query) {
                $collection = $this->post->translated()->where('status', 2)->get();
                $this->getTranslation($collection, ['route' => 'blog.slug', 'locale' => true, 'params' => ['uri' => 'slug']], '0.6', 'monthly');
            }
        }

        private function moduleIsPresentAndEnable(array $value)
        {
            if (array_key_exists($value['name'], $this->moduleManager)) {
                return $this->app->make($value['entities']);
            }

            return false;
        }
    }
