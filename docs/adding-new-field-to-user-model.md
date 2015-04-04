# Adding new field to user model

Suppose, you need to add new field to `User` model which will be editable in
admin panel. Unfortunately at the moment Yii2-user does not support adding new
fields to the registration form.

## Create new migration

Let's start with creating new migration, which will add new field to `user` table:

run `php yii migrate/create add_new_field_to_user` and open generated migration:

```php
class m123456_654321_add_new_field_to_user extends \yii\db\Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'field', Schema::TYPE_STRING);
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'field');
    }
}
```

And now you can apply that migration by running `php yii migrate`.

## Override User model

Override `User` model as desribed in [guide](overriding-models.md) and add
following lines in overriden model:

```php
class User extends \dektrium\user\models\User
{
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        // add field to create and update scenarios
        $scenarios['create'] = array_merge($scenarios['create'], ['field']);
        $scenarios['update'] = array_merge($scenarios['update'], ['field']);

        return $scenarios;
    }

    public function rules()
    {
        $rules = parent::rules();
        // let's add some rules for field
        // suppose, it is required and have max length with 10 symbols:
        $rules['fieldRequired'] = ['field', 'required'];
        $rules['fieldLength']   = ['field', 'string', 'max' => 10];
        
        return $rules;
    }
}
```

## Override view file

You should override view file `@dektrium/user/views/admin/_user.php` as described
in [special guide](overriding-views.md ) and paste there following code:

```php
<?php

/**
 * @var yii\widgets\ActiveForm    $form
 * @var dektrium\user\models\User $user
 */

?>

<?= $form->field($user, 'username')->textInput(['maxlength' => 25]) ?>
<?= $form->field($user, 'email')->textInput(['maxlength' => 255]) ?>
<?= $form->field($user, 'password')->passwordInput() ?>
<?= $form->field($user, 'field')->textInput(['maxlength' => 10]) ?>

```