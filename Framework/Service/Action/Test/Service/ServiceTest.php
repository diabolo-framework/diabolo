<?php
namespace X\Service\Action\Test\Service;
use PHPUnit\Framework\TestCase;
use X\Service\Action\Service as ActionService;
class ServiceTest extends TestCase {
    /****/
    public function test_web_action() {
        $service = ActionService::getService();
        $group = $service->addGroup('test-web-action', 'X\Service\Action\Test\Resource\Action');
        $group->setViewPath(__DIR__.'/../Resource/View');
        
        $result = $group->runAction('my-web');
        $htmlContent = '<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>TESTING</title>
<style type="text/css">
body {background-color:red;height:100%;}
#main {background-color:white;height:1000%;}
</style>
<link rel="stylesheet" href="//www.google.com/cdn/bootstrap/3.14.1/boostrap.min.css" type="text/css" />
<meta name="Copyright" content="Diabolo" />
<meta content="text/html; charset=UTF-8" http-equiv="content-type" />
<script type="text/javascript" src="//www.google.com/cdn/jquery/12.0.0/jquery.min.js" charset="UTF-8"></script>
<script type="text/javascript">
var version="0.0.0";
</script>
<script type="text/javascript">
alert("OK")
</script>
<script type="text/javascript">
alert("2");
</script>
</head>
<body>
GLOBAL KEY 001 : globalKey001
<div>
GLOBAL KEY 001 : globalKey001KEY 001 : value001</div>
Hello : diabolo</body>
</html>';
        $this->assertEquals($htmlContent, $result);
    }
    
    /****/
    public function test_command_action() {
        $service = ActionService::getService();
        $group = $service->addGroup('test-command-action', 'X\Service\Action\Test\Resource\Action');
        
        $result = $group->runAction('my-cmd');
        $this->assertEquals('readline', $result['readline']);
        $this->assertEquals('r', $result['readchar']);
        $this->assertEquals('DEFAULT_CONTENT', $result['promit_default']);
        $this->assertEquals(true,$result['confirm_default']);
        $this->assertEquals(1, $result['select_default']);
        $this->assertEquals('prompt', $result['prompt']);
        $this->assertEquals(true, $result['confirm']);
        $this->assertEquals(2, $result['select']);
    }
    
    /** */
    public function test_ajax_action () {
        $service = ActionService::getService();
        $group = $service->addGroup('test-ajax-action', 'X\Service\Action\Test\Resource\Action');
        
        ob_start();
        ob_implicit_flush(false);
        $group->runAction('my-ajax', array('account'=>'text','password'=>'password'));
        $output = ob_get_clean();
        $this->assertEquals('TEXT', $output);
        
        ob_start();
        ob_implicit_flush(false);
        $group->runAction('my-ajax', array('account'=>'account','password'=>'password'));
        $output = ob_get_clean();
        $this->assertEquals('{"success":1,"message":"","data":{"uid":1}}', $output);
        
        ob_start();
        ob_implicit_flush(false);
        $group->runAction('my-ajax', array('account'=>'admin','password'=>'password'));
        $output = ob_get_clean();
        $this->assertEquals('{"success":0,"message":"admin is not allowed","data":null}', $output);
        
        ob_start();
        ob_implicit_flush(false);
        $group->runAction('my-ajax', array('account'=>'demo','password'=>'password'));
        $output = ob_get_clean();
        $this->assertEquals('{"demo":"demo"}', $output);
        
        ob_start();
        ob_implicit_flush(false);
        $group->runAction('my-ajax', array('account'=>'zzz','password'=>'password'));
        $output = ob_get_clean();
        $this->assertEquals('{"success":0,"message":"unknown account","data":null}', $output);
    }
    
    /** */
    public function test_api_action () {
        $service = ActionService::getService();
        $group = $service->addGroup('test-api-action', 'X\Service\Action\Test\Resource\Action');
        
        ob_start();
        ob_implicit_flush(false);
        $group->runAction('my-api', array('account'=>'account','password'=>'password'));
        $output = ob_get_clean();
        $this->assertEquals('{"success":1,"message":"","data":{"uid":1}}', $output);
        
        ob_start();
        ob_implicit_flush(false);
        $group->runAction('my-api', array('account'=>'admin','password'=>'password'));
        $output = ob_get_clean();
        $this->assertEquals('{"success":0,"message":"admin is not allowed","data":null}', $output);
        
        ob_start();
        ob_implicit_flush(false);
        $group->runAction('my-api', array('account'=>'demo','password'=>'password'));
        $output = ob_get_clean();
        $this->assertEquals('{"demo":"demo"}', $output);
        
        ob_start();
        ob_implicit_flush(false);
        $group->runAction('my-api', array('account'=>'zzz','password'=>'password'));
        $output = ob_get_clean();
        $this->assertEquals('{"success":0,"message":"unknown account","data":null}', $output);
    }
}