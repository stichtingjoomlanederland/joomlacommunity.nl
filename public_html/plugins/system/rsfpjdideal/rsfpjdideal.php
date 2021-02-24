<?php
/**
 * @package     JDiDEAL
 * @subpackage  RSForm!Pro
 *
 * @author      Roland Dalmulder <contact@rolandd.com>
 * @copyright   Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

defined('_JEXEC') or die;

use Jdideal\Gateway;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\UserHelper;
use Joomla\Registry\Registry;

JLoader::register('RSFormProHelper', JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/rsform.php');
JLoader::register(
	'RsfpjdidealHelper',
	__DIR__ . '/RsfpjdidealHelper.php'
);

/**
 * RSForm! Pro RO Payments plugin.
 *
 * @package     JDiDEAL
 * @subpackage  RSForm!Pro
 * @since       1.0.0
 */
class PlgSystemRsfpJdideal extends CMSPlugin
{
	/**
	 * Database driver
	 *
	 * @var    JDatabaseDriver
	 * @since  1.0.0
	 */
	protected $db;

	/**
	 * An application instance
	 *
	 * @var    SiteApplication
	 * @since  1.0.0
	 */
	protected $app;

	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  3.1.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * The component ID for the payment package field
	 *
	 * @var    integer
	 * @since  1.0.0
	 */
	private $componentId = 5579;

	/**
	 * List of all the products on the form
	 *
	 * @var    array
	 * @since  6.0.0
	 */
	private $products = [];

	/**
	 * List of RO Payments components
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	private $newComponents;

	/**
	 * A random number for each form to handle the JS calls.
	 *
	 * @var    string
	 * @since  4.2.0
	 */
	private $randomId = '';

	/**
	 * A list of products that are used for calculations in the form
	 *
	 * @var    array
	 * @since  4.4.0
	 */
	private $calculationProducts = [];

	/**
	 * Set if the script has been loaded in an article
	 *
	 * @var    boolean
	 * @since  4.6.1
	 */
	private $setScript = false;

	/**
	 * Constructor.
	 *
	 * 5575: Single Product
	 * 5576: Multiple Products
	 * 5577: Total
	 * 5578: Input field
	 * 5579: iDEAL option for payment package
	 *
	 * @param   object  $subject  The object to observe
	 * @param   array   $config   An array that holds the plugin configuration
	 *
	 * @since   1.0.0
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		if (!$this->canRun())
		{
			return;
		}

		$this->newComponents = [5575, 5576, 5577, 5578];

		JLoader::registerNamespace('Jdideal', JPATH_LIBRARIES);

		if (class_exists(Gateway::class) === false)
		{
			JLoader::registerNamespace('Jdideal', JPATH_LIBRARIES . '/Jdideal');
		}

		$lang = Factory::getLanguage();
		$lang->load('plg_system_rsfpjdideal');
	}

	/**
	 * Initialise the plugin.
	 *
	 * @return  void
	 *
	 * @since   2.12.0
	 *
	 * @throws  RuntimeException
	 */
	public function rsfp_bk_onInit(): void
	{
		if (!$this->canRun())
		{
			return;
		}

		// Cron that sets non paid subscribers to denied after 12 h
		$query = $this->db->getQuery(true)
			->update($this->db->quoteName('#__rsform_submission_values', 'sv'))
			->leftJoin(
				$this->db->quoteName('#__rsform_submissions', 's') . ' ON '
				. $this->db->quoteName('s.SubmissionId') . ' = ' . $this->db->quoteName('sv.SubmissionId')
			)
			->set($this->db->quoteName('sv.FieldValue') . ' = -1')
			->where($this->db->quoteName('sv.FieldName') . ' = ' . $this->db->quote('_STATUS'))
			->where($this->db->quoteName('sv.FieldValue') . ' = 0')
			->where(
				$this->db->quoteName('s.DateSubmitted') . ' < ' . $this->db->quote(
					date('Y-m-d H:i:s', strtotime('-12 hours'))
				)
			);
		$this->db->setQuery($query)->execute();
	}

	/**
	 * Check if RSFormPro is loaded.
	 *
	 * @return  boolean  The field option objects.
	 *
	 * @since   2.2.0
	 */
	public function canRun(): bool
	{
		if (!file_exists(JPATH_LIBRARIES . '/Jdideal'))
		{
			return false;
		}

		if (class_exists('RSFormProHelper'))
		{
			return true;
		}

		$helper = JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/rsform.php';

		if (file_exists($helper))
		{
			require_once $helper;
			RSFormProHelper::readConfig(true);

			return true;
		}

		return false;
	}

	/**
	 * Show the list of field options.
	 *
	 * @return  void
	 *
	 * @since   2.12.0
	 */
	public function rsfp_bk_onAfterShowComponents(): void
	{
		// Load the RO Payments stylesheet
		HTMLHelper::stylesheet('com_jdidealgateway/jdidealgateway.css', ['relative' => true, 'version' => 'auto']);

		?>
		<li class="rsform_navtitle"><?php
			echo Text::_('PLG_RSFP_JDIDEAL_LABEL'); ?></li>
		<li>
			<a href="javascript: void(0);" onclick="displayTemplate('5575');return false;" id="rsfpc5575">
				<span class="rsficon jdicon-jdideal"></span>
				<span>
					<?php
					echo Text::_('PLG_RSFP_JDIDEAL_SPRODUCT'); ?>
				</span>
			</a>
		</li>
		<li>
			<a href="javascript: void(0);" onclick="displayTemplate('5576');return false;" id="rsfpc5576">
				<span class="rsficon jdicon-jdideal"></span>
				<span>
					<?php
					echo Text::_('PLG_RSFP_JDIDEAL_MPRODUCT'); ?>
				</span>
			</a>
		</li>
		<li>
			<a href="javascript: void(0);" onclick="displayTemplate('5578');return false;" id="rsfpc5578">
				<span class="rsficon jdicon-jdideal"></span>
				<span>
					<?php
					echo Text::_('PLG_RSFP_JDIDEAL_INPUTBOX'); ?>
				</span>
			</a>
		</li>
		<li>
			<a href="javascript: void(0);" onclick="displayTemplate('5577');return false;" id="rsfpc5577">
				<span class="rsficon jdicon-jdideal"></span>
				<span>
					<?php
					echo Text::_('PLG_RSFP_JDIDEAL_TOTAL'); ?>
				</span>
			</a>
		</li>
		<li>
			<a href="javascript: void(0);" onclick="displayTemplate('5579');return false;" id="rsfpc5579">
				<span class="rsficon jdicon-jdideal"></span>
				<span>
					<?php
					echo Text::_('PLG_RSFP_JDIDEAL_BUTTON'); ?>
				</span>
			</a>
		</li>
		<?php
	}

	/**
	 * Create the preview of the selected field.
	 *
	 * @param   array  $args  The field arguments and their values.
	 *
	 * @return  void
	 *
	 * @since   2.12.0
	 */
	public function rsfp_bk_onAfterCreateComponentPreview(array $args = []): void
	{
		$settings = $this->loadFormSettings((int) $args['formId']);
		$style    = 'style="font-size:24px;margin-right:5px"';

		if ($args['ComponentTypeName'] === 'jdidealButton')
		{
			$args['out'] = '<td>&nbsp;</td>';
			$args['out'] .= '<td><span class="rsficon jdicon-jdideal" ' . $style . '></span> '
				. $args['data']['LABEL']
				. '</td>';

			return;
		}

		if ($this->canRun() === false)
		{
			return;
		}

		switch ($args['ComponentTypeName'])
		{
			case 'jdidealSingleProduct':
				$formatPrice = is_float($args['data']['PRICE'])
					? number_format(
						str_replace(',', '.', $args['data']['PRICE']),
						$settings->get('numberDecimals', 2),
						$settings->get('decimalSeparator', ','),
						$settings->get('thousandSeparator', '.')
					)
					: '';
				$args['out'] = '<td>' . $args['data']['CAPTION'] . '</td>';
				$args['out'] .= '<td><span class="rsficon jdicon-jdideal" ' . $style . '></span> '
					. $args['data']['CAPTION'] . ' - ' . $formatPrice . ' ' . $settings->get('currency')
					. '</td>';
				break;
			case 'jdidealMultipleProducts':
				$args['out'] = '<td>' . $args['data']['CAPTION'] . '</td>';
				$args['out'] .= '<td><span class="rsficon jdicon-jdideal" ' . $style . '></span> '
					. $args['data']['CAPTION']
					. '</td>';
				break;
			case 'jdidealTotal':
				$args['out'] = '<td>' . $args['data']['CAPTION'] . '</td>';
				$args['out'] .= '<td><span class="rsficon jdicon-jdideal" ' . $style . '></span> ' . number_format(
						0,
						$settings->get('numberDecimals', 2),
						$settings->get('decimalSeparator', ','),
						$settings->get('thousandSeparator', '.')
					) . ' ' . $settings->get('currency') . '</td>';
				break;
			case 'jdidealInputbox':
				$defaultValue = trim($args['data']['DEFAULTVALUE']);
				$codeIcon     = '';

				if (RSFormProHelper::hasCode($defaultValue))
				{
					$defaultValue = Text::_('RSFP_PHP_CODE_PLACEHOLDER');
					$codeIcon     = '<span class="rsficon rsficon-code" ' . $style . '></span>';
				}

				$args['out'] = '<td>' . $args['data']['CAPTION'] . '</td>';
				$args['out'] .=
					'<td><span class="rsficon jdicon-jdideal" ' . $style . '></span>'
					. $codeIcon . '<input type="text" size="' . $args['data']['SIZE'] . '" value="' . RSFormProHelper::htmlEscape(
						$defaultValue
					) . '" />' .
					'</td>';
				break;
		}
	}

	/**
	 * Load the form settings.
	 *
	 * @param   int  $formId  The form ID to get the settings for.
	 *
	 * @return  Registry  The form settings.
	 *
	 * @since   4.0.0
	 *
	 * @throws  RuntimeException
	 */
	private function loadFormSettings(int $formId): Registry
	{
		$helper = new RsfpjdidealHelper;

		return $helper->loadFormSettings($formId);
	}

	/**
	 * Generates the front-end form.
	 *
	 * @param   array  $args  The field arguments and their values.
	 *
	 * @return  void
	 *
	 * @since   2.2.0
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 */
	public function rsfp_bk_onAfterCreateFrontComponentBody(array $args): void
	{
		$formId   = (int) $args['formId'];
		$settings = $this->loadFormSettings($formId);
		$form     = RSFormProHelper::getForm($formId);

		if ($form && $form->LoadFormLayoutFramework)
		{
			// Load the RO Payments RSForms! Pro stylesheet
			HTMLHelper::_(
				'stylesheet',
				'plg_system_rsfpjdideal/rsfpjdideal.css',
				['version' => 'auto', 'relative' => true]
			);
		}

		// Create a random number for the JS call
		$randomId       = UserHelper::genRandomPassword();
		$session        = Factory::getSession();
		$this->randomId = $session->get('randomId' . $formId, false, 'rsfpjdideal');

		if (!$this->randomId)
		{
			$session->set('randomId' . $formId, $randomId, 'rsfpjdideal');
			$this->randomId = $randomId;
		}

		// Get form values from the URL
		$value = $args['value'];

		switch ($args['r']['ComponentTypeId'])
		{
			// Render the 1 product field
			case 5575:
				if (isset($args['data']['SHOW']) && $args['data']['SHOW'] === 'NO')
				{
					// Hidden
					$args['out'] = '<input type="hidden" class="' . $this->randomId . '" name="rsfp_jdideal_item[]" value="' . RSFormProHelper::htmlEscape(
							$args['data']['PRICE']
						) . '"/>
					<input type="hidden" name="form[' . $args['data']['NAME'] . ']"
						id="' . $args['data']['NAME'] . '"
						value="' . RSFormProHelper::htmlEscape($args['data']['CAPTION']) . '"/>';
				}
				else
				{
					$format_price = $args['data']['PRICE']
						? number_format(
							$args['data']['PRICE'],
							$settings->get('numberDecimals', 2),
							$settings->get('decimalSeparator', ','),
							$settings->get('thousandSeparator', '.')
						)
						: '';
					$args['out']  = $args['data']['CURRENCY']
						. '<span id="rsfp_jdideal_item_' . $args['formId']
						. '" class="rsform_jdideal_item">' . $format_price . '</span> ';
					$args['out']  .= '<input type="hidden" class="' . $this->randomId . '" name="rsfp_jdideal_item[]"
							value="' . RSFormProHelper::htmlEscape($args['data']['PRICE']) . '"/>
							<input type="hidden" name="form[' . $args['data']['NAME'] . ']"
							id="' . $args['data']['NAME'] . '" value="' . RSFormProHelper::htmlEscape(
							$args['data']['CAPTION']
						) . '"/>';
				}
				break;

			// Render the multiple products field
			case 5576:
				// Check if there are any items
				if (strlen(trim($args['data']['ITEMS'])) === 0)
				{
					throw new InvalidArgumentException(
						Text::sprintf('PLG_RSFP_JDIDEAL_NO_MULTIPLE_ITEMS', $args['data']['NAME'])
					);
				}

				switch ($args['data']['VIEW_TYPE'])
				{
					case 'DROPDOWN':
						// Check if we need a quantity field
						if (array_key_exists(
								'QUANTITYBOX',
								$args['data']
							) && $args['data']['QUANTITYBOX'] === 'YES' && $args['data']['MULTIPLE'] === 'NO')
						{
							$args['out'] .= $this->renderQuantityBox($args);
						}

						$args['out'] .= '<select ' . ($args['data']['MULTIPLE'] === 'YES' ? 'multiple="multiple"' : '') . '
								name="form[' . $args['data']['NAME'] . '][]"
								id="jdideal-' . $args['componentId'] . '" ' . $args['data']['ADDITIONALATTRIBUTES'] . ' '
							. (!empty($args['data']['SIZE']) ? 'size="' . $args['data']['SIZE'] . '"' : '') . '
								onchange="rsfpjsJdideal' . $this->randomId . '.calculatePrice();" >' . "\r\n";
						$items       = RSFormProHelper::isCode($args['data']['ITEMS']);
						$items       = str_replace("\r", '', $items);
						$items       = explode("\n", $items);

						foreach ($items as $ikey => $item)
						{
							$buf = explode('|', $item);

							// Check if we have both a price and a description
							if (count($buf) === 2)
							{
								// Fix any pricing issues
								$buf[0] = str_ireplace(',', '.', $buf[0]);

								// Get the price
								$optionValue = trim($buf[0]);

								// Get the description
								$optionShown = trim($buf[1]);

								// Remove any [c] checked setting
								$optionShownTrimmed = str_replace('[c]', '', $optionShown);

								// Check for any [p] calculation setting
								$pattern   = '#\[p(.*?)\]#is';
								$calculate = false;

								if (preg_match($pattern, $optionShownTrimmed, $match))
								{
									// Remove the [p] tag
									$optionShownTrimmed = preg_replace($pattern, '', $optionShownTrimmed);
									$calculate          = true;
								}

								// Field identifier
								$fieldIdentifier = strlen($optionValue) > 0 ? $optionShownTrimmed : '';

								// Add the product to the calculation prices array
								if ($calculate)
								{
									$this->calculationProducts[$args['data']['NAME']][$fieldIdentifier] = $match[1];
								}

								// Check if we have a price higher than 0
								$optionShownTrimmed .= $optionValue > 0
									? ' - ' . $args['data']['CURRENCY'] . number_format(
										(float) $buf[0],
										$settings->get('numberDecimals', 2),
										$settings->get('decimalSeparator', ','),
										$settings->get('thousandSeparator', '.')
									)
									: '';

								// Check if we need to hide the description
								if (array_key_exists(
										'HIDE_DESCRIPTION',
										$args['data']
									) && $args['data']['HIDE_DESCRIPTION'] === 'YES')
								{
									$optionShownTrimmed = $args['data']['CURRENCY']
										. number_format(
											(float) $optionValue,
											$settings->get('numberDecimals', 2),
											$settings->get('decimalSeparator', ','),
											$settings->get('thousandSeparator', '.')
										);
								}

								// Create the product
								$product = array($args['data']['NAME'] . $ikey . '|_|' . $fieldIdentifier => $optionValue);

								// Add the product to the list of products
								$this->products = $this->merge($this->products, $product);

								$optionChecked = false;

								if (0 === count($value) && preg_match('/\[c\]/', $optionShown))
								{
									$optionChecked = true;
								}

								if (array_key_exists($args['data']['NAME'], $value)
									&& is_array($value[$args['data']['NAME']])
									&& in_array($optionShown, $value[$args['data']['NAME']], true) !== false)
								{
									$optionChecked = true;
								}

								$args['out'] .= '<option ' . ($optionChecked ? 'selected="selected"' : '') . '
										value="' . RSFormProHelper::htmlEscape(
										$fieldIdentifier
									) . '">' . RSFormProHelper::htmlEscape($optionShownTrimmed) . '</option>';
							}
						}

						$args['out'] .= '</select>';
						break;

					case 'CHECKBOX':
						$args['out'] .= $this->renderMultipleFields($args, 'checkbox');
						break;

					case 'RADIOGROUP':
						$args['out'] .= $this->renderMultipleFields($args, 'radio');
						break;
				}
				break;

			// Render the total field
			case 5577:
				$args['out'] = '';

				// Check if the total field should be displayed
				if (isset($args['data']['SHOW']) && $args['data']['SHOW'] === 'YES')
				{
					$args['out'] .= $args['data']['CURRENCY'] .
						'<span id="jdideal_total_' . $args['formId'] . '" class="rsform_jdideal_total">'
						. number_format(
							0,
							$settings->get('numberDecimals', 2),
							$settings->get('decimalSeparator', ','),
							$settings->get('thousandSeparator', '.')
						)
						. '</span> ';
				}

				$args['out'] .= '<input type="hidden" id="' . $args['data']['NAME'] . '" class="' . $this->randomId . '" value="" name="form[' . $args['data']['NAME'] . ']" />';
				break;

			// Render the input box
			case 5578:
				// Get the default value from the form
				$defaultValue = RSFormProHelper::isCode(trim($args['data']['DEFAULTVALUE']));

				// Check if there is an override from the URL
				if (isset($value[$args['data']['NAME']]))
				{
					$defaultValue = $value[$args['data']['NAME']];
				}

				switch ($args['data']['BOXTYPE'])
				{
					case 'NUMBER':
						$args['out'] = $args['data']['CURRENCY'] .
							'<input
								type="number"
								size="' . $args['data']['SIZE'] . '"
								name="rsfp_jdideal_inputbox[]"
								id="jdideal-inputbox-' . $args['componentId'] . '"
								value="' . $defaultValue . '"
								min="' . $args['data']['BOXMIN'] . '"
								max="' . $args['data']['BOXMAX'] . '"
								step="' . $args['data']['BOXSTEP'] . '"
								onchange="rsfpjsJdideal' . $this->randomId . '.calculatePrice();"
							/>
							<input
								type="hidden"
								class="' . $this->randomId . '" 
								name="form[' . $args['data']['NAME'] . ']"
								id="' . $args['data']['NAME'] . '"
								value="' . RSFormProHelper::htmlEscape($args['data']['DEFAULTVALUE']) . '"
							/>';
						break;
					case 'INPUT':
					default:
						$args['out'] = $args['data']['CURRENCY'] .
							'<input
								type="text"
								size="' . $args['data']['SIZE'] . '"
								name="rsfp_jdideal_inputbox[]"
								id="jdideal-inputbox-' . $args['componentId'] . '"
								value="' . $defaultValue . '"
								onchange="rsfpjsJdideal' . $this->randomId . '.calculatePrice();"
							/>
							<input
								type="hidden"
								class="' . $this->randomId . '" 
								name="form[' . $args['data']['NAME'] . ']"
								id="' . $args['data']['NAME'] . '"
								value="' . RSFormProHelper::htmlEscape($args['data']['DEFAULTVALUE']) . '"
							/>';
						break;
				}
				break;
		}
	}

	/**
	 * Render the quantity box.
	 *
	 * @param   array   $args     An array with form details.
	 * @param   string  $item     The form field item to render.
	 * @param   int     $counter  A counter to ensure unique IDs.
	 *
	 * @return  string  The quantity box markup.
	 *
	 * @since   4.0.0
	 *
	 * @throws  InvalidArgumentException
	 */
	private function renderQuantityBox(array $args, string $item = '', int $counter = 0): string
	{
		$output = '';

		if (array_key_exists('BOXTYPE', $args['data']))
		{
			// Check the counter to make sure the input field is unique
			$idCounter = '';

			if ($counter > 0)
			{
				// Arrays start with 0, so we need to make sure we account for this offset
				$counter--;

				$idCounter = '-' . $counter;
			}

			// Check for the default value, otherwise set it
			$defaultValue = '' !== $args['data']['DEFAULTQUANTITY'] ? $args['data']['DEFAULTQUANTITY'] : 1;

			// Get the selected quantity value
			if (0 !== count($args['value'])
				&& is_array($args['value'][$args['data']['NAME']]['quantity'])
				&& array_key_exists($counter, $args['value'][$args['data']['NAME']]['quantity']))
			{
				$defaultValue = $args['value'][$args['data']['NAME']]['quantity'][$counter];
			}

			switch ($args['data']['BOXTYPE'])
			{
				default:
				case 'INPUT':
					$output = '<input type="text" 
									name="form[' . $args['data']['NAME'] . '][quantity][]"
									id="jdideal-quantity-' . $args['componentId'] . $idCounter . '"
									value="' . $defaultValue . '"
									class="jdideal-quantityBox"
									onchange="rsfpjsJdideal' . $this->randomId . '.calculatePrice();" >' . "\r\n";
					break;
				case 'DROPDOWN':
					$items      = array();
					$boxMinimum = array_key_exists('BOXMIN', $args['data']) ? (int) $args['data']['BOXMIN'] : 1;
					$boxMaximum = array_key_exists('BOXMAX', $args['data']) ? (int) $args['data']['BOXMAX'] : 10;
					$boxStep    = array_key_exists('BOXSTEP', $args['data']) ? (int) $args['data']['BOXSTEP'] : 1;

					for ($i = $boxMinimum; $i <= $boxMaximum; $i++)
					{
						if ($boxStep === 1 || ($i % $boxStep === 1))
						{
							$items[] = HTMLHelper::_('select.option', $i, $i);
						}
					}

					$output = HTMLHelper::_(
						'select.genericlist',
						$items,
						'form[' . $args['data']['NAME'] . '][quantity][]',
						'onchange="rsfpjsJdideal' . $this->randomId . '.calculatePrice();" class="jdideal-quantityBox"',
						'value',
						'text',
						$defaultValue,
						'jdideal-quantity-' . $args['componentId'] . $idCounter
					);
					break;
				case 'NUMBER':
					$boxMinimum = array_key_exists('BOXMIN', $args['data']) ? (int) $args['data']['BOXMIN'] : 1;
					$boxMaximum = array_key_exists('BOXMAX', $args['data']) ? (int) $args['data']['BOXMAX'] : 10;
					$boxStep    = array_key_exists('BOXSTEP', $args['data']) ? (int) $args['data']['BOXSTEP'] : 1;

					$output = '<input type="number" name="form[' . $args['data']['NAME'] . '][quantity][]"
								id="jdideal-quantity-' . $args['componentId'] . $idCounter . '" min="' . $boxMinimum . '" max="' . $boxMaximum . '" step="' . $boxStep . '" value="' . $defaultValue . '"
								class="jdideal-quantityBox"
								onchange="rsfpjsJdideal' . $this->randomId . '.calculatePrice();" >' . "\r\n";
					break;
			}
		}

		return $output;
	}

	/**
	 * Array merge based on key name.
	 *
	 * @param   array  $a  The main array.
	 * @param   array  $b  The array to merge.
	 *
	 * @return  array  Return the merged array
	 *
	 * @since   2.12.0
	 */
	public function merge(array $a, array $b): array
	{
		foreach ($b as $key => $value)
		{
			$a[$key] = $value;
		}

		return $a;
	}

	/**
	 * Render the input fields for multiple.
	 *
	 * @param   array   $args       An array with form details.
	 * @param   string  $inputType  The type of input box to render.
	 *
	 * @return  string  The rendered form fields.
	 *
	 * @since   4.0.0
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 */
	private function renderMultipleFields(array $args, string $inputType): string
	{
		$output   = '';
		$i        = 0;
		$formId   = (int) $args['formId'];
		$settings = $this->loadFormSettings($formId);

		// Get form values from the URL
		$value = $args['value'];

		// Get the layout name
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('FormLayoutName'))
			->from($this->db->quoteName('#__rsform_forms'))
			->where($this->db->quoteName('FormId') . ' = ' . $formId);
		$this->db->setQuery($query);
		$layoutName = $this->db->loadResult();

		// Get the items
		$items = RSFormProHelper::isCode($args['data']['ITEMS']);
		$items = trim(str_replace("\r", '', $items));
		$items = explode("\n", $items);

		// Check if it should be hidden
		$hideField = '';

		if (array_key_exists('CHECKBOX_INVISIBLE', $args['data'])
			&& $args['data']['CHECKBOX_INVISIBLE'] === 'YES')
		{
			$hideField = 'rsfpjdhidden';
		}

		foreach ($items as $ikey => $item)
		{
			$buf = explode('|', $item);

			// Check if we have both a price and a description
			if (count($buf) === 2)
			{
				// Fix any pricing issues
				$buf[0] = str_ireplace(',', '.', $buf[0]);

				// Get the price
				$optionValue = trim($buf[0]);

				// Get the description
				$optionShown = trim($buf[1]);

				// Remove any [c] checked setting
				$optionShownTrimmed = str_replace('[c]', '', $optionShown);

				// Check for any [p] calculation setting
				$pattern   = '#\[p(.*?)\]#is';
				$calculate = false;

				if (preg_match($pattern, $optionShownTrimmed, $match))
				{
					// Remove the [p] tag
					$optionShownTrimmed = preg_replace($pattern, '', $optionShownTrimmed);
					$calculate          = true;
				}

				// Field identifier
				$fieldIdentifier = strlen($optionValue) > 0 ? $optionShownTrimmed : '';

				// Add the product to the calculation prices array
				if ($calculate)
				{
					$this->calculationProducts[$args['data']['NAME']][$fieldIdentifier] = $match[1];
				}

				// Show the price
				// Check if we have a price higher than 0
				$optionShownTrimmed .= $optionValue > 0
					? ' - ' . $args['data']['CURRENCY'] . number_format(
						(float) $optionValue,
						$settings->get('numberDecimals', 2),
						$settings->get('decimalSeparator', ','),
						$settings->get('thousandSeparator', '.')
					)
					: '';

				// Check if we need to hide the description
				if ($args['data']['HIDE_DESCRIPTION'] === 'YES')
				{
					$optionShownTrimmed = $args['data']['CURRENCY']
						. number_format(
							(float) $optionValue,
							$settings->get('numberDecimals', 2),
							$settings->get('decimalSeparator', ','),
							$settings->get('thousandSeparator', '.')
						);
				}

				// Create the product
				$product = [$args['data']['NAME'] . $ikey . '|_|' . $fieldIdentifier => $optionValue];

				// Add the product to the list of products
				$this->products = $this->merge($this->products, $product);

				$optionChecked = false;

				if (0 === count($value) && preg_match('/\[c\]/', $optionShown))
				{
					$optionChecked = true;
				}

				// Verify an existing option has been checked
				if (array_key_exists($args['data']['NAME'], $value)
					&& $value[$args['data']['NAME']] !== ''
					&& in_array($optionShown, $value[$args['data']['NAME']], true) !== false)
				{
					$optionChecked = true;
				}

				if (array_key_exists('CHECKBOX_CHECKED', $args['data'])
					&& $args['data']['CHECKBOX_CHECKED'] === 'YES'
					&& 0 === count($value))
				{
					$optionChecked = true;
				}

				if ($layoutName === 'responsive' && $args['data']['FLOW'] === 'VERTICAL')
				{
					$output .= '<p class="rsformVerticalClear">';
				}

				/**
				 * Check if we need a quantity field.
				 * This is not needed when there is a multiple select list because we don't know for which option the field
				 * is.
				 */
				if (array_key_exists('QUANTITYBOX', $args['data'])
					&& $args['data']['QUANTITYBOX'] === 'YES'
					&& $args['data']['MULTIPLE'] === 'NO')
				{
					$output .= $this->renderQuantityBox($args, $item, $ikey + 1);
				}

				/**
				 * When using a radiobutton with a quantity field, it goes wrong. This cannot be done.
				 * The quantity field will look like:
				 * form[buy][quantity][1]
				 *
				 * The [1] is required for filling in the submission details.
				 *
				 * The radiobutton field will look like:
				 * form[buy]
				 *
				 * The quantity field overrides the radiobutton field.
				 */
				$output .= '<input ' . ($optionChecked !== false ? 'checked="checked"' : '') . '
										name="form[' . $args['data']['NAME'] . ']' . ($inputType === 'checkbox' ? '[]' : '') . '"
										type="' . $inputType . '"
										value="' . RSFormProHelper::htmlEscape($fieldIdentifier) . '"
										id="jdideal-' . $args['componentId'] . '-' . $i . '" ' . $args['data']['ADDITIONALATTRIBUTES'] . '
										class="' . $hideField . '"
										onclick="rsfpjsJdideal' . $this->randomId . '.calculatePrice();" />
											<label class="jdideal-' . $inputType . '" for="jdideal-' . $args['componentId'] . '-' . $i . '">'
					. RSFormProHelper::htmlEscape($optionShownTrimmed) . '</label>';

				if ($args['data']['FLOW'] === 'VERTICAL')
				{
					if ($layoutName === 'responsive')
					{
						$output .= '</p>';
					}
					else
					{
						$output .= '<br />';
					}
				}

				$i++;
			}
		}

		return $output;
	}

	/**
	 * Generates the HTML for the fields to show on the front-end.
	 *
	 * @param   array  $args  The field arguments and their values.
	 *
	 * @return  void
	 *
	 * @since   2.2.0
	 *
	 * @throws  RuntimeException
	 */
	public function rsfp_f_onBeforeFormDisplay(array $args): void
	{
		$formId = (int) $args['formId'];

		if ($this->hasJdidealFields($formId))
		{
			$settings = $this->loadFormSettings($formId);

			// Get the session info
			$session        = Factory::getSession();
			$this->randomId = $session->get('randomId' . $args['formId'], null, 'rsfpjdideal');
			$session->clear('randomId' . $args['formId']);

			// Find the multiple product fields
			$multipleProducts = RSFormProHelper::componentExists($args['formId'], 5576);

			// Find the input boxes
			$inputBoxes    = RSFormProHelper::componentExists($args['formId'], 5578);
			$singleProduct = RSFormProHelper::componentExists($args['formId'], 5575);

			// Merge them
			$ideals = array_merge($multipleProducts, $inputBoxes, $singleProduct);

			// Find the total field
			$totalFieldId   = RSFormProHelper::componentExists($args['formId'], 5577);
			$totalDetails   = array();
			$totalFieldName = '';

			if (array_key_exists(0, $totalFieldId))
			{
				$totalDetails = RSFormProHelper::getComponentProperties($totalFieldId[0]);
			}

			if (array_key_exists('NAME', $totalDetails))
			{
				$totalFieldName = $totalDetails['NAME'];
			}

			$properties = RSFormProHelper::getComponentProperties($ideals);

			if (is_array($ideals))
			{
				$args['formLayout'] .= '<script type="text/javascript">' . "\r\n";
				$args['formLayout'] .= 'var rsfpjsJdideal' . $this->randomId . ' = new rsfpJdideal(' . $args['formId'] . ');' . "\r\n";

				// Set the random ID
				$args['formLayout'] .= 'rsfpjsJdideal' . $this->randomId . '.setRandomId("' . $this->randomId . '");' . "\r\n";

				// Set the price formatting
				$args['formLayout'] .= 'rsfpjsJdideal' . $this->randomId . '.setDecimals('
					. $settings->get('numberDecimals', 2) . ', "'
					. $settings->get('decimalSeparator', ',') . '", "'
					. $settings->get('thousandSeparator', '.') . '");'
					. "\r\n";

				// Set the total field
				$args['formLayout'] .= 'rsfpjsJdideal' . $this->randomId . '.setTotalField("' . $totalFieldName . '");' . "\r\n";

				// Set the tax rate
				if ($settings->get('tax', 0) > 0)
				{
					$args['formLayout'] .= 'rsfpjsJdideal' . $this->randomId . '.setTax(' . $settings->get(
							'taxType',
							0
						) . ',' . $settings->get('tax', 0) . ');' . "\r\n";
				}

				if (is_array($this->products))
				{
					foreach ($this->products as $product => $price)
					{
						$product = addslashes($product);
						$product = str_replace('[c]', '', $product);
						$price   = '' !== $price ? $price : 0;

						if (!preg_match('/[a-zA-Z]/', $price))
						{
							$args['formLayout'] .= 'rsfpjsJdideal' . $this->randomId . '.addProduct("' . $product . '","' . $price . '");' . "\r\n";
						}
					}
				}

				foreach ($ideals as $componentId)
				{
					$details = $properties[$componentId];

					// Check for the multiple products field dropdown
					if (array_key_exists('VIEW_TYPE', $details))
					{
						// Add the ID to the list
						$args['formLayout'] .= 'rsfpjsJdideal' . $this->randomId . '.addComponent(' . $componentId . ', \'' . $details['NAME'] . '\');' . "\r\n";
					}
				}

				// Get the hidden values
				$args['formLayout'] .= 'rsfpjsJdideal' . $this->randomId . '.calculatePrice();' . "\r\n";

				// Add the calculation products
				if (count($this->calculationProducts) > 0)
				{
					foreach ($this->calculationProducts as $field => $calculationProduct)
					{
						$args['formLayout'] .= 'RSFormProPrices[\'' . $args['formId'] . '_' . $field . '\'] = ' . json_encode(
								$calculationProduct
							) . ";\r\n";
					}

					// Add a document ready trigger for the calculations
					$args['formLayout'] .= <<<JS
if (typeof(rsfp_Calculations{$args['formId']}) === 'function') { 
	document.addEventListener("DOMContentLoaded", function(event) { 
		rsfp_Calculations{$args['formId']}();
	});
}
JS;
				}

				// Close the script tag
				$args['formLayout'] .= '</script>';
			}
		}
	}

	/**
	 * Check if the form has any RO Payments fields.
	 *
	 * @param   int  $formId  The ID of the form to check.
	 *
	 * @return  boolean  True if RO Payments fields are present | False if no fields are present.
	 *
	 * @since   4.2.0
	 * @throws  RuntimeException
	 */
	private function hasJdidealFields($formId): bool
	{
		static $cache = [];

		if (!isset($cache[$formId]))
		{
			$cache[$formId] = RSFormProHelper::componentExists($formId, $this->newComponents);
		}

		return $cache[$formId] ? true : false;
	}

	/**
	 * Store a new submission.
	 *
	 * @param   array  $args  The field arguments and their values.
	 *
	 * @return  boolean  Always returns true
	 *
	 * @since   2.12.0
	 */
	public function rsfp_f_onBeforeStoreSubmissions(array $args): bool
	{
		if (!$this->canRun())
		{
			return true;
		}

		if (RSFormProHelper::componentExists($args['formId'], $this->newComponents))
		{
			// Prepare ComponentId query
			$query = $this->db->getQuery(true)
				->select($this->db->quoteName('properties.ComponentId'))
				->from($this->db->quoteName('#__rsform_properties', 'properties'))
				->leftJoin(
					$this->db->quoteName('#__rsform_components', 'components')
					. ' ON ' . $this->db->quoteName('components.ComponentId') . ' = ' . $this->db->quoteName(
						'properties.ComponentId'
					)
				);

			// Prepare item query
			$itemQuery = $this->db->getQuery(true)
				->select($this->db->quoteName('PropertyValue'))
				->from($this->db->quoteName('#__rsform_properties'));

			// Loop through all the arrays to see if there is a nested quantity array
			foreach ($args as $area => $arg)
			{
				if (is_array($arg))
				{
					foreach ($arg as $fieldName => $values)
					{
						if (is_array($values))
						{
							// Get the component ID for the field
							$query->clear('where')
								->where($this->db->quoteName('components.ComponentTypeId') . ' = 5576')
								->where($this->db->quoteName('components.FormId') . ' = ' . (int) $args['formId'])
								->where(
									$this->db->quoteName('properties.PropertyValue') . ' = ' . $this->db->quote(
										$fieldName
									)
								);

							$this->db->setQuery($query);

							$componentId = $this->db->loadResult();

							foreach ($values as $valueKey => $userValue)
							{
								if ($valueKey !== 'quantity')
								{
									// Get the actual value from the form definition if we have a component ID
									if ($componentId)
									{
										$itemQuery->clear('where')
											->where($this->db->quoteName('ComponentId') . ' = ' . (int) $componentId)
											->where(
												$this->db->quoteName('PropertyName') . ' = ' . $this->db->quote('ITEMS')
											);

										$this->db->setQuery($itemQuery);

										$fieldValues = $this->db->loadResult();
										$fieldValues = RSFormProHelper::isCode($fieldValues);

										$allValues = explode(
											PHP_EOL,
											preg_replace(
												['~\r\n~', '~\r~', '~\n~'],
												PHP_EOL,
												$fieldValues
											)
										);

										if ($this->hasDuplicateValues($allValues))
										{
											break;
										}

										foreach ($allValues as $index => $allValue)
										{
											[$price, $name] = explode('|', $allValue);

											// Compare the name value with the value the user selected in the form
											if ((string) $name === (float) $userValue)
											{
												$args[$area][$fieldName][$valueKey] = str_replace(
													['[c]', '[g]'],
													'',
													$name
												);
											}
										}
									}
								}
							}
						}

						// Clean up the quantity
						if (is_array($values) && array_key_exists('quantity', $values))
						{
							// Flatten the multi-dimensional array
							foreach ($values['quantity'] as $key => $value)
							{
								$args[$area][$fieldName][$key] = $value . ' ' . $args[$area][$fieldName][$key];
							}

							// Remove the array since we no longer need it
							unset($args[$area][$fieldName]['quantity']);
						}
					}
				}
			}

			// Set the initial payment status for a submission
			$args['post']['_STATUS'] = '0';
		}

		return true;
	}

	/**
	 * Check if a user field has duplicate values, if so, we can't reliably find what the user chose.
	 *
	 * @param   array  $values  The array of values to check for duplicates.
	 *
	 * @return  boolean  True on duplicate fields | False if there are no duplicate fields.
	 *
	 * @since   4.3.2
	 */
	private function hasDuplicateValues(array $values): bool
	{
		$duplicate  = false;
		$foundPrice = '';

		foreach ($values as $value)
		{
			[$price, $name] = explode('|', $value);

			if ($price === $foundPrice)
			{
				$duplicate = true;
				break;
			}

			$foundPrice = $price;
		}

		return $duplicate;
	}

	/**
	 * Get the component ID.
	 *
	 * @param   string  $name    The name of the component
	 * @param   int     $formId  The ID of the form
	 *
	 * @return  integer|null  The return value or null if the query failed.
	 *
	 * @since   2.12
	 * @throws  RuntimeException
	 */
	public function getComponentId(string $name, int $formId): ?int
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('p.ComponentId'))
			->from($this->db->quoteName('#__rsform_properties', 'properties'))
			->leftJoin(
				$this->db->quoteName('#__rsform_components', 'components')
				. ' ON ' .
				$this->db->quoteName('properties.ComponentId') . ' = ' . $this->db->quoteName('components.ComponentId')
			)
			->where($this->db->quoteName('properties.PropertyValue') . ' = ' . $this->db->quote($name))
			->where($this->db->quoteName('properties.PropertyName') . ' = ' . $this->db->quote('NAME'))
			->where($this->db->quoteName('components.FormId') . ' = ' . $formId);
		$this->db->setQuery($query);

		return $this->db->loadResult();
	}

	/**
	 * Function called by RSForm!Pro for different tasks.
	 *
	 * @return  void
	 *
	 * @since   2.12.0
	 * @throws  Exception
	 */
	public function rsfp_f_onSwitchTasks(): void
	{
		$plugin_task = $this->app->input->get('plugin_task');

		switch ($plugin_task)
		{
			case 'jdideal.notify':
				$this->rsfp_f_jdidealNotify();
				break;
			case 'jdideal.return':
				$formId = $this->app->input->getInt('formId');
				$this->jdidealReturn($formId);
				break;
		}
	}

	/**
	 * Check the payment status.
	 *
	 * @return  boolean  True if payment is valid, otherwise false.
	 *
	 * @since   2.2.0
	 * @throws  Exception
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 */
	public function rsfp_f_jdidealNotify(): bool
	{
		// Load the helper
		$jdideal = new Gateway;

		$trans  = $this->app->input->get('transactionId');
		$column = 'trans';

		if (empty($trans))
		{
			$trans  = $this->app->input->get('pid');
			$column = 'pid';
		}

		$details    = $jdideal->getDetails($trans, $column, false, 'rsformpro');
		$statusCode = $jdideal->getStatusCode($details->result);
		$jdideal->log('Transaction number: ' . $trans, $details->id);
		$jdideal->log('Details loaded ', $details->id);
		$jdideal->log('Details result: ' . $details->result, $details->id);
		$jdideal->log('Status code: ' . $statusCode, $details->id);
		$isValid = $jdideal->isValid($details->result);

		// Set the status
		switch ($statusCode)
		{
			case 'X':
				$statusValue = -1;
				break;
			case 'C':
				$statusValue = 1;
				break;
			default:
				$statusValue = 0;
				break;
		}

		$jdideal->log('Status value: ' . $statusValue, $details->id);

		// Get the IDs
		[$formId, $submissionId] = explode('.', $details->order_id);
		$formId       = (int) $formId;
		$submissionId = (int) $submissionId;

		// Check the payment status
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('FieldValue'))
			->from($this->db->quoteName('#__rsform_submission_values'))
			->where($this->db->quoteName('SubmissionId') . ' = ' . $submissionId)
			->where($this->db->quoteName('FormId') . ' = ' . $formId)
			->where($this->db->quoteName('FieldName') . ' =  ' . $this->db->quote('_STATUS'));
		$this->db->setQuery($query);
		$status = (int) $this->db->loadResult();

		$jdideal->log('RSForm status: ' . $status, $details->id);

		if ($status === 0)
		{
			$query->clear()
				->update($this->db->quoteName('#__rsform_submission_values'))
				->set($this->db->quoteName('FieldValue') . ' = ' . $this->db->quote($statusValue))
				->where($this->db->quoteName('SubmissionId') . ' = ' . $submissionId)
				->where($this->db->quoteName('FormId') . ' = ' . $formId)
				->where($this->db->quoteName('FieldName') . ' =  ' . $this->db->quote('_STATUS'));
			$this->db->setQuery($query)->execute();

			$jdideal->setProcessed(1, $details->id);

			$settings = $this->loadFormSettings($formId);

			if ($statusValue === 1 || (int) $settings->get('sendEmailOnFailedPayment', 0) === 1)
			{
				$jdideal->log('Send out emails', $details->id);
				$this->sendConfirmationEmail($details, $formId, $submissionId);
				$this->app->triggerEvent('rsfp_afterConfirmPayment', [$submissionId]);
			}
		}

		// Check if the result is valid
		if (!$isValid)
		{
			return false;
		}

		return true;
	}

	/**
	 * Send out a confirmation email with payment and submission details.
	 *
	 * @param   stdClass  $details       The payment details
	 * @param   int       $formId        The form the submission belongs to
	 * @param   int       $submissionId  The submission ID to send the email for
	 *
	 * @return  void
	 *
	 * @since   4.14.0
	 * @throws  Exception
	 */
	private function sendConfirmationEmail(stdClass $details, int $formId, int $submissionId): void
	{
		// Get the form parameters
		$settings = $this->loadFormSettings($formId);

		if ((int) $settings->get('confirmationEmail', 0) === 0)
		{
			return;
		}

		[$find, $replace] = RSFormProHelper::getReplacements($submissionId);

		// Load the values of the submission keep for B/C
		$query = $this->db->getQuery(true)
			->select(
				$this->db->quoteName(
					[
						'FieldValue',
						'FieldName',
					]
				)
			)
			->from($this->db->quoteName('#__rsform_submission_values'))
			->where($this->db->quoteName('SubmissionId') . ' = ' . $submissionId);
		$this->db->setQuery($query);

		$values = $this->db->loadObjectList();

		array_map(
			static function ($field) use (&$find, &$replace) {
				$find[]    = '[' . strtoupper(trim($field->FieldName)) . ']';
				$replace[] = $field->FieldValue;
			},
			$values
		);

		// Build the placeholders
		$find[] = '[PAYMENT_METHOD]';
		$find[] = '[PAYMENT_ID]';
		$find[] = '[TRANSACTION_ID]';
		$find[] = '[CURRENCY]';
		$find[] = '[AMOUNT]';
		$find[] = '[CARD]';
		$find[] = '[RESULT]';

		$replace[] = Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_' . $details->card);
		$replace[] = $details->paymentId;
		$replace[] = $details->trans;
		$replace[] = $details->currency;
		$replace[] = number_format(
			$details->amount,
			$settings->get('numberDecimals'),
			$settings->get('decimal'),
			$settings->get('thousands')
		);
		$replace[] = $details->card;
		$replace[] = $details->result;

		// Replace the placeholders
		$body    = str_ireplace($find, $replace, $settings->get('confirmationMessage'));
		$subject = str_ireplace($find, $replace, $settings->get('confirmationSubject'));

		// Instantiate the mailer
		$config   = Factory::getConfig();
		$from     = $config->get('mailfrom');
		$fromName = $config->get('fromname');
		$mail     = Factory::getMailer();
		$email    = explode(',', $settings->get('confirmationRecipient'));

		try
		{
			$mail->sendMail($from, $fromName, $email, $subject, $body, true);
		}
		catch (Exception $exception)
		{
			Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}
	}

	/**
	 * Send a user back to the RSForms! Pro Thank you page.
	 *
	 * @param   int  $formId  The ID of the form to get the information from.
	 *
	 * @return  void
	 *
	 * @since   4.4.0
	 *
	 * @throws  Exception
	 */
	private function jdidealReturn(int $formId): void
	{
		// Get session object
		$session = Factory::getSession();

		// Load the helper
		$jdideal = new Gateway;

		$trans  = $this->app->input->get('transactionId');
		$column = 'trans';

		if (empty($trans))
		{
			$trans  = $this->app->input->get('pid');
			$column = 'pid';
		}

		$details = $jdideal->getDetails($trans, $column, false, 'rsformpro');

		// Check if we received a status, if not check again
		if (empty($details->result))
		{
			$this->rsfp_f_jdidealNotify();
		}

		// Get the form parameters
		$params = $this->loadFormSettings($formId);

		// Get data from session
		$formParams                = $session->get('com_rsform.formparams.formId' . $formId);
		$formParams->formProcessed = true;

		if ((int) $params->get('redirectRsforms') === 0)
		{
			// Show the result also used as fallback if there is no redirect information available
			[$replace, $with] = RSFormProHelper::getReplacements($details->order_number);
			$message                     = $jdideal->getMessage($details->id);
			$message                     = str_ireplace($replace, $with, $message);
			$formParams->thankYouMessage = base64_encode($message);
		}

		$session->set('com_rsform.formparams.formId' . $formId, $formParams);

		$jdideal->log('Redirect to ' . $formParams->redirectUrl, $details->id);
		$this->app->redirect($formParams->redirectUrl);
	}

	/**
	 * Send the submission emails.
	 *
	 * @param   int  $submissionId  The submission ID.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function rsfp_afterConfirmPayment(int $submissionId): void
	{
		// Only send out emails if the RSForm! Pro Payment plugin is disabled
		if (!PluginHelper::isEnabled('system', 'rsfppayment'))
		{
			RSFormProHelper::sendSubmissionEmails($submissionId);
		}
	}

	/**
	 * The name of the component.
	 *
	 * @return  void
	 *
	 * @since   2.12
	 */
	public function jdidealScreen(): void
	{
		echo 'RO Payments';
	}

	/**
	 * Enhance the condition option fields.
	 *
	 * @param   array  $args  The array of condition options.
	 *
	 * @return  void
	 *
	 * @since   2.12.0
	 */
	public function rsfp_bk_onCreateConditionOptionFields(array $args): void
	{
		$args['types'][] = '5575';
		$args['types'][] = '5576';
		$args['types'][] = '5578';
	}

	/**
	 * Update the conditions after form save.
	 *
	 * @param   TableRSForm_Forms  $form  The form object that is being stored.
	 *
	 * @return  boolean  True on success | False on failure
	 *
	 * @since   2.12.0
	 * @throws  Exception
	 * @throws  RuntimeException
	 */
	public function rsfp_onFormSave(TableRSForm_Forms $form): bool
	{
		// Get the language the form is being stored in
		$storeLanguage = $this->app->input->post->get('Language');
		$baseLanguage  = $this->app->input->post->get('Lang');
		$translate     = false;
		$formId        = (int) $form->FormId;

		if ($storeLanguage !== $baseLanguage)
		{
			$translate = true;
		}

		// Load the values
		$query = $this->db->getQuery(true)
			->select(
				$this->db->quoteName('condition_details.id', 'conditionId') . ', ' .
				$this->db->quoteName('condition_details.value') . ', ' .
				$this->db->quoteName('condition_details.component_id')
			)
			->from($this->db->quoteName('#__rsform_components', 'rsform_components'))
			->leftJoin(
				$this->db->quoteName('#__rsform_condition_details', 'condition_details')
				. ' ON ' . $this->db->quoteName('condition_details.component_id') . ' = ' . $this->db->quoteName(
					'rsform_components.ComponentId'
				)
			)
			->where($this->db->quoteName('rsform_components.FormId') . ' = ' . $formId)
			->where($this->db->quoteName('rsform_components.ComponentTypeId') . ' = 5576')
			->order($this->db->quoteName('conditionId'));
		$this->db->setQuery($query);

		$conditions = $this->db->loadObjectList();

		// Load the replacements
		$query->clear()
			->select(
				$this->db->quoteName(
					[
						'rsform_properties.PropertyValue',
						'rsform_properties.PropertyName',
						'rsform_properties.ComponentId',
						'rsform_components.ComponentTypeId'
					]
				)
			)
			->from($this->db->quoteName('#__rsform_properties', 'rsform_properties'))
			->leftJoin(
				$this->db->quoteName('#__rsform_components', 'rsform_components')
				. ' ON ' . $this->db->quoteName('rsform_properties.ComponentId') . ' = ' . $this->db->quoteName(
					'rsform_components.ComponentId'
				)
			)
			->where($this->db->quoteName('rsform_components.FormId') . ' = ' . $formId)
			->where(
				'(' . $this->db->quoteName('rsform_properties.PropertyName') . ' = ' . $this->db->quote('DEFAULTVALUE')
				. ' OR ' .
				$this->db->quoteName('rsform_properties.PropertyName') . ' = ' . $this->db->quote('ITEMS')
				. ')'
			)
			->where($this->db->quoteName('rsform_components.ComponentTypeId') . ' = 5576');
		$this->db->setQuery($query);

		$replacements = $this->db->loadObjectList();

		// Prepare the replacements
		foreach ($replacements as $index => $replacement)
		{
			// Check if we need to get a translation
			if ($translate)
			{
				$query->clear()
					->select($this->db->quoteName('value'))
					->from($this->db->quoteName('#__rsform_translations'))
					->where($this->db->quoteName('form_id') . ' = ' . $formId)
					->where($this->db->quoteName('lang_code') . ' = ' . $this->db->quote($storeLanguage))
					->where($this->db->quoteName('reference') . ' = ' . $this->db->quote('properties'))
					->where(
						$this->db->quoteName('reference_id') . ' = ' . $this->db->quote(
							$replacement->ComponentId . '.' . $replacement->PropertyName
						)
					);
				$this->db->setQuery($query);

				$replacement->PropertyValue = $this->db->loadResult();
			}

			$replacement->PropertyValue = RSFormProHelper::isCode($replacement->PropertyValue);
			$replacement->PropertyValue = str_replace(["\r\n", "\r"], "\n", $replacement->PropertyValue);
			$replacement->PropertyValue = str_replace(['[c]', '[g]'], '', $replacement->PropertyValue);
			$replacement->PropertyValue = explode("\n", $replacement->PropertyValue);

			$replacements[$index] = $replacement;
		}

		// Check if we have any condition with the value
		foreach ($conditions as $condition)
		{
			foreach ($replacements as $replacement)
			{
				if ($condition->component_id === $replacement->ComponentId)
				{
					foreach ($replacement->PropertyValue as $index => $propertyValue)
					{
						$propertyValue = explode('|', $propertyValue, 2);

						if ($condition->value === $propertyValue[0])
						{
							// We have a met condition, need to update the value
							$query->clear()
								->update($this->db->quoteName('#__rsform_condition_details'))
								->set($this->db->quoteName('value') . ' = ' . $this->db->quote(trim($propertyValue[1])))
								->where($this->db->quoteName('id') . ' = ' . (int) $condition->conditionId);
							$this->db->setQuery($query)->execute();

							break;
						}
					}
				}
			}
		}

		// Get the form settings
		$settings = $this->app->input->post->get('roPaymentsParams', [], 'array');
		$tables   = $this->db->getTableList();
		$table    = $this->db->getPrefix() . 'rsform_jdideal';

		if (!in_array($table, $tables, true))
		{
			return true;
		}

		$query->clear()
			->select($this->db->quoteName('params'))
			->from($this->db->quoteName('#__rsform_jdideal'))
			->where($this->db->quoteName('form_id') . ' = ' . $formId);
		$this->db->setQuery($query);
		$params = $this->db->loadResult();

		if ($params)
		{
			$query->clear()
				->update($this->db->quoteName('#__rsform_jdideal'))
				->set($this->db->quoteName('params') . ' = ' . $this->db->quote(json_encode($settings)))
				->where($this->db->quoteName('form_id') . ' = ' . $formId);
		}
		else
		{
			$query->clear()
				->insert($this->db->quoteName('#__rsform_jdideal'))
				->columns(['form_id', 'params'])
				->values($formId . ',' . $this->db->quote(json_encode($settings)));
		}

		$this->db->setQuery($query);

		try
		{
			$this->db->execute();
		}
		catch (Exception $e)
		{
			return false;
		}

		return true;
	}

	/**
	 * Tell RSForm!Pro that we handle payments.
	 *
	 * @param   array    $items   The list of payment methods
	 * @param   integer  $formId  The ID of the form
	 *
	 * @return  void
	 *
	 * @since   2.8.0
	 */
	public function rsfp_getPayment(array &$items, int $formId): void
	{
		if ($components = RSFormProHelper::componentExists($formId, $this->componentId))
		{
			foreach ($components as $component)
			{
				$data = RSFormProHelper::getComponentProperties($component);

				$item        = new stdClass;
				$item->value = $data['NAME'];
				$item->text  = $data['LABEL'];

				$items[] = $item;
			}
		}
	}

	/**
	 * Section to deal with integrated payment option for the Payment Package
	 */

	/**
	 * Load the RO Payments Form now the form has been submitted. This is used for the payment package.
	 * For the RO Payments buttons see the rsfp_f_onAfterFormProcess() method.
	 *
	 * @param   string   $payValue      The name of the payment method to execute.
	 * @param   integer  $formId        The ID of the form submitted.
	 * @param   integer  $submissionId  The ID of the submission.
	 * @param   float    $price         The price to pay.
	 * @param   array    $products      The list of products.
	 * @param   string   $code          Unknown code.
	 *
	 * @return  mixed  Return nothing we don't process the payment or price is 0, redirect if there is to be paid
	 *
	 * @since   2.2.0
	 * @throws  Exception
	 * @see     rsfp_f_onAfterFormProcess
	 *
	 */
	public function rsfp_doPayment(
		string $payValue,
		int $formId,
		int $submissionId,
		?float $price,
		array $products,
		string $code
	) {
		// Execute only our plugin
		$match      = false;
		$components = RSFormProHelper::componentExists($formId, $this->componentId);

		foreach ($components as $component)
		{
			$data = RSFormProHelper::getComponentProperties($component);

			if ($data['NAME'] === $payValue)
			{
				$match = true;
			}
		}

		if (!$match)
		{
			if ($payValue !== 'jdidealButton')
			{
				return;
			}

			$payValue = '';
		}

		if ($price !== null && $price > 0)
		{
			// Get the form parameters
			$settings = $this->loadFormSettings($formId);

			// Construct the feedback URLs
			$itemId = $this->app->input->getInt('Itemid', 0);

			$uri = Uri::getInstance(Route::_(Uri::root() . 'index.php?option=com_rsform'));
			$uri->setVar('formId', $formId);
			$uri->setVar('task', 'plugin');
			$uri->setVar('plugin_task', 'jdideal.return');
			$uri->setVar('Itemid', $this->app->input->get('Itemid'));
			$returnUrl = $uri->toString();

			$uri = Uri::getInstance(Route::_(Uri::root() . 'index.php?option=com_rsform'));
			$uri->setVar('formId', $formId);
			$uri->setVar('task', 'plugin');
			$uri->setVar('plugin_task', 'jdideal.notify');
			$uri->setVar('code', $code);
			$uri->setVar('Itemid', $this->app->input->get('Itemid'));
			$notifyUrl = $uri->toString();

			// Calculate price with tax
			if ($settings->get('tax', 0) > 0)
			{
				$price = $settings->get('taxType', 0) ? $price + $settings->get('tax', 0) : $price * ($settings->get(
							'tax',
							0
						) / 100 + 1);
			}

			// Load the payment provider
			$profileAlias = $this->getProfileAlias($formId);

			// Load the custom order number
			$orderNumber = $this->getCustomOrderNumber(['formId' => $formId, 'SubmissionId' => $submissionId]);

			// Get the email field
			$email = $this->getEmailField(['formId' => $formId, 'SubmissionId' => $submissionId]);

			// Set some needed data
			$data = [
				'amount'         => $price,
				'order_number'   => $orderNumber,
				'order_id'       => $formId . '.' . $submissionId,
				'origin'         => 'rsformpro',
				'return_url'     => $returnUrl,
				'notify_url'     => $notifyUrl,
				'cancel_url'     => '',
				'email'          => $email,
				'payment_method' => $payValue,
				'currency'       => $params->currency ?? '',
				'profileAlias'   => $profileAlias,
				'custom_html'    => '',
				'silent'         => false
			];

			// Show a loading message in case it takes some time
			echo Text::_('PLG_RSFP_JDIDEAL_LOADING');

			// Build the form to redirect to RO Payments
			?>
			<form id="jdideal" action="<?php
			echo Route::_('index.php?option=com_jdidealgateway&view=checkout&Itemid=' . $itemId); ?>" method="post">
				<input type="hidden" name="vars" value="<?php
				echo base64_encode(json_encode($data)); ?>"/>
			</form>
			<script type="text/javascript">
              document.getElementById('jdideal').submit()
			</script>
			<?php
			$this->app->close();
		}
		else
		{
			$this->rsfp_f_onAfterFormProcess(
				['formId' => $formId, 'SubmissionId' => $submissionId, 'internal' => true]
			);
		}
	}

	/**
	 * Load the payment provider from the form.
	 *
	 * @param   int  $formId  The form ID
	 *
	 * @return  string  The name of the payment provider.
	 *
	 * @since   4.0.0
	 * @throws  RuntimeException
	 */
	private function getProfileAlias(int $formId): string
	{
		$settings = $this->loadFormSettings($formId);

		return $settings->get('profileAlias', '');
	}

	/**
	 * Load the custom order number.
	 *
	 * @param   array  $args  List of arguments of the submission
	 *
	 * @return  string  The order number.
	 *
	 * @since   2.12.0
	 *
	 * @throws  RuntimeException
	 */
	private function getCustomOrderNumber(array $args): string
	{
		$db          = $this->db;
		$formId      = (int) $args['formId'];
		$orderNumber = false;
		$settings    = $this->loadFormSettings($formId);

		if ($fieldOrderNumber = $settings->get('fieldOrderNumber'))
		{
			$query = $db->getQuery(true)
				->select($db->quoteName('FieldValue'))
				->from($db->quoteName('#__rsform_submission_values'))
				->where($db->quoteName('FormId') . ' = ' . $formId)
				->where($db->quoteName('SubmissionId') . ' = ' . (int) $args['SubmissionId'])
				->where(
					$db->quoteName('FieldName') . ' = ' . $db->quote($fieldOrderNumber)
				);
			$db->setQuery($query);
			$orderNumber = $db->loadResult();
		}

		if (!$orderNumber)
		{
			// If no custom order number is set, we use the submission ID
			$orderNumber = $args['SubmissionId'];
		}

		return (string) $orderNumber;
	}

	/**
	 * Load the email field.
	 *
	 * @param   array  $args  List of arguments of the submission
	 *
	 * @return  string  The email address.
	 *
	 * @since   2.12.0
	 * @throws  RuntimeException
	 */
	private function getEmailField(array $args): string
	{
		$formId   = (int) $args['formId'];
		$email    = '';
		$settings = $this->loadFormSettings($formId);

		if ($fieldEmail = $settings->get('fieldEmail', false))
		{
			$query = $this->db->getQuery(true)
				->select($this->db->quoteName('FieldValue'))
				->from($this->db->quoteName('#__rsform_submission_values'))
				->where($this->db->quoteName('FormId') . ' = ' . $formId)
				->where($this->db->quoteName('SubmissionId') . ' = ' . (int) $args['SubmissionId'])
				->where($this->db->quoteName('FieldName') . ' = ' . $this->db->quote($fieldEmail));
			$this->db->setQuery($query);
			$email = $this->db->loadResult();
		}

		return $email ?? '';
	}

	/**
	 * Check if we need to defer the email for the user.
	 *
	 * @param   array  $args  The form data
	 *
	 * @return  void
	 *
	 * @since   2.2.0
	 * @throws  Exception
	 * @throws  RuntimeException
	 */
	public function rsfp_f_onAfterFormProcess(array $args): void
	{
		if (!$this->canRun())
		{
			return;
		}

		// Get the payment package payment selector if it exists
		if (!array_key_exists('internal', $args) && RSFormProHelper::componentExists($args['formId'], 27))
		{
			return;
		}

		$formId = (int) $args['formId'];

		if (RSFormProHelper::componentExists($formId, $this->newComponents))
		{
			$price    = (float) 0;
			$settings = $this->loadFormSettings($formId);
			$total    = RSFormProHelper::componentExists($formId, 5577);

			if (empty($total))
			{
				throw new InvalidArgumentException(Text::_('PLG_RSFP_JDIDEAL_TOTAL_FIELD_IS_MISSING'));
			}

			$totalDetails     = RSFormProHelper::getComponentProperties($total[0]);
			$singleProduct    = RSFormProHelper::componentExists($formId, 5575);
			$multipleProducts = RSFormProHelper::componentExists($formId, 5576);
			$inputBoxes       = RSFormProHelper::componentExists($formId, 5578);

			// Get the price
			if ($multipleProducts || $inputBoxes || $singleProduct || $total)
			{
				$price = (float) $this->getSubmissionValue($args['SubmissionId'], $totalDetails['componentId']);
			}

			if ($price > 0)
			{
				$itemId = $this->app->input->getInt('Itemid', 0);
				$lang   = substr($this->app->input->get('lang'), 0, 2);

				// Create the feedback URLs
				$uri = Uri::getInstance(Route::_(Uri::root() . 'index.php?option=com_rsform'));
				$uri->setVar('formId', $formId);
				$uri->setVar('task', 'plugin');
				$uri->setVar('plugin_task', 'jdideal.return');
				$uri->setVar('Itemid', $itemId);
				$uri->setVar('lang', $lang);
				$returnUrl = $uri->toString();

				$uri = Uri::getInstance(Route::_(Uri::root() . 'index.php?option=com_rsform'));
				$uri->setVar('formId', $formId);
				$uri->setVar('task', 'plugin');
				$uri->setVar('plugin_task', 'jdideal.notify');
				$uri->setVar('Itemid', $itemId);
				$notifyUrl = $uri->toString();

				// Load the payment provider
				$profileAlias = $this->getProfileAlias($formId);

				// Get the custom order number field
				$orderNumber = $this->getCustomOrderNumber($args);

				// Get the email field
				$email = $this->getEmailField($args);

				// Set some needed data
				$data = [
					'amount'         => $price,
					'order_number'   => $orderNumber,
					'order_id'       => $formId . '.' . $args['SubmissionId'],
					'origin'         => 'rsformpro',
					'return_url'     => $returnUrl,
					'notify_url'     => $notifyUrl,
					'cancel_url'     => '',
					'email'          => $email,
					'payment_method' => '',
					'currency'       => $settings->get('currency', ''),
					'profileAlias'   => $profileAlias,
					'custom_html'    => '',
					'silent'         => false,
				];

				// Show a loading message in case it takes some time
				echo Text::_('PLG_RSFP_JDIDEAL_LOADING');

				// Build the form to redirect to RO Payments
				?>
				<form id="jdideal" action="<?php
				echo Route::_('index.php?option=com_jdidealgateway&view=checkout&Itemid=' . $itemId); ?>" method="post">
					<input type="hidden" name="vars" value="<?php
					echo base64_encode(json_encode($data)); ?>"/>
				</form>
				<script type="text/javascript">
                  document.getElementById('jdideal').submit()
				</script>
				<?php
				$this->app->close();
			}
			elseif ($price === 0.00 && (int) $settings->get('allowEmpty', 0) === 1)
			{
				// Don't do anything to allow an empty price checkout. RSForms will send the emails.
				return;
			}
			else
			{
				$this->app->enqueueMessage(Text::_('PLG_RSFP_JDIDEAL_NO_PRICE_RECEIVED'), 'error');
			}
		}
	}

	/**
	 * Get the value of the field as submitted by the user.
	 *
	 * @param   int  $submissionId  The ID of the submitted entry
	 * @param   int  $componentId   The ID of the component the value was submitted for
	 *
	 * @return  mixed  The return value or null if the query failed.
	 *
	 * @since   2.12.0
	 * @throws  RuntimeException
	 */
	public function getSubmissionValue(int $submissionId, int $componentId)
	{
		$name = $this->getComponentName($componentId);

		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('FieldValue'))
			->from($this->db->quoteName('#__rsform_submission_values'))
			->where($this->db->quoteName('SubmissionId') . ' = ' . $submissionId)
			->where($this->db->quoteName('FieldName') . ' = ' . $this->db->quote($name));
		$this->db->setQuery($query);

		return $this->db->loadResult();
	}

	/**
	 * Load the RO Payments Form now the form has been submitted. This is used for the RO Payments only buttons.
	 * For the payment package see the rsfp_doPayment() method.
	 *
	 * @param   integer  $componentId  The component ID to get the value for
	 *
	 * @return  string The value found in the database
	 *
	 * @since   4.0.0
	 * @throws  RuntimeException
	 * @see     rsfp_doPayment
	 *
	 */
	public function getComponentName(int $componentId): string
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('PropertyValue'))
			->from($this->db->quoteName('#__rsform_properties'))
			->where($this->db->quoteName('ComponentId') . ' = ' . $componentId)
			->where($this->db->quoteName('PropertyName') . ' = ' . $this->db->quote('NAME'));
		$this->db->setQuery($query);

		return (string) $this->db->loadResult();
	}

	/**
	 * Make it possible to translate the values in the email.
	 *
	 * @param   array  $vars  An array with values to be translated.
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function rsfp_onAfterCreatePlaceholders(array $vars): void
	{
		foreach ($vars['values'] as $key => $value)
		{
			if (!strpos($value, ','))
			{
				$vars['values'][$key] = Text::_(nl2br($value));
			}
		}

		if (!in_array('{_STATUS:value}', $vars['placeholders'], true))
		{
			$vars['placeholders'][] = '{_STATUS:caption}';
			$vars['placeholders'][] = '{_STATUS:description}';
			$vars['placeholders'][] = '{_STATUS:name}';
			$vars['placeholders'][] = '{_STATUS:value}';
			$vars['values'][]       = '';
			$vars['values'][]       = '';
			$vars['values'][]       = '';
			$vars['values'][]       = Text::_(
				'PLG_RSFP_JDIDEAL_PAYMENT_STATUS_' . $vars['submission']->values['_STATUS']
			);
		}
	}

	/**
	 * Add the option under Extras when editing the form properties.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function rsfp_bk_onAfterShowFormEditTabsTab(): void
	{
		?>
		<li>
			<?php
			echo HTMLHelper::_(
				'link',
				'javascript: void(0);',
				'<span class="rsficon jdicon-jdideal"></span><span class="inner-text">' . Text::_(
					'PLG_RSFP_JDIDEAL_LABEL'
				) . '</span>'
			);
			?>
		</li>
		<?php
	}

	/**
	 * Add settings to defer sending of emails.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 * @throws  Exception
	 */
	public function rsfp_bk_onAfterShowFormEditTabs(): void
	{
		$formId = $this->app->input->getInt('formId');
		$tables = $this->db->getTableList();
		$table  = $this->db->getPrefix() . 'rsform_jdideal';

		if (!in_array($table, $tables, true))
		{
			return;
		}

		// Load the settings
		$settings = $this->loadFormSettings($formId);

		$form = new Form('ropayments');
		$form->loadFile(__DIR__ . '/configuration.xml');
		$form->bind(['roPaymentsParams' => $settings->toArray()]);

		HTMLHelper::_('formbehavior.chosen');

		?>
		<div id="ropayments" class="form-horizontal">
			<?php
			echo HTMLHelper::_('bootstrap.startTabSet', 'ropayments-config', ['active' => 'ropayments-general']);
			echo HTMLHelper::_(
				'bootstrap.addTab',
				'ropayments-config',
				'ropayments-general',
				Text::_('PLG_RSFP_JDIDEAL_CONFIG_GENERAL')
			);
			echo $form->renderField('profileAlias', 'roPaymentsParams');
			echo $form->renderField('allowEmpty', 'roPaymentsParams');
			echo $form->renderField('redirectRsforms', 'roPaymentsParams');
			echo HTMLHelper::_('bootstrap.endTab');
			echo HTMLHelper::_(
				'bootstrap.addTab',
				'ropayments-config',
				'ropayments-currency',
				Text::_('PLG_RSFP_JDIDEAL_CONFIG_CURRENCY')
			);
			echo $form->renderField('currency', 'roPaymentsParams');
			echo $form->renderField('thousands', 'roPaymentsParams');
			echo $form->renderField('decimal', 'roPaymentsParams');
			echo $form->renderField('numberDecimals', 'roPaymentsParams');
			echo $form->renderField('taxType', 'roPaymentsParams');
			echo $form->renderField('taxValue', 'roPaymentsParams');
			echo HTMLHelper::_('bootstrap.endTab');
			echo HTMLHelper::_(
				'bootstrap.addTab',
				'ropayments-config',
				'ropayments-fields',
				Text::_('PLG_RSFP_JDIDEAL_CONFIG_FIELDS')
			);
			echo $form->renderField('fieldOrderNumber', 'roPaymentsParams');
			echo $form->renderField('fieldName', 'roPaymentsParams');
			echo $form->renderField('fieldEmail', 'roPaymentsParams');
			echo HTMLHelper::_('bootstrap.endTab');
			echo HTMLHelper::_(
				'bootstrap.addTab',
				'ropayments-config',
				'ropayments-emails',
				Text::_('PLG_RSFP_JDIDEAL_CONFIG_MAIL')
			);
			echo $form->renderField('userEmail', 'roPaymentsParams');
			echo $form->renderField('adminEmail', 'roPaymentsParams');
			echo $form->renderField('additionalEmails', 'roPaymentsParams');
			echo $form->renderField('sendEmailOnFailedPayment', 'roPaymentsParams');
			echo $form->renderField('confirmationEmail', 'roPaymentsParams');
			echo '<div class="control-group ro-confirmation-info" data-showon=\'[{"field":"roPaymentsParams[confirmationEmail]","values":["1"],"sign":"=","op":""}]\' style="display: none;">';
			echo '<div class="text-info">' . Text::_('PLG_RSFP_JDIDEAL_CONFIRMATIONHELP') . '</div>';
			echo '</div>';
			echo $form->renderField('confirmationRecipient', 'roPaymentsParams');
			echo $form->renderField('confirmationSubject', 'roPaymentsParams');
			echo $form->renderField('confirmationMessage', 'roPaymentsParams');
			echo HTMLHelper::_('bootstrap.endTab');
			echo HTMLHelper::_('bootstrap.endTabSet');
			?>
		</div>
		<?php
	}

	/**
	 * Get the component name.
	 *
	 * @param   array  $args  The form argument values
	 *
	 * @return  void
	 *
	 * @since   2.12.0
	 * @throws  RuntimeException
	 */
	public function rsfp_beforeUserEmail(array $args): void
	{
		if (!$this->hasJdidealFields($args['form']->FormId))
		{
			return;
		}

		$form      = $args['form'];
		$settings  = $this->loadFormSettings((int) $form->FormId);
		$userEmail = (int) $settings->get('userEmail', 0);

		if ($userEmail === 0)
		{
			return;
		}

		$status    = $this->loadSubmissionValues($args['submissionId']);
		$isValid   = $this->isPaymentValid($args);

		if (!isset($status->FieldValue))
		{
			return;
		}

		/**
		 * Defer sending if
		 * - user email is deferred && the payment is not confirmed (send email only when payment is confirmed)
		 */
		if ($userEmail === 1 && (int) $status->FieldValue === 0)
		{
			$args['userEmail']['to'] = '';
		}

		/**
		 * Defer sending if
		 * - user email is not deferred && the payment is confirmed (don't send the email once again, it has already been sent)
		 */
		if ($userEmail === 0 && (int) $status->FieldValue === 1)
		{
			$args['userEmail']['to'] = '';
		}

		/**
		 * Do not send any emails if the payment has been cancelled or failed otherwise
		 */
		if (!$isValid && (int) $settings->get('sendEmailOnFailedPayment', 0) === 0)
		{
			$args['userEmail']['to'] = '';
		}
	}

	/**
	 * Load the form details.
	 *
	 * @param   int  $submissionId  The submission ID.
	 *
	 * @return  stdClass
	 *
	 * @since   4.0.0
	 * @throws  RuntimeException
	 */
	private function loadSubmissionValues(int $submissionId): stdClass
	{
		$query = $this->db->getQuery(true)
			->select(
				$this->db->quoteName(
					[
						'FieldValue',
						'FieldName',
					]
				)
			)
			->from($this->db->quoteName('#__rsform_submission_values'))
			->where($this->db->quoteName('FieldName') . ' = ' . $this->db->quote('_STATUS'))
			->where($this->db->quoteName('SubmissionId') . ' = ' . $submissionId);
		$this->db->setQuery($query);

		try
		{
			$status = $this->db->loadObject();
		}
		catch (Exception $exception)
		{
			$status = new stdClass;
		}

		return $status;
	}

	/**
	 * Check if a payment is valid.
	 *
	 * @param   array  $args  The form arguments.
	 *
	 * @return  boolean  True if payment is valid | False otherwise.
	 *
	 * @since   4.4.0
	 * @throws  Exception
	 */
	private function isPaymentValid(array $args): bool
	{
		$formId = (int) $args['form']->FormId;

		// Get the profile alias from the form
		$profileAlias = $this->getProfileAlias($formId);

		// Load the helper
		$jdideal = new Gateway($profileAlias);

		// Load the payment details
		$details = $jdideal->getDetails(
			$formId . '.' . $args['submissionId'],
			'order_id',
			false,
			'rsformpro'
		);

		// Let's see if there are any details
		if (!is_object($details))
		{
			return false;
		}

		// Return if payment is valid
		return $jdideal->isValid($details->result);
	}

	/**
	 * Check if we need to defer the email for the administrator.
	 *
	 * @param   array  $args  The form data
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 * @throws  RuntimeException
	 * @throws  Exception
	 */
	public function rsfp_beforeAdminEmail(array $args): void
	{
		// Check if there are any RO Payments fields
		if (!$this->hasJdidealFields($args['form']->FormId))
		{
			return;
		}

		$form       = $args['form'];
		$settings   = $this->loadFormSettings($form->FormId);
		$adminEmail = (int) $settings->get('adminEmail', 0);

		if ($adminEmail === 0)
		{
			return;
		}

		$status  = $this->loadSubmissionValues($args['submissionId']);
		$isValid = $this->isPaymentValid($args);

		if (!isset($status->FieldValue))
		{
			return;
		}

		/**
		 * Defer sending if
		 * - admin email is deferred && the payment is not confirmed (send email only when payment is confirmed)
		 */
		if ($adminEmail === 1 && (int) $status->FieldValue === 0)
		{
			$args['adminEmail']['to'] = '';
		}

		/**
		 * Defer sending if
		 * - admin email is not deferred && the payment is confirmed (don't send the email once again, it has already been sent)
		 */
		if ($adminEmail === 0 && (int) $status->FieldValue === 1)
		{
			$args['adminEmail']['to'] = '';
		}

		/**
		 * Do not send any emails if the payment has been cancelled or failed otherwise
		 */
		if (!$isValid && $settings->get('sendEmailOnFailedPayment') === 0)
		{
			$args['adminEmail']['to'] = '';
		}
	}

	/**
	 * Check if we need to defer the email for the administrator.
	 *
	 * @param   array  $args  The form data
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 * @throws  Exception
	 */
	public function rsfp_beforeAdditionalEmail(array $args): void
	{
		// Check if there are any RO Payments fields
		if (!$this->hasJdidealFields($args['form']->FormId))
		{
			return;
		}

		$form             = $args['form'];
		$settings         = $this->loadFormSettings($form->FormId);
		$additionalEmails = (int) $settings->get('additionalEmails', 0);

		if ($additionalEmails === 0)
		{
			return;
		}

		$status  = $this->loadSubmissionValues($args['submissionId']);
		$isValid = $this->isPaymentValid($args);

		if (!isset($status->FieldValue))
		{
			return;
		}

		/**
		 * Defer sending if
		 * - additional email is deferred && the payment is not confirmed (send email only when payment is confirmed)
		 */
		if ($additionalEmails === 1 && (int) $status->FieldValue === 0)
		{
			$args['additionalEmail']['to'] = '';
		}

		/**
		 * Defer sending if
		 * - additional email is not deferred && the payment is confirmed (don't send the email once again, it has already been sent)
		 */
		if ($additionalEmails === 0 && (int) $status->FieldValue === 1)
		{
			$args['additionalEmail']['to'] = '';
		}

		/**
		 * Do not send any emails if the payment has been cancelled or failed otherwise
		 */
		if (!$isValid && $settings->get('sendEmailOnFailedPayment', 0) === 0)
		{
			$args['additionalEmail']['to'] = '';
		}
	}

	/**
	 * Delete any form settings on form deletion.
	 *
	 * @param   int  $formId  The ID of the form to delete.
	 *
	 * @return  void
	 *
	 * @since   4.0
	 * @throws  RuntimeException
	 */
	public function rsfp_onFormDelete(int $formId): void
	{
		$query = $this->db->getQuery(true)
			->delete($this->db->quoteName('#__rsform_jdideal'))
			->where($this->db->quoteName('form_id') . ' = ' . $formId);
		$this->db->setQuery($query)->execute();
	}

	/**
	 * Backup the settings when the user does a form backup.
	 *
	 * @param   object              $form    The form being backed up.
	 * @param   RSFormProBackupXML  $xml     The XML object.
	 * @param   object              $fields  The form fields.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 * @throws  RuntimeException
	 */
	public function rsfp_onFormBackup($form, $xml, $fields)
	{
		$query = $this->db->getQuery(true)
			->select(
				$this->db->quoteName(
					[
						'params'
					]
				)
			)
			->from($this->db->quoteName('#__rsform_jdideal'))
			->where($this->db->quoteName('form_id') . ' = ' . (int) $form->FormId);
		$this->db->setQuery($query);

		if ($payment = $this->db->loadObject())
		{
			$xml->add('jdideal');

			foreach ($payment as $property => $value)
			{
				$xml->add($property, $value);
			}

			$xml->add('/jdideal');
		}
	}

	/**
	 * Restore the settings when the user restores a form from backup.
	 *
	 * @param   object             $form    The form being backed up.
	 * @param   SimpleXMLIterator  $xml     The XML object.
	 * @param   object             $fields  The form fields.
	 *
	 * @return  boolean  True on success | False on failure.
	 *
	 * @since   4.0.0
	 * @throws  RuntimeException
	 */
	public function rsfp_onFormRestore($form, $xml, $fields): bool
	{
		$tables = $this->db->getTableList();
		$table  = $this->db->getPrefix() . 'rsform_jdideal';

		if (!in_array($table, $tables, true))
		{
			return true;
		}

		if (isset($xml->jdideal))
		{
			$data = [];

			foreach ($xml->jdideal->children() as $property => $value)
			{
				$data[$property] = (string) $value;
			}

			if (array_key_exists('params', $data))
			{
				$settings = $this->loadFormSettings($form->FormId);

				if ($settings->count() > 0)
				{
					$query = $this->db->getQuery(true)
						->update($this->db->quoteName('#__rsform_jdideal'))
						->set($this->db->quoteName('params') . ' = ' . $this->db->quote($data['params']))
						->where($this->db->quoteName('form_id') . ' = ' . (int) $form->FormId);
				}
				else
				{
					$query = $this->db->getQuery(true)
						->insert($this->db->quoteName('#__rsform_jdideal'))
						->columns(array('form_id', 'params'))
						->values($form->FormId . ',' . $this->db->quote($data['params']));
				}

				$this->db->setQuery($query);

				try
				{
					$this->db->execute();
				}
				catch (Exception $exception)
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Empty the table when all forms are deleted.
	 *
	 * @return  void
	 *
	 * @since   4.0
	 * @throws  RuntimeException
	 */
	public function rsfp_bk_onFormRestoreTruncate(): void
	{
		$this->db->truncateTable('#__rsform_jdideal');
	}

	/**
	 * Load any files needed for the form display.
	 *
	 * @param   array  $details  An array of form details.
	 *
	 * @return  void
	 *
	 * @since   4.3.0
	 */
	public function rsfp_bk_onBeforeCreateFrontComponentBody(array $details): void
	{
		if ($details['formId'] > 0)
		{
			// Special handling in an article and other extensions
			$extensions = explode(',', $this->params->get('extensions'));
			array_unshift($extensions, 'com_content');

			if (!$this->setScript
				&& in_array($this->app->input->getCmd('option'), $extensions, true)
			)
			{
				$jsFile = HTMLHelper::_(
					'script',
					'plg_system_rsfpjdideal/rsfpjdideal.js',
					[
						'version'  => 'auto',
						'pathOnly' => true,
						'relative' => true
					]
				);

				$document                    = $this->app->getDocument();
				$document->_scripts[$jsFile] = [
					'type'    => 'text/javascript',
					'options' => [
						'version'       => 'auto',
						'relative'      => true,
						'detectDebug'   => 1,
						'detectBrowser' => 1,
						'framework'     => null,
						'pathOnly'      => null,
					]
				];
				$this->setScript             = true;

				return;
			}

			if (!$this->setScript)
			{
				HTMLHelper::_(
					'script',
					'plg_system_rsfpjdideal/rsfpjdideal.js',
					['version' => 'auto', 'relative' => true]
				);

				$this->setScript = true;
			}
		}
	}

	/**
	 * Copy the settings from the old form to the new form.
	 *
	 * @param   array  $args  The form details.
	 *
	 * @return  void
	 *
	 * @since   4.8.0
	 */
	public function rsfp_bk_onFormCopy(array $args): void
	{
		$formId    = $args['formId'];
		$newFormId = $args['newFormId'];

		// Get the settings of the current form
		$settings = $this->loadFormSettings($formId);

		// Store the settings in the new form
		$data          = new stdClass;
		$data->form_id = $newFormId;
		$data->params  = json_encode($settings);
		$this->db->insertObject('#__rsform_jdideal', $data);
	}

	/**
	 * Add our own submission headers.
	 *
	 * @param   array    $headers  The headers to show
	 * @param   integer  $formId   The form ID the submissions belong to
	 *
	 * @return  void
	 *
	 * @since   4.14
	 */
	public function rsfp_bk_onGetSubmissionHeaders(&$headers, $formId): void
	{
		if ($this->hasJdidealFields($formId))
		{
			$headers[] = Text::_('PLG_RSFP_JDIDEAL_PAYMENT_STATUS');
		}
	}

	/**
	 * Process the submissions by giving a user friendly payment status.
	 *
	 * @param   array  $args  The form submissions.
	 *
	 * @return  void
	 *
	 * @since   4.14
	 */
	public function rsfp_b_onManageSubmissions(array $args): void
	{
		foreach ($args['submissions'] as $submissionId => $submission)
		{
			foreach ($submission['SubmissionValues'] as $fieldName => $value)
			{
				if ($fieldName !== '_STATUS')
				{
					continue;
				}

				$args['submissions'][$submissionId]['SubmissionValues'][Text::_(
					'PLG_RSFP_JDIDEAL_PAYMENT_STATUS'
				)]['Value'] = $value['Value'];
			}
		}
	}

	/**
	 * Add the _STATUS field for the front-end directory view.
	 *
	 * @param   array  $fields  The fields to show in the directory list
	 * @param   int    $formId  The form ID being used
	 *
	 * @return  void
	 *
	 * @since   4.16.0
	 */
	public function rsfp_bk_onGetAllDirectoryFields(&$fields, $formId): void
	{
		if (!$this->hasJdidealFields($formId))
		{
			return;
		}

		$field               = new stdClass;
		$field->FieldName    = '_STATUS';
		$field->FieldId      = '-5575';
		$field->FieldType    = 0;
		$field->FieldCaption = Text::_('PLG_RSFP_JDIDEAL_PAYMENT_STATUS');
		$fields[-5575]       = $field;
	}

	/**
	 * Populate the directory listing with the payment status.
	 *
	 * @param   array  $items   List of items shown to be updated
	 * @param   int    $formId  The form ID being used
	 *
	 * @return  void
	 *
	 * @since   4.16.0
	 */
	public function rsfp_onAfterManageDirectoriesQuery($items, $formId): void
	{
		if (!$this->hasJdidealFields($formId))
		{
			return;
		}

		array_walk(
			$items,
			static function (&$item) {
				$item->_STATUS = Text::_('PLG_RSFP_JDIDEAL_PAYMENT_STATUS_' . $item->_STATUS);
			}
		);
	}
}
