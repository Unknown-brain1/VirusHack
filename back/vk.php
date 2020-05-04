<?php

use dan\models\user;
use evgeny\controllers\loginProvider;
use evgeny\models\vk;

require_once $_SERVER['DOCUMENT_ROOT'] . '/evgeny/loader.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dan/loader.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
$Vk = new vk();


$output = new stdClass();
if ($_GET['code'] != NULL) {
    $vk_user_id = $Vk->get_user_id($_GET['code']);
    # *** 1 *** Проверить существует ли такой юзер в оаутх и если существует получить его токен и юзер ид и записать в сессию.
    # *** 2 *** Если не существет - вызвать метод создания пользователя в юзере,
    # *** 3 *** а после - создания токена в классе вк и
    # *** 4 *** после этого авторизовать пользователя, записав в сессию токен и юзер ид. Авторизовывать через loginProvider.
    ########################

    if (!$vk_user_id){
        $output->errors[] = 'vk fail';
        echo json_encode($output);
        return;
    }

    # *** 1 ***
    $LoginProvider = new loginProvider();

    $user_id = $Vk->get_user_id_by_platform_user_id($vk_user_id);

    if ($user_id) {
        $token = $LoginProvider->auth_by_id($user_id);
        $output->result->user_token = $token;
    } else {
        #creates new user class
        $User = new user();

        #stores new user into Users table
        $token = $User->oauth_store($vk_user_id);

        #gets user_id of new user
        $user_id = $User->get_id_by_token($token);

        #stores new user into oauth
        $Vk->store($user_id, $vk_user_id);

        #sets session for new User
        $LoginProvider->auth_by_id($user_id);
        $output->result->user_token = $token;
    }
} else {
   $output->errors[] = 'code fail';
}

echo json_encode($output);
return;