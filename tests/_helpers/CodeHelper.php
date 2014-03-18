<?php

namespace Codeception\Module;

use Codeception\Module;

class CodeHelper extends Module
{
    public function assertEmailSenderEquals($needle, $email, $description = '')
    {
        $this->assertContains('From: ' . $needle, $email, $description);
    }

    public function assertEmailRecipientEquals($needle, $email, $description = '')
    {
        $this->assertContains('To: ' . $needle, $email, $description);
    }

    public function assertEmailSubjectEquals($expected, $email, $description = '')
    {
        $this->assertContains('Subject: ' . $expected, $email, $description);
    }

    public function assertEmailContains($needle, $email, $description = '')
    {
        $this->assertContains($needle, $email, $description);
    }
}
