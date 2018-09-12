<?php
use X\Service\Router\Router\UrlMap;
use X\Service\Router\Service as RouterService;
use X\Service\Router\Router\ActionPath;
return array(
'document_root' => __DIR__,
'module_path' => array(),
'service_path' => array(),
'library_path' => array(),
'modules' => array(
    'Sport' => array(),
    'User' => array(),
),
'params' => array(),
'services' => array(
    'Router' => array(
        'class' => RouterService::class,
        'enable' => true,
        'delay' => false,
        'params' => array(
            'routers' => array(
                array(
                    'class' => UrlMap::class,
                    'fakeExtension' => 'html',
                    'map' => array(
                        # home page
                        '/' => 'index.php?module=main&action=index',
                        # with params
                        '/food/edit/{id}' => 'index.php?module=food&action=edit&id={id}',
                        '{module}/{action}' => 'index.php?module={module}&action={action}',
                    ),
                ),
                array(
                    'class' => ActionPath::class,
                    'fakeExtension' => 'html',
                    'mergeParamIntoPath' => true,
                    'defaultModuleName' => 'user',
                    'defaultActionName' => 'detail'
                ),
            ),
        ),
    ),
),
);