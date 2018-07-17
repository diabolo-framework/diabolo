<?php
namespace X\Service\Database\Command\Token;
abstract class BaseToken {
    /**
     * @param unknown $commandParams
     * @param unknown $value
     * @return string
     */
    protected function getCommandParamKey ( &$commandParams, $value ) {
        $key = ':qp'.count($commandParams);
        $commandParams[$key] = $value;
        return $key;
    }
}