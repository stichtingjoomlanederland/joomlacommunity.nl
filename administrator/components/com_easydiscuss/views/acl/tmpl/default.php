<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.modal' , 'a.modal' );
?>
<script type="text/javascript">
EasyDiscuss.ready(function($){
	selectUser = function( id , name )
	{
		$( '#cid' ).val( id );
		$( '#aclid' ).val( id );
		$( '#aclname' ).val( name );

		// Close dialog
		$.Joomla( 'squeezebox' ).close();
	};
});
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm" autocomplete="off">
	<div class="row-fluid ">
		<div class="span6">
			<div class="widget accordion-group">
				<div class="whead accordion-heading">
					<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
					<h6><?php echo JText::_( 'COM_EASYDISCUSS_ACL_RULE_PROPERTY' ); ?></h6>
					<i class="icon-chevron-down"></i>
					</a>
				</div>

				<div id="option01" class="accordion-body collapse in">
					<div class="wbody">
						<div class="si-form-row">
							<div class="span3 form-row-label"><label><?php echo JText::_( 'COM_EASYDISCUSS_ID' ); ?></label></div>
							<div class="span7">
								<?php echo !empty($this->rulesets->id)? $this->rulesets->id : '0'; ?>
							</div>
						</div>
						<div class="si-form-row">
							<div class="span3 form-row-label"><label><?php echo JText::_( 'COM_EASYDISCUSS_ACL_NAME' ); ?></label></div>
							<div class="span7">
								<input type="text" readonly="readonly"  id="aclname" value="<?php echo !empty($this->rulesets->name)? $this->rulesets->name : ''; ?>">
								<?php if ( $this->type == 'assigned' ) { ?>
								[ <a class="modal" rel="{handler: 'iframe', size: {x: 650, y: 375}}" href="index.php?option=com_easydiscuss&view=users&tmpl=component&browse=1"><?php echo JText::_('COM_EASYDISCUSS_BROWSE_USERS');?></a> ]
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="span6">
			<div class="widget accordion-group">
				<div class="whead accordion-heading">
					<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
					<h6><?php echo JText::_( 'COM_EASYDISCUSS_ACL_RULE_SET' ); ?></h6>
					<i class="icon-chevron-down"></i>
					</a>
				</div>

				<div id="option01" class="accordion-body collapse in">
					<div class="wbody">
						<table class="table table-striped">
						<?php foreach($this->rulesets->rules as $key=>$data) { ?>
						<tr>
							<td>
									<label for="name">
										<?php echo JText::_( 'COM_EASYDISCUSS_ACL_OPTION_' . $key ); ?>
									</label>
							</td>
							<td>

									<?php echo $this->renderCheckbox( $key , $data );?>
									<div style="clear:both;"></div>
									<div class="small mts"><?php echo JText::_($this->getRuleDescription( $key )); ?></div>
							</td>

						<?php } ?>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>


	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="controller" value="acl" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="cid" id="cid" value="<?php echo ( isset($this->rulesets->id) && !is_null($this->rulesets->id) ) ? $this->rulesets->id : ''; ?>" />
	<input type="hidden" name="name" value="<?php echo !empty($this->rulesets->name)? $this->rulesets->name : ''; ?>" />
	<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
	<input type="hidden" name="add" value="<?php echo $this->add; ?>" />
</form>
