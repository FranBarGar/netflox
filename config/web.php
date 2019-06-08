<?php

use kartik\datecontrol\Module;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$log = require __DIR__ . '/log.php';

$config = [
    'id' => 'basic',
    'defaultRoute' => '/site/login',
    'name' => 'Netflox',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        '@uploads' => 'uploads',
        '@images' => 'images',
        '@aws' => 'aws',
        '@user' => '@aws/user',
        '@cover' => '@aws/cover',
        '@content' => '@aws/content',
    ],
    'language' => 'es-ES',
    'components' => [
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'I-zY3fgB2aJsvpHuOeYxIc2MA-A9hcmH',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\Usuarios',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => $params['smtpUsername'],
                'password' => getenv('SMTP_PASS'),
                'port' => '587',
                'encryption' => 'tls',
            ],
        ],
        'log' => $log,
        'db' => $db,
        'formatter' => [
            'timeZone' => 'Europe/Madrid',
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'params' => $params,
    'modules' => [
        'datecontrol' => [
            'class' => 'kartik\datecontrol\Module',
            'displaySettings' => [
                Module::FORMAT_DATE => 'dd-MM-yyyy',
                Module::FORMAT_TIME => 'hh:mm:ss a',
                Module::FORMAT_DATETIME => 'dd-MM-yyyy hh:mm:ss a',
            ],
            'saveSettings' => [
                Module::FORMAT_DATE => 'php:U',
                Module::FORMAT_TIME => 'php:H:i:s',
                Module::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
            ],
            'displayTimezone' => 'Europe/Madrid',
            'saveTimezone' => 'UTC',
            'autoWidget' => true,
        ]
    ]
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
