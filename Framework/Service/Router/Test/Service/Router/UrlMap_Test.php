<?php
namespace X\Service\Router\Test\Service\Router;
use PHPUnit\Framework\TestCase;
use X\Service\Router\Router\UrlMap;
class UrlMap_Test extends TestCase {
    /** */
    public function test_route() {
        $router = new UrlMap(array(
            'map' => array(
                '/test/query' => 'index.php?module=xyx',
                '/test' => 'index.php',
                '/' => 'index.php?module=main&action=index',
                '/food/edit/{id}' => 'index.php?module=food&action=edit&id={id}',
                '/{module}/{action}' => 'index.php?module={module}&action={action}&testing=1',
            ),
        ));
        
        # 还没有测试的
        $this->assertEquals(array('p001'=>'001','p002'=>'002','module'=>'xyx'), $router->router('/test/query?p001=001&p002=002'));
        $this->assertEquals(array(),$router->router('/test'));
        $this->assertEquals(array('module'=>'main','action'=>'index'), $router->router('/'));
        $this->assertEquals(array('module'=>'food','action'=>'edit','id'=>'100'), $router->router('/food/edit/100'));
        $this->assertEquals(array('module'=>'sport','action'=>'picture/add','testing'=>'1'), $router->router('/sport/picture/add'));
    }
}