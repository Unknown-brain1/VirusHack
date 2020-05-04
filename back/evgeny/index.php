<?php

use evgeny\models\facebook;
use evgeny\models\storage;
use evgeny\models\vk;

require_once 'loader.php';

$vk = new vk();


$result = $vk->get_user_id('e4d247a62f97c298d8');

var_dump($result);