# Data Model

When Yii2-user is installed the database is
configured using the Yii2-user data model.

It can be useful to know what's going on in the
database when Yii2-user is integrated your application.

## Entity Relation Diagram

    +----------------+ 1/n
    | social_account <----------+
    +----------------+          |
                              1 |
    +-------+ 1/n         1 +---v----+
    | token <--------------->  user  |
    +-------+               +---^----+
                              1 |
    +---------+ 1               |
    | profile <-----------------+
    +---------+

    +------------+
    | migrations |
    +------------+

## Explaining some basic tasks

- Each user has exactely one Profile (./models/User.php ```afterSafe()```)
- A token is generated if a user registers and ```Module::enableConfirmation```
  is set to true (./models/User.php ```register()```). Tokens are not deleted
  anymore.
