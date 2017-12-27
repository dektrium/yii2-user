#!/bin/sh

composer install --prefer-dist

php tests/_app/yii.php migrate --migrationPath=@dektrium/user/migrations --interactive=0

exec /bin/codecept $@