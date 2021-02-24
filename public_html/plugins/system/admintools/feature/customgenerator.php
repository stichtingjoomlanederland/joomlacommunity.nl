<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Joomla\CMS\Factory;

defined('_JEXEC') || die;

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
		if (!$this->container->platform->isFrontend())
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

		$buffer = $this->app->getBody();

		$buffer = preg_replace('#<generator uri(.*)/generator>#iU', '<generator>' . $generator . '</generator>', $buffer);

		$this->app->setBody($buffer);
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

		$document = Factory::getDocument();

		if (!method_exists($document, 'setGenerator'))
		{
			return;
		}

		$document->setGenerator($generator);
	}
}
