<?php

use dan\models\user;
use evgeny\controllers\loginProvider;

require_once $_SERVER['DOCUMENT_ROOT'] . '/evgeny/loader.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dan/loader.php';

require_once 'main_loader.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$fb = new Facebook\Facebook([
    'app_id' => getenv('FACEBOOK_ID'),
    'app_secret' => getenv('FACEBOOK_SECRET'),
    'default_graph_version' => 'v6.0',
]);

$helper = $fb->getRedirectLoginHelper();
//$loginUrl = $helper->getLoginUrl("https://{$_SERVER['HTTP_HOST']}/facebook.php");

if (isset($_REQUEST['code'])) { //
    try {
        $accessToken = $helper->getAccessToken();
    } catch (Facebook\Exception\FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch (Facebook\Exception\FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    } catch (Facebook\Exceptions\FacebookAuthenticationException $e) {
        echo 'WHAT A ERROR';
        echo $e->getMessage();
    } catch (Error $exception) {
        echo "Some error";
    }
    if (isset($accessToken)) {
        $_SESSION['facebook_access_token'] = (string)$accessToken;
    }

    $oAuth2Client = $fb->getOAuth2Client();
    $platform_user_id = $oAuth2Client->debugToken($accessToken)->getUserId();


    $LoginProvider = new loginProvider();
    $Facebook = new \evgeny\models\facebook();
    $user_id = $Facebook->get_user_id_by_platform_user_id($platform_user_id);
    if ($user_id) {
        $LoginProvider->auth_by_id($user_id);
        echo 'User ID есть, должны были авторизовать';
    } else {
        #creates new user class
        $User = new user();

        #stores new user into Users table
        $token = $User->oauth_store($platform_user_id);
        #gets user_id of new user
        $user_id = $User->get_id_by_token($token);

        #stores new user into oauth
        $Facebook->store($user_id, $platform_user_id);

        #sets session for new User
        $LoginProvider->auth_by_id($user_id);
        echo 'Должны были зарегать юзера, и авторизовать';
    }

}


?>
<a href="/fb_start.php">Auth page</a><br>
<p>ERROR HELL</p>
<pre>
    <?php print_r($_REQUEST) ?>
</pre>
