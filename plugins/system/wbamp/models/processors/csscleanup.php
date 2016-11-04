<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.6.0.607
 * @date        2016-10-31
 */

defined('_JEXEC') or die();

/**
 * Remove elements that have CSS classes or id in a user defined list
 *
 */
class WbampModelProcessor_Csscleanup
{

	/**
	 * Injected set of rules/configuration for cleaning html
	 *
	 * @var null
	 */
	private $config = null;

	/**
	 * Registry with user defined params
	 *
	 * @var JRegistry null
	 */
	private $params = null;

	/**
	 *  List of CSS classes that should cause an element to be deleted
	 *
	 * @var array
	 */
	private $forbiddenClasses = array();

	/**
	 * List of CSS ids that should cause an element to be deleted
	 * @var array
	 */
	private $forbiddenIds = array();

	/**
	 * Temp list of items to delete from the DOM
	 * @var array
	 */
	private $nodesToDelete = array();

	/**
	 * Stores rules set
	 *
	 * @param WbampModel_Config $rules
	 */
	public function __construct($config, $params)
	{
		$this->config = $config;
		$this->params = $params;
		$this->getForbiddenElements();
	}

	/**
	 * Strip elements with ids or class on a user-defined list
	 *
	 * @return null
	 */
	public function sanitize(& $rawContent, $dom)
	{
		// bail out if no classes listed
		if (empty($this->forbiddenClasses))
		{
			return $dom;
		}

		// break down content, and process each element
		$body = $dom->getElementsByTagName('body')->item(0);
		$this->cleanContent($body);

		// delete what needs to be deleted
		$modified = count($this->nodesToDelete) > 0;
		foreach ($this->nodesToDelete as $element)
		{
			$this->removeElement($element);
		}

		if ($modified)
		{
			$rawContent = WbampHelper_Dom::fromDom($dom);
		}
		return $dom;
	}

	/**
	 * Clean up user entry for a list of classes or ids
	 * store them in object variable
	 */
	private function getForbiddenElements()
	{
		$classes = $this->params->get('cleanup_css_classes', '');
		$classes = ShlSystem_Strings::stringToCleanedArray($classes, "\n");
		foreach ($classes as $class)
		{
			$class = preg_replace('/\s+/', ' ', $class);
			$this->forbiddenClasses[] = explode(' ', $class);
		}
		$ids = $this->params->get('cleanup_css_ids', '');
		$this->forbiddenIds = ShlSystem_Strings::stringToCleanedArray($ids, "\n");
	}

	/**
	 * Clean a node and its descendants, storing nodes
	 * that need to be removed on the way
	 *
	 * @param DOMElement $node
	 */
	private function cleanContent($node)
	{
		if ($node->nodeType !== XML_ELEMENT_NODE)
		{
			return;
		}

		// does the element has a class attr?
		if ($node->hasAttribute('class'))
		{
			// if match, put on the remove list
			if ($this->shouldRemoveByClass($node))
			{
				$this->nodesToDelete[] = $node;
			}
		}

		// does the element has an id?
		if ($node->hasAttribute('id'))
		{
			// if match, put on the remove list
			if ($this->shouldRemoveById($node))
			{
				$this->nodesToDelete[] = $node;
			}
		}

		// then process children
		foreach ($node->childNodes as $childNode)
		{
			if ($childNode->nodeName != '#text')
			{
				$this->cleanContent($childNode);
			}
		}
	}

	/**
	 * Decides whether the CSS classes of an element
	 * match our user defined list
	 *
	 * @param DOMElement $node
	 */
	private function shouldRemoveByClass($node)
	{
		$shouldRemove = false;

		$nodeClassessString = $node->getAttribute('class');
		$nodeClasses = explode(' ', $nodeClassessString);
		foreach ($this->forbiddenClasses as $forbiddenClass)
		{
			$intersect = array_intersect($forbiddenClass, $nodeClasses);
			$shouldRemove = count($intersect) >= count($forbiddenClass);
			if ($shouldRemove)
			{
				break;
			}
		}

		return $shouldRemove;
	}

	/**
	 * Decides whether the element id match our list
	 *
	 * @param DOMElement $node
	 * @return bool
	 */
	private function shouldRemoveById($node)
	{
		$nodeId = $node->getAttribute('id');

		// too simple: classes need to be exactly equal to the target
		$shouldRemove = in_array($nodeId, $this->forbiddenIds);

		return $shouldRemove;
	}

	/**
	 * Remove an element from the DOM
	 *
	 * @param DOMElement $element
	 */
	private function removeElement($element)
	{
		$elementParent = $element->parentNode;
		if (!empty($elementParent))
		{
			$elementParent->removeChild($element);
			// also remove the parent, if it just become empty
			if ('body' !== $elementParent->nodeName && $this->isEmptyNode($elementParent))
			{
				$elementParent->parentNode->removeChild($elementParent);
			}
		}
	}

	/**
	 * Test if a DOM element is empty: content and children
	 *
	 * @param DOMElement $node
	 * @return bool
	 */
	private function isEmptyNode($node)
	{
		return 0 === $node->childNodes->length && empty($node->textContent);
	}

}
