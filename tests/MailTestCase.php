<?php
/**
 * Mail test depends upon mailcatcher, so
 * make sure that the mailcatcher gem is installed
 *
 * $ sudo gem install mailcatcher
 * $ mailcatcher
 *
 * Then head on to http://localhost:1080/
 */

/**
 * Class MailTestCase
 */
class MailTestCase extends TestCase
{
    /** @test */
    public function it_is_a_dumb_to_supress_no_test_definition_error()
    {
        $this->assertTrue(true);
    }

    /**
     * @var \GuzzleHttp\Client
     */
    protected $mailcatcher;

    /**
     * MailTestCase.
     */
    public function __construct()
    {
        $this->mailcatcher = new \GuzzleHttp\Client(['base_uri' => 'http://localhost:1080']);

        if ($this->mailcatcher->head('/messages')->getStatusCode() !== 200) {
            throw new Exception('Install and boot up mailcatcher first. $ gem install mailcatcher && mailcatcher');
        }
    }

    /**
     * Clean up
     */
    public function tearDown()
    {
        parent::tearDown();

        $this->deleteAllEmails();
    }

    /**
     * Delete all emails
     *
     * @return mixed
     */
    public function deleteAllEmails()
    {
        return $this->mailcatcher->delete('/messages');
    }

    /**
     * Get json formatted all email from the mailcatcher
     *
     * @return mixed
     */
    public function getAllEmails()
    {
        $emails = json_decode($this->mailcatcher->get('/messages')->getBody()->getContents());

        if (empty($emails)) {
            $this->fail('No messages returned');
        }

        return $emails;
    }

    /**
     * Calculate the last email id from the mailcatcher
     *
     * @return mixed
     */
    public function getLastEmail()
    {
        $emailId = $this->getAllEmails()[0]->id;

        return json_decode($this->mailcatcher->get("/messages/{$emailId}.json")->getBody()->getContents());
    }

    /**
     * Assert email body contains the given string value
     *
     * @param $body
     * @param $email
     */
    public function assertEmailBodyContains($body, $email)
    {
        $this->assertContains($body, $email->source);
    }

    /**
     * Assert email body NOT contains the given string value
     *
     * @param $body
     * @param $email
     */
    public function assertNotEmailBodyContains($body, $email)
    {
        $this->assertNotContains($body, $email->source);
    }

    /**
     * Assert email was delivered to correct person
     *
     * @param $receipient
     * @param $email
     */
    public function assertEmailWasSentTo($receipient, $email)
    {
        $this->assertContains($receipient, (string) $email->recipients[0]);
    }

    /**
     * Assert email was NOT delivered to correct person
     *
     * @param $receipient
     * @param $email
     */
    public function assertNotEmailWasSentTo($receipient, $email)
    {
        $this->assertNotContains($receipient, (string) $email->recipients[0]);
    }
}