<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace dektrium\user\widgets;

use dektrium\user\models\RegistrationForm;
use yii\base\Widget;

/**
 * Login for widget.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Register extends Widget
{
    
    /**
     * @var \dektrium\user\models\RegistrationForm
     */
    public $model;
    
    /**
     * @var string 
     */
    public $id = 'registration-form';
    
    /**
     * @var string 
     */
    public $title = "";
    
    /**
     * @var bool
     */
    public $validate = true;
    
    /**
     * @var bool 
     */
    public $enableGeneratingPassword = false;
    
    /**
     * @var bool
     */
    public $displayLoginLink = true;
    
    /**
     * @var bool
     */
    public $panel = true;
    
    /**
     * @var string
     */
    public $panelType = 'default';
    
    public function init()
    {
        parent::init();
        
        if (!$this->model) {
            $this->model = \Yii::createObject(RegistrationForm::className());
        }
    }
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('register', [
            'model' => $this->model,
            'id' => $this->id,
            'title' => $this->title,
            'validate' => $this->validate,
            'enableGeneratingPassword' => $this->enableGeneratingPassword,
            'displayLoginLink' => $this->displayLoginLink,
            'panel' => $this->panel,
            'panelType' => $this->panelType,
        ]);
    }
}
