<?php


namespace evgeny\controllers;

use dan\models\user;
use dan\controllers\usersProvider;
use stdClass;
use Facebook;

class loginProvider
{
    public function get_variants()
    {
        $variants = [
            [
                'id' => 'basic',
                'icon' => 'basic.png',
                'register_method' => 'registerBasic',
                'login_method' => 'loginBasic'
            ],
            [
                'id' => 'facebook',
                'icon' => 'facebook.png',
                'register_method' => 'TODO',
                'login_method' => 'TODO'
            ],
            [
                'id' => 'vk',
                'icon' => 'vk.png',
                'register_method' => 'TODO',
                'login_method' => 'TODO'
            ]
        ];

        return $variants;
    }

    public function login_basic(stdClass $input)
    {
        $UserProvider = new usersProvider();
        $user_token = $UserProvider->password_login($input);
        if ($user_token) $this->auth_by_token($user_token);
        return $user_token;
    }

    public function register_basic(stdClass $input)
    {
        $UserProvider = new usersProvider();
        $user_token = $UserProvider->register($input);
        if ($user_token) $this->auth_by_token($user_token);
        return $user_token;
    }

    public function get_link_vk()
    {
        return "https://oauth.vk.com/authorize?client_id=7447443&redirect_uri=https://hack.triptip.tours/vk.php&display=popup";
    }

    public function get_link_fb()
    {
        $fb = new Facebook\Facebook([
            'app_id' => getenv('FACEBOOK_ID'),
            'app_secret' => getenv('FACEBOOK_SECRET'),
            'default_graph_version' => 'v6.0',
        ]);

        $helper = $fb->getRedirectLoginHelper();
        return $helper->getLoginUrl("https://{$_SERVER['HTTP_HOST']}/facebook.php");

    }

    public function auth_by_token($user_token)
    {
        $user_id = (new user())->get_id_by_token($user_token);
        $_SESSION['user_token'] = $user_token;
        $_SESSION['user_id'] = $user_id;
        $_COOKIE['user_token'] = $user_token;

        return $user_id;
    }

    public function auth_by_id($user_id)
    {
        $user_token = (new user())->get_token_by_id($user_id);
        $_SESSION['user_token'] = $user_token;
        $_SESSION['user_id'] = $user_id;
        $_COOKIE['user_token'] = $user_token;

        return $user_token;
    }

    public function logout()
    {
        $_SESSION['user_id'] = null;
        $_SESSION['user_token'] = null;
        return true;
    }
}