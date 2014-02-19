<?php

return [
	'modules' => [
		'user' => [
			'class' => 'dektrium\user\Module',
			'admins' => ['user']
		]
	],
	'components' => [
		'mail' => [
			'useFileTransport' => true,
		],
		'urlManager' => [
			'showScriptName' => true,
		],
	],
];