<?php
namespace X\Service\Database\Test\Service;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Test\Resource\Model\Student;
use X\Service\Database\Test\Resource\Model\ValidatorTestAR;
use X\Service\Database\Test\Resource\Model\Book;
use X\Service\Database\ActiveRecord;
use X\Service\Database\Table;
use X\Service\Database\Test\Resource\Model\Author;
use X\Service\Database\Test\Resource\Model\Reader;
use X\Service\Database\Test\Resource\Model\Label;
use X\Service\Database\Test\Resource\Model\BookLabelMap;
use X\Service\Database\Test\Resource\Model\Library;
require_once __DIR__.'/../Resource/Model/BookStudentMap.php';
class ActiveRecordTest extends TestCase {
    public function test_relations() {
        $library = new Library();
        $library->name = 'LIB-001';
        $library->save();
        
        $book = new Book();
        $book->name = 'BOOK-AVAILABLE';
        $book->is_borrowed = 0;
        $book->is_deleted = 0;
        $book->library_id = $library->id;
        $book->save();
        
        $author = new Author();
        $author->book_id = $book->id;
        $author->name = 'AUTHOR-001';
        $author->save();
        
        $reader = new Reader();
        $reader->name = 'READER-001';
        $reader->book_id = $book->id;
        $reader->save();
        
        $reader = new Reader();
        $reader->name = 'READER-002';
        $reader->book_id = $book->id;
        $reader->save();
        
        $label = new Label();
        $label->name = 'LABEL-001';
        $label->save();
        
        $labelMap = new BookLabelMap();
        $labelMap->book_id = $book->id;
        $labelMap->label_id = $label->id;
        $labelMap->save();
        
        $label = new Label();
        $label->name = 'LABEL-002';
        $label->save();
        
        $labelMap = new BookLabelMap();
        $labelMap->book_id = $book->id;
        $labelMap->label_id = $label->id;
        $labelMap->save();
        
        # test has one
        $testAuthor = $book->getAuthor();
        $this->assertEquals($author->name, $testAuthor->name);
        Table::get(Author::getDB(), Author::tableName())->truncate();
        
        # test has many
        $testReaders = $book->getReaders();
        $this->assertEquals(2, count($testReaders));
        Table::get(Reader::getDB(), Reader::tableName())->truncate();
        
        # test many to many
        $testLabels = $book->getLabels();
        $this->assertEquals(2, count($testLabels));
        Table::get(Label::getDB(), Label::tableName())->truncate();
        Table::get(BookLabelMap::getDB(), BookLabelMap::tableName())->truncate();
        
        # test belongs
        $testLib = $book->getLibrary();
        $this->assertEquals($library->name, $testLib->name);
        Table::get(Library::getDB(), Library::tableName())->truncate();
        
        Table::get(Book::getDB(), Book::tableName())->truncate();
    }
    
    public function test_find_with_filters() {
        $book = new Book();
        $book->name = 'BOOK-DELETED';
        $book->is_borrowed = 0;
        $book->is_deleted = 1;
        $book->save();
        
        $book = new Book();
        $book->name = 'BOOK-BORROWED';
        $book->is_borrowed = 1;
        $book->is_deleted = 0;
        $book->save();
        
        $book = new Book();
        $book->name = 'BOOK-AVAILABLE';
        $book->is_borrowed = 0;
        $book->is_deleted = 0;
        $book->save();
        
        # default and custome filter
        $books = Book::find()->filter('NotBorrowed')->all();
        $this->assertEquals('BOOK-AVAILABLE', $books[0]->name);
        $this->assertEquals(1, count($books));
        
        # default filter
        $books = Book::find()->all();
        $this->assertEquals(2, count($books));
        
        # no filter
        $books = Book::find()->withoutDefaultFileter()->all();
        $this->assertEquals(3, count($books));
        
        Table::get(Book::getDB(), Book::tableName())->truncate();
    }
    
    /***/
    public function test_get_attribute_definations_by_default() {
        $book = new Book();
        $this->assertTrue($book->has('id'));
        $this->assertTrue($book->has('name'));
        
        $attrId = $book->getAttr('id');
        $this->assertTrue($attrId->getIsPrimaryKey());
        $this->assertTrue($attrId->getIsAutoIncrement());
    }
    
    /***/
    public function test_get_table_name_by_active_record_class_name() {
        $this->assertEquals('book', Book::tableName());
        $this->assertEquals('book_student_map', \BookStudentMap::tableName());
    }
    
    /***/
    public function test_get_database_with_default_getDB_method() {
        $this->assertEquals(ActiveRecord::DB_DEFAULT_NAME, Book::getDB());
    }
    
    /***/
    public function test_save() {
        $student = new Student();
        $student->name = 'AR-001';
        $student->age = 10;
        $this->assertTrue($student->save());
        
        $student->name = 'AR-001-up';
        $this->assertTrue($student->save());
        
        $this->assertTrue($student->delete());
    }
    
    /***/
    public function test_validate() {
        $model = new ValidatorTestAR();
        $model->id = 'XXX';
        $model->validate();
        $errors = $model->getErrors('id');
        $this->assertTrue(in_array('id is not a number', $errors));
    }
    
    /**  */
    public function test_find() {
        $student1 = new Student();
        $student1->name = 'AR-001';
        $student1->age = 10;
        $this->assertTrue($student1->save());
        
        $student2 = new Student();
        $student2->name = 'AR-002';
        $student2->age = 20;
        $this->assertTrue($student2->save());
        
        $students = Student::find()->orderBy('id', SORT_ASC)->all();
        $this->assertEquals(Student::class, get_class($students[0]));
        $this->assertEquals('AR-001', $students[0]->name);
        
        $student = Student::findOne(['name'=>'AR-001']);
        $this->assertEquals(Student::class, get_class($student));
        $this->assertEquals('AR-001', $student->name);
        
        $student1->delete();
        $student2->delete();
    }
    
    /**  */
    public function test_deleteAll() {
        $student1 = new Student();
        $student1->name = 'AR-001';
        $student1->age = 10;
        $this->assertTrue($student1->save());
        
        $student2 = new Student();
        $student2->name = 'AR-002';
        $student2->age = 20;
        $this->assertTrue($student2->save());
        
        $this->assertEquals(2, Student::deleteAll(['age'=>[10,20]]));
        $this->assertEquals(0, count(Student::findAll()));
    }
    
    /**  */
    public function test_updateAll() {
        $student1 = new Student();
        $student1->name = 'AR-001';
        $student1->age = 10;
        $this->assertTrue($student1->save());
        
        $student2 = new Student();
        $student2->name = 'AR-002';
        $student2->age = 20;
        $this->assertTrue($student2->save());
        
        $this->assertEquals(2, Student::updateAll(['age'=>1], ['age'=>[10,20]]));
        $this->assertEquals(1, Student::findOne()->age);
        
        Student::deleteAll();
    }
}