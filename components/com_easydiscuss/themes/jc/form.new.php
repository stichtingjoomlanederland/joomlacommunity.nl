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
.script('posts')
.done(function($){
	$('.discuss-form').implement( EasyDiscuss.Controller.Post.Ask );
});
</script>

<script type="text/javascript">
EasyDiscuss
.require()
.script( 'legacy' )
.done(function($){
	discuss.composer.init("<?php echo $composer->selector; ?>");


	discuss.getContent = function(){
		<?php if( $system->config->get( 'layout_editor' ) == 'bbcode' ) { ?>
			return $( '#dc_reply_content' ).val();
		<?php } else { ?>
		<?php echo 'return ' . JFactory::getEditor( $system->config->get( 'layout_editor' ) )->getContent( 'dc_reply_content' ); ?>
		<?php } ?>
	};

	<?php if( $system->config->get( 'main_similartopic' ) ) { ?>

	var textField = $('input#ez-title');
	var queryJob = null;
	var menuLock = false;
	textField.keydown(function( e )
	{
		var keynum; // set the variable that will hold the number of the key that has been pressed.

		//now, set keynum = the keystroke that we determined just happened...
		if(window.event)// (IE)
		{
			keynum = e.keyCode;
		}
		else if(e.which) // (other browsers)
		{
			keynum = e.which;
		}
		else
		{ // something funky is happening and no keycode can be determined...
			keynum = 0;
		}

		if( keynum == 9 || keynum == 27)
		{
			$('#dc_similar-questions').hide();
			return;
		}

		clearTimeout(queryJob);

		// Start this job after 1 second
		queryJob = setTimeout(function()
		{

			if( textField.val().length <= 3 )
				return;

			//show loading icon
			$('#dc-search-loader').show();

			var params	= { query: textField.val() };

			params[ $( '.easydiscuss-token' ).val() ]	= 1;

			EasyDiscuss.ajax('site.views.post.similarQuestion', params ,
			function(data){
				//hide loading icon
				$('#dc-search-loader').hide();
				if( data != '' )
				{
					// Do whatever you like with the data returned from server.
					$('#dc_similar-questions').html(data);
					$('#dc_similar-questions').show();

					$('#similar-question-close').click( function()
					{
						$('#dc_similar-questions').hide();
						return;
					});
				}
			});
		}, 1500);
	});

	$('#dc_similar-questions').bind('mousemove click', function(){
		textField.focus();
		menuLock = true;
	})
	.mouseout(function(){
		menuLock = false;
	});

	textField.blur( function()
	{
		if (menuLock) return;

		$('#dc_similar-questions').hide();
		return;
	});

	<?php } ?>

// 	$( '.submitDiscussion' ).bind( 'click' , function(){
//
// // 		var selectedCategory = $( '.discuss-form *[name=category_id]' ).val();
// //
// // 		if( selectedCategory == 0 || selectedCategory.length == 0 )
// // 		{
// // 			$( '.categorySelection' ).addClass( 'error' );
// // 			disjax.loadingDialog();
// // 			disjax.load( 'post' , 'selectCategory' );
// // 			return false;
// // 		}
// //
// // 		// Disable the submit button if it's already pressed to avoid duplicate clicks.
// // 		$(this).attr( 'disabled' , 'disabled' );
// //
// // 		// Submit the form now.
// // 		$( '#dc_submit' ).submit();
//
// 		var text = discuss.getContent();
//
// 		console.log( text );
//
// 		// $( '#hidden-content-placeholder').val( discuss.getContent() );
// 		return false;
// 	});
});
</script>

<!-- do not remove this div -->
<div class="ask-notification"></div>

<form id="dc_submit" autocomplete="off" name="dc_submit" action="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&controller=posts&task=submit'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">

<div class="discuss-form discuss-composer <?php echo $composer->classname; ?> discuss-composer-<?php echo $composer->operation; ?>"
	 data-id="<?php echo $composer->id; ?>"
	 data-editortype="<?php echo $composer->editorType ?>"
	 data-operation="<?php echo $composer->operation; ?>"
	 >

	<?php if( $isEditMode ){ ?>
	<legend><?php echo JText::_( 'COM_EASYDISCUSS_ENTRY_EDITING_TITLE');?></legend>
	<input type="hidden" name="id" id="id" value="<?php echo $post->id; ?>" />
	<?php } else { ?>
	<legend><?php echo JText::_( 'COM_EASYDISCUSS_TOOLBAR_NEW_DISCUSSION');?></legend>
	<?php } ?>

	<div id="dc_post_notification"><div class="msg_in"></div></div>

	<div class="form-row discuss-category-selection categorySelection">
		<div class="form-inline">
			<?php if( $config->get( 'layout_category_selection' ) == 'multitier' ) { ?>
				<?php echo $this->loadTemplate( 'category.select.multitier.php' ); ?>
			<?php } else { ?>
				<?php echo $nestedCategories; ?>
			<?php } ?>
		</div>
	</div>

	<?php if( $system->config->get( 'layout_post_types' ) ){ ?>
	<div class="form-row grid-div grid-6-4">
		<div>
			<input type="text" id="ez-title" name="title" placeholder="<?php echo JText::_('COM_EASYDISCUSS_POST_TITLE_EXAMPLE' , true ); ?>" class="form-control" autocomplete="off" value="<?php echo $this->escape( $post->title );?>" />
			<div id="dc-search-loader" style="display:none;">
				<div class="discuss-loader"></div>
			</div>
			<div id="dc_similar-questions" style="display:none"></div>
		</div>
		<div>
			<select id="post_type" class="form-control post-type" name="post_type">
				<option value="default"><?php echo JText::_('COM_EASYDISCUSS_SELECT_POST_TYPES');?></option>
				<?php foreach( $postTypes as $type ){ ?>

					<option <?php echo ($type->alias == $post->post_type) ? 'selected="selected"' : '' ?> value="<?php echo $type->alias ?>"><?php echo $type->title ?></option>

				<?php } ?>
			</select>
		</div>
	</div>
	<?php } else { ?>
	<div class="form-row">
		<input type="text" id="ez-title" name="title" placeholder="<?php echo JText::_('COM_EASYDISCUSS_POST_TITLE_EXAMPLE' , true ); ?>" class="form-control" autocomplete="off" value="<?php echo $this->escape( $post->title );?>" />
		<div id="dc-search-loader" style="display:none;">
			<div class="discuss-loader"></div>
		</div>
		<div id="dc_similar-questions" style="display:none"></div>
	</div>
	<?php } ?>



	<div class="form-row">
		<?php echo $composer->getEditor(); ?>
	</div>

	<div class="form-row">
		<?php echo $this->loadTemplate( 'form.location.php' ); ?>
	</div>



	<?php echo $composer->getFields(); ?>
	<?php if( !$system->my->id && $acl->allowed('add_question', 0)) { ?>
	<hr />

	<div class="control-group">
		<div class="row-fluid">
			<div class="span5">
				<label for="poster_name" class="fs-12 mr-10"><?php echo JText::_('COM_EASYDISCUSS_YOUR_NAME'); ?> :</label>
				<input class="input width-200" type="text" id="poster_name" name="poster_name" value="<?php echo empty($post->poster_name) ? '' : $post->poster_name; ?>"/>
			</div>
			<div class="span7">
				<label for="poster_email" class="fs-12 mr-10"><?php echo JText::_('COM_EASYDISCUSS_YOUR_EMAIL'); ?> :</label>
				<input class="input width-200" type="text" id="poster_email" name="poster_email" value="<?php echo empty($post->poster_email) ? '' : $post->poster_email; ?>"/>
			</div>
		</div>
	</div>

	<?php } ?>

	<?php if( $recaptcha = $this->getRecaptcha() ){ ?>
	<hr />
	<div class="control-group">
		<div id="post_new_antispam"><?php echo $recaptcha; ?></div>
	</div>
	<?php }else if( DiscussHelper::getHelper( 'Captcha' )->showCaptcha() ){ ?>
		<?php echo DiscussHelper::getHelper( 'Captcha' )->getHTML();?>
	<?php } ?>

	<div class="form-actions">
		<input type="button" class="butt butt-primary submitDiscussion" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_SUBMIT' , true ); ?>" />
		<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss' );?>" class="butt butt-default"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL'); ?></a>
	</div>
</div>

<?php if( !empty( $reference ) && !empty( $referenceId ) ){ ?>
<input type="hidden" name="reference" value="<?php echo $reference; ?>" />
<input type="hidden" name="reference_id" value="<?php echo $referenceId; ?>" />
<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
<?php } ?>

<?php echo JHTML::_( 'form.token' ); ?>
</form>
