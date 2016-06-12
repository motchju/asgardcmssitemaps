<?php

    use Illuminate\Routing\Router;

    /* @var Router $router */
    if (!App::runningInConsole()) {
        $router->get('sitemaps', ['uses' => 'PublicController@sitemap', 'as' => 'sitemaps']);
    }
