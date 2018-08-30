Query
=====
query builder use to generate sql query and you don't have to change your code if
you switch to other database, because it would generate different sql for each 
database.

Select
------

**basic usage**

example : ::

    use X\Service\Database\Query;
    use X\Service\Database\Query\Condition;
    use X\Service\Database\Query\Expression;
    
    $result = Query::select($database)
        ->expression('id', 'UserId')
        ->expression('name', 'UserName'),
        ->expression('age')
        ->column('created_at', 'CreatedAt')
        ->column('updated_at')
        ->from('table_001', 'tb_user')
        ->from('table_002')
        ->table('table_003')
        ->where(['id'=>1])
        ->orderBy('age', SORT_ASC)
        ->groupBy('age')
        ->having(Condition::build()->greaterThan(Expression::count(), 1))
        ->leftJoin('another_table', $joinCondition, 'U2')
        ->offset(1)
        ->limit(1)
        ->all();

select query use ``all()`` , ``one()`` , or ``count()`` to end the query and get result.


**work with active record filter**

also, you can use active record's filter by setup releated active record, for example : ::

    $result = Query::select($database)
        -> from('table')
        ->setReleatedActiveRecord(ActiveRecordClassName::class)
        ->filter('filter-name')
        ->filter('another-filter')
        ->withoutDefaultFileter()
        ->all();


**change result data type**

as default, select query fetch result into array data, and you can set the result data type
by calling ``setFetchStyle()``, for example, to fetch into class : ::

    $result = Query::select($database)
        -> from('table')
        ->setFetchStyle(\X\Service\Database\QueryResult::FETCH_CLASS)
        ->setFetchClass(TargetClassName::class)
        ->all();

Insert
------
**basic usage**

example : ::

    $insertCount = Query::insert($dbName)
        ->table('users')
        ->value(array(
            'name' => 'INS-0001',
            'age' => 100,
            'group' => 'INS_T'
        ))
        ->exec();

**batch insert**

example : ::

    $insertCount = Query::insert($dbName)
        ->table('users')
        ->values(array(
            array('name' => 'INS-000X','age' => 100,'group' => 'INS_T'),
            array('name' => 'INS-000Y','age' => 100,'group' => 'INS_T'),
        ))
        ->exec();

we use ``value()`` to insert one row, and ``values()`` to insert mutil rows.
also, it's available to call value mutil times to set more that one row data.

Update
------

example : ::

    $updateCount = Query::update($dbName)
        ->table('users')
        ->set('group', 'GROUP-UP')
        ->values(array('name'=>'new-name', 'age'=>0))
        ->limit(1)
        ->where($condition)
        ->exec();

Delete
------

example : ::

    $deletedCount = Query::delete($dbName)
        ->from($tableName)
        ->where(['name'=>'U001-DM'])
        ->limit(1)
        ->exec();

Condition
---------
condition builder use to generate conditions for condition part of query, example : ::

    use X\Service\Database\Query\Condition;
    $condition = Condition::build()
        ->setDatabase('dbname')
        ->is('id', 10) # id = 10
        ->isNot('id', 10) # id <> 10
        ->equals('id', 10) # id = 10
        ->notEquals('id', 10) # id <> 10
        ->lessThan('age', 10) # age < 10
        ->lessOrEqual('age', 10) # age <= 10
        ->greaterThan('age', 10) # age > 10
        ->greaterOrEqual('age', 10) # age >= 10
        ->contains('name', 'xyz') # name LIKE '%xyz%'
        ->notContains('name', 'xyz') # name NOT LIKE '%xyz%'
        ->beginWith('name', 'xyz') # name LIKE 'xyz%'
        ->endWith('name', 'xyz') # name LIKE '%xyz'
        ->isNull('id') # id IS NULL
        ->isNotNull('id') # id IS NOT NULL
        ->between('age', 1, 10) # age BETWEEN 1 AND 10
        ->notBetween('age', 1, 10)  # age NOT BETWEEN 1 AND 10
        ->in('age', array(1,2,3)) # age IN(1,2,3)
        ->notIn('age', array(1,2,3)) # age NOT IN(1,2,3)
        ->add(Condition::build()->is('id',10)) # add another condition
        ->andThat(Condition::build()->is('id',10))
        ->orThat(Condition::build()->is('id',10));

to get parameters in condition you can call ``getBindParams()`` to get them, 
and if there are some extra parameters in query, you need to call ``setPreviousParams()``
to condition to make sure all query parameters are matched.

Expression
----------
expresion use to generate raw query part, expression will not be quoted, for example : ::

     $result = Query::select($database)
        ->expression(new X\Service\Database\Query\Expression("1+1"))
        ->one();

CreateTable
-----------
create new table by query : ::

    Query::createTable($dbName)
        ->name('new_table')
        ->addColumn((new Column())
            ->setName('col1')
            ->setType(Column::T_STRING)
            ->setLength(100)
        )
        ->exec();

DropTable
---------
example : ::

    Query::dropTable($dbName)
        ->table($tableName)
        ->exec();

TruncateTable
-------------
example : ::

    Query::truncateTable($dbName)
        ->table('users')
        ->exec();

AlterTable
----------
alter table contains many operations, but you could only do one thing on each query.

- rename table : ::

    Query::alterTable($dbName)->table('users')->rename('new_users')->exec();

NOTE: not all database support rename table, check option ``DatabaseDriver::OPT_ALTER_TABLE_RENAME``
to make sure if database support that operation.

- add column : ::

    Query::alterTable($dbName)
         ->table('users')
         ->addColumn('newCol', 'VARCHAR(255)')
         ->exec();

- change column : ::

    Query::alterTable($dbName)
        ->table('users')
        ->changeColumn('name','VARCHAR(255)')
        ->exec();

- drop column : ::

    Query::alterTable($dbName)
        ->table('users')
        ->dropColumn('name')
        ->exec();

- add index : ::

    Query::alterTable($dbName)
        ->table('users')
        ->addIndex('idx_001', array('name'))
        ->exec();

- drop index : ::

    Query::alterTable($dbName)
        ->table('users')
        ->dropIndex('idx_001')
        ->exec();
