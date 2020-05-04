<?php


namespace dan\controllers;


use dan\models\keys_model;
use stdClass;

class keysController
{
    public function client_key_store(stdClass $input)
    {
        $Keys_model = new keys_model();
        return $Keys_model->client_key_store($input->client_endpoint);
    }
}