<?php
namespace X\Service\Router\Test\Service\Router;
use PHPUnit\Framework\TestCase;
use X\Service\Router\Router\ActionPath;
class ActionPath_Test extends TestCase {
    /** */
    public function test_route() {
        $router = new ActionPath(array(
            'defaultModuleName' => 'user',
            'mergeParamIntoPath' => true,
            'pathActionParamSeparator' => '-',
            'defaultActionName' => 'detail',
        ));
        
        $this->assertEquals(array('module' => 'sport','action' => 'edit'),$router->router('/sport/edit'));
        $this->assertEquals(array('module' => 'sport','action' => 'picture/edit'),$router->router('/sport/picture/edit'));
        
        # test default module
        $this->assertEquals(array('module'=>'user', 'action'=>'login'), $router->router('/login'));
        $this->assertEquals(array('module'=>'user', 'action'=>'profile/update'), $router->router('/profile/update'));
        
        # merge params into path
        $this->assertEquals(array('module'=>'sport','action'=>'edit','sport'=>'001'), $router->router('/sport-001/edit'));
        
        # with default action
        $this->assertEquals(array('module'=>'sport','action'=>'detail', 'sport'=>'001'), $router->router('/sport-001'));
        
        # with query string
        $this->assertEquals(array('module'=>'user', 'action'=>'login','user'=>'xyz'), $router->router('/login?user=xyz'));
    }
}