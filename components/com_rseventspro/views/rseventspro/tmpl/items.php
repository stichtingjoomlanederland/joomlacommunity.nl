<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

echo 'RS_DELIMITER0'; 
if ($this->tmpl == 'events')
	echo $this->loadTemplate('events');
else if ($this->tmpl == 'locations')
	echo $this->loadTemplate('locations');
else if ($this->tmpl == 'categories')
	echo $this->loadTemplate('categories');
else if ($this->tmpl == 'subscribers')
	echo $this->loadTemplate('subscribers');
else if ($this->tmpl == 'search')
	echo $this->loadTemplate('search');
echo 'RS_DELIMITER1';