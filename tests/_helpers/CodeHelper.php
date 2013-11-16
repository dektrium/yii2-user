<?php
namespace Codeception\Module;

// here you can define custom functions for CodeGuy 

use yii\web\Application;

class CodeHelper extends \Codeception\Module
{
    public function mockApplication($config = [])
    {
        $defaultConfig = [
            'id' => 'testapp',
            'basePath' => __DIR__.'/../',
            'modules' => [
                'user' => '\dektrium\user\WebModule'
            ],
            'components' => [
                'user' => [
                    'class' => '\dektrium\user\components\User',
                ],
                'db' => [
                    'class' => 'yii\db\Connection',
                    'dsn' => 'mysql:host=localhost;dbname=dektrium_test',
                    'username' => 'root',
                    'password' => '',
                ],
                'mail' => [
                    'class' => 'yii\swiftmailer\Mailer',
                    'useFileTransport' => true,
                    'htmlLayout' => '@app/_data/html.php'
                ],
            ]
        ];

        new Application(array_merge($defaultConfig, $config));
    }

    public function destroyApplication()
    {
        \Yii::$app = null;
    }
}
