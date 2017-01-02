<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

/** @var $this  Akeeba\AdminTools\Admin\View\NginXConfMaker\Html */

defined('_JEXEC') or die;

$document = JFactory::getDocument();
?>
<pre><?php
	echo $this->escape($this->nginxconf);?></pre>