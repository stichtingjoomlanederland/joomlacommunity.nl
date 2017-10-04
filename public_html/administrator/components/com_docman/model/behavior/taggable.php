<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */


class ComDocmanModelBehaviorTaggable extends ComTagsModelBehaviorTaggable
{
    public function onMixin(KObjectMixable $mixer)
    {
        parent::onMixin($mixer);

        //Insert the tag model state
        $mixer->getState()->remove('tag')->insert('tag', 'alias');
    }

    protected function _afterFetch(KModelContextInterface $context)
    {
        $entities = $context->entity;

        if (count($entities))
        {
            $ids = array();

            foreach ($entities as $entity) {
                $ids[] = $entity->uuid;
            }

            $query = $this->getObject('lib:database.query.select')
                ->columns([
                    'row' => 'tags_relations.row',
                    'tags_linked' => "GROUP_CONCAT(CONCAT('{', tbl.slug, '}', tbl.title, '{/}') ORDER BY tbl.title ASC SEPARATOR ', ')",
                    'tags' => "GROUP_CONCAT(tbl.title ORDER BY tbl.title ASC SEPARATOR ', ')"
                ])
                ->table('docman_tags AS tbl')
                ->join('docman_tags_relations AS tags_relations', 'tags_relations.tag_id = tbl.tag_id')
                ->where('tags_relations.row IN :id')
                ->group('tags_relations.row')
                ->bind(array('id' => $ids));

            $map = $this->getTable()->getAdapter()->select($query, KDatabase::FETCH_OBJECT_LIST, 'row');

            foreach ($entities as $entity) {
                if (isset($map[$entity->uuid])) {
                    $entity->tag_list = $map[$entity->uuid]->tags;
                    $entity->tag_list_linked = $map[$entity->uuid]->tags_linked;
                }
            }
        }
    }
}