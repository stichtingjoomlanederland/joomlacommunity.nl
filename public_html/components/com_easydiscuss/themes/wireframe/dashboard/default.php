<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<?php if (ED::isSiteAdmin() || $this->acl->allowed('manage_pending')) { ?>
<?php echo $this->output('site/dashboard/manage/posts'); ?>
<?php } ?>

<?php if ($this->config->get('main_work_schedule') && $this->acl->allowed('manage_holiday')) { ?>
<?php echo $this->output('site/dashboard/manage/holiday'); ?>
<?php } ?>