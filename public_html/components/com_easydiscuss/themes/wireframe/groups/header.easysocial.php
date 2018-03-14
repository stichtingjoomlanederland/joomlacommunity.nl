<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<?php echo ES::template()->html('html.miniheader', $group); ?>

<?php if ($group->isMember() && $view == 'groups') { ?>
<div class="ed-entry-action-bar t-lg-mb--lg">
    <div class="o-col o-col--4">
        <div class="ed-entry-action-bar__btn-group">
            <a href="<?php echo EDR::_('view=ask&group_id=' . $group->id .'&redirect=' . $returnUrl);?>" class="btn btn-primary">
                <i class="fa fa-pencil"></i>&nbsp; <?php echo JText::_('COM_EASYDISCUSS_NEW_POST');?>
            </a>
        </div>
    </div>
</div>
<?php } ?>