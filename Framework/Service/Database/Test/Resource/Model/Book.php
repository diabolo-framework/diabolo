<?php
namespace X\Service\Database\Test\Resource\Model;
use X\Service\Database\ActiveRecord;
use X\Service\Database\Query\Condition;
/**
 * @method Author getAuthor()
 * @method Reader[] getReaders()
 * @method Label[] getLabels()
 * @method Library getLibrary()
 */
class Book extends ActiveRecord {
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\ActiveRecord::getRelations()
     */
    protected function getRelations() {
        return array(
            'author' => array(
                'type' => self::REL_HAS_ONE,
                'key' => 'book_id',
                'class' => Author::class
            ),
            'readers' => array(
                'type' => self::REL_HAS_MANY,
                'key' => 'book_id',
                'class' => Reader::class,
            ),
            'labels' => array(
                'type' => self::REL_MANY_TO_MANY,
                'targetClass' => Label::class,
                'mapClass' => BookLabelMap::class,
                'selfKey' => 'book_id',
                'targetKey' => 'label_id',
            ),
            'library' => array(
                'type' => self::REL_BELONGS,
                'class' => Library::class,
                'key' => 'library_id',
            ),
        );
    }
    
    /**
     * @return number[]
     */
    protected static function filterDefault() {
        return array('is_deleted'=>0);
    }
    
    /**
     * @return \X\Service\Database\Query\Condition
     */
    protected static function filterNotBorrowed() {
        return Condition::build()->isNot('is_borrowed', 1);
    }
}