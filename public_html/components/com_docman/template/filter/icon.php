<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Template filter to replace all icons with their thumbnails if present
 */
class ComDocmanTemplateFilterIcon extends KTemplateFilterAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            // This is high priority to run before alias filter which would replace icon:// prefixes
            'priority'   => self::PRIORITY_HIGH,
        ));

        parent::_initialize($config);
    }

    public function filter(&$text)
    {
        $config = $this->getObject('com://admin/docman.model.configs')->fetch();

        if ($config->thumbnails)
        {
            $matches = array();

            $regex = '#[\(|"]{1}icon://(.*?\.(?:png|gif|jpg|jpeg|bmp))[\)|"]{1}#si';
            if(strpos($text, 'icon://') !== false && preg_match_all($regex, $text, $matches))
            {
                $thumbnails = $this->_getThumbnails($matches[1]);

                foreach ($thumbnails as $path => $thumbnail) {
                    $text = str_replace('icon://'.$path, $thumbnail, $text);
                }
            }
        }

        return $this;
    }

    protected function _getThumbnails($paths)
    {
        $thumbnails = $this->getObject('com:files.model.thumbnails')->setState(array(
            'container' => 'docman-icons',
            'paths' => $paths,
            'folder' => null,
            'limit' => 0,
            'offset' => 0
        ))->fetch();

        $results = array();
        foreach ($thumbnails as $thumbnail)
        {
            $path = $thumbnail->filename;
            if ($thumbnail->folder) {
                $path = $thumbnail->folder.'/'.$path;
            }

            $results[$path] = $thumbnail->thumbnail;
        }

        return $results;
    }
}
