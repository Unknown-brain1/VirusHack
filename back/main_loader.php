<?php
session_start();
use Dotenv\Dotenv;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

$config = $_SERVER['DOCUMENT_ROOT'] . '/';
$env = Dotenv::createImmutable($config,'settings.env');
$env->load();

// example of get DB HOST - getenv('DB_HOST');