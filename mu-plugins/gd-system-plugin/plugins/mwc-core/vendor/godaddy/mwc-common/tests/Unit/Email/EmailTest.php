<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Email;

use GoDaddy\WordPress\MWC\Common\Email\Email;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use WP_Mock;

class EmailTest extends WPTestCase
{
    /**
     * @covers       \GoDaddy\WordPress\MWC\Common\Email\Email::__construct()
     */
    public function testConstructor()
    {
        $to = 'johndoe@example.com';

        $email = new Email($to);

        $this->assertSame($to, $email->getTo());
    }

    /**
     * @covers       \GoDaddy\WordPress\MWC\Common\Email\Email::setTo()
     * @covers       \GoDaddy\WordPress\MWC\Common\Email\Email::getTo()
     */
    public function testToSetterGetter()
    {
        $to = 'johndoe@example.com';
        $newTo = 'janedoe@example.com';

        $email = new Email($to);
        $email->setTo($newTo);

        $this->assertSame($newTo, $email->getTo());
    }

    /**
     * @covers       \GoDaddy\WordPress\MWC\Common\Email\Email::setSubject()
     * @covers       \GoDaddy\WordPress\MWC\Common\Email\Email::getSubject()
     */
    public function testSubjectSetterGetter()
    {
        $to = 'johndoe@example.com';
        $subject = 'This is a test email';

        $email = new Email($to);
        $email->setSubject($subject);

        $this->assertSame($subject, $email->getSubject());
    }

    /**
     * @covers       \GoDaddy\WordPress\MWC\Common\Email\Email::setBody
     * @covers       \GoDaddy\WordPress\MWC\Common\Email\Email::getBody
     */
    public function testBodySetterGetter()
    {
        $to = 'johndoe@example.com';
        $body = 'This is a test email
        with a multiline body';

        $email = new Email($to);
        $email->setBody($body);

        $this->assertSame($body, $email->getBody());
    }

    /**
     * @covers       \GoDaddy\WordPress\MWC\Common\Email\Email::setHeaders
     * @covers       \GoDaddy\WordPress\MWC\Common\Email\Email::getHeaders
     */
    public function testHeadersSetterGetter()
    {
        $to = 'johndoe@example.com';
        $headers = [
            'Content-type'   => 'text/html',
            'Request-source' => 'MWC Common',
        ];

        $email = new Email($to);
        $email->setHeaders($headers);

        $this->assertSame($headers, $email->getHeaders());
    }

    /**
     * @covers       \GoDaddy\WordPress\MWC\Common\Email\Email::setContentType
     * @covers       \GoDaddy\WordPress\MWC\Common\Email\Email::getContentType
     */
    public function testContentTypeSetterGetter()
    {
        $to = 'johndoe@example.com';
        $contentType = 'text/html';

        $email = new Email($to);
        $email->setContentType($contentType);

        $this->assertSame($contentType, $email->getContentType());

        $this->assertArrayHasKey('Content-type', $email->getHeaders());
        $this->assertEquals($contentType, ArrayHelper::get($email->getHeaders(), 'Content-type'));
    }

    /**
     * @covers       \GoDaddy\WordPress\MWC\Common\Email\Email::send()
     * @throws \Exception
     */
    public function testSend()
    {
        $to = 'johndoe@example.com';
        $subject = 'This is a test email';
        $body = 'This is a test email
        with a multiline body';
        $headers = [
            'test-key' => 'test value',
        ];
        $contentType = 'text/html';

        $email = new Email($to);
        $email->setSubject($subject)
              ->setBody($body)
              ->setHeaders($headers)
              ->setContentType($contentType);

        WP_Mock::expectFilterAdded('wp_mail_content_type', [$email, 'getContentType']);
        WP_Mock::userFunction('wp_mail')
            ->withArgs([
                $to, $subject, $body, $email->getHeaders(),
            ])
               ->once();

        WP_Mock::userFunction('remove_filter');

        $email->send();

        $this->assertConditionsMet();
    }
}
