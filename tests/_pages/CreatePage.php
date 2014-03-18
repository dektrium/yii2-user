<?php

namespace dektrium\user\tests\_pages;

use yii\codeception\BasePage;

class CreatePage extends BasePage
{
    /**
     * @var string
     */
    public $route = '/user/admin/create';

    /**
     * @param $username
     * @param $email
     * @param $password
     * @param null $role
     */
    public function create($username, $email, $password, $role = null)
    {
        $this->guy->fillField('#user-username', $username);
        $this->guy->fillField('#user-email', $email);
        $this->guy->fillField('#user-password', $password);
        $this->guy->fillField('#user-role', $role);
        $this->guy->click('Create');
    }
}
