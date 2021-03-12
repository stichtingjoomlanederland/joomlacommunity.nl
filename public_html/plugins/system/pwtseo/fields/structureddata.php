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
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * PWT SEO field - Structured Data
 * https://developers.google.com/search/docs/guides/intro-structured-data
 *
 * @since  1.3.0
 */
class PWTSeoFormFieldStructuredData extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.3.0
	 */
	public $type = 'structureddata';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.3.0
	 * @throws  Exception
	 */
	public function getInput()
	{
		$input   = Factory::getApplication()->input;
		$id      = $input->getInt('id', 0);
		$context = $input->getCmd('option') . '.' . $input->getCmd('view');

		$url = 'index.php?option=com_pwtseo&view=structureddata&layout=modal&tmpl=component&context=' . $context . '&context_id=' . $id;

		return '<button data-toggle="modal" onclick="jQuery( \'#structureddataModal\' ).modal(\'show\'); return false;" class="btn">
	<span class="icon-list" aria-hidden="true"></span>' . Text::_('PLG_SYSTEM_PWTSEO_FORM_DATALAYERS_CUSTOMIZE_LABEL') . '</button>' . HTMLHelper::_(
			'bootstrap.renderModal',
			'structureddataModal',
			array(
					'title'      => Text::_('PLG_SYSTEM_PWTSEO_FORM_STRUCTUREDDATA_LABEL'),
					'url'        => $url,
					'height'     => '400px',
					'width'      => '800px',
					'bodyHeight' => '70',
					'modalWidth' => '80',
					'footer'     => '<button type="button" class="btn" data-dismiss="modal" aria-hidden="true"'
						. ' onclick="jQuery(\'#structureddataModal iframe\').contents().find(\'#closeBtn\').click();">'
						. JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>'
						. '<button type="button" class="btn btn-primary" data-dismiss="modal" aria-hidden="true" onclick="jQuery(\'#structureddataModal iframe\').contents().find(\'#saveBtn\').click();">'
						. JText::_("JSAVE") . '</button>'
						. '<button type="button" class="btn btn-success" aria-hidden="true" onclick="jQuery(\'#structureddataModal iframe\').contents().find(\'#applyBtn\').click(); return false;">'
						. JText::_("JAPPLY") . '</button>'
				)
		);
	}
}
