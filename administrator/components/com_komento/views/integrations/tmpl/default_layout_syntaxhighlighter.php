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
<script type="text/javascript">
Komento.require().script('syntaxhighlighter', 'komento.common').done(function($) {
	$('pre code').each(function(i, e) {
		hljs.highlightBlock(e);
	});

	var loadCss = function(name) {
		if($('link#shtheme').exists) {
			$('link#shtheme').remove();
		}

		$("<link/>", {
			id: "shtheme",
			rel: "stylesheet",
			type: "text/css",
			href: $.rootPath + '/components/com_komento/assets/css/syntaxhighlighter/' + name + '.css'
		}).appendTo("head");
	};

	var theme = $('#syntaxhighlighter_theme');
	loadCss(theme.val());

	theme.change(function(el) {
		loadCss($(this).val());
	});
});
</script>
	<div class="row">
		<div class="col-md-6">
			<fieldset class="panel form-horizotntal">
				<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYNTAXHIGHLIGHTER' ); ?></div>
				<div class="panel-body">
					<?php $options = array();
						$options[] = array( 'arta', 'Arta' );
						$options[] = array( 'ascetic', 'Ascetic' );
						$options[] = array( 'brown_paper', 'Brown Paper' );
						$options[] = array( 'dark', 'Dark' );
						$options[] = array( 'default', 'Default' );
						$options[] = array( 'far', 'Far' );
						$options[] = array( 'github', 'Github' );
						$options[] = array( 'googlecode', 'Google Code' );
						$options[] = array( 'idea', 'Idea' );
						$options[] = array( 'ir_black', 'IR Black' );
						$options[] = array( 'magula', 'Magula' );
						$options[] = array( 'monokai', 'Monokai' );
						$options[] = array( 'pojoaque', 'Pojoaque' );
						$options[] = array( 'school_book', 'School Book' );
						$options[] = array( 'solarized_dark', 'Solarized Dark' );
						$options[] = array( 'solarized_light', 'Solarized Light' );
						$options[] = array( 'sunburst', 'Sunburst' );
						$options[] = array( 'vs', 'Vs' );
						$options[] = array( 'xcode', 'XCode' );
						$options[] = array( 'zenburn', 'Zenburn' );
						echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SYNTAXHIGHLIGHTER_THEME', 'syntaxhighlighter_theme', 'dropdown', $options );

					?>
				</div>
			</fieldset>
		</div>

		<div class="col-md-6">
			<fieldset class="panel form-horizotntal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYNTAXHIGHLIGHTER_THEME_PREVIEW' ); ?></div>
			<div class="panel-body">
				<tbody>
					<div class="form-group">
							<p>HTML</p>
							<pre><code class="language-html"><?php $text = '<!DOCTYPE html>
<title>Title</title>
<body>
	<p class="title" id="title">Title</p>
	<!-- here goes the rest of the page -->
</body>
</html>';
echo Komento::getHelper( 'String' )->escape( $text ); ?></code></pre>
					</div>
					<div class="form-group">
							<p>CSS</p>
							<pre><code class="language-css"><?php $text = 'body {
	background-color: black;
}

div.text {
	font: Tahoma, sans-serif;
}';
echo Komento::getHelper( 'String' )->escape( $text ); ?></code></pre>
					</div>
					<div class="form-group">
							<p>JavaScript</p>
							<pre><code class="language-js"><?php $text = 'function init(element) {
	for(var i = 0; i < element.length; i++) {
		// do something here;
	}
}';
echo Komento::getHelper( 'String' )->escape( $text ); ?></code></pre>
					</div>
			</div>
			</fieldset>
		</div>
	</div>
