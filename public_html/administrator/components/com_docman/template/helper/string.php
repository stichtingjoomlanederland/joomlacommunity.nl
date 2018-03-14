<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanTemplateHelperString extends KTemplateHelperAbstract
{
    public function humanize($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'string' => '',
            'strip_extension' => false
        ));

        $string = $config->string;

        if ($config->strip_extension) {
                $string = ltrim(pathinfo(' '.strtr($string, array('/' => '/ ')), PATHINFO_FILENAME));
        }

        $string = str_replace(array('_', '-', '.'), ' ', $string);
        $string = ucfirst($string);

        return $string;
    }

    public function truncate($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'text' => '',
            'offset' => 0,
            'length' => 100,
            'pad' => '...')
        );

        // Don't show endstring if actual string length is less than cutting length
        $config->pad = (mb_strlen($config->text) < $config->length) ? '' : $config->pad;

        return mb_substr(strip_tags($config->text), $config->offset, $config->length) . $config->pad;
    }

    /**
     * Converts a byte size to human readable format e.g. 1 Megabyte for 1048576
     *
     * @param array $config
     */
    public function humanize_filesize($config = array())
    {
        $config = new KObjectConfigJson($config);
        return $this->getObject('com:files.template.helper.filesize')->humanize(array(
            'size' => $config->size
        ));
    }
}
