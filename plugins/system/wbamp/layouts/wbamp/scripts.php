<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.4.2.551
 * @date        2016-07-19
 */

// no direct access
defined('_JEXEC') or die;

if (!empty($displayData['amp_scripts'])):

	foreach ($displayData['amp_scripts'] as $element => $script) :
		?>
		<script custom-element="<?php echo $this->escape($element, ENT_QUOTES); ?>" src="<?php echo JRoute::_($script); ?>" async></script>
		<?php
	endforeach;
endif;
