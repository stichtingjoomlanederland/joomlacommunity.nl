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
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="row">
	<div class="col-md-6">

        <div class="panel">
            <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_LAYOUT_FORUMS'); ?>

            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORIES_LIMIT'); ?>
                        </div>
                        <div class="col-md-7">
                            <input type="text" name="layout_categories_limit" value="<?php echo $this->config->get('layout_categories_limit');?>" size="5" style="text-align:center;" class="form-control form-control-sm text-center" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_POST_CATEGORY_LIMIT'); ?>
                        </div>
                        <div class="col-md-7">
                            <input type="text" name="layout_post_category_limit" value="<?php echo $this->config->get('layout_post_category_limit');?>" size="5" style="text-align:center;" class="form-control form-control-sm text-center" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>

	<div class="col-md-6">

	</div>
</div>
