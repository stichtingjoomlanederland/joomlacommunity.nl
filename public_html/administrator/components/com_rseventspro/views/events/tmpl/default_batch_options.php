<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<p><?php echo JText::_('COM_RSEVENTSPRO_BATCH_OPTIONS_INFO'); ?></p>
<div class="row-fluid">
	<p style="text-align: center;">
		<input type="checkbox" name="batch[enable_options]" id="enable_options" value="1" />
		<label for="enable_options" class="checkbox inline"><b><?php echo JText::_('COM_RSEVENTSPRO_ENABLE_BATCH_OPTIONS'); ?></b></label>
	</p>
</div>

<div class="row-fluid">
	<fieldset class="span4 rswidth-30 rsfltlft" id="sharing">
		<legend><?php echo JText::_('COM_RSEVENTSPRO_SHARING_OPTIONS'); ?></legend>
		<p>
			<input type="checkbox" name="sharing_all" id="sharing_all" value="1" onclick="rsepro_select_all('sharing_all','sharing')" />
			<label for="sharing_all" class="checkbox inline"><b><?php echo JText::_('COM_RSEVENTSPRO_SELECT_DESELECT_ALL'); ?></b></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][enable_rating]" id="enable_rating" value="1" />
			<label for="enable_rating" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_ENABLE_EVENT_RATING'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][enable_fb_like]" id="enable_facebook_like" value="1" />
			<label for="enable_facebook_like" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_ENABLE_FACEBOOK_LIKE'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][enable_twitter]" id="enable_twitter" value="1" />
			<label for="enable_twitter" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_ENABLE_TWITTER'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][enable_gplus]" id="enable_gplus" value="1" />
			<label for="enable_gplus" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_ENABLE_GOOGLEPLUS'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][enable_linkedin]" id="enable_linkedin" value="1" />
			<label for="enable_linkedin" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_ENABLE_LINKEDIN'); ?></label>
		</p>
	</fieldset>
	
	<fieldset class="span4 rswidth-30 rsfltlft" id="details">
		<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_OPTIONS_DETAIL'); ?></legend>
		<p>
			<input type="checkbox" name="details_all" id="details_all" value="1" onclick="rsepro_select_all('details_all','details')" />
			<label for="details_all" class="checkbox inline"><b><?php echo JText::_('COM_RSEVENTSPRO_SELECT_DESELECT_ALL'); ?></b></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][start_date]" id="start_date" value="1" />
			<label for="start_date" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_START_DATE'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][start_time]" id="start_time" value="1" />
			<label for="start_time" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_START_TIME'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][end_date]" id="end_date" value="1" />
			<label for="end_date" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_END_DATE'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][end_time]" id="end_time" value="1" />
			<label for="end_time" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_END_TIME'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][show_description]" id="show_description" value="1" />
			<label for="show_description" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_DESCRIPTION'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][show_location]" id="show_location" value="1" />
			<label for="show_location" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_LOCATION'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][show_categories]" id="show_categories" value="1" />
			<label for="show_categories" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_CATEGORIES'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][show_tags]" id="show_tags" value="1" />
			<label for="show_tags" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_TAGS'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][show_files]" id="show_files" value="1" />
			<label for="show_files" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_FILES'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][show_contact]" id="show_contact" value="1" />
			<label for="show_contact" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_CONTACT'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][show_map]" id="show_map" value="1" />
			<label for="show_map" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_MAP'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][show_export]" id="show_export" value="1" />
			<label for="show_export" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_EXPORT'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][show_invite]" id="show_invite" value="1" />
			<label for="show_invite" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_INVITE'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][show_postedby]" id="show_postedby" value="1" />
			<label for="show_postedby" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_POSTEDBY'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][show_repeats]" id="show_repeats" value="1" />
			<label for="show_repeats" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_REPEATS'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][show_hits]" id="show_hits" value="1" />
			<label for="show_hits" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_HITS'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][show_print]" id="show_print" value="1" />
			<label for="show_print" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_PRINT'); ?></label>
		</p>
	</fieldset>
	<fieldset class="span4 rswidth-30 rsfltlft" id="list">
		<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_OPTIONS_LISTINGS'); ?></legend>
		<p>
			<input type="checkbox" name="list_all" id="list_all" value="1" onclick="rsepro_select_all('list_all','list')" />
			<label for="list_all" class="checkbox inline"><b><?php echo JText::_('COM_RSEVENTSPRO_SELECT_DESELECT_ALL'); ?></b></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][start_date_list]" id="start_date_list" value="1" />
			<label for="start_date_list" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_START_DATE'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][start_time_list]" id="start_time_list" value="1" />
			<label for="start_time_list" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_START_TIME'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][end_date_list]" id="end_date_list" value="1" />
			<label for="end_date_list" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_END_DATE'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][end_time_list]" id="end_time_list" value="1" />
			<label for="end_time_list" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_END_TIME'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][show_location_list]" id="show_location_list" value="1" />
			<label for="show_location_list" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_LOCATION'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][show_categories_list]" id="show_categories_list" value="1" />
			<label for="show_categories_list" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_CATEGORIES'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][show_tags_list]" id="show_tags_list" value="1" />
			<label for="show_tags_list" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_TAGS'); ?></label>
		</p>
		<p>
			<input type="checkbox" name="batch[options][show_icon_list]" id="show_icon_list" value="1" />
			<label for="show_icon_list" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_ICON'); ?></label>
		</p>
	</fieldset>
</div>