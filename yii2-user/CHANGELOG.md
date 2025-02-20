# CHANGELOG

- Fix: Update last_login_at when successfully logged in over oauth(#899)

## 0.9.12 [11 January 2017]

- Fix: Fixed migrations (dmeroff)
- Fix #832: Fixed admin view file (thyseus)
- Enh #839: Order by most recent registration first (created_at DESC) by default (thyseus)

## 0.9.11 [10 January 2017]

- Fix: Fixed migrations for postgresql (dmeroff)
- Enh #794: Allow fast login without password when in DEBUG mode (thyseus)
- Enh #826: Auth action added to auth rules (faenir)
- Enh #820: Added ability to switch back to latest user after being impersonated (thyseus)
- Enh #774: Added last_login_at column to user table (thyseus)

## 0.9.10 [8 January 2017]

- Enh #767: Added support for MS SQL Server (tsdogs)
- Enh #795: Added method `getAccountByProvider` to User model to get account model by provider name (dmeroff)
- Fix #778: Migrations now use correct db component (dmeroff)
- Fix #777: Rethrow exception on failed user creation or registration (dmeroff)
- Enh #772, #791: Added ability to log into another user's accoun (thyseus)
- Fix #761: Fixed EVENT_AFTER_CONFIRM not triggering on user creation (dmeroff)
- Fix #757: Fixed tabindex order in security/login.php view (dmeroff)

## 0.9.9 [13 August 2016]

- Enh #746: Changed AccessRule to check other roles/permissions (markux)
- Enh #649: Updated test suite to codeception 2.2 (dmeroff)
- Enh #725: Removed message that user is not registered in Recovery and Resend forms (dmeroff)
- Fix #747: Fixed reverting migrations on PostgreSQL (dmeroff)

## 0.9.8 [09 August 2016]

- Fix: Fixed Yandex auth client (dmeroff)

## 0.9.7 [09 August 2016]

- Enh: Updated Yii2-authclient required version to 2.1.0 (dmeroff)
- Fix: Max password length set to 72 chars (dmeroff)
- Enh #378: Added events before and after confirmation (dmeroff)
- Enh #593: Added option to allow users to remove their accounts (dmeroff)
- Enh #705: Jui datepicker dependency has been removed (dmeroff) 
- Enh #648: Refactored ajax validation trait (dmeroff)
- Enh #581, #164: Added ability for users to set their timezones (yarrgh)

## 0.9.6 [27 March 2016]

- `enh` Added new method to Profile model to get avatar url (dmeroff)
- `fix` Fixed translations sourceLanguage (thezilla-)
- `enh` Added ability to grant access to admin part via roles (Talwoasc)
- `fix` Fixed postgresql migrations (kfreiman, drxwat)
- `enh` Improved login widget (dmeroff)
- `enh` Support for custom `admin` role via access control rule out of the box #510 (dmeroff)
- `fix` Removed ability to register a new account via social network when registration is disabled #512 (dmeroff)
- `fix` Added check if profile model exists in Settings controller #497 (dmeroff)
- `enh` Added event triggers to most of actions #411 (dmeroff)
- `enh` Added LinkedIn client #496 (SamMousa)

## 0.9.5 [27 September 2015]

- `fix` Don't set mailer subject in bootstrap #451 (dmeroff)
- `enh` Remove old user tokens with same type while creating new #340 (dmeroff)
- `fix` Forbid ability to login via networks for blocked users #434 (dmeroff)
- `fix` Fixed problems with url rules #351 (dmeroff)
- `enh` Regenerate `auth_key` after blocking the user (dmeroff)
- `enh` Improved registration process #236 (dmeroff)
- `fix` Ensure user/index works without php-intl extension #370 (thyseus)
- `fix` Fixed display of confirmation time #361 (pedros80)
- `fix` Do not limit username length to 25 chars #369 (thyseus)

## 0.9.4 [6 April 2015]

- `enh` Added ability to override translations (dmeroff)
- `enh` Improved authentication via social networks (dmeroff)
- `enh` Added Latvian translation (uldisn)
- `enh` Added redirect to index page when logged in user tries to access login page (thiagotalma)
- `fix` Updated Italian translation (maxxer)
- `enh` Added usage of `adminEmail` param as default sender name if set (thiagotalma)
- `enh` Added link to registration page on login form (thiagotalma)
- `enh` Improved username regexp (thiagotalma)
- `fix` Updated translations for Portuguese language (invaderhd & thiagotalma & andredp)
- `enh` Added integration with Yii2-rbac (dmeroff)
- `enh` Improved admin views and controller (dmeroff)
- `enh` Added datepicker in user grid view (dmeroff)

## 0.9.3 [9 February 2015]

- `fix` Fixed bug with inability to connect network account #275 (dmeroff)
- `enh` Added turkish translation (veyselsahin)
- `enh` Added lithuanian translation (vilkazz)
- `fix` Fixed button css on profile settings page (sosojni)
- `enh` Hidden recovery link when `enablePasswordRecovery` is false (marsuboss)
- `fix` Fixed gravatar hash creation (nigelterry)
- `fix` Fixed invalid redirection after changing account settings (WeeSee)
- `fix` Updated messages to be more readable (jspaine)
- `fix` Reverted initialization of `user` component (dmeroff)
- `fix` Fixed bug on confirmation and recovery when invalid token was used (anders-akero)

## 0.9.2 [14 January 2015]

- `enh` Improved initialization of `user` application component (dmeroff)
- `fix` Fixed encoding problem with plain text email messages (dmeroff)
- `fix` Fixed migration (dmeroff)
- `fix` Fixed overriding of mailer view files (dmeroff)
- `fix` Fixed troubles with overriding user search model (dmeroff)
- `fix` Fixed bug when model was defined as array in modelMap (dmeroff)
- `fix` Removed ajax-request flood on login form (thiagotalma)
- `enh` Added `th` translation (kongoon)

## 0.9.1 [1 January 2015]

- `fix` Fixed overriding of active record models (dmeroff)
- `fix` Fixed pt-BR translation (thiagotalma)

## 0.9.0 [30 December 2014]

- `fix` Changed ip field type to VARCHAR(45) to handle IPv6 (dmeroff)
- `enh` Improved mailer component (dmeroff)
- `enh` Updated flash messages and added new module option to disable them (dmeroff)
- `enh` Added ajax-validation (dmeroff)
- `enh` Added secured email changing strategy (dmeroff)
- `chg` Removed ability to delete or block your own account from admin pages (dmeroff)
- `chg` Updated create and update admin pages (dmeroff)
- `chg` Updated admin index page (dmeroff)
- `chg` Removed auto-injecting module in application (belerophon)
- `chg` Removed Mailcatcher dependency from test suite (dmeroff)
- `chg` Refactored all models (dmeroff)
- `enh` Refactored model overriding system (dmeroff)

## 0.8.2 [14 December 2014]

- `fix` Fixed croatian translation (trbsi)
- `fix` Fixed spanish translation (abolivar)
- `chg` Added persian farsi translation (bepehr)
- `chg` Added hungarian translation (akosk)
- `chg` Added dutch translation (infoweb-internet-solutions)
- `fix` Fixed pt_BR translate (thiagotalma)
- `fix` Fixed relation between User and Profile (anders-akero)
- `fix` Fixed translations (sosojni)
- `fix` Added registration_ip label (sosojni)

## 0.8.1 [13 October 2014] Yii 2.0.0 compatible

- `fix` Fixed test suite (dmeroff)
- `chg` Updated `composer.json` (dmeroff)
- `chg` Added check for `enableConfirmation` for resending link (makroxyz)
- `fix` Fixed compatibility with PostgreSQL (makroxyz)

## 0.8.0 [5 October 2014] Yii 2.0.0-rc compatible

- `fix` Updated admin grid view (hoksilato)
- `fix` Recovery token is now deleted after recovery process (mrarthur)
- `fix` Registration_ip is now filled with user ip address on registration (mrarthur)
- `chg` Refactored email change process (dmeroff)
- `enh` Added account settings page and removed email and password settings pages (dmeroff)
- `enh` Added tab index on login page (maxxer)
- `enh` Added `getIsAdmin` method on user model (maxxer)
- `fix` Fixed bug when registration page was not available with `enableConfirmation` option disabled (marsuboss)

## 0.7.0 [31 August 2014]

- Reverted removing registration without password
- Updated test suite
- Added new translations
- Added options to configure url rules (#99)
- Added new advanced configuration (#93) (BC break)
- Refactored registration, confirmation, password recovery
- Updated database structure (BC break)

## 0.6.0 [04 May 2014]

- Supported MySQL and PostgreSQL
- Added login widget
- Added registration via social networks
- Moved Pass*Field to separate extension
- Updated login process: user can log in using email or username
- Fixed bug when user password has not been updated from admin panel
- Added Vietnamese translation
- Refactored test suite
- Added Mailer component
- Added ModelManager component
- Removed factory
- Updated composer.json
- Added bootstrap class that configures module automatically
- Removed custom User component
- Removed registration without password
- Removed default controller
- Removed captcha from all forms
- Updated i18n messages and translations
- Updated view files

## v0.5.1 [22 March 2014]

- Fixed documentation issues
- Fixed bug when registration date was not displayed on profile page
- Removed checking of user role in admin controller

## v0.5.0 [20 March 2014]

- Refactored User model
- Added profile page
- Refactored tests
- Added user settings page
- Updated admin views
- Added account blocking
- Updated database schema
- Added [Pass*Field library](http://antelle.github.io/passfield/index.html)

## v0.4.0 [6 February 2014]

- Added list of available actions
- Added user management (create, update, delete, confirm)
- Added installation and configuration guide
- Refactored forms
- Enabled Trackable as default
- Removed Registration form
- Fixed bug with captcha
- Added prefix usage in migrations and model
- Refactored way of sending emails
- Modified mail views with [Really Simple Responsive HTML Email Template](https://github.com/leemunroe/html-email-template)
- Added factory class
- Merged interfaces into new one

## v0.3.0 [16 January 2014]

- Added console commands
- Refactored test suite
- Added 'simple' registration (when password is generated automatically)
- Refactored forms
- Extracted interfaces for identity class
- Added registration form class
- Added optional captcha on login, registration, resend and recovery pages
- Added access control in recovery controller

## v0.2.0 [12 December 2013]

- Added russian translation
- Added password recovery feature
- Added different login types: `email`, `username`, `both`
- Added regular expression on validating username

## v0.1.0 [29 November 2013]

- Initial release
