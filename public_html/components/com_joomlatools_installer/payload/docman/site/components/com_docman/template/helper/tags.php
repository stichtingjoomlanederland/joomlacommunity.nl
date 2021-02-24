<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanTemplateHelperTags extends KTemplateHelperAbstract
{
    public function title($config = [])
    {
        $config = new KObjectConfigJson($config);
        $config->append([
            'tag' => null
        ]);

        $html = '';

        if ($config->tag)
        {
            $model  = $this->getObject('com:tags.model.tags', array('table' => 'docman_tags'));
            $tags   = $model->slug(KObjectConfig::unbox($config->tag))->fetch();
            $titles = [];

            foreach ($tags as $tag) {
                $titles[] = $tag->title;
            }

            $html = implode(', ', $titles);
        }

        return $html;
    }

    public function link($config = [])
    {
        $config = new KObjectConfigJson($config);
        $config->append([
            'entity' => null,
            'params' => null,
            'menu'   => null,
        ]);

        $entity = $config->entity;
        $menu   = $config->menu;

        $tags = $entity->tag_list;

        if ($menu->query['view'] !== 'document')
        {
            $group  = $menu->query['view'] === 'flat' ? 'tag[]' : 'filter[tag][]';
            $link   = sprintf($menu->link.'&Itemid=%d&%s=', $menu->id, $group);

            $tags = preg_replace_callback('#\{([^\}]+)\}#i', function($matches) use ($link) {
                if ($matches[0] === '{/}') {
                    return '</a>';
                } else {
                    return '<a href="'.JRoute::_($link.$matches[1]).'">';
                }
            }, $entity->tag_list_linked);
        }

        return $tags;
    }
}