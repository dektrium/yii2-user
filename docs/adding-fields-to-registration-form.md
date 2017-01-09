# Adding fields from Profile to the registration form

Sometimes you may need to add more fields to registration form. They may be both fields from User model or from
it's relations.

Registration form has method called `getMappings`. It sets association between registration form properties and
properties of user model and it's relations. Keys of the array are properties of registration form and values are
properties of User model. For example:

```php
return [
    'phone' => 'phone',
    'first_name' => 'profile.first_name',
];
```

Notice, you may use dot notation to set fields from registration model to the related model's properties. In the
above example we map first_name property of registration model to the first_name property of profile relation. And
fields without dot are the fields of the User model itself.

## Learn by example

Assume that we need to add `name` field of Profile model to the registration form.

### 1. Override the registration form model

```php
namespace app\models;

use dektrium\user\models\RegistrationForm as BaseRegistrationForm;

class RegistrationForm extends BaseRegistrationForm
{
    /**
     * @var string
     */
    public $name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['name', 'required'],
            ['name', 'string', 'max' => 255],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name' => \Yii::t('user', 'Name')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getMappings()
    {
        return [
            'name' => 'profile.name',
        ];
    }
}
```

## 2. Override the registration form view

The only thing you need to do is to override registration form view and add `name` form field.
