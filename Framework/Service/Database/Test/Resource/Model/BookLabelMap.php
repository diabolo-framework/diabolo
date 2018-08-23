<?php
namespace X\Service\Database\Test\Resource\Model;
use X\Service\Database\ActiveRecord;
class BookLabelMap extends ActiveRecord {
    /**
     * @return string
     */
    public static function tableName() {
        return 'book_label_map';
    }
}