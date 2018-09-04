<?php
use X\Service\Action\Service as ActionService;
return array(
'document_root' => __DIR__,
'module_path' => array(),
'service_path' => array(),
'library_path' => array(),
'modules' => array(),
'params' => array(),
'services' => array(
    'Action' => array(
        'class' => ActionService::class,
        'enable' => true,
        'delay' => true,
        'params' => array(),
    ),
),
);