<?php
namespace X\Service\Database\Query;
class TruncateTable extends DatabaseQuery {
    /** @var string */
    private $table = null;
    
    /**
     * @param string $name
     * @return self
     */
    public function table( $name ) {
        $this->table = $name;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Query\DatabaseQuery::toString()
     */
    public function toString() {
        $query = array();
        $query[] = 'TRUNCATE TABLE';
        $query[] = $this->getDatabase()->quoteTableName($this->table);
        return implode(' ', $query);
    }
    
    /**
     * @return integer
     */
    public function exec() {
        return $this->getDatabase()->exec($this->toString());
    }
}