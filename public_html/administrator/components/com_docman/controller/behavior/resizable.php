<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Used by the file controller to make icons smaller if they are bigger than a certain size
 */
class ComDocmanControllerBehaviorResizable extends KControllerBehaviorAbstract
{
    protected $_thumbnail_size;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->setThumbnailSize(KObjectConfig::unbox($config->thumbnail_size));
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'thumbnail_size' => array('x' => 64, 'y' => 64)
        ));

        parent::_initialize($config);
    }

    protected function _beforeAdd(KControllerContextInterface $context)
    {
        $state = $this->getModel()->getState();

        if ($state->container)
        {
            $container = $this->getModel()->getContainer();
            $size      = $container->getParameters()->thumbnail_size;

            if (isset($size['x']) && isset($size['y'])) {
                $this->setThumbnailSize($size);
            }
        }

        @ini_set('memory_limit', '256M');

        if ($source = $context->request->data->file)
        {
            try
            {
                $imagine = new \Imagine\Gd\Imagine();
                $image   = $imagine->open($source);

                $size = $this->getThumbnailSize();

                $image->resize(new \Imagine\Image\Box($size['x'], $size['y']));

                $string = sprintf('data:%s;base64,%s', 'image/png', base64_encode((string) $image));

                $context->request->data->thumbnail_string = $string;
            }
            catch (Exception $e) {
                return;
            }
        }
    }

    public function getThumbnailSize()
    {
        return $this->_thumbnail_size;
    }

    /**
     * @param array $size An array with x and y properties
     * @return $this
     */
    public function setThumbnailSize(array $size)
    {
        $this->_thumbnail_size = $size;

        return $this;
    }
}
