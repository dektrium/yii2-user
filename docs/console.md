# Console commands

## Setup
To enable console commands, you need to add module into console config of you app.
`/config/console.php` in yii2-app-basic template, or `/console/config/main.php` in yii2-app-advanced.

```php

    return [
        'id' => 'app-console',
        'modules' => [
            'user' => [
                'class' => 'dektrium\user\Module',
            ],
        ],
        ...

```

## Available console actions

- **user/confirm** Confirms a user.
- **user/create** Creates new user account.
- **user/delete** Deletes a user.
- **user/password** Updates user's password.

### user/confirm
Confirms a user by setting confirmed_at field to current time.

```sh

./yii user/confirm <search> [...options...]

- search (required): string
  Email or username

```

### user/create
This command creates new user account. If password is not set, this command will generate new 8-char password.
After saving user to database, this command uses mailer component to send credentials (username and password) to
user via email.


```sh

./yii user/create <email> <username> [password] [...options...]

- email (required): string
  Email address

- username (required): string
  Username

- password: null|string
  Password (if null it will be generated automatically)

```

### user/delete
Deletes a user.

```sh

./yii user/delete <search> [...options...]

- search (required): string
  Email or username

```

### user/password
Updates user's password to given.

```sh

./yii user/password <search> <password> [...options...]

- search (required): string
  Email or username

- password (required): string
  New password


```
