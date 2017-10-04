<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Thumbnails Model State
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesModelStateThumbnails extends KModelState
{
    protected $_source_file;

    public function set($name, $value = null)
    {
        if ($name == 'source')
        {
            if ($this->get($name) != $value) {
                $this->_source_file = null; // Reset source file if source gets changed
            }

            $parts = explode('://', $value);

            $this->set('name', basename($parts[1]) . '.jpg');
            $this->set('folder', dirname($parts[1]));
        }

        return parent::set($name, $value);
    }

    public function remove($name)
    {
        if ($name == 'source') {
            $this->_source_file = null;
        }

        return parent::remove($name);
    }

    public function reset($default = true)
    {
        $this->_source_file = null;

        return parent::reset($default);
    }

    public function getSourceFile()
    {
        if ($this->has('source') && !$this->_source_file)
        {
            $parts = explode('://', $this->get('source'));

            $file = $this->getObject('com:files.model.files')
                         ->container($parts[0])
                         ->folder(dirname($parts[1]))
                         ->name(basename($parts[1]))
                         ->fetch();

            if (!$file->isNew()) {
                $this->_source_file = $file;
            }
        }

        return $this->_source_file;
    }
}