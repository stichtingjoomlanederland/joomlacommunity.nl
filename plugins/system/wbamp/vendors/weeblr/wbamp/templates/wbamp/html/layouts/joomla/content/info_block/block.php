<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

$createdDate = '<time datetime="' . JHtml::_('date', $displayData['item']->created, 'c') . '" itemprop="dateCreated">'
	. JHtml::_('date', $displayData['item']->created, JText::_('DATE_FORMAT_LC3')) . '</time>';

$author = $displayData['item']->created_by_alias ? $displayData['item']->created_by_alias : $displayData['item']->author;
$author = '<span class="wbamp-author" itemprop="name">' . $author . '</span>';

if (!empty($displayData['item']->contact_link) && $displayData['params']->get('link_author') == true)
{
	$author = JHtml::_('link', $displayData['item']->contact_link, $author, array('itemprop' => 'url'));
}
$createdBy = '<span class="wbamp-created-by" itemprop="author" itemscope itemtype="http://schema.org/Person">' . $author . '</span>';

?>

<div class="wbamp-info-block">
	<p><?php echo JText::sprintf('TPL_WBAMP_WRITTEN_BY_ON', $createdBy, $createdDate); ?></p>
</div>
