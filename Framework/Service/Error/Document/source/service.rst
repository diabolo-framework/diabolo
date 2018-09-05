Service
=======
error service use to handle runtime error or uncatched exceptions.

Configuration
-------------
example : ::

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

Error Handlers
--------------
- ``X\Service\Error\Handler\Email`` 

**template**  the name of email template. if template set to ``default``, it would use the default tempalte 
to render mail content,  or it would find the template in document root path.

**isHtml** whether the content use html format.

mail handler will trigger magic call ``mailRuntimeError()``, and the parameter to it looks like this : ::

    arrray(
        'content' => 'demo content',
        'isHtml' => false,
    )

- ``X\Service\Error\Handler\FunctionCall`` 

**callback** the callback handler to handle error or exception

- ``X\Service\Error\Handler\Url`` 

**url** the target url to handle the exception or error

**gotoUrl** if set it to ture, error will gnereate a jump page and the use form or location to target url,
or false will call that url with curl.

**method** the request method name, for example ``get`` or ``post``

**parameters** contains the params to target url, some special will be replace into error info, and 
other value will keep what it is, the following keys will be replaced : 

    - code
    - message
    - file
    - line

- ``X\Service\Error\Handler\View`` 

**path** the view path to render error
