<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

HTMLHelper::_('stylesheet', 'com_pwtseo/pwtseo.css', array('version' => 'auto', 'relative' => true));

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

?>
<form action="<?php echo Route::_('index.php?option=com_pwtseo&layout=modal'); ?>" method="post" name="adminForm"
      id="adminForm">
    <button id="applyBtn" type="button" class="hidden" onclick="Joomla.submitbutton('structureddata.apply');"></button>
    <button id="saveBtn" type="button" class="hidden" onclick="Joomla.submitbutton('structureddata.save');"></button>
    <button id="closeBtn" type="button" class="hidden" onclick="Joomla.submitbutton('structureddata.cancel');"></button>


    <div id="j-main-container" class="span10">
	    <?php echo $this->form->renderFieldset('structureddata'); ?>
    </div>

    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="jform[pwtseo][context]" value="<?php echo $this->context ?>"/>
    <input type="hidden" name="jform[pwtseo][context_id]" value="<?php echo $this->context_id ?>"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
