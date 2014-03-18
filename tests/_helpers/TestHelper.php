<?php

namespace Codeception\Module;

use Codeception\TestCase;
use dektrium\user\tests\_fixtures\ProfileFixture;
use dektrium\user\tests\_fixtures\UserFixture;
use Guzzle\Http\Client;
use yii\test\FixtureTrait;
use Codeception\Module;

class TestHelper extends Module
{
    /**
     * Redeclare visibility because codeception includes all public methods that not starts from "_"
     * and not excluded by module settings, in guy class.
     */
    use FixtureTrait {
        loadFixtures as protected;
        fixtures as protected;
        globalFixtures as protected;
        unloadFixtures as protected;
        getFixtures as protected;
        getFixture as protected;
    }

    /**
     * @var \Guzzle\Http\Client
     */
    private $mailcatcher;

    /**
     * Method called before any suite tests run. Loads User fixture login user
     * to use in acceptance and functional tests.
     * @param array $settings
     */
    public function _beforeSuite($settings = [])
    {
        $this->mailcatcher = new Client('http://127.0.0.1:1080');
        $this->cleanMessages();
    }

    public function _before(TestCase $test)
    {
        $this->loadFixtures();
        parent::_before($test);
    }

    public function _after(TestCase $test)
    {
        $this->unloadFixtures();
        parent::_after($test);
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
        $this->assertContains($needle, (string) $response->getBody(), $description);
    }

    public function seeEmailTextContains($needle, $email, $description = '')
    {
        $response = $this->mailcatcher->get("/messages/{$email->id}.plain")->send();
        $this->assertContains($needle, (string) $response->getBody(), $description);
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

    protected function fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => '@tests/_fixtures/init_user.php'
            ],
            'profile' => [
                'class' => ProfileFixture::className(),
                'dataFile' => '@tests/_fixtures/init_profile.php'
            ],
        ];
    }
}
