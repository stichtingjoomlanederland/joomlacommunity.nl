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
?>
<div class="input-group">
    <input type="text" name="<?php echo $name; ?>" value="<?php echo $this->escape($search); ?>" class="form-control" placeholder="<?php echo JText::_('COM_EASYDISCUSS_SEARCH', true);?>" data-ed-table-search />
    <span class="input-group-btn">
        <button class="btn btn-default" type="button" data-ed-table-search-submit>
            <i class="fa fa-search"></i>
        </button>
        <button class="btn btn-default" type="button" data-ed-table-search-reset>
            <i class="fa fa-times"></i>
        </button>
    </span>
</div>