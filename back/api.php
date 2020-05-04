<?php

require_once 'evgeny/loader.php';
require_once 'dan/loader.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');

$output = new stdClass();

$input_from_client = file_get_contents('php://input');
$method_form_client = $_SERVER['REQUEST_METHOD'];
$request_data = json_decode($input_from_client); // null or user input in stdClass

// How to call controllers(providers) methods
// method => [ provider_name, provider_method, array_of_params ]
$methods = [
    #Evgeny
    'storagePut' => (object)['class' => 'storageProvider', 'method' => 'put', 'params' => ['authId', 'baseData']],
    'storageGet' => (object)['class' => 'storageProvider', 'method' => 'get', 'params' => ['authId']],
    'storageIsFound' => (object)['class' => 'storageProvider', 'method' => 'is_found', 'params' => ['authId']],
    'storageRemove' => (object)['class' => 'storageProvider', 'method' => 'remove', 'params' => ['authId']],

    'getLoginProvidersList' => (object)['class' => 'loginProvider', 'method' => 'get_variants', 'params' => []],
    'logout' => (object)['class' => 'loginProvider', 'method' => 'logout', 'params' => []],

    #Dan
    'registerBasic' => (object)['class' => 'loginProvider', 'method' => 'register_basic', 'params' => ['login', 'password']],
    'loginBasic' => (object)['class' => 'loginProvider', 'method' => 'login_basic', 'params' => ['login', 'password']],
    'user_lookup' => (object)['class' => 'usersProvider', 'method' => 'user_lookup', 'params' => ['login']],
    'get_link_vk' => (object)['class' => 'loginProvider', 'method' => 'get_link_vk'],
    'get_link_fb' => (object)['class' => 'loginProvider', 'method' => 'get_link_fb'],
    'client_key_store' => (object)['class' => 'keysController', 'method' => 'client_key_store', 'params' => ['client_key']]
];
// class => namespace
$namespaces = [
    'storageProvider' => 'evgeny\controllers\\',
    'loginProvider' => 'evgeny\controllers\\',
    'usersProvider' => 'dan\controllers\\',
    'keysController' => 'dan\controllers\\'
];


if (!in_array($request_data->method, array_keys($methods))) { // IF method not exist
    $output->errors[] = 'method not found';
    echo json_encode($output);
    return;
}

// Code below make call of needed class and method by $methods and $namespaces

$to_call = $methods[$request_data->method];
$class_name = $namespaces[$to_call->class] . $to_call->class;

foreach ($to_call->params as $param_name) // check for required params
    if (!isset($request_data->$param_name))
        $output->errors[] = "param {{$param_name}} error";

if ($output->errors) {
    echo json_encode($output);
    return;
}

if (class_exists($class_name)) {
    $Class = new $class_name();
    if (method_exists($Class, $to_call->method))
        $output->result = $Class->{$to_call->method}($request_data);
    else
        $output->errors[] = 'method error';
} else
    $output->errors[] = 'class error';

echo json_encode($output);
return;