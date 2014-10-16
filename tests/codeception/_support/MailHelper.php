<?php

namespace tests\codeception\_support;

use Codeception\Module;
use dektrium\user\Mailer;
use GuzzleHttp\Client;

class TestMailer extends Mailer
{
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

class MailHelper extends Module
{
    public static $mails = [];

    /**
     * Used after configuration is loaded
     */
    public function _initialize() {
        \Yii::$container->set('dektrium\user\Mailer', 'tests\codeception\_support\TestMailer');
    }

    /**
     * Asserts that last message contains $needle
     *
     * @param $needle
     */
    public function seeInEmail($needle)
    {
        $email = end(static::$mails);
        $this->assertContains($needle, $email['body']);
    }


    /**
     * Asserts that last message subject contains $needle
     *
     * @param $needle
     */
    public function seeInEmailSubject($needle)
    {
        $email = end(static::$mails);
        $this->assertContains($needle, $email['subject']);
    }
    /**
     * Asserts that last message recipients contain $needle
     *
     * @param $needle
     */
    public function seeInEmailRecipients($needle)
    {
        $email = end(static::$mails);
        $this->assertEquals($needle, $email['to']);
    }
}
