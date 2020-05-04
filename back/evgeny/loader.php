<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/main_loader.php';

// Main classes
require_once 'app/database.php';

// Models
require_once 'app/models/storage.php';
require_once 'app/models/oauth.php';
require_once 'app/models/facebook.php';
require_once 'app/models/vk.php';

// Controllers
require_once 'app/controllers/storageProvider.php';
require_once 'app/controllers/loginProvider.php';