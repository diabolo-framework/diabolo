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