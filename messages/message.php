<?php

return [
	'sourcePath' => __DIR__ . '/../',
	'messagePath' => __DIR__,
	'languages' => ['ca', 'da', 'es', 'fr', 'hu', 'it', 'nl', 'pt', 'pt-BR', 'ru', 'vi' , 'fa-IR', 'kz', 'th', 'lt'],
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
