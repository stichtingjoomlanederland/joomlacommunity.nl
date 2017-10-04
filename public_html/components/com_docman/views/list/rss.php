<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewListRss extends ComDocmanViewRss
{
    protected function _fetchData(KViewContext $context)
    {
        $state  = $this->getModel()->getState();
        $params = $this->getParameters();
        $user   = $this->getObject('user');

        //Category
        if ($this->getModel()->getState()->isUnique()) {
            $category = $this->getModel()->fetch();
        }
        else
        {
            $category = $this->getModel()->create();
            $category->title = $params->page_heading ? $params->page_heading : $this->getActiveMenu()->title;
        }

        if ($state->isUnique() && $category->isNew()) {
            throw new KControllerExceptionResourceNotFound('Category not found');
        }

        $this->_setPageTitle();

        $model = $this->getObject('com://site/docman.controller.document')
            ->enabled($state->enabled)
            ->status($state->status)
            ->access($state->access)
            ->current_user($user->getId())
            ->page($state->page)
            ->category($category->id ? $category->id : '')
            ->category_children(true)
            ->limit($state->limit)
            ->sort($state->sort)
            ->direction($state->direction)
            ->offset($state->offset)
            ->getModel();

        $internals = array('limit', 'offset', 'direction', 'sort');

        foreach ($internals as $internal) {
            $model->getState()->setProperty($internal, 'internal', true);
        }

        $this->setModel($model);

        if ($category->image) {
            $context->data->append(array(
                'image' => $category->image_path
            ));
        }

        $context->data->append(array(
            'channel_link' => $this->getRoute('format=html&layout=default&slug='.$category->slug),
            'feed_link'    => $this->getRoute('format=rss&layout=default&slug='.$category->slug),
            'description'  => $category->description_summary
        ));

        parent::_fetchData($context);
    }

    /**
     * If the current page is not the menu category, use the current category title
     */
    protected function _setPageTitle()
    {
        if (in_array($this->getName(), array('list', 'tree')) && $this->getName() === $this->getActiveMenu()->query['view'])
        {
            $category = $this->getModel()->fetch();
            $slug     = isset($this->getActiveMenu()->query['slug']) ? $this->getActiveMenu()->query['slug'] : null;

            if ($category->slug !== $slug) {
                $this->getParameters()->set('page_heading', $category->title);
            }
        }
    }
}
