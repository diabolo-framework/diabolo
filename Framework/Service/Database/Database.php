<?php
namespace X\Service\Database;
use X\Service\Database\Driver\DatabaseDriver;
use X\Service\Database\Query\Expression;
use X\Service\Database\Query\DatabaseQuery;
class Database {
    /** @var DatabaseDriver */
    private $driver = null;
    
    /**
     * @param array $config
     */
    public function __construct( $config ) {
        $driverClass = $config['driver'];
        if ( !class_exists($driverClass) ) {
            throw new DatabaseException("driver `{$config['driver']}` is not available");
        }
        
        $driver = new $driverClass($config);
        $this->driver = $driver;
    }
    
    /**
     * quote table name to make sure it's safe in query
     * @param string $tableName
     * @return string
     */
    public function quoteTableName( $tableName ) {
        return $this->driver->quoteTableName($tableName);
    }
    
    /**
     * quote column name to make sure it's safe in query
     * @param string $columnName
     * @return string
     */
    public function quoteColumnName( $columnName ) {
        return $this->driver->quoteColumnName($columnName);
    }
    
    /**
     * quote expression to make it suitable for table's name, 
     * column's name or custom expression like COUNT(*) or subquery.
     * @param mixed $expression
     * @return string
     */
    public function quoteExpression( $expression ) {
        if ( is_string($expression) ) {
            $expression = explode('.', $expression);
            $exprIndex = 0;
            switch ( count($expression) ) {
            case 3 :
                $expression[$exprIndex] = $this->quoteTableName($expression[$exprIndex]);
                $exprIndex ++;
            case 2 :
                $expression[$exprIndex] = $this->quoteTableName($expression[$exprIndex]);
                $exprIndex ++;
            case 1 : 
                $expression[$exprIndex] = $this->quoteColumnName($expression[$exprIndex]);
            }
            return implode('.', $expression);
        } else if ( $expression instanceof Expression ) {
            return $expression->toString();
        } else if ( $expression instanceof DatabaseQuery ) {
            return '( '.$expression->toString().' )';
        }
    }
    
    /**
     * Execute the sql query and return the query result
     * @param string $query the query to exec
     * @param array $params the key is param's name, and
     * value is the value to bind
     * @return \X\Service\Database\QueryResult
     */
    public function query( $query, array $params=array() ) {
        return $this->driver->query($query, $params);
    }
    
    /**
     * Execute the sql query and return the number of
     * affected rows.
     * @param string $query the query to exec
     * @param array $params the key is param's name, and
     * value is the value to bind
     * @return integer
     */
    public function exec( $query, array $params=array() ) {
        return $this->driver->exec($query, $params);
    }
    
    /**
     * get the last insert id
     * @param string $sequenceName name of sequence
     * @return mixed
     */
    public function getLastInsertId($sequenceName=null) {
        return $this->driver->getLastInsertId($sequenceName);
    }
}