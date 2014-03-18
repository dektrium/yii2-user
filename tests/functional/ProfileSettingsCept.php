<?php

use dektrium\user\tests\_pages\ProfileSettingsPage;
use dektrium\user\tests\_pages\LoginPage;
use dektrium\user\models\Profile;

$I = new TestGuy($scenario);
$I->wantTo('ensure that profile settings works');

$loginPage = LoginPage::openBy($I);
$loginPage->login('user@example.com', 'qwerty');

$name = 'Tester';
$public_email = 'public@email.com';
$website = 'http://website.com';
$location = 'Russia';
$gravatar_email = 'gravatar@email.com';
$bio = 'My short bio';

$page = ProfileSettingsPage::openBy($I);
$page->update($name, $public_email, $website, $location, $gravatar_email, $bio);

$I->see('Profile updated successfully');
$I->seeRecord(Profile::className(), [
    'user_id' => 1,
    'name' => $name,
    'public_email' => $public_email,
    'website' => $website,
    'location' => $location,
    'gravatar_email' => $gravatar_email,
    'bio' => $bio
]);
