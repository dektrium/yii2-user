User management
===============

Yii2-user has CRUD interface to manage all users. It allows you to create, update, delete and confirm users.

## Usage

Before we start you need to add administrators in your module configuration as follows:

```php
'modules' => [
	...
	'user' => [
		'class' => 'dektrium\user\Module',
		// add your username to admins array
		'admins' => ['your-username'],
	]
	...
],
```

Now you can manage users on page `http://localhost/index.php?r=user/admin`