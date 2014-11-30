Configuration
=============

Available configuration options
-------------------------------

- **enableRegistration** Whether registration is enabled. Defaults to **True**.

- **enableGeneratingPassword** Whether password field is not shown on registration page and password is generated
 automatically and sent to user via email. Defaults to **False**.

- **enableConfirmation** Whether users have to confirm their accounts by clicking confirmation link sent them by email.
 In order to enable this option you have to configure **mail** application component. Defaults to **True**.

- **enableUnconfirmedLogin** Whether users are allowed to sign in without activating their accounts. Default to **False**.

- **enablePasswordRecovery** Whether users are allowed to recover their passwords. Defaults to **True**.

- **emailChangingStrategy** The strategy that will be used on changing user's email address on settings page. Defaults
 to **STRATEGY_DEFAULT**, which means that confirmation message will be sent to new user's email. Other strategies are
 **STRATEGY_INSECURE** (when email is changed as is without confirmation) and **STRATEGY_SECURE** (when confirmation
 emails are sent to both old and new email addresses).

- **confirmWithin** The time in seconds before a confirmation token becomes invalid. After expiring this time user have
 to request new confirmation token on special page. Defaults to **86400** (24 hours).

- **rememberFor** The time in seconds you want the user will be remembered without asking for credentials. Defaults
  to **1209600** (2 weeks).

- **recoverWithin** The time in seconds before a recovery token becomes invalid. After expiring this time user
  have to request new recovery message. Defaults to **21600** (6 hours).

- **admins** An array of user's usernames who can manage users from admin panel. Defaults to empty array.

- **cost** Cost parameter used by the Blowfish hash algorithm. Defaults to **10**.

- **urlPrefix** The prefix for user module URL. Defaults to **"user"**.

- **urlRules** The rules to be used in URL management.


Configuration example
---------------------

The configuration should be applied in your main configuration file:


```php
...
'modules' => [
    ...
    'user' => [
        'class' => 'dektrium\user\Module',
        'enableUnconfirmedLogin' => true,
        'confirmWithin' => 21600,
        'cost' => 12,
        'admins' => ['admin']
    ],
    ...
],
...
```
