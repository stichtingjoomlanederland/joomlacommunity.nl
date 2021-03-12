<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

/**
 * PWT SEO field - Datalayers
 * https://developers.google.com/tag-manager/devguide
 *
 * @since  1.3.0
 */
class PWTSeoFormFieldDatalayers extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.3.0
	 */
	public $type = 'datalayers';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.3.0
	 * @throws  Exception When application if not reachable
	 */
	public function getInput()
	{
		$input   = Factory::getApplication()->input;
		$id      = $input->getInt('id', 0);
		$context = $input->getCmd('option') . '.' . $input->getCmd('view');

		$url = 'index.php?option=com_pwtseo&view=datalayersedit&layout=modal&tmpl=component&context=' . $context . '&context_id=' . $id;

		$script   = array();
		$script[] = 'function performDeleteDatalayers() {';
		$script[] = '   if (confirm(\'' . Text::_('PLG_SYSTEM_PWTSEO_FORM_DATALAYERS_CONFIRM_DELETE_LABEL') . '\')) {';
		$script[] = '       jQuery.ajax({';
		$script[] = '           url: \'' . Uri::base(true) . '/index.php' . '\',';
		$script[] = '           beforeSend: function() {';
		$script[] = '               var $loader = jQuery(\'.js-loader\');';
		$script[] = '               $loader.attr(\'disabled\', true);';
		$script[] = '               $loader.children(\'.js-icon\').removeClass(\'icon-delete\').addClass(\'icon-cogs\');';
		$script[] = '           },';
		$script[] = '           complete: function() {';
		$script[] = '               var $loader = jQuery(\'.js-loader\');';
		$script[] = '               setTimeout(function() {';
		$script[] = '                   $loader.attr(\'disabled\', false);';
		$script[] = '                   $loader.children(\'.js-icon\').removeClass(\'icon-publish\').addClass(\'icon-delete\');';
		$script[] = '               }, 2000)';
		$script[] = '           },';
		$script[] = '           success: function() {';
		$script[] = '               var $loader = jQuery(\'.js-loader\');';
		$script[] = '               $loader.children(\'.js-icon\').removeClass(\'icon-cogs\').addClass(\'icon-publish\');';
		$script[] = '           },';
		$script[] = '           data: {';
		$script[] = '               format: \'json\',';
		$script[] = '               task: \'datalayersedit.delete\',';
		$script[] = '               option: \'com_pwtseo\',';
		$script[] = '               context_id: \'' . $id . '\',';
		$script[] = '               context: \'' . $context . '\',';
		$script[] = '           }';
		$script[] = '       })';
		$script[] = '   }';
		$script[] = '}';

		Factory::getDocument()->addScriptDeclaration(implode("", $script));

		return '<button data-toggle="modal" onclick="jQuery( \'#datalayersModal\' ).modal(\'show\'); return false;" class="btn">
	<span class="icon-list" aria-hidden="true"></span>' . Text::_('PLG_SYSTEM_PWTSEO_FORM_DATALAYERS_CUSTOMIZE_LABEL') . '</button>' . HTMLHelper::_(
			'bootstrap.renderModal',
			'datalayersModal',
			array(
					'title'      => Text::_('PLG_SYSTEM_PWTSEO_FORM_DATALAYER_LABEL'),
					'url'        => $url,
					'height'     => '400px',
					'width'      => '800px',
					'bodyHeight' => '70',
					'modalWidth' => '80',
					'footer'     => '<button type="button" class="btn" data-dismiss="modal" aria-hidden="true"'
						. ' onclick="jQuery(\'#datalayersModal iframe\').contents().find(\'#closeBtn\').click();">'
						. JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>'
						. '<button type="button" class="btn btn-primary" data-dismiss="modal" aria-hidden="true" onclick="jQuery(\'#datalayersModal iframe\').contents().find(\'#saveBtn\').click();">'
						. JText::_("JSAVE") . '</button>'
						. '<button type="button" class="btn btn-success" aria-hidden="true" onclick="jQuery(\'#datalayersModal iframe\').contents().find(\'#applyBtn\').click(); return false;">'
						. JText::_("JAPPLY") . '</button>'
				)
		) . '<button class="btn btn-small button-delete js-loader" onclick="performDeleteDatalayers(); return false;" class=""><span class="js-icon icon-delete"></span></button>' .
			'<div class="js-message"></div>';
	}
}
