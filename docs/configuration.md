Configuration
=============

All available configuration options are listed below with their default values:

```php
'modules' => [
	...
	'user' => [
		'class' => 'dektrium\user\Module',
		// An array of usernames who can manage users
		'admins' => ['your-username'],
		// Whether to allow login without confirmation.
		'allowUnconfirmedLogin' => false,
		// The time you want the user will be remembered without asking for credentials.
		'rememberFor' => 1209600, // two weeks
		// Whether email confirmation is required.
		'confirmable' => true,
		// The time before a sent confirmation token becomes invalid.
		'confirmWithin' => 86400, // 24 hours
		// The time before a recovery token becomes invalid.
		'recoverWithin' => 21600, // 6 hours
		// Cost parameter used by the Blowfish hash algorithm.
		'cost' => 10,
	]
	...
],
```
