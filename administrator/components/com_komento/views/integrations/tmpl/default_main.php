<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="row">
	<div class="col-md-6">
		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_WORKFLOW_GENERAL' ); ?></div>
			<div class="panel-body">
					<!-- Enable Comments on this Component -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ENABLE_SYSTEM', 'enable_komento' ); ?>

					<!-- Disable Komento on tmpl=component -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_DISABLE_ON_TMPL_COMPONENT', 'disable_komento_on_tmpl_component' ); ?>
			</div>
		</fieldset>

		<?php if( method_exists( $this->componentObj , 'getCategories' ) ) { ?>
		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_CATEGORIES' ); ?></div>
			<div class="panel-body">
				<p><?php echo JText::_( 'COM_KOMENTO_SETTINGS_CATEGORIES_INFO' ); ?></p>
					<!-- Categories Assignment -->
					<?php $options = array();
						$options[] = array( '0', 'COM_KOMENTO_SETTINGS_CATEGORIES_ON_ALL_CATEGORIES' );
						$options[] = array( '1', 'COM_KOMENTO_SETTINGS_CATEGORIES_ON_SELECTED_CATEGORIES' );
						$options[] = array( '2', 'COM_KOMENTO_SETTINGS_CATEGORIES_ON_ALL_CATEGORIES_EXCEPT_SELECTED' );
						$options[] = array( '3', 'COM_KOMENTO_SETTINGS_CATEGORIES_NO_CATEGORIES' );
						echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_CATEGORIES_ASSIGNMENT', 'allowed_categories_mode', 'dropdown', $options );
					?>

					<!-- Enable Comments on this Categories -->
					<?php $categories = $this->componentObj->getCategories();
						echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ENABLE_ON_CATEGORIES', 'allowed_categories', 'multilist', $categories );
					?>
			</div>
		</fieldset>
		<?php } ?>

		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM' ); ?></div>
			<div class="panel-body">
					<!-- Convert Orphanitem -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ENABLE_ORPHANITEM_CONVERT', 'enable_orphanitem_convert' ); ?>

					<!-- Orphanitem Ownership -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ORPHANITEM_OWNERSHIP', 'orphanitem_ownership', 'input' ); ?>
			</div>
		</fieldset>

		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LOGIN_FORM' ); ?></div>
			<div class="panel-body">
					<!-- Use login form -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ENABLE_LOGIN_FORM', 'enable_login_form' ); ?>

					<!-- Login provider -->
					<?php $options = array();
						$options[] = array( 'joomla', 'COM_KOMENTO_SETTINGS_LOGIN_PROVIDER_JOOMLA' );
						$options[] = array( 'easysocial', 'COM_KOMENTO_SETTINGS_LOGIN_PROVIDER_EASYSOCIAL' );
						$options[] = array( 'cb', 'COM_KOMENTO_SETTINGS_LOGIN_PROVIDER_COMMUNITYBUILDER' );
						$options[] = array( 'jomsocial', 'COM_KOMENTO_SETTINGS_LOGIN_PROVIDER_JOMSOCIAL' );

						echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_LOGIN_PROVIDER', 'login_provider', 'dropdown', $options );
					?>
			</div>
		</fieldset>
	</div>

	<div class="col-md-6">
		<!-- START: Pro Version Only -->
		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_('COM_KOMENTO_SETTINGS_RATINGS'); ?></div>
			<div class="panel-body">
					<!-- Enable Ratings -->
					<?php echo $this->renderSetting('COM_KOMENTO_SETTINGS_RATINGS_ENABLE', 'enable_ratings' ); ?>
			</div>
		</fieldset>
		<!-- END: Pro Version Only -->

		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_MODERATION' ); ?></div>
			<div class="panel-body">
				<p><?php echo JText::_( 'COM_KOMENTO_SETTINGS_MODERATION_INFO' ); ?></p>
					<!-- Enable moderation -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ENABLE_MODERATION', 'enable_moderation' ); ?>

					<!-- Requires Moderation -->
					<?php
						$usergroups = $this->getUsergroupsMultilist();
						echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_MODERATION_USERGROUP', 'requires_moderation', 'multilist', $usergroups );
					?>
			</div>
		</fieldset>

		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_SUBSCRIPTION' ); ?></div>
			<div class="panel-body">
					<!-- Enforce subscription -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SUBSCRIPTION_AUTO', 'subscription_auto' ); ?>

					<!-- Requires confirmation -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SUBSCRIPTION_CONFIRMATION', 'subscription_confirmation' ); ?>
			</div>
		</fieldset>

		<!-- START: Pro Version Only -->
		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_RSS' ); ?></div>
			<div class="panel-body">
					<!-- Enable RSS -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_RSS_ENABLE', 'enable_rss' ); ?>

					<!-- Max RSS Items -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_RSS_MAX_ITEMS', 'rss_max_items', 'input' ); ?>
			</div>
		</fieldset>
		<!-- END: Pro Version Only -->

		<?php if( $this->component == 'com_content' ) { ?>
		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_PAGEBREAK' ); ?></div>
			<div class="panel-body">
					<!-- Load Komento on which page break -->
					<?php $options = array();
						$options[] = array( 'all', 'COM_KOMENTO_SETTINGS_PAGEBREAK_LOAD_ALL' );
						$options[] = array( 'first', 'COM_KOMENTO_SETTINGS_PAGEBREAK_LOAD_FIRST' );
						$options[] = array( 'last', 'COM_KOMENTO_SETTINGS_PAGEBREAK_LOAD_LAST' );
						echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_PAGEBREAK_LOAD', 'pagebreak_load', 'dropdown', $options ); ?>
			</div>
		</fieldset>
		<?php } ?>
	</div>
</div>