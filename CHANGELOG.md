# CHANGELOG

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