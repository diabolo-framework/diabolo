<?php
namespace X\Service\Database\Query\Sqlite;
class TruncateTable extends \X\Service\Database\Query\TruncateTable {
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Query\DatabaseQuery::toString()
     */
    public function toString() {
        $query = array();
        $query[] = 'DELETE FROM';
        $query[] = $this->getDatabase()->quoteTableName($this->table);
        return implode(' ', $query);
    }
}