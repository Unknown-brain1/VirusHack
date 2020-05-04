<?php


namespace evgeny\models;


class vk extends oauth
{
    public function __construct()
    {
        parent::__construct('vk');
    }

    public function get_user_id($oauth_code)
    {
        $curl = curl_init();

        $auth = [
            'client_id' => getenv('VK_ID'),
            'client_secret' => getenv('VK_SECRET'),
            'redirect_uri' => 'https://hack.triptip.tours/vk.php',
            'code' => $oauth_code
        ];

        $params = http_build_query($auth);
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://oauth.vk.com/access_token?$params",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return false; // todo log
        }
        $response = json_decode($response);
        if (isset($response->user_id))
            return $response->user_id;
        var_dump($response);
        return false;
    }
}
