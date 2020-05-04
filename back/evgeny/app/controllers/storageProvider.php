<?php


namespace evgeny\controllers;


use evgeny\models\storage;
use stdClass;

class storageProvider
{
    //authId === storage->token
    public function put(stdClass $input)
    {
        $Storage = new storage($input->authId);
        return $Storage->store($input->baseData);
    }

    public function get(stdClass $input)
    {
        $Storage = new storage($input->authId);
        return $Storage->load();
    }

    public function is_found(stdClass $input)
    {

        $Storage = new storage($input->authId);
        return $Storage->is_isset();
    }

    public function remove(stdClass $input)
    {
        $Storage = new storage($input->authId);
        return $Storage->remove();
    }
}