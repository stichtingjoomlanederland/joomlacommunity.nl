<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

?>
<?php echo ED::renderModule('easydiscuss-before-searchbar'); ?>

<?php if ($this->config->get('layout_toolbar_searchbar')) { ?>
<form name="discuss-search" data-search-toolbar-form method="GET" action="<?php echo EDR::_('index.php?option=com_easydiscuss&view=search'); ?>">
	<div class="ed-searchbar t-lg-mt--lg t-lg-mb--lg">
	    <div class="o-col o-col--top col--btn-ask">
			<?php if ($this->acl->allowed('add_question') && $this->config->get('layout_toolbarcreate')) { ?>
				<a class="btn btn-<?php echo $this->config->get('layout_ask_color'); ?> btn-primary btn-lg btn-searchbar-ask" href="<?php echo DiscussRouter::getAskRoute( $categoryId );?>"><?php echo JText::_( 'COM_EASYDISCUSS_ASK_QUESTION' );?></a>
			<?php } else if( $this->my->id == 0 ) { ?>
				<a class="btn btn-<?php echo $this->config->get('layout_ask_color'); ?> btn-primary btn-lg btn-searchbar-ask" href="<?php echo DiscussHelper::getRegistrationLink();?>"><?php echo JText::_( 'COM_EASYDISCUSS_ASK_QUESTION' );?></a>
			<?php } ?>
	    </div>
		<?php if ($this->config->get('layout_toolbar_cat_filter')) { ?>
			<?php if (!empty($nestedCategories)) { ?>
				<div class="o-col o-col--top col--filter">
				    <div class="ed-searchbar__select-wrap">
						<?php echo $nestedCategories; ?>
				    </div>
				</div>
			<?php } ?>
		<?php } ?>
	    <div class="o-col o-col--top col--input">
	        <div class="ed-searchbar__input-wrap">
				<input type="text" class="form-control ed-searchbar__input"
					   style="position: inherit;"
					   placeholder="<?php echo JText::_('COM_EASYDISCUSS_SEARCH_PLACEHOLDER');?>"
					   name="query"
					   value="<?php echo $this->escape($query);?>"
					   data-search-input
					   />
				<input type="hidden" name="option" value="com_easydiscuss" />
				<input type="hidden" name="view" value="search" />
				<input type="hidden" name="Itemid" value="<?php echo DiscussRouter::getItemId('search'); ?>" />
	        </div>
	    </div>
	    <div class="o-col o-col--top col--btn-search">
	        <a href="javascript:void(0);" class="btn btn-default btn-lg btn-searchbar-search" data-search-button><?php echo JText::_('COM_EASYDISCUSS_SEARCH'); ?></a>
	    </div>
	</div>
</form>
<?php } ?>
<?php echo DiscussHelper::renderModule( 'easydiscuss-after-searchbar' ); ?>
