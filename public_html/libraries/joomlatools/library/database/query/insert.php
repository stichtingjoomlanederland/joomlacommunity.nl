<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Insert Database Query
 *
 * @author  Gergo Erdosi <https://github.com/gergoerdosi>
 * @package Koowa\Library\Database\Query
 */
class KDatabaseQueryInsert extends KDatabaseQueryAbstract
{
    /**
     * The table name.
     *
     * @var string
     */
    public $table;

    /**
     * Array of column names.
     *
     * @var array
     */
    public $columns = array();

    /**
     * Array of values.
     *
     * @var array
     */
    public $values = array();

    /**
     * Build the table clause 
     *
     * @param  string $table The table name.
     * @return KDatabaseQueryInsert
     */
    public function table($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Build the columns clause 
     *
     * @param  array $columns Array of column names.
     * @return KDatabaseQueryInsert
     */
    public function columns(array $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Build the values clause 
     *
     * @param  array $values Array of values.
     * @return KDatabaseQueryInsert
     */
    public function values($values)
    {
        if(!$values instanceof KDatabaseQuerySelect)
        {
            if (!$this->columns && !is_numeric(key($values))) {
                $this->columns(array_keys($values));
            }

            $this->values[] = array_values($values);
        }
        else $this->values = $values;

        return $this;
    }

    /**
     * Render the query to a string.
     *
     * @return  string  The query string.
     */
    public function toString()
    {
        $adapter = $this->getAdapter();
        $prefix  = $adapter->getTablePrefix();
        $query   = 'INSERT';

        if($this->table) {
            $query .= ' INTO '.$adapter->quoteIdentifier($prefix.$this->table);
        }

        if($this->columns) {
            $query .= '('.implode(', ', array_map(array($adapter, 'quoteIdentifier'), $this->columns)).')';
        }

        if($this->values)
        {
            if(!$this->values instanceof KDatabaseQuerySelect)
            {
                $query .= ' VALUES'.PHP_EOL;

                $values = array();
                foreach ($this->values as $row)
                {
                    $data = array();
                    foreach($row as $column) {
                        $data[] = $adapter->quoteValue(is_object($column) ? (string) $column : $column);
                    }

                    $values[] = '('.implode(', ', $data).')';
                }

                $query .= implode(', '.PHP_EOL, $values);
            }
            else $query .= ' '.$this->values;
        }

        return $query;
    }
}
