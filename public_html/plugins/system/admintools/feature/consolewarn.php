<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2017 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureConsolewarn extends AtsystemFeatureAbstract
{
	protected $loadOrder = 999;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return ($this->cparams->getValue('consolewarn', 0) == 1);
	}

	/**
	 * Inject some Javascript to display a warning inside browser console
	 */
	public function onAfterInitialise()
	{
		// There's nothing to steal if you're a guest
		if (JFactory::getUser()->guest)
		{
			return;
		}

		$document = JFactory::getDocument();

		// Only work with HTML documents
		if ($document->getType() != 'html')
		{
			return;
		}

		$tmpl = $this->input->getCmd('tmpl', '');

		// We have some forced template? Better stop here
		if ($tmpl != '')
		{
			return;
		}

		$this->parentPlugin->loadLanguage('com_admintools');

		$warn_title = JText::_('COM_ADMINTOOLS_CONSOLEWARN_TITLE', true);
		$body1 = JText::_('COM_ADMINTOOLS_CONSOLEWARN_BODY1', true);
		$body2 = JText::_('COM_ADMINTOOLS_CONSOLEWARN_BODY2', true);

		// Guess what? Coloured background works everywhere EXCEPT IE
		$js = <<<JS
// Internet Explorer 6-11
var isIE = /*@cc_on!@*/false || !!document.documentMode;
// Edge 20+
var isEdge = !isIE && !!window.StyleMedia;

if (!isIE && !isEdge)
{
    console.log('%c $warn_title ', 'font-size: 36px; color: red; font-weight: bold;');
    console.log('%c $body1 ', 'font-size: 14px;');
    console.log('%c $body2 ', 'font-size: 14px;');
}
JS;

		$document->addScriptDeclaration($js);
	}
} 