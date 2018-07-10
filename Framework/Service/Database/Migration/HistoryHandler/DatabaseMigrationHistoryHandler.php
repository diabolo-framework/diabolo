<?php
namespace X\Service\Database\Migration\HistoryHandler;
interface DatabaseMigrationHistoryHandler {
    function save();
    function hasProcessed($name);
    function add($name);
    function drop($name);
}