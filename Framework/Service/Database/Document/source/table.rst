Table
=====
you can use ``X\Service\Database\Table`` to manage database table, such as
create, alter, drop and much more than that.

you need to get table instance before the operations, except creating new table.
you can get all tables in database by following code : ::

    $tables = X\Service\Database\Table::all($db);

or you can get one table by given table name : ::

    $table = X\Service\Database\Table::get($db, $tableName);

Table
-----

- create table : ::

    $newTable = X\Service\Database\Table::($db, $name, array(
        # define table column by string
        'id INTEGER AUTO_INCREASEMENT PRIMARY KEY',
        
        # define table column by column object
        X\Service\Database\Table\Column::build()
           ->setName('name')
           ->setType('String')
           ->setLength(10)
    ));

- drop table : ::

    $table = X\Service\Database\Table::get($db, $tableName);
    $table->drop();

- trucate table : ::

    $table = X\Service\Database\Table::get($db, $tableName);
    $table->truncate();
    
- rename : ::

    $table = X\Service\Database\Table::get($db, $tableName);
    $table->rename('my-new-table-name');

Column
------

- list all columns : ::

    $table = X\Service\Database\Table::get($db, $tableName);
    $columns = $table->getColumns();

- add column : ::

    $table = X\Service\Database\Table::get($db, $tableName);
    $table->addColumn('newColumn', 'TEXT');

- drop column : ::

    $table = X\Service\Database\Table::get($db, $tableName);
    $table->dropColumn('columnName');

- rename column : ::

    $table = X\Service\Database\Table::get($db, $tableName);
    $table->renameColumn('columnName', 'newColumnName');

- change column : ::

    $table = X\Service\Database\Table::get($db, $tableName);
    $table->changeColumn('columnName', 'TEXT NOT NULL');

Index
-----

- add index : ::

    $table = X\Service\Database\Table::get($db, $tableName);
    $table->addIndex('idx_id_name', array('id','name'));

- drop index : ::

    $table = X\Service\Database\Table::get($db, $tableName);
    $table->dropIndex('idx_id_name');

Forgin Key
-----------

- add forgin key : ::

    $table = X\Service\Database\Table::get($db, $tableName);
    $table->addForginKey('fk_user_id',array('user_id'), 'users', array('id'));

- drop forgin key : ::

    $table = X\Service\Database\Table::get($db, $tableName);
    $table->dropForginKey('fk_user_id');

