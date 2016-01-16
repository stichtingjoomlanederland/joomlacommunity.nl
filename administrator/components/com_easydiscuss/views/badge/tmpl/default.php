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
EasyDiscuss
.require()
.library('ui/datepicker')
.done(function($){

	$( "#datepicker" ).datepicker({
		dateFormat: "DD, d MM, yy"
	});

	$.datepicker.setDefaults( $.datepicker.regional[ "" ] );

	window.showDescription = function( id )
	{
		$( '.rule-description' ).hide();
		$( '#rule-' + id ).show();
	}

	EasyDiscuss.ready(function($){
			$.Joomla( 'submitbutton' , function(action ){
				if( action == 'save' || action == 'saveNew' ){
					if( action == 'saveNew' ) {
						$( '#savenew' ).val( '1' );
						action = 'save';
					}
				}

				$( "#datepicker" ).datepicker( "option", "dateFormat", "yy-mm-dd" );

				$.Joomla( 'submitform' , [action] );
			});
		});
});
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

<div class="row-fluid">
	<div class="span7">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#badge01">
					<h6><?php echo JText::_( 'COM_EASYDISCUSS_DETAILS' );?></h6>
					<i class="icon-chevron-down"></i>
				</a>
			</div>
			<div id="badge01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span3 form-row-label">
							<label><?php echo JText::_( 'COM_EASYDISCUSS_BADGE_TITLE' );?></label>
						</div>
						<div class="span9">
							<input type="text" class="full-width inputbox" name="title" value="<?php echo $this->badge->get( 'title' );?>" />
						</div>
					</div>

					<div class="si-form-row">
						<div class="span3">
							<?php echo JText::_( 'COM_EASYDISCUSS_BADGE_DESCRIPTION' );?>
						</div>
						<div class="span9">
							<?php echo $this->editor->display( 'description' , $this->badge->description , '100%' , '300' , 10 , 10 , array( 'zemanta' , 'readmore' , 'pagebreak' , 'article' , 'image' ) ); ?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span3 form-row-label">
							<label><?php echo JText::_( 'COM_EASYDISCUSS_BADGE_PUBLISHED' );?></label>
						</div>
						<div class="span9">
							<?php echo $this->renderCheckbox( 'published' , $this->badge->get( 'published' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span3 form-row-label">
							<label><?php echo JText::_( 'COM_EASYDISCUSS_BADGE_CREATED_DATE' );?></label>
						</div>
						<div class="span9">
							<input type="text" id="datepicker" class="input-large" name="created" value="<?php echo DiscussDateHelper::toFormat($this->badge->created, '%A, %B %d %Y'); ?>" />
						</div>
					</div>
					<div class="si-form-row">
						<div class="span3 form-row-label">
							<label><?php echo JText::_( 'COM_EASYDISCUSS_BADGE_ACTION' );?></label>
						</div>
						<div class="span9">
							<select name="rule_id" onchange="showDescription( this.value );" class="full-width">
								<option value="0"<?php echo !$this->badge->get( 'rule_id' ) ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYDISCUSS_SELECT_RULE' );?></option>
								<option value="-1"<?php echo $this->badge->get( 'rule_id' ) == '-1'? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYDISCUSS_MANUAL_ASSIGNMENT' );?></option>
							<?php foreach( $this->rules as $rule ){ ?>
								<option value="<?php echo $rule->id;?>"<?php echo $this->badge->get( 'rule_id' ) == $rule->id ? ' selected="selected"' : '';?>><?php echo $rule->title; ?></option>
							<?php } ?>
							</select>
							<?php foreach( $this->rules as $rule ){ ?>
							<div id="rule-<?php echo $rule->id;?>" class="rule-description" style="display:none;"><?php echo $rule->description;?></div>
							<?php } ?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span3 form-row-label">
							<label><?php echo JText::_( 'COM_EASYDISCUSS_BADGE_ACTION_THRESHOLD' );?></label>
						</div>
						<div class="span9">
							<input type="text" name="rule_limit" class="input-mini" style="text-align: center;" value="<?php echo $this->badge->get( 'rule_limit'); ?>" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="span5">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#badge02">
					<h6><?php echo JText::_( 'COM_EASYDISCUSS_BADGE' );?></h6>
					<i class="icon-chevron-down"></i>
				</a>
			</div>
			<div id="badge02" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span12">
							<p><?php echo JText::_( 'COM_EASYDISCUSS_UPLOAD_BADGE_DESC' );?></p>
							<code class="pa-5"><?php echo DISCUSS_BADGES_PATH; ?></code>
						</div>
					</div>
					<div class="si-form-row">
						<ul class="badges-list unstyled pull-left clearfix">
							<?php foreach( $this->badges as $badge ){ ?>
								<li class="badge-item<?php echo $this->badge->avatar == $badge ? ' selected-badge' : '';?>">
									<label for="<?php echo $badge;?>">
										<div><img src="<?php echo DISCUSS_BADGES_URI . '/' . $badge;?>" width="48" /></div>
										<input type="radio" value="<?php echo $badge;?>" name="avatar" id="<?php echo $badge;?>"<?php echo $this->badge->avatar == $badge ? ' checked="checked"' : '';?> />
									</label>
								</li>
							<?php } ?>
						</ul>

						<div class="span8">

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<input type="hidden" name="id" value="<?php echo $this->badge->id; ?>" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="controller" value="badges" />
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="savenew" id="savenew" value="0" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
