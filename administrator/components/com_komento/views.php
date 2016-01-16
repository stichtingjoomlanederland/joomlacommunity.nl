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
defined('_JEXEC') or die();

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'parent.php' );

class KomentoAdminView extends KomentoParentView
{

	public function display( $tpl = null )
	{
		$advance = JRequest::getVar('advance', '');
		$layout = JRequest::getVar('layout', '');
		$view = JRequest::getVar('view', '');
		
		$this->assignRef('view', $view);
		$this->assignRef('layout', $layout);
		$this->assignRef( 'advance', $advance );

		echo '<div id="si" class="komento master row-table cell-top">';
		echo '<div class="container-nav">
				<a class="nav-sidebar-toggle" data-toggle="collapse" data-target=".sidebar"><i class="fa fa-bars"></i></a>
				<a class="nav-subhead-toggle" data-toggle="collapse" data-target=".subhead-collapse"><i class="fa fa-cog"></i></a>
			</div>';
	
		include(dirname(__FILE__) . '/views/sidebar.php');

			echo '<div class="content col-cell">';
			parent::display($tpl);
			echo '</div>';

		echo '</div>';
	}


	public function getModel( $name = null )
	{
		static $model = array();

		$name = preg_replace('/[^A-Z0-9_]/i', '', trim($name));

		if( !isset( $model[ $name ] ) )
		{
			if( !$name )
			{
				$name = JRequest::getString( 'view', null );
			}


			$model[ $name ] = Komento::getModel( $name, true );
		}

		return $model[ $name ];
	}

	/**
	 * Determines if needed to load the bootstrap or joomla version
	 * of the theme file.
	 *
	 * @since	3.7
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getTheme()
	{
		$version 	= Komento::joomlaVersion();

		if( $version >= '3.0' )
		{
			JHtmlSidebar::addEntry(
				JText::_('COM_TEMPLATES_SUBMENU_STYLES'),
				'index.php?option=com_templates&view=styles',
				true
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_TEMPLATES_SUBMENU_TEMPLATES'),
				'index.php?option=com_templates&view=templates',
				false
			);
			if( method_exists( $this , 'addSidebar' ) )
			{
				$this->addSidebar();
			}

			return 'bootstrap';
		}

		return 'joomla';
	}

	public function renderCheckbox( $configName , $state = null )
	{
		if( is_null( $state ) )
		{
			$config = Komento::getConfig();
			$state = $config->get( $configName, 0 );
		}

		ob_start();
	?>
		<div class="toggle-options">
			<label class="option-enable<?php echo $state == 1 ? ' selected' : '';?>"><span><?php echo JText::_( 'COM_KOMENTO_YES_OPTION' );?></span></label>
			<label class="option-disable<?php echo $state == 0 ? ' selected' : '';?>"><span><?php echo JText::_( 'COM_KOMENTO_NO_OPTION' ); ?></span></label>
			<input name="<?php echo $configName; ?>" value="<?php echo $state;?>" type="radio" id="<?php echo $configName; ?>" class="radiobox" checked="checked" />
		</div>
	<?php
		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function renderOption( $value, $text )
	{
		return JHtml::_( 'select.option', $value, JText::_( $text ) );
	}

	public function renderDropdown( $configName, $state = null, $options )
	{
		if( is_null( $state ) )
		{
			$config = Komento::getConfig();
			$state = $config->get( $configName, '' );
		}

		$this->makeListOptions( $options );

		return JHtml::_('select.genericlist', $options, $configName, '.fa- class="form-control"', 'value', 'text', $state, $configName );
	}

	public function renderInput( $configName, $state = null, $options = null )
	{
		if( is_null( $state ) )
		{
			$config = Komento::getConfig();
			$state = $config->get( $configName, '' );
		}

		$size = 5;
		$pretext = '';
		$posttext = '';
		$align = '';
		$class = '';
		if( is_array( $options ) )
		{
			if( isset( $options['size'] ) )
			{
				$size = $options['size'];
			}

			if( isset( $options['pretext'] ) )
			{
				$pretext = $options['pretext'];
			}

			if( isset( $options['posttext'] ) )
			{
				$posttext = $options['posttext'];
			}

			if( isset( $options['align'] ) )
			{
				$align = $options['align'];
			}

			if( isset( $options['class'] ) )
			{
				$class = $options['class'];
			}
		}
		else
		{
			if( $options != '' )
			{
				$size = $options;
			}
		}

		ob_start();
		?>
		<span class="small"><?php echo $pretext; ?></span><input type="text" class="inputbox <?php echo $class; ?>" id="<?php echo $configName; ?>" name="<?php echo $configName; ?>" value="<?php echo $this->escape( $state ); ?>" size="<?php echo $size; ?>"<?php echo $align ? ' style="text-align:'.$align.';"' : ''; ?>/><span class="small"><?php echo $posttext; ?></span>
		<?php
		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function renderMultilist( $configName, $selected = '', $options )
	{
		if( empty( $selected ) )
		{
			$selected = array();
		}

		if( !is_array( $selected ) )
		{
			$selected	= explode( ',' , $selected );
		}

		$key = $configName . '[]';

		$this->makeListOptions( $options );

		return JHTML::_( 'select.genericlist', $options, $key, 'multiple="multiple" size="10" style="height: auto !important;"', 'value', 'text', $selected );
	}

	public function renderText( $value, $type = 'notice' )
	{
		// Prepare text class
		$class = '';

		switch( $type )
		{
			case 'error':
			case 'warning':
				$class = 'warning';
			break;
		}

		ob_start();
	?>
		<tr>
			<td width="300" class="key">
			</td>
			<td valign="top">
				<div class="has-tip">
					<p class="<?php echo $class; ?>"><?php echo $value ;?></p>
				</div>
			</td>
		</tr>

	<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function renderFilters( $options = array() , $value , $element )
	{
		ob_start();

		foreach( $options as $key => $val )
		{
		?>
		<a class="kmt-filter<?php echo $value == $key ? ' kmt-filter-active' : '';?>" href="javascript:void(0);" onclick="Foundry('#<?php echo $element;?>').val('<?php echo $key;?>');submitform();"><?php echo JText::_( $val ); ?></a>
		<?php
		}
		?>
		<input type="hidden" name="filter_type" id="filter_type" value="<?php echo $this->escape($value); ?>" />
		<?php
		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function makeListOptions( &$options = array() )
	{
		// accepts array of object with either format:
		// $object->id & $object->title | $object->treename
		// $object->value & $object->text

		if( !is_array( $options ) )
		{
			$options = array();
		}

		foreach( $options as &$option )
		{
			// convert array to object
			if( is_array( $option ) )
			{
				$tmp = new stdClass();
				$tmp->id = $option[0];
				$tmp->title = $option[1];
				$option = $tmp;
			}

			if( isset( $option->value ) && !isset( $option->id ) )
			{
				$option->id = $option->value;
			}

			if( isset( $option->text ) && !isset( $option->title ) )
			{
				$option->title = $option->text;
			}

			// if it is a tree item, then treename always take effect
			if( isset( $option->treename ) )
			{
				$option->title = $option->treename;
			}

			$option = $this->renderOption( $option->id, $option->title );
		}
	}

	public function renderTextarea( $configName, $state = '', $options = array() )
	{
		if( $state == '' )
		{
			$config = Komento::getConfig();
			$state = $config->get( $configName, '' );
		}

		$cols = isset( $options['cols'] ) ? $options['cols'] : 25;
		$rows = isset( $options['rows'] ) ? $options['rows'] : 5;

		ob_start();
		?>
		<textarea name="<?php echo $configName; ?>" class="inputbox full-width" cols="<?php echo $cols; ?>" rows="<?php echo $rows; ?>"><?php echo str_replace( '<br />', "\n", $state ); ?></textarea>
		<?php
		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function renderColumnsConfiguration( $columns, $columnsConfig )
	{
		$html = '<button type="button" class="btn btn-primary" onclick="Komento.saveColumns()">' . JText::_( 'COM_KOMENTO_COMMENTS_SAVE_COLUMNS' ) . '</button>';

		$html .= '<table class="table table-options">';

		foreach( $columns as $column )
		{
			$html .= '<tr><td>' . JText::_( 'COM_KOMENTO_COLUMN_' . strtoupper( $column ) ) . '</td><td>' . $this->renderCheckbox( 'column_' . $column, $columnsConfig->get( 'column_' . $column ) ) . '</td></tr>';
		}

		$html .= '</table>';

		return $html;
	}
}
