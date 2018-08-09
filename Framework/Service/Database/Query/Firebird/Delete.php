<?php
namespace X\Service\Database\Query\Firebird;
use X\Service\Database\Query\Delete as QueryDelete;
use X\Service\Database\Query;
class Delete extends QueryDelete {
    /**
     * @param array $query
     * @return void
     */
    protected function buildLimit( &$query ) {
        if ( null === $this->limit ) {
            return;
        }
        $query[] = 'ROWS '.$this->limit;
    }
}