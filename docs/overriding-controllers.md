# Overriding controllers

The default Yii2-user controllers provide a lot of functionality that is sufficient for general use cases. But sometimes
you may need to extend that functionality and add some logic that suits your needs.

## Step 1: Create new controller

First of all you should create new controller under your own namespace (it is recommended to use `app\controllers\user`)
and extend it from the controller you want to override.

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

## Step 2: Add your controller to controller map

To let Yii2-user know about your controller, you should add it to the module's controller map, as follows:

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
