<?php

namespace Codeception\Module;

use Guzzle\Http\Client;

class TestHelper extends \Codeception\Module
{
	/**
	 * @var \Guzzle\Http\Client
	 */
	private $mailcatcher;

	public function __construct()
	{
		parent::__construct();
		$this->mailcatcher = new Client('http://127.0.0.1:1080');
		$this->cleanMessages();
	}

	public function cleanMessages()
	{
		$this->mailcatcher->delete('/messages')->send();
	}

	public function getLastMessage()
	{
		$messages = $this->getMessages();
		if (empty($messages)) {
			$this->fail("No messages received");
		}

		return reset($messages);
	}

	public function getMessages()
	{
		$jsonResponse = $this->mailcatcher->get('/messages')->send();
		return json_decode($jsonResponse->getBody());
	}

	public function seeEmailIsSent($description = '')
	{
		$this->assertNotEmpty($this->getMessages(), $description);
	}
	public function seeEmailSubjectContains($needle, $email, $description = '')
	{
		$this->assertContains($needle, $email->subject, $description);
	}

	public function seeEmailSubjectEquals($expected, $email, $description = '')
	{
		$this->assertContains($expected, $email->subject, $description);
	}

	public function seeEmailHtmlContains($needle, $email, $description = '')
	{
		$response = $this->mailcatcher->get("/messages/{$email->id}.html")->send();
		$this->assertContains($needle, (string)$response->getBody(), $description);
	}

	public function seeEmailTextContains($needle, $email, $description = '')
	{
		$response = $this->mailcatcher->get("/messages/{$email->id}.plain")->send();
		$this->assertContains($needle, (string)$response->getBody(), $description);
	}

	public function seeEmailSenderEquals($expected, $email, $description = '')
	{
		$response = $this->mailcatcher->get("/messages/{$email->id}.json")->send();
		$email = json_decode($response->getBody());
		$this->assertEquals($expected, $email->sender, $description);
	}

	public function seeEmailRecipientsContain($needle, $email, $description = '')
	{
		$response = $this->mailcatcher->get("/messages/{$email->id}.json")->send();
		$email = json_decode($response->getBody());
		$this->assertContains($needle, $email->recipients, $description);
	}
}
