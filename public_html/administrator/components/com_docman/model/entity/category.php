<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanModelEntityCategory extends KModelEntityRow
{
    public function save()
    {
        if (!$this->getParameters()->icon) {
            $this->getParameters()->icon = 'folder';
        }

        return parent::save();
    }

    public function toArray()
    {
        $data = parent::toArray();
        $data['hierarchy_title']  = $this->hierarchy_title;
        $data['groups']           = $this->getGroups();
        $data['parent_id']        = $this->getParentId();
        $data['automatic_folder'] = $this->automatic_folder;

        return $data;
    }

    public function getPropertyHierarchyTitle()
    {
        return str_repeat('- ', ($this->level - 1) >= 0 ? ($this->level - 1) : 0) . $this->title;
    }

    public function getPropertyImagePath()
    {
        if ($this->image)
        {
            $image = implode('/', array_map('rawurlencode', explode('/', $this->image)));

            return $this->getObject('request')->getSiteUrl().'/joomlatools-files/docman-images/'.$image;
        }

        return null;

    }

    public function getPropertyIcon()
    {
        $icon = $this->getParameters()->get('icon', 'folder');

        // Backwards compatibility: remove .png from old style icons
        if (substr($icon, 0, 5) !== 'icon:' && substr($icon, -4) === '.png') {
            $icon = substr($icon, 0, strlen($icon)-4);
        }

        return $icon;
    }

    public function getPropertyIconPath()
    {
        $path = $this->icon;

        if (substr($path, 0, 5) === 'icon:')
        {
            $icon = implode('/', array_map('rawurlencode', explode('/', substr($path, 5))));
            $path = $this->getObject('request')->getSiteUrl().'/joomlatools-files/docman-icons/'.$icon;
        } else {
            $path = null;
        }

        return $path;
    }

    public function getPropertyDescriptionSummary()
    {
        $description = $this->description;
        $position    = strpos($description, '<hr id="system-readmore" />');
        if ($position !== false) {
            return substr($description, 0, $position);
        }

        return $description;
    }

    public function getPropertyDescriptionFull()
    {
        return str_replace('<hr id="system-readmore" />', '', $this->description);
    }
}
