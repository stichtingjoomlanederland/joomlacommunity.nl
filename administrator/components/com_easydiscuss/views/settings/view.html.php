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

require_once DISCUSS_ADMIN_ROOT . '/views.php';

class EasyDiscussViewSettings extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('discuss.manage.settings' , 'com_easydiscuss') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}
		// Initialise variables
		$config			= DiscussHelper::getConfig();
		$jconfig		= DiscussHelper::getJConfig();
		$defaultSAId	= DiscussHelper::getDefaultSAIds();
		$joomlaVersion	= DiscussHelper::getJoomlaVersion();
		$joomlaGroups	= DiscussHelper::getJoomlaUserGroups();

		$this->assignRef( 'config'			, $config );
		$this->assignRef( 'jconfig'			, $jconfig );
		$this->assignRef( 'defaultSAId'		, $defaultSAId );
		$this->assignRef( 'defaultLength'	, $defaultLength );
		$this->assignRef( 'joomlaversion'	, $joomlaVersion );
		$this->assignRef( 'joomlaGroups'	, $joomlaGroups );

		if( $this->getLayout() == 'default' )
		{
			$app	= JFactory::getApplication();
			$app->redirect( 'index.php?option=com_easydiscuss&view=settings&layout=default_main_workflow&child=general' );
		}

		parent::display($tpl);
	}

	public function getEmailsTemplate()
	{
		$html	= '';
		$path	= DISCUSS_SITE_THEMES . '/simplistic/emails';
		$emails	= JFolder::files( $path , 'email.*'  );

		ob_start();
		foreach($emails as $email)
		{
		?>
			<li class="unstyled file-list">
				<!-- <li style="float:left; margin-right:5px;"> -->

				<!-- [ -->
				<?php
				if(is_writable( DISCUSS_SITE_THEMES . '/simplistic/emails/' . $email))
				{
				?>
					<a class="modal" rel="{handler: 'iframe', size: {x: 700, y: 500}}" href="index.php?option=com_easydiscuss&view=settings&layout=editEmailTemplate&file=<?php echo $email; ?>&tmpl=component&browse=1">
						<?php echo JText::_($email); ?>
						<?php //echo JText::_('COM_EASYDISCUSS_EDIT');?>
						<i class="icon-edit"></i>
					</a>
				<?php
				}
				else
				{
				?>
					<?php echo JText::_($email); ?> <span style="color:red; font-weight:bold;"><?php echo JText::_('COM_EASYDISCUSS_UNWRITABLE');?></span>
				<?php
				}
				?>
				<!-- ] -->

				<!-- </li> -->
			</li>
		<?php
		}
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function getCategories()
	{
		$db			= DiscussHelper::getDBO();
		$query		= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_category' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
		$db->setQuery( $query );
		$categories	= $db->loadObjectList();

		return $categories;
	}

	public function editEmailTemplate()
	{
		$file		= JRequest::getVar('file', '', 'GET');
		$filepath	= DISCUSS_SITE_THEMES . '/simplistic/emails/' . $file;
		$content	= '';
		$html		= '';
		$msg		= JRequest::getVar('msg', '', 'GET');
		$msgType	= JRequest::getVar('msgtype', '', 'GET');

		ob_start();

		if(!empty($msg))
		{
		?>
			<div id="discuss-message" class="<?php echo $msgType; ?>"><?php echo $msg; ?></div>
		<?php
		}

		if(is_writable($filepath))
		{
			$content = JFile::read($filepath);
		?>
			<form name="emailTemplate" id="emailTemplate" method="POST">
				<div>
				<?php if(DiscussHelper::getJoomlaVersion() <= '1.5') : ?>
				<input type="button" value="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_CLOSE' );?>" onclick="window.parent.document.getElementById('sbox-window').close();">
				<?php endif; ?>
				<input type="submit" name="save" value="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_SAVE' );?>">
				</div>
				<textarea rows="28" cols="93" name="content"><?php echo $content; ?></textarea>
				<input type="hidden" name="option" value="com_easydiscuss">
				<input type="hidden" name="controller" value="settings">
				<input type="hidden" name="task" value="saveEmailTemplate">
				<input type="hidden" name="file" value="<?php echo $file; ?>">
				<input type="hidden" name="tmpl" value="component">
				<input type="hidden" name="browse" value="1">


			</form>
		<?php
		}
		else
		{
		?>
			<div><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_UNWRITABLE'); ?></div>
		<?php
		}

		$html = ob_get_contents();
		ob_end_clean();

		echo $html;
	}

	public function getThemes( $selectedTheme = 'default' )
	{
		$html	= '<select name="layout_site_theme" class="full-width">';

		$themes	= $this->get( 'Themes' );

		for( $i = 0; $i < count( $themes ); $i++ )
		{
			$theme		= JString::strtolower( $themes[ $i ] );

			if ( $theme != 'dashboard' ) {
				$selected	= ( $selectedTheme == $theme ) ? ' selected="selected"' : '';
				$html		.= '<option' . $selected . '>' . $theme . '</option>';
			}
		}

		$html	.= '</select>';

		return $html;
	}

	public function getEditorList( $selected, $name = 'layout_editor' )
	{
		$db		= DiscussHelper::getDBO();

		// compile list of the editors
		if(DiscussHelper::getJoomlaVersion() >= '1.6')
		{
			$query = 'SELECT `element` AS value, `name` AS text'
					.' FROM `#__extensions`'
					.' WHERE `folder` = "editors"'
					.' AND `type` = "plugin"'
					.' AND `enabled` = 1'
					.' ORDER BY ordering, name'
					;
		}
		else
		{
			$query = 'SELECT element AS value, name AS text'
					.' FROM #__plugins'
					.' WHERE folder = "editors"'
					.' AND published = 1'
					.' ORDER BY ordering, name'
					;
		}

		$db->setQuery($query);
		$editors = $db->loadObjectList();

		if(count($editors) > 0)
		{
			if(DiscussHelper::getJoomlaVersion() >= '1.6')
			{
				$lang = JFactory::getLanguage();
				for($i = 0; $i < count($editors); $i++)
				{
					$editor =& $editors[$i];
					$lang->load($editor->text . '.sys', JPATH_ADMINISTRATOR, null, false, false);
					$editor->text   = JText::_($editor->text);
				}
			}
		}

		$bbcode = new stdClass();
		$bbcode->value  = 'bbcode';
		$bbcode->text   = JText::_( 'Built-in BBCode' );

		array_unshift( $editors, $bbcode);

		return JHTML::_('select.genericlist',  $editors , $name, 'class="full-width" size="1"', 'value', 'text', $selected );
	}

	public function getCategorySelection( $selected, $name )
	{
		$categorySelection = array();
		$selectType = array( 'select', 'multitier' );

		foreach( $selectType as $stype)
		{
			$selection = new stdClass();
			$selection->value  = $stype;
			$selection->text   = JText::_( 'COM_EASYDISCUSS_DISCUSSION_CATEGORY_SELECTION_TYPE_' . strtoupper( $stype ) );
			$categorySelection[] = $selection;
		}

		return JHTML::_('select.genericlist',  $categorySelection , $name, 'class="full-width" size="1"', 'value', 'text', $selected );
	}

	public function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_SETTINGS' ), 'settings' );

		JToolBarHelper::custom( 'home', 'arrow-left', '', JText::_( 'COM_EASYDISCUSS_TOOLBAR_HOME' ), false);
		JToolBarHelper::divider();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
	}
}
