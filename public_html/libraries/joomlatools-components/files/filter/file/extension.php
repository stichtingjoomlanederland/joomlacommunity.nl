<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * File Extension Filter
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesFilterFileExtension extends KFilterAbstract
{
    public function validate($entity)
	{
        $allowed = KObjectConfig::unbox($entity->getContainer()->getParameters()->allowed_extensions);
        $value   = $entity->extension;

		if (is_array($allowed) && (empty($value) || !in_array(strtolower($value), $allowed))) {
            return $this->_error($this->getObject('translator')->translate('Invalid file extension'));
		}
	}
}
