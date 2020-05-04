<?php
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
$loginUrl = $helper->getLoginUrl("https://{$_SERVER['HTTP_HOST']}/facebook.php");

if (isset($_SESSION['facebook_access_token'])) {
    $oAuth2Client = $fb->getOAuth2Client();
    $user_id = $oAuth2Client->debugToken($_SESSION['facebook_access_token'])->getUserId();
}
?>

<a href="<?php echo $loginUrl ?>">Auth</a><br>
<b>You token: <?php echo $_SESSION['facebook_access_token'] ?? 'NULL' ?></b><br><br>
<b>User id: <?php echo $user_id ?? 'NULL' ?></b>
