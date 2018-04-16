<?php
namespace X\Service\XAction\Handler;
use X\Service\XAction\Util\Action;
use X\Service\XAction\Util\WebActionTrait;
class AjaxAction extends Action {
    /** Uses */
    use WebActionTrait;
    
    /**
     * 数据响应格式 - JSON
     * @var integer
     */
    const RESPONSE_FORMAT_JSON = 0;
    
    /**
     * 数据响应格式 - XML
     * @var integer
     */
    const RESPONSE_FORMAT_XML = 1;
    
    /**
     * 数据响应格式 - 纯文本
     * @var integer
     */
    const RESPONSE_FORMAT_TEXT = 2;
    
    /**
     * 数据响应格式
     * @var integer
     */
    protected $responseFormat = self::RESPONSE_FORMAT_JSON;
    
    /**
     * 响应自定义的数据格式
     * @param array|mixed $data
     */
    public function response( $data ) {
        switch ( $this->responseFormat ) {
        case self::RESPONSE_FORMAT_JSON : 
            echo json_encode($data);
            break;
        case self::RESPONSE_FORMAT_XML :
            throw new \Exception("Not supported.");
            break;
        case self::RESPONSE_FORMAT_TEXT :
            echo $data;
            break;
        }
    }
    
    /**
     * 错误响应
     * @param unknown $message
     * @param unknown $code
     */
    public function error($message, $code=null) {
        $this->response(array(
            'success' => false,
            'message' => $message,
            'code' => $code
        ));
    }
    
    /**
     * 成功响应
     * @param array $data
     */
    public function success($data) {
        $this->response(array(
            'success' => true,
            'data' => $data,
        ));
    }
    
    public function throw404() {}
}