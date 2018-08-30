Driver
======
driver use to connect with database and test database meta information.

Supported Database
------------------
- Firebird
- Mssql
- Mysql
- Oracle
- Postgresql
- Sqlite

Options
-------
we call ``getOption()`` to check if the driver support the geiven function.
here is a list of options :

- OPT_PREPARE_CUSTOM_EXPRESSION 

check preparing custom expression

- OPT_ALTER_TABLE_DROP_COLUMN 

check able to drop column

- OPT_ALTER_TABLE_CHANGE_COLUMN 

check able to change column

- OPT_AUTO_INCREASE_ON_INSERT 

check able to auto increase on insert

- OPT_RENAME_COLUMN_ON_CHANGING_COLUMN 

check wether able to rename column on changing column

- OPT_UPPERCASE_TABLE_NAME 

check wehter uppercase table name

- OPT_UPPERCASE_COLUMN_NAME

check wether uppercase column name

- OPT_ALTER_TABLE_RENAME

check wether able to rename table
