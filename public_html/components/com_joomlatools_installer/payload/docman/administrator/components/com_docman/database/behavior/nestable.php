<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Companion behavior for the node row
 *
 * This behavior is used for saving and deleting relations. A separate behavior is used to make sure that other behaviors
 * like orderable can use methods like getAncestors, getParent.
 */
class ComDocmanDatabaseBehaviorNestable extends KDatabaseBehaviorAbstract
{
    /**
     * Constant to fetch all levels in traverse methods
     *
     * @var int
     */
    const FETCH_ALL_LEVELS = 0;

    /**
     * We do not run afterDelete event for rows in this array
     * since they will be taken care of by their parent row.
     *
     * @var array
     */
    protected static $_to_be_deleted = array();

    protected $_relation_table;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        if (empty($config->relation_table)) {
            throw new InvalidArgumentException('Relation table cannot be empty');
        }

        $this->setRelationTable($config->relation_table);
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority' => self::PRIORITY_HIGHEST,
        ));

        parent::_initialize($config);
    }

    /**
     * Get relatives of the row
     *
     * @param string $type ancestors or descendants
     * @param int    $level Filters results by the level difference between ancestor and the row, self::FETCH_ALL_LEVELS for all
     * @param int    $mode    The database fetch style.
     *
     * @throws InvalidArgumentException
     * @return KModelEntityInterface
     */
    public function getRelatives($type, $level = self::FETCH_ALL_LEVELS, $mode = KDatabase::FETCH_ROWSET)
    {
        if (empty($type) || !in_array($type, array('ancestors', 'descendants'))) {
            throw new InvalidArgumentException('Unknown type value');
        }

        if (!$this->id && $type === 'ancestors') {
            return $this->getTable()->createRowset();
        }

        $id_column = $this->getTable()->getIdentityColumn();

        $join_column  = $type === 'ancestors' ? 'r.ancestor_id' : 'r.descendant_id';
        $where_column = $type === 'ancestors' ? 'r.descendant_id' : 'r.ancestor_id';

        $query = $this->getObject('lib:database.query.select')
            ->columns('tbl.*')
            ->columns(array('level' => 'COUNT(crumbs.ancestor_id)'))
            ->columns(array('path' => 'GROUP_CONCAT(crumbs.ancestor_id ORDER BY crumbs.level DESC SEPARATOR \'/\')'))
            ->table(array('tbl' => $this->getTable()->getName()))
            ->join(array('crumbs' => $this->getRelationTable()), 'crumbs.descendant_id = tbl.' . $id_column, 'inner')
            ->group('tbl.' . $id_column)
            ->order('path', 'ASC');

        if ($level !== self::FETCH_ALL_LEVELS)
        {
            if ($this->id) {
                $query->where('r.level IN :level');
            } else {
                $query->having('level IN :level');
            }
            $query->bind(array('level' => (array)$level));
        }

        if ($this->id)
        {
            $query->join(array('r' => $this->getRelationTable()), $join_column . ' = crumbs.descendant_id', 'inner')
                ->where($where_column . ' IN :id')
                ->where('tbl.docman_category_id NOT IN :id')
                ->bind(array('id' => (array)$this->id));
        }

        $this->getTable()->getCommandChain()->disable();
        $result = $this->getTable()->select($query, $mode);
        $this->getTable()->getCommandChain()->enable();

        return $result;
    }

    /**
     * Returns the siblings of the row
     *
     * @return KModelEntityInterface
     */
    public function getSiblings()
    {
        $parent = $this->getParent();

        return $parent && !$parent->isNew() ? $parent->getDescendants(1) : $this->getTable()->createRow()->getDescendants(1);
    }

    /**
     * Returns the first ancestor of the row
     *
     * @return KModelEntityInterface|null Parent row or null if there is no parent
     */
    public function getParent()
    {
        return $this->getRelatives('ancestors', 1);
    }

    /**
     * Get ancestors of the row
     *
     * @param int $level Filters results by the level difference between ancestor and the row, self::FETCH_ALL_LEVELS for all
     *
     * @return KModelEntityInterface A rowset containing all ancestors
     */
    public function getAncestors($level = self::FETCH_ALL_LEVELS)
    {
        return $this->getRelatives('ancestors', $level);
    }

    /**
     * Get descendants of the row
     *
     * @param int|array $level Filters results by the level difference between descendant and the row, self::FETCH_ALL_LEVELS for all
     *
     * @return KModelEntityInterface A rowset containing all descendants
     */
    public function getDescendants($level = self::FETCH_ALL_LEVELS)
    {
        return $this->getRelatives('descendants', $level);
    }

    /**
     *
     * Move the row and all its descendants to a new position
     *
     * @link http://www.mysqlperformanceblog.com/2011/02/14/moving-subtrees-in-closure-table/
     *
     * @param  int $id        Row id
     * @param  int $target_id Target to move the subtree under
     * @return boolean Result of the operation
     */
    public function move($id, $target_id)
    {
        $query = 'DELETE a FROM #__%1$s AS a'
            . ' JOIN #__%1$s AS d ON a.descendant_id = d.descendant_id'
            . ' LEFT JOIN #__%1$s AS x ON x.ancestor_id = d.ancestor_id AND x.descendant_id = a.ancestor_id'
            . ' WHERE d.ancestor_id = %2$d AND x.ancestor_id IS NULL';

        $result = $this->getTable()->getAdapter()->execute(sprintf($query, $this->getRelationTable(), $id));

        $query = 'INSERT INTO #__%1$s (ancestor_id, descendant_id, level)'
            . ' SELECT a.ancestor_id, b.descendant_id, a.level+b.level+1'
            . ' FROM #__%1$s AS a'
            . ' JOIN #__%1$s AS b'
            . ' WHERE b.ancestor_id = %2$d AND a.descendant_id = %3$d';

        $result = $this->getTable()->getAdapter()->execute(sprintf($query, $this->getRelationTable(), $id, $target_id));

        return $result;
    }

    /**
     * Get parent id
     *
     * @return int|null The parent id if row has a parent, null otherwise.
     */
    public function getParentId()
    {
        $ids = array_values($this->getParentIds());

        return $this->level > 1 ? end($ids) : null;
    }

    /**
     * Get parent ids
     *
     * @return array The parent ids.
     */
    public function getParentIds()
    {
        $ids = array_map('intval', explode('/', $this->path));
        array_pop($ids);

        return $ids;
    }

    /**
     * Checks if the given row is a descendant of this one
     *
     * @param  int|object $target Either an integer or an object with id property
     * @return boolean
     */
    public function hasAncestor($target)
    {
        $target_id = is_object($target) ? $target->id : $target;

        return $this->_checkRelationship($this->id, $target_id);
    }

    /**
     * Checks if the given row is an ancestor of this one
     *
     * @param  int|object $target Either an integer or an object with id property
     * @return boolean
     */
    public function hasDescendant($target)
    {
        $target_id = is_object($target) ? $target->id : $target;

        return $this->_checkRelationship($target_id, $this->id);
    }

    /**
     * Checks if an ID is descendant of another
     *
     * @param int $descendant Descendant ID
     * @param int $ancestor Ancestor ID
     *
     * @return boolean True if descendant is a child of the ancestor
     */
    protected function _checkRelationship($descendant, $ancestor)
    {
        if (empty($this->id)) {
            return false;
        }

        $query = $this->getObject('lib:database.query.select');
        $query->columns('COUNT(*)')
            ->table(array('r' => $this->getRelationTable()))
            ->where('r.descendant_id = :descendant_id')->bind(array('descendant_id' => (int)$descendant))
            ->where('r.ancestor_id = :ancestor_id')->bind(array('ancestor_id' => (int)$ancestor));

        $this->getTable()->getCommandChain()->disable();
        $result = (bool)$this->getTable()->select($query, KDatabase::FETCH_FIELD);
        $this->getTable()->getCommandChain()->enable();

        return $result;
    }

    protected function _beforeSelect(KDatabaseContextInterface $context)
    {
        $query  = $context->query;
        $params = $context->query->params;

        if (!$query) {
            return true;
        }

        $is_count = false;
        if ($query->isCountQuery() && $context->mode === KDatabase::FETCH_FIELD) {
            $is_count = true;
            $query->columns = array();
        }

        $id_column     = $context->getSubject()->getIdentityColumn();
        $closure_table = $this->getRelationTable();

        // We are going to force ordering ourselves here
        $query->order = array();
        $sort         = 'path';
        $direction    = 'ASC';

        $query->columns(array('level' => 'COUNT(crumbs.ancestor_id)'))
            ->columns(array('path' => 'GROUP_CONCAT(crumbs.ancestor_id ORDER BY crumbs.level DESC SEPARATOR \'/\')'))
            ->join(array('crumbs' => $closure_table), 'crumbs.descendant_id = tbl.' . $id_column, 'INNER')
            ->group('tbl.' . $id_column);

        if ($max_level = (int) $params->get('max_level')) {
            $params->set('level', range(1, $max_level));
        }

        if ($params->has('parent_id'))
        {
            $query->join(array('closures' => $closure_table), 'closures.descendant_id = tbl.' . $id_column, 'inner')
                ->where('closures.ancestor_id IN :parent_id')
                ->bind(array('parent_id' => (array)$params->get('parent_id')));

            if (!$params->has('include_self')) {
                $query->where('tbl.' . $id_column . ' NOT IN :parent_id');
            }

            if ($params->has('level')) {
                $query->where('closures.level IN :level')->bind(array('level' => (array)$params->get('level')));
            }

        } elseif ($params->has('level')) {
            $query->having('level IN :level')->bind(array('level' => (array)$params->get('level')));
        }

        // If we are fetching the immediate children of a category we can sort however we want
        if (in_array($params->level, [1, [1]]) && $params->sort !== 'custom')
        {
            $sort      = 'tbl.' . $params->sort;
            $direction = $params->direction;
        }

        $query->order($sort, $direction);

        if ($is_count) {
            $data          = $context->getSubject()->getAdapter()->select($context->query, KDatabase::FETCH_FIELD_LIST);
            $context->data = count($data);

            return false;
        }

        return true;
    }

    protected function _afterInsert(KDatabaseContextInterface $context)
    {
        if ($context->affected !== false) {
            $this->_saveRelations($context);
        }
    }

    protected function _afterUpdate(KDatabaseContextInterface $context)
    {
        $this->_saveRelations($context);
    }

    protected function _beforeDelete(KDatabaseContextInterface $context)
    {
        if (!in_array($this->id, self::$_to_be_deleted))
        {
            foreach ($this->getDescendants() as $descendant) {
                self::$_to_be_deleted[] = $descendant->id;
            }
        }
    }

    /**
     * Deletes the row, its children and its node relations
     *
     * @param KDatabaseContextInterface
     */
    protected function _afterDelete(KDatabaseContextInterface $context)
    {
        if ($context->affected)
        {
            if (!in_array($context->data->id, self::$_to_be_deleted))
            {
                $descendants = $this->getDescendants();
                $ids         = array();

                foreach ($descendants as $descendant) {
                    $ids[] = $descendant->id;
                }

                $ids[] = $context->data->id;

                $descendants->delete();

                $query = $this->getObject('lib:database.query.delete')
                    ->table($this->getRelationTable())
                    ->where('descendant_id IN :id')
                    ->bind(array('id' => $ids));
                $this->getTable()->getAdapter()->execute($query);
            }
        }
    }

    /**
     * Saves the row hierarchy to the relations table
     *
     * @param KDatabaseContextInterface $context
     * @return bool
     */
    protected function _saveRelations(KDatabaseContextInterface $context)
    {
        $entity  = $context->data;

        if ($context->query instanceof KDatabaseQueryInsert)
        {
            $query = sprintf('INSERT INTO #__%s (ancestor_id, descendant_id, level)
                SELECT ancestor_id, %d, level+1 FROM #__%1$s
                WHERE descendant_id = %d
                UNION ALL SELECT %2$d, %2$d, 0
                ', $this->getRelationTable(), $entity->id, (int)$entity->parent_id);

            $this->getTable()->getAdapter()->execute($query);
        }
        else
        {
            if ($entity->parent_id && $entity->hasDescendant($entity->parent_id))
            {
                $translator = $this->getObject('translator');
                $this->setStatusMessage($translator->translate('You cannot move a node under one of its descendants'));
                $this->setStatus(KDatabase::STATUS_FAILED);

                return false;
            }

            // Check if parent_id is the same in the relation table and the data table
            $parent  = $entity->getParent();

            if ($entity->isModified('parent_id') && (!$parent || $entity->parent_id != $parent->id)){
                $this->move($entity->id, $entity->parent_id);
            }
        }

        return true;
    }

    public function getRelationTable()
    {
        return $this->_relation_table;
    }

    public function setRelationTable($table)
    {
        $this->_relation_table = $table;

        return $this;
    }
}
