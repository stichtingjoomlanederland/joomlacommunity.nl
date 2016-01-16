<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die();


if( DiscussHelper::getJoomlaVersion() >= '3.0' )
{
	class EasyDiscussViewParent extends JViewLegacy
	{
		function __construct($config = array())
		{
			return parent::__construct( $config );
		}
	}
}
else
{
	jimport( 'joomla.application.component.view');
	
	class EasyDiscussViewParent extends JView
	{
		function __construct($config = array())
		{
			return parent::__construct( $config );
		}
	}
}

class EasyDiscussAdminView extends EasyDiscussViewParent
{
	protected $breadcrumbs  = array();
	protected $panelTitle 	= '';

	public function display( $tpl = null )
	{
		$active		= JRequest::getCmd( 'view' , 'discuss' );
		$frontCss	= ( $active == 'discuss' ) ? ' front' : '';
		$menus		= $this->getXMLData( JPATH_COMPONENT . '/views/menu.xml' );
		$browseMode	= JRequest::getCmd('browse');

		$message	= DiscussHelper::getMessageQueue();

		echo '<div id="discuss-wrapper">';

				if( !$browseMode )
				{
					include( dirname( __FILE__ ) . '/themes/default/sidebar.php' );
				}

				echo $browseMode ? '<div>' : '<div class="content' . $frontCss . '">';
					if( !$browseMode )
					{
						include( dirname( __FILE__ ) . '/themes/default/breadcrumbs.php' );
					}

					if( isset( $this->panelTitle ) && !empty( $this->panelTitle ) )
					{
						echo '<div class="content-top">';
							echo '<h2 class="panel-title panel-title-alt">'. $this->panelTitle . '</h2>';
						echo '</div>';
					}

					echo '<div class="wrapper clearfix clear accordion">';

					include( dirname( __FILE__ ) . '/themes/default/notice.php' );

					echo $this->_formStart($active);
						parent::display( $tpl );
					echo $this->_formEnd($active);
					echo '</div>';

				echo '</div>';

		echo '</div>';
	}

	private function _formStart( $view )
	{
		if( $view !== 'settings' )
		{
			return;
		}

		return '<form action="index.php" method="post" name="adminForm" id="adminForm">';
	}

	private function _formEnd( $view )
	{
		if( $view !== 'settings' )
		{
			return;
		}

		ob_start(); ?>
		<input type="hidden" name="child" value="<?php echo JRequest::getCmd('child'); ?>" />
		<input type="hidden" name="layout" value="<?php echo JRequest::getCmd('layout'); ?>" />
		<input type="hidden" name="active" id="active" value="" />
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="option" value="com_easydiscuss" />
		<input type="hidden" name="controller" value="settings" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php

		$content = ob_get_clean();

		return $content;
	}

	/**
	 * Allows caller to specify the panel's title.
	 *
	 * @since	3.0
	 * @param	string	$title 	The panel's title.
	 * @return	null
	 */
	public function setPanelTitle( $title )
	{
		$this->panelTitle	= $title;
	}

	public function addPathway( $title , $link = '' )
	{
		$obj = new stdclass();

		$obj->title	= $title;
		$obj->link	= $link;

		$this->breadcrumbs[] = $obj;
	}

	public function getModel( $name = null )
	{
		static $model = array();

		if( !isset( $model[ $name ] ) )
		{
			$path = DISCUSS_ADMIN_ROOT . '/models/' . JString::strtolower( $name ) . '.php';

			jimport('joomla.filesystem.path');
			if ( !JFile::exists( $path ))
			{
				JError::raiseWarning( 0, 'Model file not found.' );
			}

			$modelClass = 'EasyDiscussModel' . ucfirst( $name );

			if( !class_exists( $modelClass ) )
				require_once( $path );


			$model[$name] = new $modelClass();
		}

		return $model[$name];
	}

	public function renderCheckbox( $configName , $state )
	{
		ob_start();
	?>
		
		<div class="btn-group-yesno"
			data-foundry-toggle="buttons-radio"
			>
			<button type="button" class="btn btn-yes<?php echo $state ? ' active' : '';?>" data-fd-toggle-value="1"><?php echo JText::_( 'COM_EASYDISCUSS_YES_OPTION' );?></button>
			<button type="button" class="btn btn-no<?php echo !$state ? ' active' : '';?>" data-fd-toggle-value="0"><?php echo JText::_( 'COM_EASYDISCUSS_NO_OPTION' );?></button>
			<input type="hidden" id="<?php echo empty( $id ) ? $configName : $id; ?>" name="<?php echo $configName ;?>" value="<?php echo $state ? '1' : '0'; ?>" />
		</div>
	<?php
		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function getFilterState( $filter_state='*' )
	{
		$state[] = JHTML::_('select.option',  '', '- '. JText::_( 'COM_EASYDISCUSS_SELECT_STATE' ) .' -' );
		$state[] = JHTML::_('select.option',  'P', JText::_( 'COM_EASYDISCUSS_PUBLISHED' ) );
		$state[] = JHTML::_('select.option',  'U', JText::_( 'COM_EASYDISCUSS_UNPUBLISHED' ) );
		$state[] = JHTML::_('select.option',  'A', JText::_( 'COM_EASYDISCUSS_PENDING' ) );

		return JHTML::_('select.genericlist',   $state, 'filter_state', ' size="1" onchange="submitform( );"', 'value', 'text', $filter_state );
	}

	public function getXMLData($source)
	{
		$xml	= DiscussHelper::getXML($source, true);
		$data	= $this->parseXML($xml);

		return $data;
	}

	public function parseXML($manifest)
	{
		$items = array();

		if( count($manifest) )
		{
			foreach ($manifest->children() as $item)
			{
				$obj	= new stdClass;

				foreach ($item->attributes() as $itemkey => $itemvalue)
				{
					if( ($key = (string) $itemkey) && ($value = (string) $itemvalue) )
					{
						$obj->$key = $value;
					}
				}

				if( $item->children() )
				{
					$doSelf = __FUNCTION__;
					$obj->child = $this->{$doSelf}($item);
				}

				$items[] = $obj;
			}
		}

		return $items;
	}
}
