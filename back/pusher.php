<?php
require_once 'main_loader.php';
require_once 'dan/loader.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

$message = 'Самое время надеть маску!';


$Keys = new \dan\models\keys_model();
$users = $Keys->get_all();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$notifications = [];

foreach ($users as $user) {
    $data = json_decode($user->client_endpoint);
    $notifications[] = [
        'subscription' => Subscription::create([
            'endpoint' => $data->endpoint,
            'contentEncoding' => 'aes128gcm',
            "keys" => [
                'p256dh' => $data->keys->p256dh,
                'auth' => $data->keys->auth
            ],
        ]),
        'payload' => $message,
    ];
}

$auth = [
    'VAPID' => [
        'subject' => 'https://pwa.coxel.ru/', // can be a mailto: or your website address
        'publicKey' => getenv('PUSH_PUBLIC'), // (recommended) uncompressed public key P-256 encoded in Base64-URL
        'privateKey' => getenv('PUSH_PRIVATE'), // (recommended) in fact the secret multiplier of the private key encoded in Base64-URL
    ],
];

$webPush = new WebPush($auth);
// send multiple notifications with payload
foreach ($notifications as $notification) {
    $webPush->sendNotification(
        $notification['subscription'],
        $notification['payload']);
}

/**
 * Check sent results
 * @var MessageSentReport $report
 */
foreach ($webPush->flush() as $report) {
    $endpoint = $report->getRequest()->getUri()->__toString();

    if ($report->isSuccess()) {
        echo "[v] Message sent";
    } else {
        echo "[x] Message failed";
    }
    echo '<BR>';
}