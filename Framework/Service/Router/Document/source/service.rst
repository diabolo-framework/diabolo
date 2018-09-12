Service
=======


Configuration
-------------
example : ::

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
                                 '/food/edit/{id}' => 
                                      'index.php?module=food&action=edit&id={id}',
                                 '{module}/{action}' => 
                                      'index.php?module={module}&action={action}',
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


- ``routers``

routers contains all rules to route url, and group by different 
routers.

the service will try to route url with configed routers until the
route result is false, and that means, the router will stop on the 
first match.

Routers
-------
now we support two routers, here there are :

**X\Service\Router\Router\UrlMap**

urlmap router route url by defined url map, for example : ::

    # source url => target url
    '/' => 'index.php?module=main&action=index'
    '/food/edit/{id}' => 'index.php?module=food&action=edit&id={id}'

urlmap support parameter place holder to match parameters in url, the 
placeholder start with ``{`` and end with ``}``, for example, the ``{id}``
in demo url, the target placeholder will be replced if source url matched.

**X\Service\Router\Router\ActionPath**

action-path router will route url by defined format, for example : ::

    /sport/picture/edit => index.php?module=sport&action=picture/edit

so, the first part of path ``sport`` will be treated as module name, and
others will be the action name, this is the main of action-path router.


``defaultModuleName`` use to hide the module name in url path, 
for example : ::
   
   # defaultModuleName = user 
   /login => index.php?module=user&action=login

``mergeParamIntoPath`` use to parse parameters form path, 
for example : ::

    # mergeParamIntoPath = true
    # pathActionParamSeparator = -
    /sport-001/edit => index.php?module=sport&action=edit&sport=001

``defaultActionName`` use to set action name if no action set while mergeParamIntoPath enabled. for example : :: 

    /sport-001/picture-001 
    => 
    index.php?module=sport&action=picture&sport=001&picture=001

so, it's not good to understand what action it would do, of course, mostly we
treat this kind of url to show the detail page, we can use the following url
to make it clear : ::

    /sport-001/picture-001/detail

but we don't want to add ``detail`` to detail url, and we want to keep the action to detail, so we set default action name, if the last part of path has a parameter with it, the default action name will be appended to the path : ::

   /sport-001/picture-001
   =>
   /sport-001/picture-001/detail
   =>
   index.php?module=sport&action=picture/detail&sport=001&picture=001  

