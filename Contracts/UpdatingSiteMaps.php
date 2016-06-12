<?php

namespace Modules\SiteMaps\Contracts;

interface UpdatingSiteMaps
{
    /**
     * Return the entity.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getEntity();
    /**
     * Return the ALL data sent.
     *
     * @return array
     */
    public function getSubmissionData();
}
