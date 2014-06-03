<?php

return [
	'sourcePath' => __DIR__ . '/../',
	'messagePath' => __DIR__,
	'languages' => ['ca', 'da', 'es', 'ru', 'vi'],
	'translator' => 'Yii::t',
	'sort' => false,
	'overwrite' => true,
	'removeUnused' => false,
	'only' => ['*.php'],
	'except' => [
		'.svn',
		'.git',
		'.gitignore',
		'.gitkeep',
		'.hgignore',
		'.hgkeep',
		'/messages',
	],
	'format' => 'php',
];
