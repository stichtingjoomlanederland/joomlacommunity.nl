<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanModelScans extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->remove('sort')
            ->insert('sort', 'string')
            ->insert('status', 'int')
            ->insert('identifier', 'identifier');
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();
        
        if ($state->identifier) {
            $query->where('identifier IN :identifier')->bind(array('identifier' => (array) $state->identifier));
        }

        if ($state->status !== null) {
        	$query->where('status IN :status')->bind(array('status' => (array) $state->status));
        }
    }
}
