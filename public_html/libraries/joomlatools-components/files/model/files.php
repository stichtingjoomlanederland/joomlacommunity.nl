<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Files Model
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesModelFiles extends ComFilesModelNodes
{
    protected function _actionFetch(KModelContext $context)
    {
        $state = $this->getState();
        $files = $this->getContainer()->getAdapter('iterator')->getFiles(array(
            'path'    => $this->getPath(),
            'exclude' => array('.svn', '.htaccess', 'web.config', '.git', 'CVS', 'index.html', '.DS_Store', 'Thumbs.db', 'Desktop.ini'),
            'filter'  => array($this, 'iteratorFilter'),
            'map'     => array($this, 'iteratorMap'),
            'sort'    => $state->sort
        ));

        if ($files === false) {
            throw new UnexpectedValueException('Invalid folder');
        }

        $this->_count = count($files);

        if (strtolower($state->direction) == 'desc') {
            $files = array_reverse($files);
        }

        $files = array_slice($files, $state->offset, $state->limit ? $state->limit : $this->_count);

        $data = array();
        foreach ($files as $file)
        {
            $data[] = array(
                'container' => $state->container,
                'folder'    => $state->folder,
                'name'      => $file
            );
        }

        $identifier         = $this->getIdentifier()->toArray();
        $identifier['path'] = array('model', 'entity');

        return $this->getObject($identifier, array('data' => $data));
    }

    protected function _actionCount(KModelContext $context)
    {
        if (!isset($this->_count)) {
            $this->fetch();
        }

        return $this->_count;
    }

	public function iteratorMap($path)
	{
		return ltrim(basename(' '.strtr($path, array('/' => '/ '))));
	}

	public function iteratorFilter($path)
	{
        $state     = $this->getState();
		$filename  = ltrim(basename(' '.strtr($path, array('/' => '/ '))));
		$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if ($filename && $filename[0] === '.') {
            return false;
        }

        if ($state->name)
        {
			if (!in_array($filename, (array) $state->name)) {
				return false;
			}
		}

		if ($state->types)
        {
			if ((in_array($extension, ComFilesModelEntityFile::$image_extensions) && !in_array('image', (array) $state->types))
			|| (!in_array($extension, ComFilesModelEntityFile::$image_extensions) && !in_array('file', (array) $state->types))
			) {
				return false;
			}
		}

		if ($state->search && stripos($filename, $state->search) === false) {
            return false;
        }
	}
}
