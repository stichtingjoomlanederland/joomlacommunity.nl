<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      wbAmp
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      1.6.0.607
 * @date        2016-10-31
 *
 */

defined('_JEXEC') or die;

/**
 * wbAMP Editor button
 *
 */
class PlgButtonWbamp extends JPlugin
{
	private $options = array(
		'default' => array(
			'title' => 'PLG_EDITORS_XTD_WBAMP_DEFAULT_TAB_LABEL',
			'showInsertButton' => false,
			'help' => 'going-further/embedded-tags/hide-for-amp.html',
			'content' => '',
			'form' => ''
		),
		'docimage' => array(
			'title' => 'PLG_EDITORS_XTD_WBAMP_DOC_IMAGE_LABEL',
			'showInsertButton' => true,
			'help' => 'going-further/embedded-tags/meta-data-tags.html',
			'content' => '',
			'form' => '
		<fieldset name="docimage">
 			<field name="page_image_url" type="text" default="" size="60" class="wb_text_wide"
                       label="PLG_EDITORS_XTD_WBAMP_DOC_IMAGE_LABEL">
            </field>
            <field name="page_image_width" type="text" default="" size="10" class="wb_text_wide"
                       label="PLG_SYSTEM_WBAMP_FALLBACK_IMAGE_WIDTH_LABEL">
            </field>
            <field name="page_image_height" type="text" default="" size="10" class="wb_text_wide"
                       label="PLG_SYSTEM_WBAMP_FALLBACK_IMAGE_HEIGHT_LABEL">
            </field>
		</fieldset>
'
		),
		'doctype' => array(
			'title' => 'PLG_EDITORS_XTD_WBAMP_DOC_TYPE_LABEL',
			'showInsertButton' => true,
			'help' => 'going-further/embedded-tags/meta-data-tags.html',
			'content' => '',
			'form' => '
		<fieldset name="doctype">
			<field
				name="document_type"
				type="text"
				label="PLG_EDITORS_XTD_WBAMP_DOC_TYPE_LABEL"
				size="60"
			/>
		</fieldset>
'
		),
		'docauthor' => array(
			'title' => 'PLG_EDITORS_XTD_WBAMP_DOC_AUTHOR_LABEL',
			'showInsertButton' => true,
			'help' => 'going-further/embedded-tags/meta-data-tags.html',
			'content' => '',
			'form' => '
		<fieldset name="docauthor">
			<field
				name="document_author"
				type="text"
				label="PLG_EDITORS_XTD_WBAMP_DOC_AUTHOR_VALUE_LABEL"
				size="60"
			/>
			<field
				name="document_author_type"
				type="text"
				default="Person"
				label="PLG_EDITORS_XTD_WBAMP_DOC_AUTHOR_TYPE_LABEL"
				size="60"
			/>
		</fieldset>
'
		)
	);

	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * Display the button
	 *
	 * @param   string $name The name of the button to add
	 *
	 * @return JObject
	 */
	public function onDisplay($name)
	{
		$document = JFactory::getDocument();

		// path to our layouts, used by layouts themselves
		defined('WBAMP_EDITOR_LAYOUTS_PATH') or define('WBAMP_EDITOR_LAYOUTS_PATH', realpath(dirname(__FILE__)) . '/layouts');

		// load main wbAMP plugin language strings
		$lang = JFactory::getLanguage();
		$extension = 'Plg_system_wbamp';
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, true)
		|| $lang->load(strtolower($extension), JPATH_PLUGINS . '/system/wbamp', null, false, true);

		// add modal css and js
		$htmlManager = ShlHtml_Manager::getInstance();
		$htmlManager->addAssets($document)
		            ->addSpinnerAssets($document);
		$htmlManager->addStylesheet('wbamp_be', array('files_path' => '/media/plg_wbamp/assets/default', 'assets_bundling' => false));
		$htmlManager->addStylesheet('wbamp_be', array('files_path' => '/media/plg_wbamp/assets_full/default', 'assets_bundling' => false));

		ShlHtmlBs_helper::addBootstrapCss($document);
		ShlHtmlBs_helper::addBootstrapJs($document);
		JHtml::_('formbehavior.chosen', 'select');
		JHtml::_('behavior.formvalidator');

		$document->addScript(
			$htmlManager->getMediaLink(
				'editor', 'js', array('files_path' => '/media/plg_wbamp/assets_full/default', 'url_base' => JURI::root(true), 'assets_bundling' => false)
			)
		);

		// add some js to show or hide the footer Insert tag button per tab
		$toShow = array();
		$helpIds = array();
		foreach ($this->options as $key => $option)
		{
			if ($option['showInsertButton'])
			{
				$toShow[] = '"' . $key . '"';
			}
			if (!empty($option['help']))
			{
				$helpIds[] = '"' . $key . '":"' . $option['help'] . '"';
			}
		}
		$js = '
	function wbAmpEditorUpdateFooterButton(currentTab){
		var showFooterButtonTabs = [' . implode($toShow, ',') . '];
		var showFooterHelpTabs = {' . implode($helpIds, ',') . '};
		if(showFooterButtonTabs.indexOf(currentTab) == -1)
		{jQuery("#wbamp-editor-insert-tag-button").hide();}
		else{jQuery("#wbamp-editor-insert-tag-button").show();}
		if(!showFooterHelpTabs[currentTab])
		{jQuery("#wbamp-editor-help-button").attr("data-helpid","");}
		else{jQuery("#wbamp-editor-help-button").attr("data-helpid", showFooterHelpTabs[currentTab]);}
	}
		';
		$document->addScriptDeclaration($js);

		// prepare html content for the default tab
		// which is not a form (XML forms are rendered automatically)
		$tabsContent = $this->options;
		$tabsContent['default']['content'] =
			ShlMvcLayout_Helper::render('plg_editors-xtd_wbamp.defaulttab', $this->options['default'], WBAMP_EDITOR_LAYOUTS_PATH);

		// render a base code snippet for the modal
		$params = array();
		$params['title'] = JText::_('PLG_EDITORS_XTD_WBAMP_WINDOW_TITLE');
		$params['height'] = '0.6';
		$params['width'] = '0.7';
		$displayData = array(
			'options' => $tabsContent,
			'forms' => $this->getForms()
		);
		$params['content'] = ShlMvcLayout_Helper::render('plg_editors-xtd_wbamp.main', $displayData, WBAMP_EDITOR_LAYOUTS_PATH);
		$params['footer'] = ShlMvcLayout_Helper::render('plg_editors-xtd_wbamp.footer', $displayData, WBAMP_EDITOR_LAYOUTS_PATH);
		$params['onDisplay'] = 'wbampEditorPluginOnDisplay';

		$modalInitCode = $this->renderModalJs('modal-wbamp-editor', $params);
		$document->addScriptDeclaration($modalInitCode);

		// function to apply chosen styling to selects
		$js = '
function wbampEditorPluginOnDisplay(o) {
	jQuery("select").chosen({"disable_search_threshold":10});
}';
		$document->addScriptDeclaration($js);

		// insert help modal
		$params = array();
		$params['title'] = 'wbAMP ' . JText::_('JHELP');
		$params['height'] = '0.6';
		$params['width'] = '0.8';
		$params['content'] = '<iframe id="wbamp-editor-help-frame" src="" width="100%" height="100%"></iframe>';
		$params['footer'] = ShlMvcLayout_Helper::render('plg_editors-xtd_wbamp.footer-help', array(), WBAMP_EDITOR_LAYOUTS_PATH);;
		$params['onDisplay'] = '';
		$modalInitCode = $this->renderModalJs('modal-wbamp-help', $params);
		$document->addScriptDeclaration($modalInitCode);

		// add code to collect editor data, in order to
		// verify it before adding more stuff
		$document->addScriptDeclaration($this->getEditorHandlingCode($name));
		$editor = JFactory::getEditor();

		$button = new JObject;
		$button->modal = false;
		$button->class = 'btn modal-wbamp-editor';

		// on J! 3.5.0+, editors-xtd buttons are processed by Joomla and integrated in the
		// TinyMCE toolbar instead of being displayed below the editor
		// The buttons onclick code is put into a wrapper and executed, however not a B/C manner
		// (I don't see how B/C can be achieved), as click handled parameters are not
		// available any longer ie onclick="aFunction(event, var1, var2, "constant1", "constant2");"
		// so on J! >= 3.5.0, we have a different syntax
		// Main issue is how to preventDefault().
		if ($editor->get('_name') == 'tinymce' and version_compare(JVERSION, '3.5', 'ge'))
		{
			$button->onclick = 'wblib.wbampeditor.clickRelay(\'' . $name . '\', \'#modal-wbamp-editor\', \'default\');';
		}
		else
		{
			$button->onclick = 'wblib.wbampeditor.clickRelay(\'' . $name . '\', \'#modal-wbamp-editor\', \'default\', event);';
		}

		// # required to prevent TInyMCE/Joomla to open some URL in a (mootools) modal
		$button->link = '#';
		$button->text = JText::_('PLG_EDITORS_XTD_WBAMP_BUTTON_TITLE');
		$button->name = 'lightning';
		$button->option = '';

		return $button;
	}

	private function getEditorHandlingCode($name)
	{
		// get the set/getContent methods for editor
		$editor = JFactory::getEditor();
		$getContent = $editor->getContent('__EDITOR_INSTANCE__');
		$getContent = str_replace("'__EDITOR_INSTANCE__'", '"' . $name . '"', $getContent);
		$getContent = str_replace('"__EDITOR_INSTANCE__"', '"' . $name . '"', $getContent);  // codeMirror
		// fix for TinyMCE: Joomla 3.4.3 code works only if there's one editor instance on page
		$getContent = str_replace('tinyMCE.activeEditor.', 'tinyMCE.get("' . $name . '").', $getContent);

		$setContent = $editor->setContent('__EDITOR_INSTANCE__', '__EDITOR_CONTENT__');
		// fix for TinyMCE: Joomla 3.4.3 code works only if there's one editor instance on page
		$setContent = str_replace('tinyMCE.activeEditor.', 'tinyMCE.get("' . $name . '").', $setContent);

		$setContent = str_replace("'__EDITOR_INSTANCE__'", '"' . $name . '"', $setContent);
		$setContent = str_replace('"__EDITOR_INSTANCE__"', '"' . $name . '"', $setContent);  // codeMirror
		$setContent = str_replace("'__EDITOR_CONTENT__'", 'content', $setContent); // JCE
		$setContent = str_replace('"__EDITOR_CONTENT__"', 'content', $setContent); // Code mirror
		$setContent = str_replace("__EDITOR_CONTENT__", 'content', $setContent); // TinyMCE

		$js = "
    function wbampEditorPluginGetContent() {
    var content = " . $getContent . "
    return content;
    }
    function wbampEditorPluginSetContent(content) {
    " . $setContent . "
    }
    ";

		return $js;
	}

	private function getForms()
	{
		$forms = array();
		foreach ($this->options as $key => $option)
		{
			if (!empty($option['form']))
			{
				$fullForm = '<form><fields name="params">' . $option['form'] . '</fields></form>';
				$forms[$key] = JForm::getInstance(
					'wbamp-editor-form-' . $key,
					$fullForm,
					$options = array(
						'control' => 'wbamp-editor-form-' . $key
						, 'load_data' => false
					),
					$clear = false,
					$xpath = false
				);
			}
			else
			{
				$forms[$key] = null;
			}
		}

		return $forms;
	}

	private function renderModalJs($selector, $params)
	{
		// Ensure the behavior is loaded
		JHtml::_('bootstrap.framework');
		JHtml::_('bootstrap.tooltip');

		$params['selector'] = $selector;
		$js = '
(function() {
	var params = ' . json_encode($params) . ';
	shlBootstrap.registerModal(params);
	}
)();' . "\n";

		return $js;
	}
}
