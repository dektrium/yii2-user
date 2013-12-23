<?php

yii\codeception\TestCase::$appConfig = yii\helpers\ArrayHelper::merge(
	require(__DIR__ . '/../../../../config/web.php'),
	[
		'components' => [
			'db' => [
				'dsn' => 'mysql:host=localhost;dbname=dektrium_test',
			]
		]
	]
);