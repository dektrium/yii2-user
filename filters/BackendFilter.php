<?php

/* 
 * This file is part of the Dektrium project
 * 
 * (c) Dektrium project <http://github.com/dektrium>
 * 
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace dektrium\user\filters;

/**
 * BackendFilter is used to allow access only to admin controller in frontend
 * when using Yii2-user with Yii2 advanced template.
 * 
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class BackendFilter extends \yii\base\ActionFilter
{
    /**
     * @var array
     */
    public $controllers = ['profile', 'recovery', 'registration', 'security', 'settings'];
    
    /**
     * @param \yii\base\Action $action
     */
    public function beforeAction($action)
    {
        if (in_array($action->controller->id, $this->controllers)) {
            throw new \yii\web\NotFoundHttpException('Not found');
        }
        
        return true;
    }
}