<?php
require_once 'main_loader.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$notifications = [
    [
        'subscription' => Subscription::create([
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/cyZIkzYXqx0:APA91bHQFEJcgBZGxO4Ns9Kyh0N-iYxP4HHBimLlJCIm1nz-TM9RsM6oMI5_bGyF0kuhMSQFjaDhJqs1MWTXtW4nPEaaBOVv6PqPKnADlOFSgTdiprvJl-c0jkzKTn9bO8njdlQKfFm8',
            'contentEncoding' => 'aesgcm',
        ]),
        'payload' => 'work',
    ],
    [ // mobile
        'subscription' => Subscription::create([
            'endpoint' => 'https://updates.push.services.mozilla.com/wpush/v2/gAAAAABesKz5EPBCT2bVTsN4zQfReXYc5wCePpleUWhHKMKbaZPvnlXbWinsHaXEu6eV1NeWua8oFhCkZKW-_uSrgibUwLhNdAkLttmM2YEOjOAsEK2oIRbe6-ii036Vi6Pj6LoIhC99ZPsAsGsk6S9ZU-4xHve_jpQNmzLfeziNpC844ZwwXFc',
            'contentEncoding' => 'aesgcm',
        ]),
        'payload' => '{"text":"Hello World!"}',
    ],
    [ // pc
        'subscription' => Subscription::create([
            'endpoint' => 'https://updates.push.services.mozilla.com/wpush/v2/gAAAAABesKXPtb4qC3rWLagUkTrs-VljQc2iHDOjEM0dU_eKREzrcxe_CAhMuZ-kXiZgbyNgEVOW9OkbuAEscww9IiG4UkiJtYNX2OkOb-COoaN4RzQCrJHRixTxqiti_OZIqlvCpaY9CY7kVtiKOOIkSrUnL9hnr8o3NeOlTA4XinTOhG2hk54',
            'contentEncoding' => 'aesgcm',
        ]),
        'payload' => '{"text":"Hello PC"}',
    ]
];

$auth = [
//    'GCM' => 'MY_GCM_API_KEY', // deprecated and optional, it's here only for compatibility reasons
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
        echo "[v] Message sent successfully for subscription {$endpoint}.";
    } else {
        echo "[x] Message failed to sent for subscription {$endpoint}: {$report->getReason()}";
    }
    echo '<BR>';
}