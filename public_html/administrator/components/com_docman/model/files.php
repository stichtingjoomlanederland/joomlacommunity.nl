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
class ComDocmanModelFiles extends ComDocmanModelNodes
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->remove('folder')->insert('folder', 'com:files.filter.path', null)
            ->remove('name')->insert('name', 'com:files.filter.path', null, true)
            ->insert('extension', 'cmd')
            ->insert('mimetype', 'string')
            ->insert('types'	, 'cmd', '')
            ->insert('count', 'boolean', false)
            ->remove('sort')->insert('sort', 'cmd', 'path');
    }

    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryColumns($query);

        $query->columns('SUBSTRING_INDEX(tbl.name, ".", -1) AS extension');
        $query->columns('m.mimetype AS mimetype');

        if ($this->getState()->count) {
            $query->columns('fc.count AS count');
        }
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryJoins($query);

        $query->join(array('m' => 'files_mimetypes'), 'm.extension = SUBSTRING_INDEX(tbl.name, ".", -1)');

        if ($this->getState()->count) {
            $query->join(array('fc' => 'docman_file_counts'),
                'fc.storage_path = CONCAT_WS("/", NULLIF(tbl.folder, ""), tbl.name)');
        }
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($state->types)
        {
            $types     = (array) $state->types;
            $extension = $state->extension ? (array) $state->extension : array();

            // Image only
            if (in_array('image', $types) && !in_array('file', $types)) {
                $extension = array_merge($extension, ComFilesModelEntityFile::$image_extensions);
                $state->extension = $extension;
            }

            if (!in_array('file', $types)) {
                $query->where('1 = 2');
            }
        }

        if ($state->extension)
        {
            $query->where('SUBSTRING_INDEX(tbl.name, ".", -1) IN :extension')
                ->bind(array('extension' => (array) $state->extension));
        }

        if ($state->mimetype)
        {
            $query->where('m.mimetype IN :mimetype')
                ->bind(array('mimetype' => (array) $state->mimetype));
        }
    }
}
