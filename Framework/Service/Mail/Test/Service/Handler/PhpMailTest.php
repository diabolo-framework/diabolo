<?php
namespace X\Service\Error\Test\Service\Handler;
use PHPUnit\Framework\TestCase;
use X\Service\Mail\Mail;
class PhpMailTest extends TestCase {
    /***/
    public function test_send() {
        $mail = new Mail();
        $mail->subject = 'TEST PHP MAIL';
        $mail->content = 'THIS MESSAG SEND BY PHP MAIL() FUNCTION';
        $mail->addRecipient('568109749@qq.com');
        $mail->addRecipient('2971307115@qq.com');
        $mail->addAttachment('1.jpeg', __DIR__.'/../../Resource/Attachments/1.jpeg');
        $mail->addAttachment('1.json', __DIR__.'/../../Resource/Attachments/1.json');
        $mail->send('phpmail_tester');
    }
}