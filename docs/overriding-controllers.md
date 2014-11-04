Overriding controllers
======================

Sometimes you may need to override default Yii2-user controllers. It is really easy and takes two steps.

Step 1: Create new controller
-----------------------------

First of all you need to create new controller under your own namespace (it is recommended to use `app\controllers\user`)
and extend it from needed Yii2-user controller.

For example, if you want to override AdminController you should create `app\controllers\user\AdminController` and extend
it from `dektrium\user\controllers\AdminController`:

```php
namespace app\controllers\user;

use dektrium\user\controllers\AdminController as BaseAdminController;

class AdminController extends BaseAdminController
{
    public function actionCreate()
    {
        // do your magic
    }
}
```

Step 2: Add your controller to controller map
---------------------------------------------

To let Yii2-user know about your controller you should add it to controller map as follows:

```php
...
'modules' => [
    ...
    'user' => [
        'class' => 'dektrium\user\Module',
        'controllerMap' => [
            'admin' => 'app\controllers\user\AdminController'
        ],
        ...
    ],
    ...
],
...
```
