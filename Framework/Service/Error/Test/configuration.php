<?php
use X\Service\Error\Service as ErrorService;
use X\Service\Error\Handler\Email;
use X\Service\Error\Handler\FunctionCall;
use X\Service\Error\Handler\Url;
use X\Service\Error\Handler\View;
return array(
'document_root' => __DIR__,
'module_path' => array(),
'service_path' => array(),
'library_path' => array(),
'modules' => array(),
'params' => array(),
'services' => array(
    'Error' => array(
        'class' => ErrorService::class,
        'enable' => true,
        'delay' => false,
        'params' => array(
            'stopOnError' => false,
            'types' => E_ALL,
            'handlers' => array(
                array(
                    'class' => Email::class,
                    'template' => 'default',
                    'isHtml' => false,
                ),
                array(
                    'class' => FunctionCall::class,
                    'callback' => function() {
                        echo "FUNCTION CALL HANDLER\n";
                    },
                ),
                array(
                    'class' => Url::class,
                    'url' => 'https://www.baidu.com',
                    'gotoUrl' => false,
                    'method' => 'post',
                    'parameters' => array(
                        'code' => null,
                        'message' => null,
                        'custext' => '123',
                    )
                ),
                array(
                    'class' => View::class,
                    'path' => __DIR__.'/Resource/Template/ErrorHandlerView.php',
                ),
            ),
        ),
    ),
),
);