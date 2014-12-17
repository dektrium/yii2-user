<?php

namespace tests\codeception\_support;

use Codeception\Module;

class MailHelper extends Module
{
    public static $mails = [];

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
