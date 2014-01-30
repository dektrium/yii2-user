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
		// An array of pages on which captcha should be shown (e.g. ['registration', 'login', 'recovery', 'resend'])
		'captcha' => [],
		// Login type : email, username or both.
		'loginType' => 'email',
		// Whether to allow login without confirmation.
		'allowUnconfirmedLogin' => false,
		// The time you want the user will be remembered without asking for credentials.
		'rememberFor' => 1209600, // two weeks
		// Whether to generate user's password automatically on registration.
		'generatePassword' => false,
		// Whether to track user's IP address on login and registration.
		'trackable' => true,
		// Whether email confirmation is required.
		'confirmable' => true,
		// The time before a sent confirmation token becomes invalid.
		'confirmWithin' => 86400, // 24 hours
		// Whether to allow users recover their passwords.
		'recoverable' => true,
		// The time before a recovery token becomes invalid.
		'recoverWithin' => 21600 // 6 hours
		// Cost parameter used by the Blowfish hash algorithm.
		'cost' => 10,
		// Directory where email templates are stored.
		'emailViewPath' => '@dektrium/user/views/mail',
		// Factory settings
		'factory' => [
			// User class
			'userClass' => '\dektrium\user\models\User',
			// Resend form
			'resendFormClass' => '\dektrium\user\forms\Resend',
			// Login form
			'loginFormClass' => '\dektrium\user\forms\Login',
			// Recovery form
			'recoveryFormClass' => '\dektrium\user\forms\Recovery'
		]
	]
	...
],
```