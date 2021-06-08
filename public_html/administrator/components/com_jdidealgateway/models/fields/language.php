<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * List of available languages.
 *
 * @package  JDiDEAL
 * @since    4.1.0
 */
class JdidealFormFieldLanguage extends JFormFieldList
{
	/**
	 * Type of field
	 *
	 * @var    string
	 * @since  4.1.0
	 */
	protected $type = 'Language';

	/**
	 * Build a list of payment results.
	 *
	 * @return  array  List of payment results.
	 *
	 * @since   4.1.0
	 *
	 * @throws  RuntimeException
	 */
	public function getOptions(): array
	{
		switch ($this->element['provider'])
		{
			case 'ogone':
				$options = array(
					'ar_AR' => Text::_('COM_ROPAYMENTS_LANG_ARABIC'),
					'cs_CZ' => Text::_('COM_ROPAYMENTS_LANG_CZECH'),
					'dk_DK' => Text::_('COM_ROPAYMENTS_LANG_DANISH'),
					'de_DE' => Text::_('COM_ROPAYMENTS_LANG_GERMAN'),
					'el_GR' => Text::_('COM_ROPAYMENTS_LANG_GREEK'),
					'es_ES' => Text::_('COM_ROPAYMENTS_LANG_SPANISH'),
					'fi_FI' => Text::_('COM_ROPAYMENTS_LANG_FINNISH'),
					'fr_FR' => Text::_('COM_ROPAYMENTS_LANG_FRENCH'),
					'he_IL' => Text::_('COM_ROPAYMENTS_LANG_HEBREW'),
					'hu_HU' => Text::_('COM_ROPAYMENTS_LANG_HUNGARIAN'),
					'it_IT' => Text::_('COM_ROPAYMENTS_LANG_ITALIAN'),
					'ja_JP' => Text::_('COM_ROPAYMENTS_LANG_JAPANESE'),
					'ko_KR' => Text::_('COM_ROPAYMENTS_LANG_KOREAN'),
					'nl_BE' => Text::_('COM_ROPAYMENTS_LANG_FLEMISH'),
					'nl_NL' => Text::_('COM_ROPAYMENTS_LANG_DUTCH'),
					'no_NO' => Text::_('COM_ROPAYMENTS_LANG_NORWEGAIN'),
					'pl_PL' => Text::_('COM_ROPAYMENTS_LANG_POLISH'),
					'pt_PT' => Text::_('COM_ROPAYMENTS_LANG_PORTUGESE'),
					'ru_RU' => Text::_('COM_ROPAYMENTS_LANG_RUSSIAN'),
					'se_SE' => Text::_('COM_ROPAYMENTS_LANG_SWEDISH'),
					'sk_SK' => Text::_('COM_ROPAYMENTS_LANG_SLOVAK'),
					'tr_TR' => Text::_('COM_ROPAYMENTS_LANG_TURKISH'),
					'zh_CN' => Text::_('COM_ROPAYMENTS_LANG_CHINESE'),
				);
				break;
			default:
				$options = array();
				break;
		}

		// Natural sorting of the options
		natsort($options);

		return array_merge(parent::getOptions(), $options);
	}
}
