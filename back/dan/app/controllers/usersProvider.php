<?php


namespace dan\controllers;


use dan\models\user;
use stdClass;

class usersProvider
{
    public function register(stdClass $input)
    {
        $User = new user();
        $result = $User->store($input->login, $input->password);
        return ['user_token' => $result];
    }

    public function password_login(stdClass $input)
    {
        $User = new user();
        $result = $User->get($input->login, $input->password);
        return ['user_token' => $result];
    }
    public function user_lookup(stdClass $input)
    {
        $User = new user();
        return $User->exists($input->login);
    }


}