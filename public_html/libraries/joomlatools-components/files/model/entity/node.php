<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Node Entity
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesModelEntityNode extends KModelEntityAbstract
{
	protected $_adapter;

    protected $_container;

    protected static $_container_cache = array();

	public function __construct(KObjectConfig $config)
	{
		parent::__construct($config);

        // Mixin the behavior interface
        $this->mixin('lib:behavior.mixin', $config);

		if ($config->validator !== false)
		{
			if ($config->validator === true) {
				$config->validator = 'com:files.database.validator.'.$this->getIdentifier()->name;
			}

			$this->addCommandHandler($this->getObject($config->validator));
		}
	}

	protected function _initialize(KObjectConfig $config)
	{
		$config->append(array(
			'validator' => true
		));

		parent::_initialize($config);
	}

	public function isNew()
	{
		return empty($this->name) || !$this->_adapter->exists();
	}

	public function copy()
	{
		$context = $this->getContext();
		$context->result = false;

		if ($this->invokeCommand('before.copy', $context) !== false)
		{
			$context->result = $this->_adapter->copy($this->destination_fullpath);
			$this->invokeCommand('after.copy', $context);
        }

		if ($context->result === false) {
			$this->setStatus(KDatabase::STATUS_FAILED);
		}
		else
		{
			if (!is_null($this->destination_folder)) {
				$this->folder = $this->destination_folder;
			}
			if ($this->destination_name) {
				$this->name = $this->destination_name;
			}

			$this->setStatus($this->overwritten ? KDatabase::STATUS_UPDATED : KDatabase::STATUS_CREATED);
		}

		return $context->result;
	}

	public function move()
	{
		$context = $this->getContext();
		$context->result = false;

		if ($this->invokeCommand('before.move', $context) !== false)
		{
			$context->result = $this->_adapter->move($this->destination_fullpath);
			$this->invokeCommand('after.move', $context);
        }

		if ($context->result === false) {
			$this->setStatus(KDatabase::STATUS_FAILED);
		}
		else
		{
			if (!is_null($this->destination_folder)) {
				$this->folder = $this->destination_folder;
			}

			if ($this->destination_name) {
				$this->name = $this->destination_name;
			}

			$this->setStatus($this->overwritten ? KDatabase::STATUS_UPDATED : KDatabase::STATUS_CREATED);
		}

		return $context->result;
	}

	public function delete()
	{
		$context = $this->getContext();
		$context->result = false;

		if ($this->invokeCommand('before.delete', $context) !== false)
		{
			$context->result = $this->_adapter->delete();
			$this->invokeCommand('after.delete', $context);
        }

		if ($context->result === false) {
			$this->setStatus(KDatabase::STATUS_FAILED);
		}
		else $this->setStatus(KDatabase::STATUS_DELETED);

		return $context->result;
	}

    public function getPropertyFullpath()
    {
        return $this->_adapter->getRealPath();
    }

    public function getPropertyPath()
    {
        return trim(($this->folder ? $this->folder . '/' : '') . $this->name, '/\\');
    }

    public function getPropertyDestinationPath()
    {
        $folder = isset($this->destination_folder) ? $this->destination_folder . '/' : (!empty($this->folder) ? $this->folder . '/' : '');
        $name   = isset($this->destination_name) ? $this->destination_name : $this->name;

        return trim($folder . $name, '/\\');
    }

    public function getPropertyDestinationFullpath()
    {
        return $this->getContainer()->fullpath . '/' . $this->destination_path;
    }

    public function getPropertyUri()
    {
        return $this->getContainer()->slug . '://' . $this->path;
    }

    public function getPropertyRelativePath()
    {
        return $this->getContainer()->relative_path . '/' . $this->path;
    }

    public function getPropertyAdapter()
    {
        return $this->_adapter;
    }

	public function setProperty($column, $value, $modified = true)
	{
		parent::setProperty($column, $value, $modified = true);

        if ($column === 'container' || (in_array($column, array('folder', 'name')) && $this->container)) {
			$this->setAdapter();
		}
	}

    public function getContainer()
    {
        if(!isset($this->_container))
        {
            //Set the container
            $container = $this->container;

            if (is_string($container))
            {
                if (!isset(self::$_container_cache[$container])) {
                    self::$_container_cache[$container] = $this->getObject('com:files.model.containers')->slug($container)->fetch();
                }

                $container = self::$_container_cache[$container];
            }

            if (!is_object($container) || !count($container) || $container->isNew()) {
                throw new UnexpectedValueException('Invalid container');
            }

            $this->_container = $container->top();
        }

        return $this->_container;
    }

	public function setAdapter()
	{
		$type = $this->getIdentifier()->name;
		$this->_adapter = $this->getContainer()->getAdapter($type, array(
			'path' => $this->getContainer()->fullpath.'/'.($this->folder ? $this->folder.'/' : '').$this->name
		));

		unset($this->_data['fullpath']);
		unset($this->_data['metadata']);

		return $this;
	}

    public function setProperties($data, $modified = true)
    {
        $result = parent::setProperties($data, $modified);

        if (isset($data['container'])) {
            $this->setAdapter();
        }

        return $result;
    }

    public function toArray()
    {
        $data = parent::toArray();

        foreach ($data as $key => $value)
        {
            if ($value instanceof KModelEntityAbstract || $value instanceof KModelEntityComposite) {
                $data[$key] = $value->toArray();
            }
        }

        unset($data['csrf_token']);
        unset($data['action']);
        unset($data['option']);
        unset($data['format']);
        unset($data['view']);

        $data['container'] = $this->getContainer()->slug;
        $data['type']      = $this->getIdentifier()->name;
        $data['path']      = $this->path;
        $data['uri']       = $this->uri;

        return $data;
    }

    public function count()
    {
        return (int) !$this->isNew();
    }

    /**
     * Get the context
     *
     * @return KCommand
     */
    public function getContext()
    {
        $context = new KDatabaseContext();
        $context->setSubject($this);

        return $context;
    }

    public function isLockable()
    {
        return false;
    }
}
