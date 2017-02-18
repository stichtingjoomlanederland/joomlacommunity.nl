<?php
/**
 * Tagging Component for Joomlatools Framework - http://developer.joomlatools.com/framework
 *
 * @copyright   Copyright (C) 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://github.com/joomlatools/joomlatools-framework-tags for the canonical source repository
 */


/**
 * Taggable Database Behavior
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Koowa\Component\Tags
 */
class ComTagsDatabaseBehaviorTaggable extends KDatabaseBehaviorAbstract
{
    /**
     * Get a list of tags
     *
     * @return KDatabaseRowsetInterface
     */
    public function getTags()
    {
        $package = $this->getMixer()->getIdentifier()->package;
        $model   = $this->getObject('com:tags.model.tags', array('table' => $package.'_tags'));

        if(!$this->isNew()) {
            $tags = $model->row($this->uuid)->fetch();
        } else {
            $tags = $model->fetch();
        }

        return $tags;
    }

    /**
     * Modify the select query
     *
     * If the query's where information includes a tag property, auto-join the tags tables with the query and select
     * all the rows that are tagged with a term.
     */
    protected function _beforeSelect(KDatabaseContext $context)
    {
        $query = $context->query;
        if($context->query->params->has('tag'))
        {
            $package = $this->getMixer()->getIdentifier()->package;

            $query->join($package.'_tags_relations AS tags_relations', 'tags_relations.row = tbl.uuid');
            $query->join($package.'_tags AS tags', 'tags.tag_id = tags_relations.tag_id');
            $query->where('tags.slug IN :tag');
        }
    }
}
