Service
=======

Configuration
-------------
example : ::

    <?php
    use X\Service\Mail\Service as MailService;
    use X\Service\Mail\Handler\PhpMail;
    use X\Service\Mail\Handler\Smtp;
    return array(
        'document_root' => __DIR__,
        'module_path' => array(),
        'service_path' => array(),
        'library_path' => array(),
        'modules' => array(),
        'params' => array(),
        'services' => array(
            'Mail' => array(
                'class' => MailService::class,
                'enable' => true,
                'delay' => false,
                'params' => array(
                    'mailRuntimeError' => array(
                        'handler' => 'phpmail_tester',
                        'subject' => 'SYSTEM ERROR'
                    ),
                    'mailers' => array(
                        'phpmail_tester' => array(
                            'class' => PhpMail::class,
                        ),
                        'smtp_tester' => array(
                            'class' => Smtp::class,
                            'host' => 'smtp.sina.com',
                            'port' => 25,
                            'username' => 'michaelluthor@sina.com',
                            'password' => 'ginhappy1215',
                            'isAuthRequired' => true,
                        ),
                    ),
                ),
            ),
        ),
    );


- smtp config

    - ``class`` class name of smtp handler \X\Service\Mail\Handler\Smtp::class,
    - ``host`` smtp host 
    - ``port`` smtp server port
    - ``isAuthRequired`` is authoration required
    - ``username`` user name to smtp server
    - ``password`` password of user

- php mail() config

    - ``class`` class name of phpmail handler X\Service\Mail\Handler\PhpMail:class

Send Runtime Error
------------------
mail service will register magic call hander ``mailRuntimeError()`` if you configed it.
the configuration example : ::

    'mailRuntimeError' => array(
        'handler' => 'phpmail_tester',
        'subject' => 'SYSTEM ERROR'
    ),

- ``handler`` name of mail hander to send error mail
- ``subject`` content of mail title

Send Mail
---------
example : ::

    $mail = new Mail();
    $mail->from = 'michaelluthor@sina.com';
    $mail->subject = 'TEST PHP MAIL';
    $mail->content = 'THIS MESSAG SEND BY PHP MAIL() FUNCTION';
    $mail->addRecipient('568109749@qq.com');
    $mail->addRecipient('2971307115@qq.com');
    $mail->addAttachment('1.jpeg', __DIR__.'/../../Resource/Attachments/1.jpeg');
    $mail->addAttachment('1.json', __DIR__.'/../../Resource/Attachments/1.json');
    $mail->send('phpmail_tester');

