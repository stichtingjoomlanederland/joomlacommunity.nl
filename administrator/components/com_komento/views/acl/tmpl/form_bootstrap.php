<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access'); ?>
<div class="row-fluid">
	<div class="span2">
		<div id="sidebar">
			<h4 class="page-header"><?php echo JText::_( 'COM_KOMENTO_FILTER' ); ?>:</h4>

			<div class="filter-select hidden-phone">

				<?php echo $this->type == 'usergroup' ? $this->usergroups : ''; ?>
				<hr class="hr-condensed" />

				<?php echo $this->components; ?>
				<hr class="hr-condensed" />
			</div>
		</div>
	</div>

	<div class="span10 adminform-body">
		<table class="aclTable">
			<tr>
			<?php foreach( $this->rulesets as $section => $rules ) { ?>
			<td valign="top" width="50%">
				<fieldset class="adminform">
					<legend><?php echo JText::_( 'COM_KOMENTO_ACL_SECTION_' . strtoupper($section) ); ?></legend>
					<table class="admintable" cellspacing="1">
						<?php foreach( $rules as $key => $value ) { ?>
						<tr>
							<td width="300" class="key">
								<span><?php echo JText::_( 'COM_KOMENTO_ACL_RULE_' . strtoupper( $key ) ); ?></span>
							</td>
							<td valign="top">
								<div class="has-tip <?php echo $key; ?>">
									<div class="tip"><i></i><?php echo JText::_( 'COM_KOMENTO_ACL_RULE_' . strtoupper( $key ) . '_DESC' ); ?></div>
									<?php echo $this->renderCheckbox( $key, $value ); ?>
								</div>
							</td>
						</tr>
						<?php } ?>
					</table>
				</fieldset>
			</td>
			<?php } ?>
		</tr>
		</table>
	</div>

</div>
