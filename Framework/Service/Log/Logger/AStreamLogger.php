<?php
namespace X\Service\Log\Logger;
use X\Service\Log\Logger\ALogger;
use X\Service\Log\LogContent;
abstract class AStreamLogger extends ALogger {
    /**
     * log format defination
     * @var string
     * */
    protected $format = '{prettyTime} [{levelName}] {content}';
    /**
     * formate tempate for sprintf
     * @var string
     * */
    private $formatTemplate = null;
    /**
     * parameter name lists
     * @var array
     * */
    private $formatParamNames = array();
    
    /**
     * {@inheritDoc}
     * @see \X\Core\Component\OptionalObject::init()
     */
    protected function init() {
        parent::init();
        
        $this->formatTemplate = $this->format;
        preg_match_all('#\\{(?P<name>.*?)\\}#is', $this->format, $pnames);
        foreach ( $pnames['name'] as $pname ) {
            $this->formatParamNames[] = $pname;
            $this->formatTemplate = str_replace('{'.$pname.'}', '%s', $this->formatTemplate);
        }
    }
    
    /**
     * @param LogContent $log
     * @return string
     */
    protected function buildLogRow( LogContent $log ) {
        $params = array();
        $params[] = $this->formatTemplate;
        foreach ( $this->formatParamNames as $attribute ) {
            $params[] = $log->getAttributeByName($attribute);
        }
        return call_user_func_array('sprintf', $params);
    }
}