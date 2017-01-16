# Simpler RBAC using custom access control filter

Yii2-user comes with access control rule which adds support of `admin` role which allows access to users
added to `admins` property of the module.

## Apply filter to your controller

Here is an example of how to use access rule in your access control filter:

```php
<?php

namespace app\controllers;

use yii\filters\AccessControl;
use dektrium\user\filters\AccessRule;
use yii\web\Controller;

class SiteController extends Controller
{
	...
	public function behaviors()
	{
		return [
			'access' => [
			    'class' => AccessControl::className(),
			    'ruleConfig' => [
			        'class' => AccessRule::className(),
			    ],
			    'rules' => [
			        [
			            'actions' => ['create'],
			            'allow' => true,
			            'roles' => ['admin'],
			        ],
			        [
			            'actions' => ['view', 'search'],
			            'allow' => true,
			            'roles' => ['?', '@', 'admin'],
			        ],
			    ],
			],
		];
	}
	...
}
```
