<?php

namespace app\components;

use dektrium\user\Mailer;
use tests\codeception\_support\MailHelper;

class MailerMock extends Mailer
{
    public static $mails = [];

    protected function sendMessage($to, $subject, $view, $params = [])
    {
        /** @var \yii\mail\BaseMailer $mailer */
        $mailer = \Yii::$app->mailer;
        $mailer->viewPath = $this->viewPath;
        $body = $mailer->render($view, $params);
        MailHelper::$mails[] = [
            'body'    => $body,
            'to'      => $to,
            'subject' => $subject,
        ];
    }
}
 