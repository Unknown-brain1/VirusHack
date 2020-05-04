<?php


namespace dan\controllers;


use dan\models\locations;

class locationsProvider
{
    public function locations_get()
    {
        $Locations = new locations();
        return $Locations->locations_provide();
    }
}