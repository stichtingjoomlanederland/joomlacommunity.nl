<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;

?>
<div id="dopay">
	<fieldset>
		<legend><?php echo Text::_('COM_ROPAYMENTS_DO_PAYMENT'); ?></legend>

		<?php
		$layout = new FileLayout('forms.form');
		echo $layout->render(['data' => $this->data]);
		?>
	</fieldset>
</div>
