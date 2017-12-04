<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class plgSearchDocman extends JPlugin
{
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    /**
     * Overridden to only run if we have Nooku framework installed
     */
    public function update(&$args)
    {
        $return = null;

        if (class_exists('Koowa')) {
            $return = parent::update($args);
        }

        return $return;
    }

    /**
     * @return array An array of search areas
     */
    public function onContentSearchAreas()
    {
        static $areas = array(
            'docman' => 'PLG_SEARCH_DOCMAN_DOCUMENTS'
        );

        return $areas;
    }

    /**
     * Search method
     *
     * The sql must return the following fields that are used in a common display
     * routine: href, title, section, created, text, browsernav
     *
     * @param string $keyword Target search string
     * @param string $type  matching option, exact|any|all
     * @param string $order ordering option, newest|oldest|popular|alpha|category
     * @param null   $areas An array if the search it to be restricted to areas, null if search all
     *
     * @return array results
     */
    public function onContentSearch($keyword, $type='', $order='', $areas=null)
    {
        if (is_array($areas))
        {
            if (!array_intersect($areas, array_keys($this->onContentSearchAreas()))) {
                return array();
            }
        }

        $keyword = trim($keyword);
        if (empty($keyword)) {
            return array();
        }

        $return = array();
        $pages  = KObjectManager::getInstance()->getObject('com://admin/docman.model.pages')->fetch();
        if (count($pages))
        {
            $limit = $this->params->def('search_limit', 50);

            $order_map = array(
                'default'  => array('tbl.title', 'ASC'),
                'oldest'   => array('tbl.created_on', 'ASC'),
                'newest'   => array('tbl.created_on', 'DESC'),
                'category' => array('category_title', 'ASC'),
                'popular'  => array('tbl.hits', 'DESC')
            );

            if (!array_key_exists($order, $order_map)) {
                $order = 'default';
            }
            list($sort, $direction) = $order_map[$order];

            $user = KObjectManager::getInstance()->getObject('user');

            $model = KObjectManager::getInstance()->getObject('com://admin/docman.model.documents');
            $model->enabled(1)
                ->status('published')
                ->current_user($user->getId())
                ->access($user->getRoles())
                ->page('all')
                ->search($keyword)
                ->search_by($type)
                ->limit($limit)
                ->sort($sort)
                ->direction($direction);

            $list = $model->fetch();

            if (!count($list)) {
                return array();
            }

            $return = array();
            foreach ($list as $item)
            {
                if (!$item->itemid) {
                    continue;
                }

                $entity = new stdClass();
                $entity->created = $item->created_on;

                $entity->href = JRoute::_(sprintf('index.php?option=com_docman&view=document&alias=%s&category_slug=%s&Itemid=%d',
                    $item->alias, $item->category->slug, $item->itemid));
                $entity->browsernav = '';
                $entity->title = $item->title;
                $entity->section = '';
                $entity->text = $item->description;

                if ($item->image) {
                    $entity->image = $item->image_path; // Universal AJAX live search integration
                }

                $return[] = $entity;
            }
        }

        return $return;
    }
}
