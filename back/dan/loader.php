<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/main_loader.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/evgeny/loader.php';

// Main classes
#require_once 'app/database.php';

// Models
require_once 'app/models/user.php';
require_once 'app/models/keys_model.php';
require_once 'app/models/locations.php';

// Controllers
require_once 'app/controllers/usersProvider.php';
require_once 'app/controllers/keysController.php';
require_once 'app/controllers/locationsProvider.php';