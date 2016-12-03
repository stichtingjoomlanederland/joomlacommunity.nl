ed.require(['edq', 'site/vendors/markitup', 'site/src/ask', 'site/src/composer'], function($, markitup, ask, composer, _) {

    window.insertVideoCode = function(videoURL , caretPosition , elementId) {
        if (videoURL.length == 0)
        {
            return false;
        }

        var textarea    = $('textarea[name=' + elementId + ']');
        var tag = '[video]' + videoURL + '[/video]';

        // If this is at the first position, we don't want to do anything here.
        if (caretPosition == 0)
        {
            $(textarea).val(tag);
            disjax.closedlg();
            return true;
        }

        var contents    = $(textarea).val();

        $(textarea).val(contents.substring(0, caretPosition) + tag + contents.substring(caretPosition, contents.length));

        disjax.closedlg();
    };

    var bbcodeSettings = {
        onTab: {
            keepDefault: false,
            replaceWith: '    '
        },
        previewParserVar: 'data',
        markupSet: [
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_BOLD');?>",
                key: 'B',
                openWith: '[b]',
                closeWith: '[/b]',
                className: 'markitup-bold'
            },
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_ITALIC');?>",
                key: 'I',
                openWith: '[i]',
                closeWith: '[/i]',
                className: 'markitup-italic'
            },
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_UNDERLINE');?>",
                key: 'U',
                openWith: '[u]',
                closeWith: '[/u]',
                className: 'markitup-underline'
            },
            {separator: '---------------' },
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_URL');?>",
                key: 'L',
                openWith: '[url=[![Link:]!]]',
                closeWith: '[/url]',
                placeHolder: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_TITLE');?>",
                beforeInsert: function(h ) {
                },
                className: 'markitup-url'
            },
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_PICTURE');?>",
                key: 'P',
                replaceWith: '[img][![Url]!][/img]',
                className: 'markitup-picture'
            },
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_VIDEO');?>",

                replaceWith: function(h) {
                    disjax.loadingDialog();
                    disjax.load('post' , 'showVideoDialog' , $(h.textarea).attr('name') , h.caretPosition.toString());
                },
                beforeInsert: function(h) {
                },
                afterInsert: function(h) {
                },
                className: 'markitup-video'
            },
            {separator: '---------------' },
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_BULLETED_LIST');?>",
                openWith: '[list]\n',
                closeWith: '\n[/list]',
                className: 'markitup-bullet'
            },
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_NUMERIC_LIST');?>",
                openWith: '[list=[![Starting number]!]]\n',
                closeWith: '\n[/list]',
                className: 'markitup-numeric'
            },
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_LIST_ITEM');?>",
                openWith: '[*] ',
                className: 'markitup-list'
            },
            {separator: '---------------' },
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_QUOTES');?>",
                openWith: '[quote]',
                closeWith: '[/quote]',
                className: 'markitup-quote'
            },
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_CODE');?>",
                openWith: '[code type="markup"]',
                closeWith: '[/code]',
                className: 'markitup-code'
            },
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
        ]
    };

	// Implement form
	ask.execute('[data-ask-form]');

	// initialize ed composer? wtf?
    composer.execute('<?php echo $composer->selector;?>', {
        "bbcodeSettings": bbcodeSettings
    });

    return;

	discuss.getContent = function() {

		<?php if ($this->config->get('layout_editor') == 'bbcode') { ?>
			return $('#dc_reply_content').val();
		<?php } else { ?>
			<?php echo 'return ' . JFactory::getEditor($this->config->get('layout_editor'))->getContent('dc_reply_content'); ?>
		<?php } ?>
	};


});
