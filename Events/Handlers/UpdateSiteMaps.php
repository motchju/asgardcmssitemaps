<?php

namespace Modules\Sitemap\Events\Handlers;

use Modules\Sitemap\Services\SiteMapsGenerator;

class UpdateSiteMaps
{
    /**
     * @var siteMaps
     */
    private $siteMaps;

    public function __construct(SiteMapsGenerator $siteMaps)
    {
        $this->siteMaps = $siteMaps;
    }

    public function handle()
    {
        $this->siteMaps->generateSiteMaps();
    }
}
