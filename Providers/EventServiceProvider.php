<?php

namespace Modules\Sitemap\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Page\Events\PageWasUpdated;
use Modules\Page\Events\PageWasCreated;
use Modules\Page\Events\PageWasDeleted;
use Modules\Blog\Events\PostWasUpdated;
use Modules\Blog\Events\PostWasCreated;
use Modules\Blog\Events\PostWasDeleted;
use Modules\Sitemap\Events\Handlers\UpdateSiteMaps;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        PageWasUpdated::class => [
            UpdateSiteMaps::class,
        ],

        PageWasCreated::class => [
            UpdateSiteMaps::class,
        ],

        PageWasDeleted::class => [
            UpdateSiteMaps::class,
        ],

        PostWasUpdated::class => [
            UpdateSiteMaps::class,
        ],

        PostWasCreated::class => [
            UpdateSiteMaps::class,
        ],

        PostWasDeleted::class => [
            UpdateSiteMaps::class,
        ],
    ];
}
