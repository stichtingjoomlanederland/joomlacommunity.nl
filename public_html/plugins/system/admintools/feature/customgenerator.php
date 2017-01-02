<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureCustomgenerator extends AtsystemFeatureAbstract
{
	protected $loadOrder = 700;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		if (!$this->helper->isFrontend())
		{
			return false;
		}

		return ($this->cparams->getValue('custgenerator', 0) != 0);
	}

	/**
	 * Cloak the generator meta tag in feeds. This method deals with the hardcoded Joomla! reference. Yeah, I know,
	 * hardcoded?
	 */
	public function onAfterRender()
	{
		if ($this->input->getCmd('format', 'html') != 'feed')
		{
			return;
		}

		$generator = $this->cparams->getValue('generator', '');

		if (empty($generator))
		{
			$generator = 'MYOB';
		}

		if (method_exists($this->app, 'getBody'))
		{
			$buffer = $this->app->getBody();
		}
		else
		{
			$buffer = JResponse::getBody();
		}

		$buffer = preg_replace('#<generator uri(.*)/generator>#iU', '<generator>' . $generator . '</generator>', $buffer);

		if (method_exists($this->app, 'setBody'))
		{
			$this->app->setBody($buffer);
		}
		else
		{
			JResponse::setBody($buffer);
		}
	}

	/**
	 * Override the generator
	 */
	public function onAfterDispatch()
	{
		$generator = $this->cparams->getValue('generator', 'MYOB');

		// Mind Your Own Business
		if (empty($generator))
		{
			$generator = 'MYOB';
		}

		$document = JFactory::getDocument();

		if (!method_exists($document, 'setGenerator'))
		{
			return;
		}

		$document->setGenerator($generator);
	}
}