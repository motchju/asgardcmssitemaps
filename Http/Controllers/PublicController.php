<?php

namespace Modules\SiteMap\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Modules\Core\Http\Controllers\BasePublicController;
use Modules\Sitemap\Services\SiteMapsGenerator;

class PublicController extends BasePublicController
{
    /**
     * @var Application
     */
    private $app;

    private $siteMaps;

    public function __construct(Application $app, SiteMapsGenerator $siteMaps)
    {
        parent::__construct();
        $this->app = $app;
        $this->siteMaps = $siteMaps;
    }

    public function sitemap()
    {
        $result = $this->siteMaps->generateSiteMaps();

        return $result;
    }
}
