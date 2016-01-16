<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
function showDescription( id )
{
	EasyDiscuss.$( '.rule-description' ).hide();
	EasyDiscuss.$( '#rule-' + id ).show();
}
EasyDiscuss(function($){
	$.Joomla( 'submitbutton' , function(action){
		$.Joomla( 'submitform' , [action] );
	});
});
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div class="row-fluid">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_DETAILS' );?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>
			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span4 form-row-label"><label><?php echo JText::_( 'COM_EASYDISCUSS_POINT_TITLE' );?></label></div>
						<div class="span8">
							<input type="text" class="full-width inputbox" name="title" value="<?php echo $this->point->get( 'title' );?>" />
						</div>
					</div>
					<div class="si-form-row">
						<div class="span4 form-row-label"><label><?php echo JText::_( 'COM_EASYDISCUSS_PUBLISHED' );?></label></div>
						<div class="span8">
							<?php echo $this->renderCheckbox( 'published' , $this->point->get( 'published' , 1 ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span4 form-row-label"><label><?php echo JText::_( 'COM_EASYDISCUSS_CREATION_DATE' );?></label></div>
						<div class="span8">
							<?php echo DiscussDateHelper::toFormat($this->point->created, '%A, %B %d %Y, %I:%M %p'); ?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span4 form-row-label"><label><?php echo JText::_( 'COM_EASYDISCUSS_POINT_ACTION' );?></label></div>
						<div class="span8">
							<select name="rule_id" onchange="showDescription( this.value );" class="full-width" >
								<option value="0"<?php echo !$this->point->get( 'rule_id' ) ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYDISCUSS_SELECT_RULE' );?></option>
							<?php foreach( $this->rules as $rule ){ ?>
								<option value="<?php echo $rule->id;?>"<?php echo $this->point->get( 'rule_id' ) == $rule->id ? ' selected="selected"' : '';?>><?php echo $rule->title; ?></option>
							<?php } ?>
							</select>
							<?php foreach( $this->rules as $rule ){ ?>
							<div id="rule-<?php echo $rule->id;?>" class="rule-description" style="display:none;"><?php echo $rule->description;?></div>
							<?php } ?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span4 form-row-label"><label><?php echo JText::_( 'COM_EASYDISCUSS_POINTS_GIVEN' );?></label></div>
						<div class="span8">
							<input type="text" name="rule_limit" class="input-mini" style="text-align: center;" value="<?php echo $this->point->get( 'rule_limit'); ?>" />
						</div>
					</div>
				</div>
			</div>

		</div>

	</div>
	<div class="span6">

	</div>
</div>

<input type="hidden" name="id" value="<?php echo $this->point->id; ?>" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="controller" value="points" />
<input type="hidden" name="option" value="com_easydiscuss" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
