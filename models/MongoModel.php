<?php

namespace app\models;

use yii\mongodb\ActiveRecord;
use yii\mongodb\Query;

abstract class MongoModel extends ActiveRecord
{
    public function __construct($config = [])
    {
        parent::__construct($config);
        
        // Update case
        if (!empty($config) && !empty($config['_id'])) {
            $this->setOldAttributes($config);
        }
    }
    
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $now = time();
            if ($insert) {
                // Create case
                $this->created = $now;
                // Node status (published by default)
                if (!isset($this->status)) {
                    $this->status = 1;
                }
            }
            $this->modified = $now;
            return true;
        }
        return false;
    }
    
    /**
     * Returns the attribute values that have been modified since they are loaded or saved most recently.
     * @param string[]|null $names the names of the attributes whose values may be returned if they are
     * changed recently. If null, [[attributes()]] will be used.
     * @return array the changed attribute values (name-value pairs)
     */
    public function getDirtyAttributes($names = null)
    {
        $attributes = parent::getDirtyAttributes($names);
        // BUG? Removing PK to avoid update errors...
        if (isset($attributes['_id'])) {
            unset($attributes['_id']);
        }
        return $attributes;
    }
    
    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return array_merge(
            ['_id', 'created', 'modified'], 
            $this->_attributes()
        );
    }
    
    /**
     * @return array list of attribute names.
     */
    abstract protected function _attributes();
    
    /**
     * Auxiliar method to find elements
     * 
     * @param array $params
     * @return \static
     */
    protected static function _findOneBy(array $params)
    {
        $query = new Query;
        $data = $query->from(self::collectionName())
                ->where($params)
                ->one();
        
        return empty($data) ? null : new static($data);
    }
}