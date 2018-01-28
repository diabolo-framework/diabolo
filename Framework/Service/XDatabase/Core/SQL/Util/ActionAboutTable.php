<?php
/**
 * This file is part of x-database service.
 * @license LGPL http://www.gnu.de/documents/lgpl.en.html
 */
namespace X\Service\XDatabase\Core\SQL\Util;
/**
 * ActionAboutTable
 * @author  Michael Luthor <michael.the.ranidae@gmail.com>
 * @since   0.0.0
 * @version 0.0.0
 */
abstract class ActionAboutTable extends ActionBase {
    /**
     * The name of table to operate.
     * @var string
     */
    protected $name = null;
    
    /**
     * Set the name of the table to operate
     * @param string $name The name for table to rename
     * @return ActionAboutTable
     */
    public function name( $name ) {
        $this->name = $name;
        return $this;
    }
}