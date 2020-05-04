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
    'get_link_fb' => (object)['class' => 'loginProvider', 'method' => 'get_link_fb']