<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2017 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Gallery Template Helper
 *
 * @author  Rastin Mehr <https://github.com/rmdstudio>
 * @package Koowa\Component\Files
 */
class ComDocmanTemplateHelperGallery extends KTemplateHelperAbstract
{
    public function load($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'params' => null
        ));

        static $imported = false;

        if (! $imported) {

            $this->getObject('com:docman.view.documents.html')
                ->getTemplate()
                ->addFilter('style')
                ->addFilter('script')
                ->loadFile('com:docman.documents.gallery_scripts.html')
                ->render(array(
                    'params' => $config->params
                ));

            $imported = true;
        }
    }
}