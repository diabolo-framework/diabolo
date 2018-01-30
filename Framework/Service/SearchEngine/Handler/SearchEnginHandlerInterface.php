<?php
namespace X\Service\SearchEngine\Handler;
interface SearchEnginHandlerInterface {
    /**
     * 增加索引文档
     * @param array $docData
     */
    function addDoc($docData);
    
    
    function __construct( $option );
    function query( $query );
    function updateDocById($id, $docData );
    function deleteDocById($id);
}