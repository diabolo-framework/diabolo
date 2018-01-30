<?php
namespace X\Service\SearchEngine\Handler;
/**
 * @author admin
 */
class Solr implements SearchEnginHandlerInterface {
    /** 配置信息 */
    private $option = array();
    /** @var \SolrClient */
    private $client = null;
    
    /**
     * @param unknown $option
     */
    public function __construct( $option ) {
        $clientOptions = array(
             'hostname' => $option['host'],
             'path'     => $option['path'],
             'port'     => $option['port'],
        );
        $client = new \SolrClient($clientOptions);
        $this->client = $client;
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\SearchEngine\Handler\SearchEnginHandlerInterface::addDoc()
     */
    public function addDoc( $docData ) {
        $doc = new \SolrInputDocument();
        foreach ( $docData as $key => $value ) {
            $doc->addField($key, $value);
        }
        $this->client->addDocument($doc, true);
        $this->client->commit();
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\SearchEngine\Handler\SearchEnginHandlerInterface::deleteDocById()
     */
    public function deleteDocById( $id ) {
        $this->client->deleteById($id);
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\SearchEngine\Handler\SearchEnginHandlerInterface::updateDocById()
     */
    public function updateDocById( $id, $docData ) {
        $this->addDoc(array_merge(array('id'=>$id), $docData));
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\SearchEngine\Handler\SearchEnginHandlerInterface::query()
     */
    public function query( $query ) {
        $response = $this->client->query($query);
        $responseBody = $response->getResponse();
        
        if ( property_exists($responseBody, 'error') ) {
            throw new \Exception("Solr Query Error : {$responseBody->error}");
        }
        
        $result = array();
        $result['count'] = $responseBody->response->numFound;
        $result['start'] = $responseBody->response->start;
        $result['docs'] = array();
        if ( false !== $responseBody->response->docs ) {
            foreach ( $responseBody->response->docs as $doc ) {
                $result['docs'][] = (array)$doc;
            }
        }
        return $result;
    }
}