<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access'); ?>
<script type="text/javascript">
Komento.ready(function($) {
	window.resetDefaultClassNames = function()
	{
		$('#layout_css_admin').val("<?php echo $this->config->default->layout_css_admin; ?>");
		$('#layout_css_author').val("<?php echo $this->config->default->layout_css_author; ?>");
		$('#layout_css_registered').val("<?php echo $this->config->default->layout_css_registered; ?>");
		$('#layout_css_public').val("<?php echo $this->config->default->layout_css_public; ?>");
	}
});
</script>

<table class="noshow">
	<tr>
		<td width="50%" valign="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_GENERAL' ); ?></legend>
			<!-- [UPGRADENOW] -->
			<table class="admintable" cellspacing="1">
				<tbody>
					<!-- Themes Selection -->
					<?php
						$result		= JFolder::folders( KOMENTO_THEMES , '.', false , true , $exclude = array('.svn', 'CVS' , '.' , '.DS_Store', '__MACOSX' ) );
						$options	= array();

						foreach( $result as $item )
						{
							$options[] = array( basename($item), ucfirst(basename($item)) );
						}

						echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_THEME', 'layout_theme', 'dropdown', $options );
					?>

					<!-- Responsive -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_RESPONSIVE_ENABLE', 'enable_responsive' ); ?>

					<!-- Enable Tabbed Comments -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_TABBED_COMMENTS', 'tabbed_comments' ); ?>

					<!-- Max Threaded Level -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_COMMENTS_MAX_THREADED_LEVEL', 'max_threaded_level', 'input' ); ?>

					<!-- Enable threaded view -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_THREADED_VIEW_ENABLE', 'enable_threaded' ); ?>

					<!-- Change Thread Indentation Pixels -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_THREAD_INDENTATION', 'thread_indentation', 'input', array( 'posttext' => 'px' ) ); ?>

					<!-- Show sort buttons -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SHOW_SORT_BUTTONS', 'show_sort_buttons' ); ?>

					<!-- Default sorting -->
					<?php $options = array();
						$options[] = array( 'oldest', 'COM_KOMENTO_SETTINGS_SORT_OLDEST' );
						$options[] = array( 'latest', 'COM_KOMENTO_SETTINGS_SORT_LATEST' );
						echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_DEFAULT_SORT', 'default_sort', 'dropdown', $options );
					?>

					<!-- Switch between Load More or Load Previous -->
					<?php $options = array();
						$options[] = array( '0', 'COM_KOMENTO_SETTINGS_LOAD_BAR_BOTTOM' );
						$options[] = array( '1', 'COM_KOMENTO_SETTINGS_LOAD_BAR_TOP' );
						echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_LOAD_BAR_POSITION', 'load_previous', 'dropdown', $options );
					?>

					<!-- Max Comment Per Page -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_COMMENTS_MAX_PER_PAGE', 'max_comments_per_page', 'input' ); ?>

				</tbody>
			</table>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_TEMPLATE' ); ?></legend>
				<p class="small"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_TEMPLATE_DESC' ); ?></p>
				<table class="admintable" cellspacing="1">
					<tbody>

						<!-- Overriding order -->
						<?php echo $this->renderText( JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_TEMPLATE_OVERRIDING_ORDER' ) ); ?>

						<!-- Use template override -->
						<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_LAYOUT_TEMPLATE_OVERRIDE', 'layout_template_override' ); ?>

						<!-- Use component override -->
						<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_LAYOUT_TEMPLATE_COMPONENT_OVERRIDE', 'layout_component_override' ); ?>

						<!-- Inherit parent (Kuro) css -->
						<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_LAYOUT_TEMPLATE_INHERIT_KURO_CSS', 'layout_inherit_kuro_css' ); ?>

					</tbody>
				</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_CSS_CONTROL' ); ?></legend>
			<p class="small"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_CSS_CONTROL_DESC' ); ?></p>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Admin CSS Class -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_CSS_CLASS_ADMIN', 'layout_css_admin', 'input', array( 'size' => 60, 'class' => 'full-width' ) ); ?>

					<!-- Registered CSS Class -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_CSS_CLASS_REGISTERED', 'layout_css_registered', 'input', array( 'size' => 60, 'class' => 'full-width' ) ); ?>

					<!-- Author CSS Class -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_CSS_CLASS_CONTENT_AUTHOR', 'layout_css_author', 'input', array( 'size' => 60, 'class' => 'full-width' ) ); ?>

					<!-- Publis CSS Class -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_CSS_CLASS_PUBLIC', 'layout_css_public', 'input', array( 'size' => 60, 'class' => 'full-width' ) ); ?>

					<!-- Reset to default classname -->
					<tr>
						<td width="150" class="key">
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_KOMENTO_SETTINGS_CSS_CLASS_RESET_DESC' ); ?></div>
								<a href="javascript:void(0);" onclick="resetDefaultClassNames()"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_CSS_CLASS_RESET' ); ?></a>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			</fieldset>
		</td>

		<td width="50%" valign="top">
			
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_APPEARANCE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
					<!-- Username/Real Name/Stored Name -->
					<?php $options = array();
						$options[] = array( 'default', 'COM_KOMENTO_SETTINGS_NAME_TYPE_DEFAULT' );
						$options[] = array( 'username', 'COM_KOMENTO_SETTINGS_NAME_TYPE_USERNAME' );
						$options[] = array( 'name', 'COM_KOMENTO_SETTINGS_NAME_TYPE_NAME' );
						echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_COMMENTS_NAME_TYPE', 'name_type', 'dropdown', $options );
					?>

					<!-- Guest label -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_LAYOUT_GUEST_LABEL', 'guest_label' ); ?>

					<!-- auto hyperlinking -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_LAYOUT_AUTO_HYPERLINK', 'auto_hyperlink' ); ?>

					<!-- nofollow for links -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_LAYOUT_LINKS_NOFOLLOW', 'links_nofollow' ); ?>

					<!-- Use Date/Time as Permalink -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_LAYOUT_DATETIME_PERMALINK', 'datetime_permalink' ); ?>

					<!-- Date format -->
					<?php
						$dateFormatLink = 'http://php.net/manual/en/function.date.php';

						if( Komento::joomlaVersion() == '1.5' )
						{
							$dateFormatLink = 'http://php.net/manual/en/function.strftime.php';
						}
						echo $this->renderText( '<a href="' . $dateFormatLink . '" target="_blank">' . JText::_( 'COM_KOMENTO_SETTINGS_DATE_FORMAT_REFERENCE' ) . '</a>' ); ?>

					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_LAYOUT_DATE_FORMAT', 'date_format', 'input', 10 ); ?>

					<!-- Image Width -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_LAYOUT_IMAGE_WIDTH', 'max_image_width', 'input', array( 'posttext' => ' px' ) ); ?>

					<!-- Image Height -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_LAYOUT_IMAGE_HEIGHT', 'max_image_height', 'input', array( 'posttext' => ' px' ) ); ?>

					<!-- Allow Video -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_LAYOUT_ALLOW_VIDEO', 'allow_video' ); ?>

					<!-- Video Width -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_LAYOUT_VIDEO_WIDTH', 'bbcode_video_width', 'input', array( 'posttext' => ' px' ) ); ?>

					<!-- Video Height -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_LAYOUT_VIDEO_HEIGHT', 'bbcode_video_height', 'input', array( 'posttext' => ' px' ) ); ?>
				</tbody>
			</table>
			</fieldset>
		</td>
	</tr>
</table>
