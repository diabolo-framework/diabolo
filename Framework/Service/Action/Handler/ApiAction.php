<?php 
namespace X\Service\Action\Handler;
abstract class ApiAction extends ActionBase {
    /** @var string */
    const FMT_JSON = 'json';
    /** @var string */
    protected $responseFormat = 'json';
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Action\Handler\ActionBase::execute()
     */
    public function execute() {
        try {
            return parent::execute();
        } catch ( \Exception $e ) {
            $this->error($e->getMessage());
            return false;
        }
    }
    
    /**
     * @param array $data
     * @return void
     */
    protected function success($data=null) {
        $this->response(array(
            'success' => 1,
            'message' => '',
            'data' => $data,
        ));
    }
    
    /**
     * @param unknown $message
     * @return void
     */
    protected function error( $message ) {
        $this->response(array(
            'success' => 0,
            'message' => $message,
            'data' => null,
        ));
    }
    
    /**
     * @param unknown $data
     */
    protected function response($data) {
        $handler = 'response'.ucfirst($this->responseFormat);
        return $this->$handler($data);
    }
    
    /**
     * @param unknown $data
     */
    protected function responseJson($data) {
        echo json_encode($data);
    }
}