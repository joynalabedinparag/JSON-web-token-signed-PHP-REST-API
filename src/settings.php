<?php
ini_set('display_errors','0');

define(DB_HOSTNAME, "localhost");
define(DB_USERNAME, "root");
define(DB_PASSWORD, "");
define(DB_DATABASE, "");

$db_conn = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
if (mysqli_connect_error()) {
    die('DB Connect Error');
}

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
        ],
        // Database Settings
        'db' =>[
           'con' =>$db_conn,
        ],

    ],
];
