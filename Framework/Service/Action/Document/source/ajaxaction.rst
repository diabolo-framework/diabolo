Ajax Action
===========
ajax action use to handle ajax request.

Class Defination
----------------
example : ::

    <?php
    namespace X\Service\Action\Test\Resource\Action;
    use X\Service\Action\Handler\AjaxAction;
    class MyAjax extends AjaxAction {
        protected function run( $account, $password ) {
            if ( 'account'===$account && 'password'===$password ) {
                $this->success(array('uid'=>1));
            } else {
                 throw new \Exception('unknown account');
            }
        }
    }

as default, ``success()`` and ``error()`` responses following formation : ::

    {
        success : 1,
        message : 'successed',
        data : {
            uid : 1
        }
    }

you can response custom formation by using ``response()`` method, for example : ::

    $this->response(array(
       'xxx' => 'yyy'
    ));

and the response text will be : ::

    {
        xxx : yyy
    }