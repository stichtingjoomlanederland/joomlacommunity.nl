<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

HTMLHelper::_('behavior.core');
HTMLHelper::_('formbehavior.chosen');
HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
//HTMLHelper::stylesheet('com_finder/finder.css', false, true, false);
?>

<div class="finder<?php echo $this->pageclass_sfx; ?>"><?php
    if ($this->params->get('show_page_heading')) : ?>
        <h1><?php
			if ($this->escape($this->params->get('page_heading'))) :
				echo $this->escape($this->params->get('page_heading'));
			else :
				echo $this->escape($this->params->get('page_title'));
			endif; ?></h1><?php
    endif;

	if ($this->params->get('show_search_form', 1)) : ?>
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="well" id="search-form">
					<?php echo $this->loadTemplate('form'); ?>
				</div>
			</div>
		</div><?php
    endif;

	if ($this->query->search !== true): ?>
        <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div id="search-results" class="well">
				<p><?php echo Text::_('COM_FINDER_START_SEARCH_LABEL'); ?></p>
            </div>
        </div>
        </div><?php
	endif;

	// Load the search results layout if we are performing a search.
	if ($this->query->search === true): ?>
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div id="search-results" class="well">
					<?php echo $this->loadTemplate('results'); ?>
				</div>
			</div>
		</div><?php
    endif; ?>
</div>
