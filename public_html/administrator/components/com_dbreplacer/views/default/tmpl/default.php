<?php
/**
 * @package         DB Replacer
 * @version         6.3.8PRO
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2020 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Uri\Uri as JUri;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\License as RL_License;
use RegularLabs\Library\ShowOn as RL_ShowOn;
use RegularLabs\Library\StringHelper as RL_String;
use RegularLabs\Library\Version as RL_Version;

RL_Document::loadMainDependencies();

/* SCRIPTS */
$alert = "RLDBReplacer.protectSpaces();form.task.value = 'replace';form.submit();";
if ($this->config->show_alert)
{
	$alert = "if ( confirm( '" . str_replace(['<br>', "\n", "'"], ['\n', '\n', "\\'"], JText::_('DBR_ARE_YOU_REALLY_SURE')) . "' ) ) {" . $alert . "}";
}
$alert  = "if ( confirm( '" . str_replace(['<br>', "\n", "'"], ['\n', '\n', "\\'"], JText::_('RL_ARE_YOU_SURE')) . "' ) ) {" . $alert . "}";
$script = "
	function submitform( task )
	{
		var form = document.adminForm;
		try {
			form.onsubmit();
			}
		catch( e ) {}
		var form = document.adminForm;
		" . $alert . "
	}
	var DBR_root = '" . JUri::root() . "';
	var DBR_INVALID_QUERY = '" . addslashes(JText::_('DBR_INVALID_QUERY')) . "';
";
RL_Document::scriptDeclaration($script);
RL_Document::script('dbreplacer/script.min.js', '6.3.8.p');

// Version check

if ($this->config->show_update_notification)
{
	echo RL_Version::getMessage('DBREPLACER');
}

$search  = JFactory::getApplication()->input->get('search', '', 'RAW');
$replace = JFactory::getApplication()->input->get('replace', '', 'RAW');
$search  = str_replace('||space||', ' ', $search);
$replace = str_replace('||space||', ' ', $replace);

$class = 'pro';
?>
	<div class="dbr">
		<form action="<?php echo $this->request_url; ?>" method="post"
		      name="adminForm" id="adminForm" class="<?php echo $class; ?>">
			<input type="hidden" name="controller" value="default">
			<input type="hidden" name="task" value="">

			<div class="row-fluid">
				<div class="span4">
					<div class="well well-small tables">
						<h4><?php echo RL_String::html_entity_decoder(JText::_('DBR_TABLE')); ?></h4>
						<div id="dbr_tables"><?php echo $this->tables; ?></div>
					</div>

					<div class="well well-small columns">
						<h4><?php echo RL_String::html_entity_decoder(JText::_('DBR_COLUMNS')); ?></h4>
						<div id="dbr_columns">
							<input type="hidden" name="columns"
							       value="<?php echo implode(',', JFactory::getApplication()->input->get('columns', [0], 'array')); ?>"
							       class="element">
						</div>
					</div>
				</div>

				<div class="span8">
					<div class="well well-small where">
						<h4>
							<?php echo RL_String::html_entity_decoder(JText::_('DBR_WHERE')); ?>
							<button class="btn btn-link btn-mini pull-right clear-button"
							        title="<?php echo JText::_('JLIB_FORM_BUTTON_CLEAR'); ?>"
							        onclick="RLDBReplacer.clearWhere();return false;">
								<?php echo JText::_('JLIB_FORM_BUTTON_CLEAR'); ?>
							</button>
						</h4>

						<?php echo JText::_('DBR_WHERE_DESC'); ?><br>

						<textarea name="where" class="element" cols="30"
						          rows="3"><?php echo JFactory::getApplication()->input->get('where', '', 'RAW'); ?></textarea>
					</div>

					<div class="well well-small search">
						<h4>
							<?php echo RL_String::html_entity_decoder(JText::_('DBR_SEARCH')); ?>
							<button class="btn btn-link btn-micro pull-right clear-button"
							        title="<?php echo JText::_('JLIB_FORM_BUTTON_CLEAR'); ?>"
							        onclick="RLDBReplacer.clearSearch();return false;">
								<?php echo JText::_('JLIB_FORM_BUTTON_CLEAR'); ?>
							</button>
						</h4>

						<div style="clear:both;margin-bottom: 5px;">
							* = <?php echo JText::_('DBR_ALL'); ?> &nbsp; &nbsp;
							NULL = <?php echo JText::_('DBR_NULL'); ?>
						</div>

						<textarea name="search" class="element" cols="30" rows="3"><?php echo htmlentities($search); ?></textarea>

						<div class="row-fluid">
							<div class="span4">
								<label for="dbr_case" class="checkbox">
									<input type="checkbox" value="1" name="case" id="dbr_case"
									       class="element" <?php echo JFactory::getApplication()->input->getInt('case', 0) ? 'checked="checked"' : ''; ?>>
									<?php echo JText::_('DBR_CASE_SENSITIVE'); ?>
								</label>
							</div>
							<div class="span4">
								<label for="dbr_regex" class="checkbox">
									<input type="checkbox" value="1" name="regex" id="dbr_regex"
									       class="element" <?php echo JFactory::getApplication()->input->getInt('regex', 0) ? 'checked="checked"' : ''; ?>>
									<?php echo JText::_('DBR_REGULAR_EXPRESSION'); ?>
								</label>
							</div>
							<div class="span4">
								<?php echo RL_ShowOn::open('regex:1'); ?>
								<label for="dbr_utf8" class="checkbox">
									<input type="checkbox" value="1" name="utf8" id="dbr_utf8"
									       class="element" <?php echo JFactory::getApplication()->input->getInt('utf8', 0) ? 'checked="checked"' : ''; ?>>
									<?php echo JText::_('RL_UTF8'); ?>
								</label>
								<?php echo RL_ShowOn::close(); ?>
							</div>
						</div>
					</div>

					<div class="well well-small replace">
						<h4>
							<?php echo RL_String::html_entity_decoder(JText::_('DBR_REPLACE')); ?>
							<button class="btn btn-link btn-micro pull-right clear-button"
							        title="<?php echo JText::_('JLIB_FORM_BUTTON_CLEAR'); ?>"
							        onclick="RLDBReplacer.clearReplace();return false;">
								<?php echo JText::_('JLIB_FORM_BUTTON_CLEAR'); ?>
							</button>
						</h4>

						<textarea name="replace" class="element" cols="30" rows="3"><?php echo htmlentities($replace); ?></textarea>
					</div>

					<div class="btn-group" id="dbr_search">
						<a onclick="RLDBReplacer.updateRows();" class="btn btn-default">
							<span class="icon-search"></span> <?php echo JText::_('DBR_SEARCH'); ?>
						</a>
					</div>

					<div class="btn-group" id="dbr_submit">
						<a onclick="return false;" class="btn btn-success">
							<span class="icon-shuffle"></span> <?php echo JText::_('DBR_REPLACE'); ?>
						</a>
					</div>
				</div>
			</div>

			<fieldset class="adminform">
				<legend><?php echo RL_String::html_entity_decoder(JText::_('DBR_PREVIEW')); ?></legend>
				<div id="dbr_rows"></div>
			</fieldset>

		</form>
	</div>
<?php

// Copyright
echo RL_Version::getFooter('DBREPLACER', $this->config->show_copyright);
