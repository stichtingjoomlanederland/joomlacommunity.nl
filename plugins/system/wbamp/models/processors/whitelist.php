<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.4.2.551
 * @date        2016-07-19
 */

defined('_JEXEC') or die();

/**
 * Screen tags and some attributes in a page based on white lists
 * (and a couple of small blacklists)
 *
 */
class WbampModelProcessor_Whitelist
{

	/**
	 * Injected set of rules/configuration for cleaning html
	 *
	 * @var null
	 */
	private $config = null;

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
	public function __construct($config)
	{
		$this->config = $config;
	}

	/**
	 * Strip tags and attributes not on white lis
	 * @return null
	 */
	public function sanitize($dom)
	{
		$body = $dom->getElementsByTagName('body')->item(0);
		$this->cleanContent($body);
		foreach ($this->nodesToDelete as $element)
		{
			$this->removeElement($element);
		}

		return $dom;
	}

	/**
	 * Clean a node and its descendant, storing nodes
	 * that need to be removed on the way
	 *
	 * @param $node
	 */
	private function cleanContent($node)
	{
		if ($node->nodeType !== XML_ELEMENT_NODE)
		{
			return;
		}

		// store element name
		$nodeName = $node->nodeName;

		// if not a whitelisted element, discard, else dig further
		// allow empty tags white list, for testing
		if (empty($this->config->tagsWhiteList) || in_array($nodeName, $this->config->tagsWhiteList))
		{
			// check if tag has all mandatory attr
			$removed = $this->checkTagMandatoryParent($node);
			if ($removed)
			{
				// we can cut through all further processing
				// tag was (will be) removed
				return;
			}

			// check if tag has all mandatory attr
			$removed = $this->checkTagMandatoryAttributes($node);
			if ($removed)
			{
				// we can cut through all further processing
				// tag was (will be) removed
				return;
			}

			// if we have some attributes white list for this element, apply them
			if ($node->hasAttributes())
			{
				$this->cleanAttributes($node);
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
		else
		{
			// tags not on white list, remove
			$this->nodesToDelete[] = $node;
		}
	}

	/**
	 * Verify is a tag complies with parent rules:
	 * whether its direct parent is on the allowed list
	 * or is NOT on the disallowed list
	 *
	 * @param $tag
	 * @return bool true when tag is set to be removed
	 * @throws Exception
	 */
	private function checkTagMandatoryParent($tag)
	{
		$willBeRemoved = false;
		if (array_key_exists($tag->nodeName, $this->config->tagMandatoryParents))
		{
			$elementParent = $tag->parentNode;
			if (!empty($elementParent))
			{
				$willBeRemoved =
					// parent is on disallowed list
					in_array($elementParent->nodeName, $this->config->tagMandatoryParents[$tag->nodeName]['forbidden_parents'])
					// or we have an allowed list, and parent is not on it
					||
					(
						!empty($this->config->tagMandatoryParents[$tag->nodeName]['mandatory_parents'])
						&&
						!in_array($elementParent->nodeName, $this->config->tagMandatoryParents[$tag->nodeName]['mandatory_parents'])
					);
				if ($willBeRemoved)
				{
					$this->nodesToDelete[] = $tag;
				}
			}
		}

		return $willBeRemoved;
	}

	/**
	 * Check if a tag has all mandatory attributes
	 *
	 * @param $tag
	 * @return bool true if the tag was removed, to allow cutting down further processing
	 */
	private function checkTagMandatoryAttributes($tag)
	{
		$willBeRemoved = false;
		if (array_key_exists($tag->nodeName, $this->config->tagMandatoryAttr))
		{
			foreach ($this->config->tagMandatoryAttr[$tag->nodeName] as $attrName => $rule)
			{
				if (!$tag->hasAttribute($attrName))
				{
					// missing attribute
					switch ($rule['action'])
					{
						case 'add':
							$tag->setAttribute($attrName, $rule['add_value']);
							break;
						case 'remove_tag':
							$this->nodesToDelete[] = $tag;
							$willBeRemoved = true;
							break;
						default:
							throw new Exception('Internal error: invalid AttrMandatory rule action ' . $attrName . ' for tag ' . $tag->nodeName);
							break;
					}
				}
			}
		}

		return $willBeRemoved;
	}

	/**
	 * Clean a node attributes, according to white list
	 * and a few special cases, currently hardcoded
	 *
	 * @param $tag
	 */
	private function cleanAttributes($tag)
	{
		$tagName = $tag->nodeName;
		$length = $tag->attributes->length;

		if (substr($tagName, 0, 4) == 'amp-')
		{
			$tagHasWhiteList = false;
		}
		else
		{
			if (array_key_exists($tagName, $this->config->perTagAttrWhiteList))
			{
				$perTagAttrWhiteList = $this->config->perTagAttrWhiteList[$tagName];
			}
			else
			{
				$perTagAttrWhiteList = $this->config->perTagAttrDefaultWhiteList;
			}
			$tagHasWhiteList = true;
		}

		$tagHasBlackList = array_key_exists($tagName, $this->config->perTagAttrBlackList);

		// review all attributes of this tag
		for ($i = $length - 1; $i >= 0; $i--)
		{
			$attribute = $tag->attributes->item($i);
			$attributeName = strtolower($attribute->name);

			// some attr are forbidden entirely, for any tag
			if (in_array($attributeName, $this->config->invalidAttributes))
			{
				$tag->removeAttribute($attributeName);
				continue;
			}

			// some attr are only forbidden on some tags
			if ($tagHasBlackList && in_array($attributeName, $this->config->perTagAttrBlackList[$tagName]))
			{
				$tag->removeAttribute($attributeName);
				continue;
			}

			// if an attribute whitelist is available for this tag
			if ($tagHasWhiteList)
			{
				// if we accept any attribute, move on
				if (in_array('__wbamp_any__', $perTagAttrWhiteList))
				{
					$this->cleanAttribute($tag, $tagName, $attribute, $attributeName);
					continue;
				}

				// if this attribute is a global HTML attribute, and this tag accept that, clean the attribute content and move to next
				if (in_array('__wbamp_global__', $perTagAttrWhiteList) && in_array($attributeName, $this->config->globalAttributes))
				{
					$this->cleanAttribute($tag, $tagName, $attribute, $attributeName);
					continue;
				}

				// if this attribute is a data attribute, and this tag accept that, clean the attribute content and move to next
				if (in_array('__wbamp_data__', $perTagAttrWhiteList) && substr($attributeName, 0, 5) == 'data-')
				{
					$this->cleanAttribute($tag, $tagName, $attribute, $attributeName);
					continue;
				}

				// if this attribute is an aria attribute, and this tag accept that, clean the attribute content and move to next
				if (in_array('__wbamp_aria__', $perTagAttrWhiteList) && substr($attributeName, 0, 5) == 'aria-')
				{
					$this->cleanAttribute($tag, $tagName, $attribute, $attributeName);
					continue;
				}

				// if this attribute is on a specific list of attributes accepted by this tag, clean the attribute content and move to next
				if (!empty($perTagAttrWhiteList) && in_array($attributeName, $perTagAttrWhiteList))
				{
					$this->cleanAttribute($tag, $tagName, $attribute, $attributeName);
					continue;
				}

				// we had a white list of attributes for this tag, but this attribute was not on it, remove it
				$tag->removeAttribute($attributeName);

				continue;
			}

			// not on any white or black list, keep it but clean it first
			$this->cleanAttribute($tag, $tagName, $attribute, $attributeName);
		}
	}

	/**
	 * Clean up an attribute of a given html tag
	 *
	 * @param DOMElement $tag
	 * @param String $tagName
	 * @param DOMAttr $attribute
	 * @param String $attributeName
	 */
	private function cleanAttribute($tag, $tagName, $attribute, $attributeName)
	{
		if ($attributeName == 'rel' && !in_array(strtolower($attribute->value), $this->config->relWhiteList) && !in_array($attribute->value, $this->config->relWhiteList))
		{
			$tag->removeAttribute($attributeName);
			return;
		}

		// special cases, hardcoded

		// remove event listeners
		if (substr($attributeName, 0, 2) == 'on' && $attributeName != 'on')
		{
			$tag->removeAttribute($attributeName);
			return;
		}

		// no js links
		if ($attributeName == 'href')
		{
			$protocol = strtok($attribute->value, ':');
			if (in_array($protocol, $this->config->invalidProtocols))
			{
				$tag->removeAttribute($attributeName);
				return;
			}
		}

		// rules on attributes content
		$descriptor = $tagName . '.' . $attributeName;

		// forced value
		$this->cleanAttrForcedValues($tag, $attribute, $descriptor);

		// mandatory value
		if (!$this->cleanAttrMandatoryValues($tag, $attribute, $descriptor))
		{
			return;
		}

		// forbidden values
		if (!$this->cleanAttrForbiddenValue($tag, $attribute, $descriptor))
		{
			return;
		}
	}

	/**
	 * if present, this attribute must have a specific value, but we can't enforce it
	 * so instead we remove the invalid attribute
	 *
	 * @param $tag
	 * @param $attribute
	 * @param $attributeName
	 * @param $descriptor
	 * @return bool
	 */
	private function cleanAttrMandatoryValues($tag, $attribute, $descriptor)
	{
		if (array_key_exists($descriptor, $this->config->attrMandatoryValue))
		{
			// if present, this attribute must have a specific value, but we can't enforce it
			// so instead we remove the invalid attribute
			if (array_key_exists($attribute->value, $this->config->attrMandatoryValue[$descriptor]['processed_values']))
			{
				switch ($this->config->attrMandatoryValue[$descriptor]['processed_values'][$attribute->value]['action'])
				{
					case 'allow':
						break;
					case 'replace':
						$tag->setAttribute($attribute->name, $this->config->attrMandatoryValue[$descriptor]['processed_values'][$attribute->value]['replace_with']);
						break;
					case 'remove_attr':
						$tag->removeAttribute($attribute->name);
						break;
					case 'remove_tag':
						$this->nodesToDelete[] = $tag;
						break;
					default:
						throw new Exception('Internal error: invalid AttrMandatory rule action ' . $this->config->attrMandatoryValue[$descriptor]['processed_values'][$attribute->value]['action']);
						break;
				}
			}
			else
			{
				// there is no specific rule for that tag/attribute combination
				// apply the "other_values" rules, to decide what to do with it
				switch ($this->config->attrMandatoryValue[$descriptor]['other_values']['action'])
				{
					case 'allow':
						break;
					case 'replace':
						$tag->setAttribute($attribute->name, $this->config->attrMandatoryValue[$descriptor]['other_values']['replace_with']);
						break;
					case 'remove_attr':
						$tag->removeAttribute($attribute->name);
						break;
					case 'remove_tag':
						$this->nodesToDelete[] = $tag;
						break;
					default:
						throw new Exception('Internal error: invalid AttrMandatory rule action ' . $this->config->attrMandatoryValue[$descriptor]['other_values']['action']);
						break;
				}
			}
			return false;
		}

		return true;
	}

	/**
	 * Remove or replace attributes with forbidden values
	 *
	 * @param $tag
	 * @param $attribute
	 * @param $descriptor
	 * @return bool
	 */
	private function cleanAttrForbiddenValue($tag, $attribute, $descriptor)
	{
		if (array_key_exists($descriptor, $this->config->attrForbiddenValue))
		{
			$forbiddenValues = $this->config->attrForbiddenValue[$descriptor];
			if (array_key_exists($attribute->value, $forbiddenValues))
			{
				// if present, this attribute cannot have a specific value
				// if this value is found, we either remove it or replace the value with a new one
				if ($forbiddenValues[$attribute->value]['action'] == 'replace')
				{
					$tag->setAttribute($attribute->name, $forbiddenValues[$attribute->value]['replace_with']);
				}
				else
				{
					$tag->removeAttribute($attribute->name);
				}

				return false;
			}
		}

		return true;
	}

	/**
	 * If present, this attribute must have a specific value, and we enforce it
	 *
	 * @param $tag
	 * @param $attribute
	 * @param $descriptor
	 */
	private function cleanAttrForcedValues($tag, $attribute, $descriptor)
	{
		if (array_key_exists($descriptor, $this->config->attrForcedValue))
		{
			if (!in_array($attribute->value, $this->config->attrForcedValue[$descriptor]['allow']))
			{
				$tag->setAttribute($attribute->name, $this->config->attrForcedValue[$descriptor]['forced_value']);
			}
		}
	}

	/**
	 * Remove an element from the DOM
	 *
	 * @param $element
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
	 * @param $node
	 * @return bool
	 */
	private function isEmptyNode($node)
	{
		return 0 === $node->childNodes->length && empty($node->textContent);
	}

}
