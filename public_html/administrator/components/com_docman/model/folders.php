<?php
/**
 * @package     Nooku_Components
 * @subpackage  Files
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Files Model
 *
 * @author      Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package     Nooku_Components
 * @subpackage  Files
 */
class ComDocmanModelFolders extends ComDocmanModelNodes
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->remove('folder')->insert('folder', 'com:files.filter.path', null)
            ->remove('name')->insert('name', 'com:files.filter.path', null, true)
            ->remove('sort')->insert('sort', 'cmd', 'path')
            ->insert('tree', 'boolean', false);
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($state->types)
        {
            $types     = (array) $state->types;

            if (!in_array('folder', $types)) {
                $query->where('1 = 2');
            }
        }

    }
}
