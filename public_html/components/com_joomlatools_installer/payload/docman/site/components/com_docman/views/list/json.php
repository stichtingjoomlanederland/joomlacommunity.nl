<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewListJson extends ComDocmanViewJson
{
    public function isCollection()
    {
        return true;
    }

    /**
     * Returns the JSON output
     *
     * Overridden to return both child categories and documents as well as category information
     * for the category in menu item
     *
     * @return array
     */
    protected function _renderData()
    {
        $state  = $this->getModel()->getState();
        $params = $this->getParameters();
        $user   = $this->getObject('user');

        if ($this->getModel()->getState()->isUnique()) {
            $category = $this->getModel()->fetch();
        }
        else
        {
            $category = $this->getModel()->create();
            $category->title = $params->page_heading ? $params->page_heading : $this->getActiveMenu()->title;
        }

        $data = parent::_renderData();
        $data['entities'] = $this->_getCollection($category);

        if ($params->show_subcategories)
        {
            $subcategories = $this->getObject('com://site/docman.model.categories')
                ->level(1)
                ->parent_id($category->id)
                ->enabled($state->enabled)
                ->access($state->access)
                ->current_user($user->getId())
                ->page($state->page)
                ->sort($params->sort_categories)
                ->limit(0)
                ->fetch();

            $data['linked']['categories'] = $this->_getCollection($subcategories);
        }
        else $data['linked']['categories'] = array();

        if ($category->id)
        {
            $model = $this->getObject('com://site/docman.controller.document')
                ->enabled($state->enabled)
                ->status($state->status)
                ->access($state->access)
                ->current_user($user->getId())
                ->page($state->page)
                ->category($category->id)
                ->limit($state->limit)
                ->offset($state->offset)
                ->sort($state->sort)
                ->direction($state->direction)
                ->getModel();

            $total     = $model->count();
            $documents = $model->fetch();

            $limit  = (int) $model->getState()->limit;
            $offset = (int) $model->getState()->offset;

            if ($limit && $total-($limit + $offset) > 0)
            {
                $data['links']['next'] = array(
                    'href' => $this->_getPageUrl(array('offset' => $limit+$offset)),
                    'type' => $this->mimetype
                );
            }

            if ($limit && $offset && $offset >= $limit)
            {
                $data['links']['previous'] = array(
                    'href' => $this->_getPageUrl(array('offset' => max($offset-$limit, 0))),
                    'type' => $this->mimetype
                );
            }

            $data['meta']['total'] = $total;
            $data['linked']['documents'] = $this->_getCollection($documents);
        }
        else
        {
            $data['meta']['total'] = 0;
            $data['linked']['documents'] = array();
        }

        return $data;
    }
}
