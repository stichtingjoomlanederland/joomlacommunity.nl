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
<form id="discussCommentForm">
	<div class="ed-comment-form t-lg-mt--md">
	    
	    <textarea name="comment" id="comment" cols="30" rows="5" class="form-control" data-ed-comment-message></textarea>
	    
	    <div class="o-row t-lg-mt--md">
	    	<?php if ($this->config->get('main_tnc_comment')) { ?>
	        <div class="pull-left">
	            <div class="o- t-lg-mb--lg">
	                
	                <div class="o-checkbox o-checkbox--inline t-mr--md">
	                    <input type="checkbox" name="tnc-<?php echo $post->id;?>" id="tnc-<?php echo $post->id;?>" data-ed-comment-tnc-checkbox <?php echo ED::tnc()->hasAcceptedTnc('comment') ? 'checked="checked"' : '' ?>/>
	                    <label for="tnc-<?php echo $post->id;?>">
                        	<?php echo JText::_('COM_EASYDISCUSS_I_HAVE_READ_AND_AGREED');?> 
                        	<a href="javascript:void(0);" style="text-decoration: underline;" data-ed-comment-tnc-link>
                        		<?php echo JText::_('COM_EASYDISCUSS_TERMS_AND_CONDITIONS');?>
                        	</a>  
	                    </label>
	                </div>
	                
	            </div>    
	        </div>
	        <?php } ?>
	        
	        <div class="pull-right">
	            <a href="javascript:void(0);" class="btn btn-primary btn-sm" data-ed-comment-submit><?php echo JText::_('COM_EASYDISCUSS_BUTTON_SUBMIT'); ?></a>
	        </div>
	    </div>
	</div>
</form>