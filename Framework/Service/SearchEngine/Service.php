<?php
namespace X\Service\SearchEngine;
use X\Core\Service\XService;
use X\Service\SearchEngine\Handler\SearchEnginHandlerInterface;
class Service extends XService {
    /**
     * 服务名称
     * @var string
     */
    protected static $serviceName = 'SearchEngine';
    
    /** @var SearchEnginHandlerInterface */
    private $handler = null;
    
    /**
     * 获取当前搜索引擎处理器
     * @return SearchEnginHandlerInterface
     */
    public function getHandler() {
        if ( null === $this->handler ) {
            $config = $this->getConfiguration()->toArray();
            $handlerClass = "\\X\\Service\\SearchEngine\\Handler\\{$config['type']}";
            $handler = new $handlerClass($config);
            $this->handler = $handler;
        }
        return $this->handler;
    }
}