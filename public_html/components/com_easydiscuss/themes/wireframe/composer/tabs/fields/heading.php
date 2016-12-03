<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

if (!$this->config->get('main_customfields_input')) {
    return;
}

$model = ED::model('CustomFields');
$fields = $model->getFields(DISCUSS_CUSTOMFIELDS_ACL_INPUT, $operation, $post->id);

// if empty fields then we do not show this tab.
if (! $fields) {
    return;
}

?>
<li data-ed-tab-field-heading>
    <a href="#fields-<?php echo $editorId;?>" data-ed-toggle="tab">
        <i class="fa fa-table"></i>&nbsp; <?php echo JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_TITLE'); ?>
    </a>
</li>
