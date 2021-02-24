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

/** @var JdidealgatewayViewEmails $this */

?>
<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo Text::_('COM_ROPAYMENTS_MODAL_CLOSE'); ?></button>
<button type="submit" class="btn btn-success"><?php echo Text::_('COM_ROPAYMENTS_SEND_TESTMAIL'); ?></button>
