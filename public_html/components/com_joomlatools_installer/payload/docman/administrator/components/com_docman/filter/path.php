<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

class ComDocmanFilterPath extends KFilterPath implements KFilterTraversable
{
    /**
     * Also validate using JFilter::makeSafe
     *
     * @param mixed $value Value to be validated
     * @return bool True when the variable is valid
     */
    public function validate($value)
    {
        $result = parent::validate($value);

        $result = $result && JFile::makeSafe($value) === $value;

        return $result;
    }

    /**
     * Sanitize a value
     *
     * @param	mixed	$value Value to be sanitized
     * @return string
     */
    public function sanitize($value)
    {
        $value = parent::sanitize($value);

        return JFile::makeSafe($value);
    }
}
