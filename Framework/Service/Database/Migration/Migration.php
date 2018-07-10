<?php
namespace X\Service\Database\Migration;
use X\Service\Database\Database;
abstract class Migration {
    /**
     * 
     */
    abstract public function up();
    
    /**
     * 
     */
    abstract public function down();
}