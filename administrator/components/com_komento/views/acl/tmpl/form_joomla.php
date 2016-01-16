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
<div class="adminform-head">
	<table class="adminform">
		<tr>
			<td width="50%">
				<?php if( $this->type == 'usergroup' ) { ?>
				<label for="usergroup"><?php echo JText::_( 'COM_KOMENTO_ACL_SELECT_USERGROUP' ); ?> :</label>
				<?php echo $this->usergroups;
				} ?>

				<!-- replace with <input type="hidden" name="component" value="<?php echo $this->escape( $this->component ); ?>" /> -->
				<label for="component"><?php echo JText::_( 'COM_KOMENTO_ACL_SELECT_COMPONENT' ); ?> :</label>
				<?php echo $this->components; ?>
			</td>
		</tr>
	</table>
</div>

<div class="adminform-body">
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
