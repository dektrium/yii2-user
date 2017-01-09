# Registration service

Registration service is used to handle process of registering new users. It also may be configured to omit password
field on registration form, and generate password for user automatically.

## Configuration

Here is an example of configuring confirmation service with all available options with their default values:

```php
\Yii::$container->set('dektrium\user\service\RegistrationService', [
    'isEnabled' => true,
    'isPasswordGenerated' => false,
]);
```

## Restricting registration

If you don't want users to be able to register on your site, then you need to set `isEnabled` property to be false. 

## Password generation

If you set `isPasswordGenerated` to be true, then service will generate random 8-char password for newly registered
users. Generated password will be injected into email that is sent after registration is complete.

## Adding more fields to registration form

Sometimes you may need to add more fields to the registration form. In this case you can read 
[this article](adding-fields-to-registration-form.md)