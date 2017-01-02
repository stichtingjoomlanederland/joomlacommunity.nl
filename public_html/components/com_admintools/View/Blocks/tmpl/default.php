<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

/** @var $this Akeeba\AdminTools\Site\View\Blocks\Html */

defined('_JEXEC') or die();

header('HTTP/1.1 403 Forbidden', true, 403);
?>
<html>
<head>
	<title>
		<?php echo JFactory::getConfig()->get('sitename', 'Non-descript Site') ?>
		:: <?php echo JText::_('JGLOBAL_AUTH_ACCESS_DENIED'); ?>
	</title>
	<style>
		body {
			font-family: Calibri, Arial, Helvetica, sans-serif;
			background-color: #c00;
		}

		#blocked {
			background-color: white;
			border: thick solid #600;
			border-radius: 10px;
			width: 350px;
			height: 150px;
			position: absolute;
			top: 50%;
			left: 50%;
			margin-left: -175px;
			margin-top: -75px;
			padding: 5px;
			box-shadow: 0px 0px 50px #ff0;
		}

		#blocked h1 {
			margin: 0 0 0.5em;
			padding: 0;
			font-size: 24pt;
			font-weight: bold;
			font-style: normal;
			text-align: center;
			color: red;
			text-shadow: 1px 1px 2px black;
		}

		#blocked p {
			color: #300;
			font-size: 11pt;
			text-align: center;
		}
	</style>
</head>
<body>
<div id="blocked">
	<h1><?php echo JText::_('JGLOBAL_AUTH_ACCESS_DENIED'); ?></h1>

	<p><?php echo $this->message; ?></p>
</div>
</body>
</html>
<?php /*

Feel free to customize this file using a standard template override. For more
information on template overrides, please consult Joomla!'s documentation wiki:
http://docs.joomla.org/How_to_override_the_output_from_the_Joomla!_core

 */
?>
