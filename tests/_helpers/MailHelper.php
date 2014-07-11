<?php

namespace Codeception\Module;

use Codeception\Module;
use GuzzleHttp\Client;

class MailHelper extends Module
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $mailcatcher;

    /**
     * @var array
     */
    protected $config = ['url', 'port'];

    /**
     * @var array
     */
    protected $requiredFields = ['url', 'port'];

    /**
     * Used after configuration is loaded
     */
    public function _initialize() {
        $url = $this->config['url'] . ':' . $this->config['port'];
        $this->mailcatcher = new Client(['base_url'=>$url]);
    }

    /**
     * Clears all emails from mailcatcher.
     */
    public function cleanEmails()
    {
        $this->mailcatcher->delete('/messages');
    }

    /**
     * Asserts that last message contains $needle
     *
     * @param $needle
     */
    public function seeInEmail($needle)
    {
        $response = $this->mailcatcher->get("/messages/{$this->getLastMessage()->id}.html");
        $this->assertContains($needle, (string) $response->getBody());
    }

    /**
     * Asserts that last message subject contains $needle
     *
     * @param $needle
     */
    public function seeInEmailSubject($needle)
    {
        $this->assertContains($needle, $this->getLastMessage()->subject);
    }

    /**
     * Asserts that last message recipients contain $needle
     *
     * @param $needle
     */
    public function seeInEmailRecipients($needle)
    {
        $response = $this->mailcatcher->get("/messages/{$this->getLastMessage()->id}.json");
        $email = json_decode($response->getBody());
        $this->assertContains('<' . $needle . '>', $email->recipients);
    }

    /**
     * @return mixed
     */
    protected function getLastMessage()
    {
        $messages = $this->getMessages();
        if (empty($messages)) {
            $this->fail("No messages received");
        }

        return reset($messages);
    }

    /**
     * @return mixed
     */
    protected function getMessages()
    {
        $jsonResponse = $this->mailcatcher->get('/messages');
        return json_decode($jsonResponse->getBody());
    }
}
