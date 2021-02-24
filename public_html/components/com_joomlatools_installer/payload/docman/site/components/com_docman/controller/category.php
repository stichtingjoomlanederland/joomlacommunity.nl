<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerCategory extends ComDocmanControllerList
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('after.read'   , '_populateCategory');
    }

    /**
     * Load admin language file in forms since some of the layouts are shared
     *
     * @param KControllerContextInterface $context
     */
    protected function _beforeRender(KControllerContextInterface $context)
    {
        $request = $context->getRequest();

        if ($request->getFormat() === 'html' && $request->query->view === 'category'
            && $this->getView()->getLayout() === 'form')
        {
            $this->getObject('translator')->load('com://admin/docman');
        }
    }

    /**
     * If the category slug is supplied in the URL, prepopulate it in the new document form
     *
     * @param KControllerContextInterface $context
     */
    protected function _populateCategory(KControllerContextInterface $context)
    {
        if ($context->result->isNew())
        {
            $query = $this->getRequest()->query;
            $view = $this->getView();

            if ($this->getRequest()->getFormat() === 'html' && $view->getName() == 'category')
            {
                $slug = $query->category_slug;

                if (empty($slug) && $query->path)
                {
                    $slug = explode('/', $query->path);
                    $slug = array_pop($slug);
                }

                if (empty($slug))
                {
                    $menu = JFactory::getApplication()->getMenu()->getActive();
                    if (($menu->query['view'] === 'list' || $menu->query['view'] === 'tree') && isset($menu->query['slug'])) {
                        $slug = $menu->query['slug'];
                    }
                }

                if (!empty($slug))
                {
                    $parent = $this->getObject('com://site/docman.model.categories')->slug($slug)->fetch();
                    $this->getView()->set('parent_id', $parent->id);
                }
            }
        }
    }
}
