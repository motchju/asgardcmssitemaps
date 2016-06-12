<?php

namespace Modules\SiteMap\Services;

use Illuminate\Contracts\Foundation\Application;
use Modules\Page\Entities\Page;
use Modules\Blog\Entities\Post;
use LaravelLocalization;

class SiteMapsGenerator
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var PageRepository
     */
    private $page;

    /**
     * @var PostRepository
     */
    private $post;

    /**
     * @var SiteMaps
     */
    private $sitemap;

    public function __construct(Application $app, Page $page, Post $post)
    {
        $this->app = $app;
        $this->page = $page;
        $this->post = $post;
        $this->sitemap = $this->app->make('sitemap');
        $this->availableLanguage = $this->getAllLanguageAvailable();
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
        $collection = $this->page->translated()->get();
        $this->getTranslation($collection, ['route' => 'page', 'params' => ['uri' => 'slug']], '0.8', 'monthly');
    }

    private function allPosts()
    {
        $collection = $this->post->translated()->where('status', 2)->get();
        $this->getTranslation($collection, ['route' => 'blog.slug', 'locale' => true, 'params' => ['uri' => 'slug']], '0.6', 'monthly');
    }
}
