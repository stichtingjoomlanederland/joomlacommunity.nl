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
require_once DISCUSS_HELPERS . '/router.php';

$config				= DiscussHelper::getConfig();
$isEditMode			= false;

$post				= DiscussHelper::getTable( 'Post' );
$category			= JRequest::getInt( 'category' , $post->category_id );

$categoryModel		= DiscussHelper::getModel( 'Category' );
$defaultCategory	= $categoryModel->getDefaultCategory();

$my					= JFactory::getUser();
$showPrivateCat		= ( $my->id == 0 ) ? false : true;

if( $category == 0 && $defaultCategory !== false )
{
	$category		= $defaultCategory->id;
}
$nestedCategories	= DiscussHelper::populateCategories('', '', 'select', 'category_id', $category , true, true, $showPrivateCat);

//recaptcha integration
$recaptcha	= '';
$enableRecaptcha	= $config->get('antispam_recaptcha');
$publicKey			= $config->get('antispam_recaptcha_public');
$skipRecaptcha		= $config->get('antispam_skip_recaptcha');

$model		= DiscussHelper::getModel( 'Posts' );
$postCount	= count( $model->getPostsBy( 'user' , $my->id ) );

if( $enableRecaptcha && !empty( $publicKey ) && $postCount < $skipRecaptcha )

{
	require_once DISCUSS_CLASSES . '/recaptcha.php';
	$recaptcha	= getRecaptchaData( $publicKey , $config->get('antispam_recaptcha_theme') , $config->get('antispam_recaptcha_lang') , null, $config->get('antispam_recaptcha_ssl') );
}

?>

<script src="http://easydiscuss.dev/media/foundry/js/foundry.js" type="text/javascript"></script>


<script type="text/javascript">
	Foundry.rootPath   = 'http://easydiscuss.dev/';
	Foundry.indexUrl   = 'http://easydiscuss.dev/index.php';
	Foundry.scriptPath = 'http://easydiscuss.dev/media/foundry/js/';

/*<![CDATA[*/
	var discuss_site 	= 'http://easydiscuss.dev/index.php?option=com_easydiscuss&lang=none';
	var spinnerPath		= 'http://easydiscuss.dev/components/com_easydiscuss/assets/images/loading.gif';
	var lang_direction	= 'ltr';
	var discuss_featured_style	= '0';
/*]]>*/
/*<![CDATA[*/ Foundry.run(function($)
{
	$.Component(
		'EasyDiscuss',
		{
			debug: '1',

			// TODO: Get the actual version
			version: '2.0',

			require: [
				// TODO: Replace disjax with Foundry ajax layer.
				'easydiscuss.disjax',
				'easydiscuss.discuss',
				'jquery.ui.core',
				'jquery.ui.position'
			]
		},
		function()
		{
		}
	);
});
 /*]]>*/

</script>

<script type="text/javascript">
EasyDiscuss.require([
	'jquery.autogrow'
], function($){

	$('#new_tags').keypress(function(event) {
		if(event.which == '13'){
			discuss.post.tags.add();
		}
	});
	discuss.widget.init();
	//$( '#quick_question_reply_content' ).markItUp( mySettings );

	$( '#quick_question_reply_content' ).autogrow();


	var textField = $('input#ez-title');
	var queryJob = null;
	textField.keydown(function()
	{
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
					});
				}
			 });
		 }, 1500);
	});


	// Try to test if there is a 'default' class in all of the tabs
	if( Foundry( 'ul.form-tab' ).children().find( '.default' ).html() != null )
	{
		var id 	= $( 'ul.form-tab' ).children().find( '.default' ).attr( 'id' );
		var tab = id.substr( id.indexOf( '-' ) + 1 , id.length );

		$( 'ul.form-tab' ).children().find( '.default' ).parent().addClass( 'active' );

		$( 'div.form-tab-contents' ).children().hide();
		$( '.tab-' + tab ).show();
	}
	else
	{
		// First tab always gets the active class.
		$( 'ul.form-tab' ).children( ':first' ).addClass( 'active' );
		$( 'div.form-tab-contents' ).children().hide();
		$( 'div.form-tab-contents' ).children( ':first' ).show();
	}

});
</script>
<style>
#discuss-wrapper .discuss-postbox input.postbox-title{height:30px;font-size:14px;font-weight:bold;color:#999;padding:4px 3px 4px 5px;}
#discuss-wrapper .discuss-postbox input.postbox-title:focus{color:#555;font-style:normal;}
#discuss-wrapper .discuss-postbox i{width:5px;height:15px;background:url(../images/postbox-arrow.png);position:absolute;left:-4px;top:12px;}
#discuss-wrapper .postbox-content .input{width:100%;width:100%;line-height:20px;font-family:Arial;font-size:inherit;line-height:20px;height:80px;}
#discuss-wrapper .postbox-content select.inputbox{padding:4px;font-size:12px;font-weight:bold;font-family:Arial;border:1px solid #ccc;border-top:1px solid #aaa;color:#666;min-width:300px;}
</style>

<div id="dc_quick-discussion" class="discuss-index mt-20 mb-20" style="margin-bottom:30px">
	<div class="avatar float-l">
		<img class="avatar small" src="<?php echo $profile->getAvatar();?>">
	</div>
	<div class="discuss-story" style="margin-right:0">
		<form id="mod_edqq" name="mod_edqq" action="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&controller=posts&task=submit'); ?>" method="post">
			<div class="discuss-postbox">
				<div class="mr-10 pos-r">
					<i></i>
					<input id="ez-title" class="postbox-title input width-full for-title fwb fs-14" type="text" value="<?php echo JText::_('MOD_EASYDISCUSS_QUICKQUESTION_POST_TITLE_EXAMPLE'); ?>" onfocus="if (this.value == '<?php echo JText::_('MOD_EASYDISCUSS_QUICKQUESTION_POST_TITLE_EXAMPLE'); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php echo JText::_('MOD_EASYDISCUSS_QUICKQUESTION_POST_TITLE_EXAMPLE'); ?>';}" name="title" autocomplete="off">
				</div>
				<img id="dc-search-loader" src="<?php echo DISCUSS_SPINNER; ?>" class="pos-a" style="top:11px;right:11px;display:none;" >
				<div id="dc_similar-questions" style="display:none"></div>
			</div>
			<div class="postbox-content mt-5" style="display:nones">
				<div class="mr-10">
					<textarea class="input" id="quick_question_reply_content" name="quick_question_reply_content"></textarea>
				</div>
				<div class="mt-5 clearfix">
					<input type="button" value="Submit Discussion" class="button-submit float-r" id="submit-reply" name="submit-reply" onclick="discuss.post.qqSubmit();">
					<?php echo $nestedCategories; ?>
				</div>
			</div>

			<?php if(empty($user->id) && $config->get('main_allowguestpostquestion', 0)) { ?>
			<div class="form-tab-item discuss-author mt-15 clearfix">
				<div class="float-l mr-15">
					<label for="poster_name" class="float-l fs-11 mr-10"><?php echo JText::_('MOD_EASYDISCUSS_QUICKQUESTION_NAME'); ?> :</label>
					<div class="input-wrap mr-10">
						<input class="input width-200" type="text" id="poster_name" name="poster_name" value="<?php echo empty($post->poster_name) ? '' : $post->poster_name; ?>"/>
					</div>
				</div>
				<div class="float-l">
					<label for="poster_email" class="float-l fs-11 mr-10"><?php echo JText::_('MOD_EASYDISCUSS_QUICKQUESTION_EMAIL'); ?> :</label>
					<div class="input-wrap mr-10">
						<input class="input width-200" type="text" id="poster_email" name="poster_email" value="<?php echo empty($post->poster_email) ? '' : $post->poster_email; ?>"/>
					</div>
				</div>
			</div>
			<?php } ?>

			<?php if(! empty($recaptcha)) { ?>
			<div class="form-row discuss-recaptcha">
				<div id="post_new_antispam"><?php echo $recaptcha; ?></div>
			</div>
			<?php } ?>

			<input type="button" name="submit-reply" id="createpost" class="button-submit float-r" value="<?php echo JText::_('MOD_EASYDISCUSS_QUICKQUESTION_BUTTON_SUBMIT'); ?>" onclick="discuss.post.qqSubmit();" />
			<input type="hidden" name="id" id="id" value="" />
			<input type="hidden" name="parent_id" id="parent_id" value="0" />

			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	</div>
	<div class="clear"></div>
</div>
