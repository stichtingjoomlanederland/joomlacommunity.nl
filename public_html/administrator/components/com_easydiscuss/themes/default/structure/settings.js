ed.require(['edq'], function($){
	EasyDiscuss.renderDialogForBBcode = function(namespace, bbcodeItem) {
		var editorName = $(bbcodeItem.textarea).attr('name');
		var caretPosition = bbcodeItem.caretPosition.toString();
		var contents = $(bbcodeItem.textarea).val();

		// check if the composer is a dialog
		var dialogRecipient = $(bbcodeItem.textarea).data('dialog-recipient');

		EasyDiscuss.dialog({
			'content': EasyDiscuss.ajax(namespace, {
				'editorName': editorName, 
				'caretPosition': caretPosition, 
				'contents': contents, 
				'dialogRecipient': dialogRecipient
			})
		});
	};

	EasyDiscuss.bbcode = [
			<?php if ($this->config->get('layout_bbcode_bold')) { ?>
			{
				name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_BOLD');?>",
				key: 'B',
				openWith: '[b]',
				closeWith: '[/b]',
				className: 'markitup-bold'
			},
			<?php } ?>

			<?php if ($this->config->get('layout_bbcode_italic')) { ?>
			{
				name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_ITALIC');?>",
				key: 'I',
				openWith: '[i]',
				closeWith: '[/i]',
				className: 'markitup-italic'
			},
			<?php } ?>

			<?php if ($this->config->get('layout_bbcode_underline')) { ?>
			{
				name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_UNDERLINE');?>",
				key: 'U',
				openWith: '[u]',
				closeWith: '[/u]',
				className: 'markitup-underline'
			},
			<?php } ?>

			<?php $giphy = ED::giphy(); ?>
			<?php if ($this->config->get('layout_bbcode_bold') || $this->config->get('layout_bbcode_underline') || $this->config->get('layout_bbcode_italic') || $giphy->isEnabled()) { ?>
			{separator: '---------------' },
			<?php } ?>

			<?php if ($this->config->get('layout_bbcode_link')) { ?>
			{
				name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_URL');?>",
				replaceWith: function(h) {
					EasyDiscuss.renderDialogForBBcode('site/views/post/showLinkDialog', h);
				},
				beforeInsert: function(h) {},
				afterInsert: function(h) {},
				className: 'markitup-url'
			},
			<?php } ?>

			<?php if ($this->config->get('layout_bbcode_image')) { ?>
			{
				name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_PICTURE');?>",

				replaceWith: function(h) {
					EasyDiscuss.renderDialogForBBcode('site/views/post/showPhotoDialog', h);
				},
				beforeInsert: function(h) {
				},
				afterInsert: function(h) {
				},
				className: 'markitup-picture'
			},
			<?php } ?>

			<?php if ($this->config->get('layout_bbcode_video')) { ?>
			{
				name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_VIDEO');?>",
				replaceWith: function(h) {
					EasyDiscuss.renderDialogForBBcode('site/views/post/showVideoDialog', h);
				},
				beforeInsert: function(h) {
				},
				afterInsert: function(h) {
				},
				className: 'markitup-video'
			},
			<?php } ?>

			<?php if ($giphy->isEnabled()) { ?>
			{
				name: "<?php echo JText::_('COM_ED_GIPHY'); ?>",
				replaceWith: function() {
					var giphyBrowser = $('[data-giphy-browser]');

					$(document).trigger('initializeGiphy');

					if (giphyBrowser.hasClass('is-open')) {
						giphyBrowser.removeClass('is-open');

						return;
					}

					giphyBrowser.addClass('is-open');
				},
				className: 'markitup-giphy'
			},
			<?php } ?>

			<?php if ($this->config->get('layout_bbcode_link') || $this->config->get('layout_bbcode_image') || $this->config->get('layout_bbcode_video')) { ?>
			{separator: '---------------'},
			<?php } ?>

			<?php if ($this->config->get('layout_bbcode_bullets')) { ?>
			{
				name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_BULLETED_LIST');?>",
				openWith: '[list]\n[*]',
				closeWith: '\n[/list]',
				className: 'markitup-bullet'
			},
			<?php } ?>

			<?php if ($this->config->get('layout_bbcode_numeric')) { ?>
			{
				name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_NUMERIC_LIST');?>",
				openWith: '[list=[![Starting number]!]]\n[*]',
				closeWith: '\n[/list]',
				className: 'markitup-numeric'
			},
			<?php } ?>

			<?php if ($this->config->get('layout_bbcode_bullets') || $this->config->get('layout_bbcode_numeric')) { ?>
			{
				name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_LIST_ITEM');?>",
				openWith: '[*] ',
				className: 'markitup-list'
			},
			<?php } ?>

			<?php if ($this->config->get('layout_bbcode_table')) { ?>
			{
				name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_TABLE');?>",
				openWith: '[table][tr]\n[td]',
				closeWith: '[/td]\n[/tr][/table]',
				className: 'markitup-table'
			},
			{separator: '---------------' },
			<?php } ?>

			<?php if ($this->config->get('layout_bbcode_quote')) { ?>
			{
				name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_QUOTES');?>",
				openWith: '[quote]',
				closeWith: '[/quote]',
				className: 'markitup-quote'
			},
			<?php } ?>

			<?php if ($this->config->get('layout_bbcode_code')) { ?>
			{
				name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_CODE');?>",
				openWith: '[code type="markup"]\n',
				closeWith: '\n[/code]',
				className: 'markitup-code'
			},
			<?php } ?>

			<?php if ($this->config->get('integrations_github')) { ?>
			{
				name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_GIST');?>",
				openWith: '[gist type="php"]\n',
				closeWith: '\n[/gist]',
				className: 'markitup-gist'
			},
			<?php } ?>

			<?php if ($this->config->get('layout_bbcode_article')) { ?>
			{
				name: "<?php echo JText::_('COM_ED_EMBED_ARTICLE');?>",
				replaceWith: function(h) {
					EasyDiscuss.renderDialogForBBcode('site/views/post/showArticleDialog', h);
				},
				className: 'markitup-article'
			},
			<?php } ?>

			<?php if ($this->config->get('layout_bbcode_emoji')) { ?>
			{separator: '---------------' },
			{
				name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_HAPPY');?>",
				openWith: ':D ',
				className: 'markitup-happy'
			},
			{
				name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_SMILE');?>",
				openWith: ':) ',
				className: 'markitup-smile'
			},
			{
				name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_SURPRISED');?>",
				openWith: ':o ',
				className: 'markitup-surprised'
			},
			{
				name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_TONGUE');?>",
				openWith: ':p ',
				className: 'markitup-tongue'
			},
			{
				name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_UNHAPPY');?>",
				openWith: ':( ',
				className: 'markitup-unhappy'
			},
			{
				name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_WINK');?>",
				openWith: ';) ',
				className: 'markitup-wink'
			}
			<?php } ?>
		]
});
