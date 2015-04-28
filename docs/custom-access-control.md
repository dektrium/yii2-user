# Simpler RBAC using custom access control filter

Built-in Yii2 access control filter supports only two roles -
`@` and `?`. In this guide you will learn how to add new role
named `admin`, which will use the admin list provided by
Yii2-user.

## Create filter class

Let's create new file under `@app/filters` named
`AccessRule.php`:

```php
<?php

namespace app\filters;

use yii\filters\AccessRule;

class AccessRule extends AccessRule
{

    /** @inheritdoc */
    protected function matchRole($user)
    {
        if (empty($this->roles)) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role === '?') {
                if (Yii::$app->user->isGuest) {
                    return true;
                }
            } elseif ($role === '@') {
                if (!Yii::$app->user->isGuest) {
                    return true;
                }
            } elseif ($role === 'admin') {
                if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin) {
                    return true;
                }
            }
        }

        return false;
    }
}
```

## Apply new filter to your controller

Here is an example of how to use created access rule in your
access control filter:

```php
<?php

namespace app\controllers;

use yii\filters\AccessControl;
use app\filters\AccessRule;
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
			            'roles' => ['?', '*', 'admin'],
			        ],
			    ],
			],
		];
	}
	...
}
```
