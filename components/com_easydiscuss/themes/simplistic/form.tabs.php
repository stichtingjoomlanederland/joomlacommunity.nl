<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

$canAddTag = $acl->allowed('add_tag');
?>
<!-- Custom tabs -->
<div class="tab-box">
	<div class="tabbable">
		<ul class="nav nav-tabs formTabs">
			<?php if( $system->config->get( 'main_master_tags' ) ){ ?>
				<?php if( $system->config->get( 'main_tags' ) && $isDiscussion && $canAddTag ){ ?>
				<li>
					<a data-foundry-toggle="tab" href="#tagsTab-<?php echo $composer->id; ?>">
						<i class="icon-tags"></i>
						<?php echo JText::_('COM_EASYDISCUSS_POST_CREATE_TAGS'); ?>
					</a>
				</li>
				<?php } ?>
			<?php } ?>

			<?php echo $this->getFieldTabs( $isDiscussion , $post );?>

			<?php if( $system->config->get( 'main_customfields_input' ) ){ ?>
			<li>
				<a data-foundry-toggle="tab" href="#customfieldsTab-<?php echo $composer->id; ?>">
					<i class="icon-list-alt"></i>
					<?php echo JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_TITLE' ); ?>
				</a>
			</li>
			<?php } ?>
		</ul>

		<!-- Tab contents -->
		<div class="tab-content formTabsContent">
			<?php if( $system->config->get( 'main_master_tags' ) ){ ?>
				<?php if( $system->config->get( 'main_tags' ) && $isDiscussion ){ ?>
				<div id="tagsTab-<?php echo $composer->id; ?>" class="tab-pane">
					<?php echo $this->loadTemplate( 'field.tags.php' ); ?>
				</div>
				<?php } ?>
			<?php } ?>

			<?php echo $this->getFieldForms( $isDiscussion , $post ); ?>

			<?php if( $system->config->get( 'main_customfields_input') ){ ?>
			<div id="customfieldsTab-<?php echo $composer->id; ?>" class="tab-pane">
				<?php echo $this->loadTemplate( 'field.customfields.php' , array( 'isDiscussion' => $isDiscussion, 'post' => $post ) ); ?>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
