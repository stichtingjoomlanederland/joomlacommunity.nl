EasyDiscuss.module('bbcode', function($) {

	var module = this;

	window.insertVideoCode = function(videoURL , caretPosition , elementId )
	{
		if (videoURL.length == 0)
		{
			return false;
		}

		var textarea	= $('textarea[name=' + elementId + ']');
		var tag = '[video]' + videoURL + '[/video]';

		// If this is at the first position, we don't want to do anything here.
		if (caretPosition == 0)
		{
			$(textarea).val(tag);
			disjax.closedlg();
			return true;
		}

		var contents	= $(textarea).val();

		$(textarea).val(contents.substring(0, caretPosition) + tag + contents.substring(caretPosition, contents.length));

		disjax.closedlg();
	};

	$.getEasyDiscussBBCodeSettings = {

		onTab:	{keepDefault: false, replaceWith: '    '},
		previewParserVar: 'data',
		markupSet: [
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_BOLD' ), key: 'B', openWith: '[b]', closeWith: '[/b]', className: 'markitup-bold'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_ITALIC' ), key: 'I', openWith: '[i]', closeWith: '[/i]', className: 'markitup-italic'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_UNDERLINE' ), key: 'U', openWith: '[u]', closeWith: '[/u]', className: 'markitup-underline'},
			{separator: '---------------' },
			{
				name: $.language( 'COM_EASYDISCUSS_BBCODE_URL' ),
				key: 'L',
				openWith: '[url=[![Link:]!]]',
				closeWith: '[/url]',
				placeHolder: $.language( 'COM_EASYDISCUSS_BBCODE_TITLE' ),
				beforeInsert: function(h ) {
				},
				className: 'markitup-url'
			},
			{
				name: $.language( 'COM_EASYDISCUSS_BBCODE_PICTURE' ),
				key: 'P',
				replaceWith: '[img][![Url]!][/img]',
				className: 'markitup-picture'
			},
			{
				name: $.language( 'COM_EASYDISCUSS_BBCODE_VIDEO' ),
				replaceWith: function(h) {

					disjax.loadingDialog();
					disjax.load('post' , 'showVideoDialog' , $(h.textarea).attr('name') , h.caretPosition.toString());

				},
				beforeInsert: function(h ) {
				},
				afterInsert: function(h ) {
				},
				className: 'markitup-video'
			},

			{separator: '---------------' },
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_BULLETED_LIST' ), openWith: '[list]\n', closeWith: '\n[/list]', className: 'markitup-bullet'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_NUMERIC_LIST' ) , openWith: '[list=[![Starting number]!]]\n', closeWith: '\n[/list]', className: 'markitup-numeric'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_LIST_ITEM' ) , openWith: '[*] ', className: 'markitup-list'},
			{separator: '---------------' },
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_QUOTES' ) , openWith: '[quote]', closeWith: '[/quote]', className: 'markitup-quote'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_CODE' ), openWith: '[code type="markup"]', closeWith: '[/code]', className: 'markitup-code'},
			{separator: '---------------' },
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_HAPPY' ) , openWith: ':D ', className: 'markitup-happy'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_SMILE' ) , openWith: ':) ', className: 'markitup-smile'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_SURPRISED' ) , openWith: ':o ', className: 'markitup-surprised'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_TONGUE' ) , openWith: ':p ', className: 'markitup-tongue'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_UNHAPPY' ) , openWith: ':( ', className: 'markitup-unhappy'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_WINK' ), openWith: ';) ', className: 'markitup-wink'}
		]
	};

	// $.markItUp.sets.bbcode_easydiscuss_dialog = {
	$.getEasyDiscussDialogBBCodeSettings = {

		onShiftEnter:	{keepDefault: false, replaceWith: '<br />\n'},
		onCtrlEnter:	{keepDefault: false, openWith: '\n<p>', closeWith: '</p>'},
		onTab:	{keepDefault: false, replaceWith: '    '},
		previewParserVar: 'data',
		markupSet: [
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_BOLD' ), key: 'B', openWith: '[b]', closeWith: '[/b]', className: 'markitup-bold'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_ITALIC' ), key: 'I', openWith: '[i]', closeWith: '[/i]', className: 'markitup-italic'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_UNDERLINE' ), key: 'U', openWith: '[u]', closeWith: '[/u]', className: 'markitup-underline'},
			{separator: '---------------' },
			{
				name: $.language( 'COM_EASYDISCUSS_BBCODE_URL' ),
				key: 'L',
				openWith: '[url=[![Link:!:http://]!]]',
				closeWith: '[/url]',
				placeHolder: $.language( 'COM_EASYDISCUSS_BBCODE_TITLE' ),
				className: 'markitup-url'
			},
			{
				name: $.language( 'COM_EASYDISCUSS_BBCODE_PICTURE' ),
				key: 'P',
				replaceWith: '[img][![Url]!][/img]',
				className: 'markitup-picture'
			},
			{separator: '---------------' },
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_BULLETED_LIST' ), openWith: '[list]\n', closeWith: '\n[/list]', className: 'markitup-bullet'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_NUMERIC_LIST' ), openWith: '[list=[![Starting number]!]]\n', closeWith: '\n[/list]', className: 'markitup-numeric'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_LIST_ITEM' ), openWith: '[*] ', className: 'markitup-list'},
			{separator: '---------------' },
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_QUOTES' ), openWith: '[quote]', closeWith: '[/quote]', className: 'markitup-quote'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_CODE' ), openWith: '[code type="xml"]', closeWith: '[/code]', className: 'markitup-code'},
			{separator: '---------------' },
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_HAPPY' ), openWith: ':D ', className: 'markitup-happy'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_SMILE' ), openWith: ':) ', className: 'markitup-smile'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_SURPRISED' ), openWith: ':o ', className: 'markitup-surprised'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_TONGUE' ), openWith: ':p ', className: 'markitup-tongue'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_UNHAPPY' ), openWith: ':( ', className: 'markitup-unhappy'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_WINK' ), openWith: ';) ', className: 'markitup-wink'}
		]
	};

	module.resolve();
});
