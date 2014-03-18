<?php

$I = new TestGuy($scenario);
$I->wantTo('ensure that confirmation works');

$I->amGoingTo('check that error is showed when token expired');
$I->amOnPage('?r=user/registration/confirm&id=3&token=qxYa315rqRgCOjYGk82GFHMEAV3T82AX');
$I->see('Confirmation token is invalid');

$I->amGoingTo('check that user get confirmed');
$I->amOnPage('?r=user/registration/confirm&id=2&token=NO2aCmBIjFQX624xmAc3VBu7Th3NJoa6');
$I->see('Your account has been confirmed');
$I->grabRecord('\dektrium\user\models\User', [
    'id' => 2,
    'confirmation_token' => null,
]);
