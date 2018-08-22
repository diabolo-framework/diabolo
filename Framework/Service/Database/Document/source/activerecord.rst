Active Record
=============

Active Record is a easy way to operate database rows, it maps database row data into
an object, and all operations on this object will effected into database row without
using sql.

class defination ::

    class User extends \X\Service\Database\ActiveRecord {
        
    }


now you can use class ``User`` to operation user data, as default, it would use ``default`` as
database connection and ``user`` as table name, for example ::

    $user = new User();
    $user->name = 'sige';
    $user->save();
    # same as "INSERT INTO user (name) VALUES ('sige')"
    
    $user = User::findOne(['id'=>1]);
    $user->delete();
    # same as "DELETE FROM USER WHERE id=1"

Defination
----------
You don't have to define attributes for the active record, it would init attribute by fetching
table column definations, and cache them to make it faster.

Sometimes, definations fetched from database table may not usefully enought for your case, for 
example, the attribute in database has not default value, and you have no right to alter the table
to add a default value, now you can deine the default value in your custom definations.

*NOTE:* once you decide to custome definations, you have to define all attributes, custom definations
will stop active record fetching definations from database table.

here is an example ::

    protected function getDefination() {
        return array(
            # Define by a string
            'id' => 'INT(10)  NOT_NULL  PRIMARY_KEY AUTO_INCREASE [0]',
            # Define by Attribute Object
            'age' =>  (new \X\Service\Database\ActiveRecord\Attribute())->setDefaultVal('mike'),
            # Define name only
            'name',
        );
    }
    
The definations support three data types : 

- Name Only.
define the name of attribute only, no key for the array element.

- Attribute Object.
create a new attribute object and set as attribute defineation.
this is the best way to define the attribute, bacause you can do whatever you want 
to define the attribute, such as add a custom validator or value builder.
  
- String
defina an attribute by string is a quick way, you can set data type, data length,
and some other things easily.
the key word ``PRIMARY_KEY`` to mark the attribute as a primary key, and the key word
``AUTO_INCREASE`` use to mark attribute as an auto increase attribute. 
all contents between ``[`` and ``]`` will be treated as default value.
and you can put some validators in defination string, such as ``NOT_NULL``, ``NOT_EMPTY``,
``UNIQUE``, ``UNSIGNED`` to validate the attribute value.
the rest part will be treated as data type and data length, if the rest part contains ``()``,
the content in `()` will be treated as data length.

Query / Find
------------
Active record make query and finding easier, there are three methods to support it, and 
they are ``findOne()``, ``findAll()`` and ``find()``.

``findAll()`` and ``findOne()`` accepts a condtion and returns the object or object array.
the ``find()`` method returns an query, which is a powerfully query builder for select.

**filter :**

you also able to build some filters on query, here is a filter in ``Book`` class ::

    class Book extends ActiveRecord {
        protected static function filterDefault() {
            return array('is_deleted'=>0);
        }
        protected static function filterNotBorrowed() {
            return Condition::build()->isNot('is_borrowed', 1);
        }
    }

the default filter ``filterDefault`` will be used everytime you call ``Book::find*()``,
use ``withoutDefaultFilter()`` on query to disable this filter.

while you calling the filter, the name of filter is the filter method's name without prefix 
``filter``, for example, ``filterNotBorrowed`` should be use as ``NotBorrowed`` on query.

here are some query examples : ::

    # find all users
    $users = User::findAll();
    # same as 
    $users = Users::find()->all();

    # find all vip users
    $users = Users::findAll(['vip'=>1]);
    # same as 
    $users = Users::find()->where(['vip'=>1])->all();
    
    # find all none-vip users
    $users = Users::findAll(Condition::build()->isNot('vip', 1));
    # same as 
    $users = Users::find()->where(Condition::build()->isNot('vip', 1))->all();
    
    # limit and offset
    $users = Users::find()->where(['vip'=>1])->offset(20)->limit(10)->all();
    
    # with filters
    $users = User::find()->filter('vip')->filter('actived')->all();
    
    # without default filter
    $users = User::find()->withoutDefaultFilter()->all();

Create / Update
---------------

- Delete
- Relation
- Validate
- Attribute
