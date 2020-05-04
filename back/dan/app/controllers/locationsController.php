<?php


namespace dan\controllers;


use dan\models\keys_model;
use stdClass;

class locationsController
{
    public function client_key_store(stdClass $input)
    {
        $Keys_model = new keys_model();
        $result = $Keys_model->client_key_store($input -> client_key);
        return $result;
    }
}