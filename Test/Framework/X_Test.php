<?php
namespace X\XAction;
use X\Core\X;
use PHPUnit\Framework\TestCase;
class X_Test extends TestCase {
    /**
     * 测试框架启动
     */
    public function test_start() {
        $app = X::start(array(
            'document_root' => __DIR__,
        ));
        $this->assertEquals(__DIR__, $app->getPath());
        $this->assertInstanceOf(X::class, $app);
    }
}