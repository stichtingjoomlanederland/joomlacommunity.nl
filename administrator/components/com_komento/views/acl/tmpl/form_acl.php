<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

defined('_JEXEC') or die('Restricted access'); ?>

<div class="filter-bar">
	<div class="row-table">
		<div class="filter-search form-inline col-cell">
			<?php echo $this->type == 'usergroup' ? $this->usergroups : ''; ?>
			<?php echo $this->components; ?>
		</div>
	</div>
</div>

<div class="content col-cell">
	<div class="grids clearfix" style="margin-top: 20px;">
		<?php foreach( $this->rulesets as $section => $rules ) { ?>
		<div class="grid">
			<fieldset class="panel">
				<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_ACL_SECTION_' . strtoupper($section) ); ?></div>
				<div class="panel-body">
					<table class="admintable table table-options" cellspacing="1">
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
				</div>
			</fieldset>
		</div>
		<?php } ?>
	</div>
</div>

