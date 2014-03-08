<?php
$time = time();
return [
	[
		'username' => 'user',
		'email' => 'user@example.com',
		'password_hash' => '$2y$13$qY.ImaYBppt66qez6B31QO92jc5DYVRzo5NxM1ivItkW74WsSG6Ui',
		'auth_key' => '39HU0m5lpjWtqstFVGFjj6lFb7UZDeRq',
		'created_at' => $time,
		'updated_at' => $time,
		'confirmed_at' => $time,
	],
	[
		'username' => 'unconfirmed',
		'email' => 'unconfirmed@example.com',
		'password_hash' => '$2y$13$CIH1LSMPzU9xDCywt3QO8uovAu2axp8hwuXVa72oI.1G/USsGyMBS',
		'auth_key' => 'mhh1A6KfqQLmHP-MiWN0WB0M90Q2u5OE',
		'created_at' => $time,
		'updated_at' => $time,
		'confirmation_token' => 'NO2aCmBIjFQX624xmAc3VBu7Th3NJoa6',
		'confirmation_sent_at' => $time
	],
	[
		'username' => 'john',
		'email' => 'john@example.com',
		'password_hash' => '$2y$13$qY.ImaYBppt66qez6B31QO92jc5DYVRzo5NxM1ivItkW74WsSG6Ui',
		'auth_key' => 'h6OS9csJbZEOW59ZILmJxU6bCiqVno9A',
		'created_at' => $time - 86401,
		'updated_at' => $time - 86401,
		'confirmation_token' => 'qxYa315rqRgCOjYGk82GFHMEAV3T82AX',
		'confirmation_sent_at' => $time - 86401
	],
	[
		'username' => 'blocked',
		'email' => 'blocked@example.com',
		'password_hash' => '$2y$13$qY.ImaYBppt66qez6B31QO92jc5DYVRzo5NxM1ivItkW74WsSG6Ui',
		'auth_key' => 'TnXTrtLdj-YJBlG2A6jFHJreKgbsLYCa',
		'created_at' => $time,
		'updated_at' => $time,
		'blocked_at' => $time,
		'confirmed_at' => $time
	],
];