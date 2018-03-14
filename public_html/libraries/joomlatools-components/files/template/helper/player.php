<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Player Template Helper
 *
 * @author  Rastin Mehr <https://github.com/rmdstudio>
 * @package Koowa\Component\Files
 */
class ComFilesTemplateHelperPlayer extends KTemplateHelperAbstract
{
    protected static $_SUPPORTED_FORMATS = array(
        'audio' => array('aac', 'mp3', 'ogg', 'flac','x-flac', 'wave', 'wav', 'x-wav', 'x-pn-wav'),
        'video' => array('mp4', 'webm', 'ogg')
    );

    public function load()
    {
        static $imported = false;

        $html = '';

        if (!$imported)
        {
            $html = $this->getObject('com:files.view.plyr.html')
                ->getTemplate()
                ->addFilter('style')
                ->addFilter('script')
                ->loadFile('com:files.player.default.html')
                ->render();

            $imported = true;
        }

        return $html;
    }
}