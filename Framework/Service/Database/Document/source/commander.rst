Commander
=========

Commander use to execute some complicated sql query, and it support some basic
controlling tokens, such as ``foreach`` and ``if``, here is a command example : ::

    -- command : testMixedUp
    SELECT * FROM students
    WHERE 1=1
    {{foreach conditions as key => value}}
      {{if value}}
      AND {{#key}} = {{value}}
      {{endif}}
    {{endforeach}}
    ORDER BY id DESC

and then, you can execute commander like this : ::

    $command = Commander::getCommand('student.testMixedUp', $database);
    $command->query(['conditions'=>['name'=>'michael','age'=>10, 'falseTest'=>false]])->count();

Command File
------------
Command file is able to contains more that one sql's defination, the file's extension must be ``.sql``.
here is an template of command defination : ::

    -- command : search
    -- return : Student
    -- This is a comment, a comment, a comment 
    -- and a comment, a comment, a comment
    Select * FROM students

- ``-- command : search`` 

not optional, define the name of command.

- ``-- return : Student``

optional, define the return type, default to array

- the other part of sql comments before the query content will be treated as comment of command

- query content

Tokens
------
Commander support follwing tokens : 

- **Placeholder Token**

placeholder token handle the placeholder, placeholder will be replaced in sql query, without 
any quote operation, template : ::

    {{#placeholder}}

for example : ::

    SELECT * FROM {{#tableName}}

execute : ::

    $command->query(['tableName'=>'mytable']);

the query will be : ::

    SELECT * FROM mytable

- **Value Token**

value token handle the value to query params, the value token will be replace as as parameter
placeholder on query, template : ::

    {{value}}

for example : ::

    SELECT * FROM mytable WHERE id={{id}}

execute : ::

    $commande->query(['id'=>1]);

the query will be : ::

    SELECT * FROM mytable WHERE id=:qp0

- **If Token**

if token allows for conditional generation of sql query, template : ::

    {{if value}}
       Query Content
    {{endif}}

or : ::

    {{if value operator anothervalue}}
      Query Content
    {{endif}}

or : ::

    {{if value operator fixedvalue}}
      Query Content
    {{endif}}

for exmple : ::

    -- if age is set and it's not empty 
    {{if age}} 
       AND age > {{age}}
    {{endif}}
    
    -- if name is equals to `michael`
    {{if name = 'michael'}} 
      AND name = 'michael'
    {{endif}} 
    
    -- if class is greater than value maxClass
    {{if class > maxClass}} 
      AND age = 'MAX_CLASS'
    {{endif}}
    
    -- if class is greater that 10
    {{if class > 10}} 
      AND age > 10
    {{endif}}

operator supports ``=``, ``!=``, ``>``, ``>=``, ``<``, ``<=``. 

- **Foreach Token**

foreach token use to genereate query content by a loop, template : ::

    {{foreach conditions as key => value}}
      AND {{#key}} = {{value}}
    {{endforeach}}

for example : ::

   SELECT * FROM users
   ORDER BY 
   {{foreach orders as column => order}}
     {{#column}} {{#order}},
   {{endforeach}}
   created_at DESC

exec : ::

    $query->query(['orders'=>array('id'=>'DESC', 'level'=>'DESC')]);

the query will be : ::

    SELECT * FROM users
    ORDER BY 
      id DESC,
      level DESC,
    created_at DESC

Execute Command
---------------

Before you execute a command, you have to tell commander where to find the commannd 
files, you can call ``\X\Service\Database\Commander::addPath($path)`` to add searching
path, and you can call it again and again to add more path.

a command support ``exec()`` and ``query()``, ``exec()`` returns affected rows count, 
and ``query()`` returns data rows.

for example : ::

    $db = \X\Service\Database\Service::getService()->getDB('demodb');
    Commander::addPath(Service::getService()->getPath('Paht/To/Command'));
    Commander::addPath(Service::getService()->getPath('Another/Path/To/Command'));
    
    $command = Commander::getCommand('student.testMixedUp', $this->db);
    $command->query(['conditions'=>['name'=>'michael','age'=>10, 'falseTest'=>false]])->count();
    
    $command = Commander::getCommand('student.testStartByToken', $this->db);
    $command->query(['value'=>'SELECT'])->count();

Command path could be configured at service confiuration, so that you do have to set them every time.
for example : ::

    'commandPaht' => array(
        '/Paht/To/Command',
        '/Another/Paht/To/Command'
    ),
    
