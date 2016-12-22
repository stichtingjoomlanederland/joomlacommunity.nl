<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Thumbnail Entity
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesModelEntityThumbnail extends KModelEntityRow
{
    /**
     * @var array Associative array containing the thumbnail size (x, y);
     */
    protected $_size;

	public function __construct(KObjectConfig $config)
	{
		parent::__construct($config);

        $this->setSize(KObjectConfig::unbox($config->size));
	}

    protected function _initialize(KObjectConfig $config)
    {
        $size = KObjectConfig::unbox($config->size);

        if (empty($size)) {
            $config->size = array('x' => 200, 'y' => 150);
        }

        parent::_initialize($config);
    }

    public function generateThumbnail($in_place = false)
    {
		@ini_set('memory_limit', '256M');

    	if (($source = $this->getSource()) && $this->_canGenerate())
		{
            try
            {
                $imagine = new \Imagine\Gd\Imagine();
                $image   = $imagine->open($source->fullpath);

                $size = $this->getSize();

                if ($size['x'] && $size['y']) {
                    $size = new \Imagine\Image\Box($size['x'], $size['y']);
                }
                else
                {
                    $image_size = $image->getSize();
                    $larger     = max($image_size->getWidth(), $image_size->getHeight());
                    $scale      = max($size['x'], $size['y']);

                    $size       = $image_size->scale(1/($larger/$scale));
                }

                if ($in_place) {
                    return $image->resize($size)->save();
                } else {
                    $string = (string) $image->thumbnail($size, \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND);
                    $string = sprintf('data:%s;base64,%s', $source->mimetype, base64_encode($string));

                    return $string;
                }
			}
			catch (Exception $e) {
				return false;
			}
		}

		return false;
    }

    public function save()
    {
        $result = false;

        if ($source = $this->getSource())
        {
            $str = $source->thumbnail_string ? $source->thumbnail_string : $this->generateThumbnail();

            if ($str)
            {
                $this->setProperties(array(
                    'files_container_id' => $source->getContainer()->id,
                    'folder'             => $source->folder,
                    'filename'           => $source->name,
                    'thumbnail'          => $str
                ));

                $result = parent::save();
            }
        }

        return $result;
    }

    public function toArray()
    {
        $data = parent::toArray();

		unset($data['_thumbnail_size']);
		unset($data['source']);

        return $data;
    }

    public function getSource()
    {
        return ($this->source && !$this->source->isNew()) ? $this->source : null;
    }

    /**
     * Thumbnail size setter.
     *
     * @param  array $size
     * @throws BadMethodCallException
     * @return $this
     */
    public function setSize(array $size)
    {
        if (!(isset($size['x']) || isset($size['y']))) {
            throw new BadMethodCallException('The provided size is invalid');
        }

        $this->_size = $size;

        return $this;
    }

    /**
     * Thumbnail size getter.
     *
     * @throws RuntimeException If no source or its size cannot be determined.
     *
     * @return array Associative array containing the thumbnail width and height.
     */
    public function getSize()
    {
        $size = $this->_size;

        if ($size && (!isset($size['x']) || !isset($size['y'])))
        {
            $source = $this->getSource();

            if (!($source && ($image = @getimagesize($source->fullpath)))) {
                throw new RuntimeException('Unable to get source size');
            }

            $ratio = $image[0] / $image [1];

            if (isset($size['x'])) {
                $size['y'] = round($size['x'] / $ratio);
            } else {
                $size['x'] = round($size['y'] * $ratio);
            }
        }

        return $size;
    }

    /**
     * Checks if a thumbnail for the current source and provided size can be generated given the
     * amount of memory that's available.
     *
     * @return bool True if the thumbnail can be "safely" processed, false otherwise.
     */
    protected function _canGenerate()
    {
        $result = false;

        // Multiplier to take into account memory consumed by the Image Processing Library.
        $tweak_factor  = 6;

        $source = @getimagesize($this->getSource()->fullpath);

        $channels      = isset($source['channels']) ? $source['channels'] : 4;
        $bits          = isset($source['bits']) ? $source['bits'] : 8;
        $source_memory = ceil($source[0] * $source[1] * $bits * $channels / 8 * $tweak_factor);

        $thumb = $this->getSize();

        // We assume the same amount of bits and channels as source.
        $thumb_memory = ceil($thumb['x'] * $thumb['y'] * $bits * $channels / 8 * $tweak_factor);

        //If memory is limited
        $limit = ini_get('memory_limit');
        if ($limit != '-1')
        {
            $limit = self::convertToBytes($limit);
            $available_memory = $limit - memory_get_usage();

            // Leave 16 megs for the rest of the request
            $available_memory -= 16777216;

            if ($source_memory + $thumb_memory < $available_memory) {
                $result = true;
            }
        }
        else $result = true;

        return $result;
    }

    public static function convertToBytes($value)
    {
        $keys = array('k', 'm', 'g');
        $last_char = strtolower(substr($value, -1));
        $value = (int) $value;

        if (in_array($last_char, $keys)) {
            $value *= pow(1024, array_search($last_char, $keys)+1);
        }

        return $value;
    }
}
