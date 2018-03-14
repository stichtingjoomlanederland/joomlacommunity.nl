<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanModelEntityCategories extends KModelEntityRowset
{
    protected function _getCountQuery()
    {
        $ids = array();

        foreach ($this as $entity) {
            $ids[] = $entity->id;
        }

        return $this->getObject('lib:database.query.select')
            ->columns(array('count' => 'COUNT(*)'))
            ->table('docman_documents')
            ->where('docman_category_id IN :id')
            ->bind(array('id' => $ids));
    }

    public function setDocumentCount()
    {
        $map = count($this) ? $this->getDocumentCountMap() : array();

        foreach ($this as $category) {
            $category->document_count = isset($map[$category->id]) ? $map[$category->id]->count : 0;
        }

        return $this;
    }

    public function getDocumentCountMap()
    {
        $table = $this->getTable();
        $query = $this->_getCountQuery()->columns(array('docman_category_id'))->group('docman_category_id');

        return $table->getAdapter()->select($query, KDatabase::FETCH_OBJECT_LIST, 'docman_category_id');
    }

    /**
     * Returns the total number of documents in all categories and their children
     *
     * @return int Document count
     */
    public function countDocuments()
    {
        $ids = array();

        foreach ($this as $entity)
        {
            $descendants = $entity->getDescendants();
            $ids[] = $entity->id;

            foreach ($descendants as $descendant) {
                $ids[] = $descendant->id;
            }
        }

        $ids = array_unique($ids);

        $table = $this->getTable();
        $query = $this->_getCountQuery()->bind(array('id' => $ids));

        return $table->getAdapter()->select($query, KDatabase::FETCH_FIELD);
    }
}
