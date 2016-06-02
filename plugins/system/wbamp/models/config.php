<?php

/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.3.1.490
 * @date        2016-05-18
 */

defined('_JEXEC') or die();

class WbampModel_Config
{

	/**
	 * Global white list
	 *
	 * https://github.com/ampproject/amphtml/blob/master/spec/amp-tag-addendum.md
	 *
	 * @var array
	 */
	private $_tagsWhiteList = array(
		/* 'html', 'head','title','link','meta','style' not in body */
		'body', 'article', 'section', 'nav', 'aside', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'header', 'footer', 'address',
		'p', 'hr', 'pre', 'blockquote', 'ol', 'ul', 'li', 'dl', 'dt', 'dd', 'figure', 'figcaption', 'div', 'main',
		'a', 'em', 'strong', 'small', 's', 'cite', 'q', 'dfn', 'abbr', 'data', 'time', 'code', 'var', 'samp', 'kbd', 'sub', 'sup', 'i', 'b', 'u', 'mark', 'ruby', 'rb', 'rt', 'rtc', 'rp', 'bdi', 'bdo', 'span', 'br', 'wbr',
		'ins', 'del',
		'source',
		'svg', 'g', 'path', 'glyph', 'glyphref', 'marker', 'view', 'circle', 'line', 'polygon', 'polyline', 'rect', 'text', 'textpath', 'tref', 'tspan', 'clippath', 'filter', 'lineargradient', 'radialgradient', 'mask', 'pattern', 'vkern', 'hkern', 'defs', 'use', 'symbol', 'desc', 'title',
		'table', 'caption', 'colgroup', 'col', 'tbody', 'thead', 'tfoot', 'tr', 'td', 'th',
		'button',
		'script',
		'noscript',
		'acronym', 'big', 'center', 'dir', 'hgroup', 'listing', 'multicol', 'nextid', 'nobr', 'spacer', 'strike', 'tt', 'xmp',
		'o:p',
		'amp-ad', 'amp-access', 'amp-accordion', 'amp-analytics', 'amp-anim', 'amp-audio', 'amp-brid-player', 'amp-brightcove', 'amp-carousel', 'amp-dailymotion', 'amp-dynamic-css-classes', 'amp-embed', 'amp-facebook', 'amp-fit-text', 'amp-font', 'amp-iframe', 'amp-image-lightbox', 'amp-img', 'amp-instagram', 'amp-install-serviceworker', 'amp-kaltura-player', 'amp-lightbox', 'amp-list', 'amp-mustache', 'amp-pinterest', 'amp-pixel', 'amp-reach-player', 'amp-slides', 'amp-social-share', 'amp-soundcloud', 'amp-springboard-player', 'amp-twitter', 'amp-user-notification', 'amp-video', 'amp-vimeo', 'amp-vine', 'amp-youtube'
	);

	/**
	 * HTML global attributes
	 *
	 * @var array
	 */
	private $_globalAttributes = array(
		'itemid', 'itemprop', 'itemref', 'itemscope', 'itemtype',
		'class', 'id', 'title', 'tabindex', 'dir', 'draggable', 'lang', 'accesskey', 'translate',
		'role',
		'placeholder', 'fallback'
	);

	/**
	 * http://microformats.org/wiki/existing-rel-values
	 * @var array
	 */
	private $_relWhiteList = array(
		'accessibility',
		'alternate',
		'apple-touch-icon',
		'apple-touch-icon-precomposed',
		'apple-touch-startup-image',
		'appendix',
		'archived',
		'archive',
		'archives',
		'attachment',
		'author',
		'bibliography',
		'category',
		'cc:attributionurl',
		'chapter',
		'chrome-webstore-item',
		'cite',
		'code-license',
		'code-repository',
		'colorschememapping',
		'comment',
		'content-license',
		'content-repository',
		'contents',
		'contribution',
		'copyright',
		'designer',
		'directory',
		'discussion',
		'dofollow',
		'edit-time-data',
		'EditURI',
		'endorsed',
		'fan',
		'feed',
		'file-list',
		'follow',
		'footnote',
		'galeria',
		'galeria2',
		'generator',
		'glossary',
		'group',
		'help',
		'home',
		'homepage',
		'hub',
		'icon',
		'image_src',
		'in-reply-to',
		'index',
		'indieauth',
		'introspection',
		'issues',
		'its-rules',
		'jslicense',
		'license',
		'lightbox',
		'made',
		'map',
		'me',
		'member',
		'meta',
		'micropub',
		'microsummary',
		'next',
		'nofollow',
		'noreferrer',
		'ole-object-data',
		'original-source',
		'owns',
		'p3pv1',
		'payment',
		'pgpkey',
		'pingback',
		'prettyphoto',
		'privacy',
		'pronounciation',
		'profile',
		'pronunciation',
		'publisher',
		'prev',
		'previous',
		'referral',
		'related',
		'rendition',
		'replies',
		'reply-to',
		'schema.dc',
		'schema.DCTERMS',
		'search',
		'section',
		'service',
		'service.post',
		'shortcut',
		'shortlink',
		'source',
		'sidebar',
		'sitemap',
		'sponsor',
		'start',
		'status',
		'subsection',
		'syndication',
		'tag',
		'themedata',
		'timesheet',
		'toc',
		'token_endpoint',
		'top',
		'trackback',
		'transformation',
		'unendorsed',
		'up',
		'user',
		'vcalendar-parent',
		'vcalendar-sibling',
		'webmention',
		'wikipedia',
		'wlwmanifest',
		'yandex-tableau-widget'
	);

	private $_perTagAttrDefaultWhiteList = array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__');

	/**
	 * Partial per tag white list for attributes
	 * Complete per tag, but only some tags are included
	 *
	 * @var array
	 */
	private $_perTagAttrWhiteList = array(
		'a' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__', 'href', 'hreflang', 'target', 'rel', 'name', 'download', 'media', 'type', 'border'),
		'audio' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__', 'autoplay', 'controls', 'loop', 'muted', 'preload', 'src'),
		'bdo' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__', 'dir'),
		'blockquote' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__', 'cite'),
		'button' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__', 'disabled', 'name', 'type', 'value'),
		'caption' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__', 'align'),
		'col' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__', 'span'),
		'colgroup' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__', 'align'),
		'del' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__', 'cite', 'datetime'),
		'img' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__', 'alt', 'border', 'height', 'ismap', 'longdesc', 'src', 'srcset', 'width'),
		'ins' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__', 'cite', 'datetime'),
		'li' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__', 'value'),
		'ol' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__', 'reversed', 'start', 'type'),
		'q' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__', 'cite'),
		'script' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__', 'type'),
		'source' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__', 'sizes', 'src', 'type'),
		'svg' => array('__wbamp_any__'),
		'table' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__', 'align', 'border', 'bgcolor', 'cellpadding', 'cellspacing', 'width'),
		'tbody' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__'),
		'td' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__', 'colspan', 'headers', 'rowspan', 'align', 'bgcolor', 'height', 'valign', 'width'),
		'tfoot' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__'),
		'th' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__', 'abbr', 'colspan', 'headers', 'rowspan', 'scope', 'sorted', 'align', 'bgcolor', 'height', 'valign', 'width'),
		'thead' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__'),
		'tr' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__', 'align', 'bgcolor', 'height', 'valign'),
		'video' => array('__wbamp_global__', '__wbamp_data__', '__wbamp_aria__', 'autoplay', 'controls', 'height', 'loop', 'muted', 'poster', 'preload', 'src', 'width'),

		'amp-ad' => array('__wbamp_any__'),
		'amp-anim' => array('__wbamp_any__'),
		'amp-audio' => array('__wbamp_any__'),
		'amp-carousel' => array('__wbamp_any__'),
		'amp-fit-text' => array('__wbamp_any__'),
		'amp-font' => array('__wbamp_any__'),
		'amp-iframe' => array('__wbamp_any__'),
		'amp-image-lightbox' => array('__wbamp_any__'),
		'amp-img' => array('__wbamp_any__'),
		'amp-instagram' => array('__wbamp_any__'),
		'amp-lightbox' => array('__wbamp_any__'),
		'amp-pixel' => array('__wbamp_any__'),
		'amp-twitter' => array('__wbamp_any__'),
		'amp-vine' => array('__wbamp_any__'),
		'amp-video' => array('__wbamp_any__'),
		'amp-youtube' => array('__wbamp_any__'),
	);

	/**
	 * Attributes that must be removed, but only
	 * on some tags
	 *
	 * @var array
	 */
	private $_perTagAttrBlackList = array(
		'table' => array('width', 'height'),
		'thead' => array('width', 'height'),
		'tbody' => array('width', 'height'),
		'tfoot' => array('width', 'height'),
		'th' => array('width', 'height'),
		'tr' => array('width', 'height'),
		'td' => array('width', 'height'),
		'article' => array('itemtype'),
		'section' => array('itemtype'),
		'aside' => array('itemtype')
	);

	/**
	 * Invalid protocols for href
	 * Attribute to be removed
	 *
	 * @var array
	 */
	private $_invalidProtocols = array(
		'javascript'
	);

	/**
	 * Globally invalid attributes,
	 * Attribute to be removed
	 *
	 * @var array
	 */
	private $_invalidAttributes = array(
		'style'
	);

	/**
	 * Some tags are allowed only within others
	 * Currently only checking direct parent
	 * @var array
	 */
	private $_tagMandatoryParents = array(
		'script' => array
		(
			'forbidden_parents' => array(),
			'mandatory_parents' => array('amp-analytics', 'amp-social-share')
		)
	);
	/**
	 * Some tags may be required to have one or more
	 * attributes. They can either be removed if
	 * an attribute is missing, or the attr can
	 * be added with a default value
	 *
	 * @var array
	 */
	private $_tagMandatoryAttr = array(
		'script' => array
		(
			'type' => array(
				'action' => 'remove_tag', // add | remove_tag
				'add_value' => ''
			)
		)
	);

	/**
	 * Attribute is valid but must have specific values
	 * Attribute value is enforced
	 *
	 * @var array
	 */
	private $_attrForcedValue = array(
		'a.target' => array
		(
			'allow' => array('_blank', '_self'),
			'forced_value' => '_blank'
		)
	);

	/**
	 * Attribute is valid but must have a specific value
	 * Attribute is removed if incorrect value
	 *
	 * @TODO: remove $_attrForcedValue rules, which can now
	 * be expressed using $_attrMandatoryValue
	 *
	 * @var array
	 */
	private $_attrMandatoryValue = array(
		'script.type' => array
		(
			'processed_values' => array(
				'application/ld+json' => array(
					'action' => 'allow', // allow | replace | remove_attr | remove_tag
					'replace_with' => ''
				),
				'application/json' => array(
					'action' => 'allow', // allow | replace | remove_attr | remove_tag
					'replace_with' => ''
				)
			),
			'other_values' => array(
				'action' => 'remove_tag', // allow | replace | remove_attr | remove_tag
				'replace_with' => ''
			)
		),
		'a.type' => array
		(
			'processed_values' => array(
				'text/html' => array(
					'action' => 'allow', // allow | replace | remove_attr | remove_tag
					'replace_with' => ''
				)
			),
			'other_values' => array(
				'action' => 'remove_attr', // allow | replace | remove_attr | remove_tag
				'replace_with' => ''
			)
		)
	);

	private $_attrForbiddenValue = array(
		'div.itemtype' => array(
			'http://schema.org/Article' => array(
				'action' => 'remove', // replace | remove
				'replace_with' => ''
			),
			'http://schema.org/NewsArticle' => array(
				'action' => 'remove', // replace | remove
				'replace_with' => ''
			),
			'http://schema.org/BlogPosting' => array(
				'action' => 'remove', // replace | remove
				'replace_with' => ''
			),
			'https://schema.org/Article' => array(
				'action' => 'remove', // replace | remove
				'replace_with' => ''
			),
			'https://schema.org/NewsArticle' => array(
				'action' => 'remove', // replace | remove
				'replace_with' => ''
			),
			'https://schema.org/BlogPosting' => array(
				'action' => 'remove', // replace | remove
				'replace_with' => ''
			)
		)
	);

	/**
	 * List of article types used
	 * as default values for documents
	 * Not used to whitelist, to allow
	 * for user customization
	 *
	 * @var array
	 */
	private $_documentTypes = array(
		'news' => 'NewsArticle',
		'blog' => 'BlogPosting'
	);

	/**
	 * WIdth and height required for a publisher logo
	 *
	 * @var array
	 */
	private $_publisherLogoSize = array(
		'width' => 600,
		'height' => 60
	);

	/**
	 * Minimal width for a page image
	 *
	 * @var int
	 */
	private $_pageImageMinWidth = 696;

	/**
	 * Magic method to fetch a config value directly
	 * or possibly through remote configuration
	 *
	 * @param $name
	 * @return mixed
	 */
	public function __get($name)
	{
		switch ($name)
		{
			// remote value will overwrite hardcoded value
			default:
				$prop = '_' . $name;
				return $this->$prop;
				break;
		}
	}

	/**
	 * Magic method to find if a config value
	 * exists in this config object
	 *
	 * @param $name
	 * @return mixed
	 */
	public function __isset($name)
	{
		$prop = '_' . $name;
		return isset($this->$prop);
	}

	/**
	 * Magic method to override items
	 * Used for testing only
	 *
	 * @param $name
	 * @param $value
	 * @return $this
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
			// remote value will overwrite hardcoded value
			default:
				$prop = '_' . $name;
				$this->$prop = $value;
				break;
		}

		return $this;
	}
}

