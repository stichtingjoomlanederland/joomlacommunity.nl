<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class plgButtonRsfiles extends JPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;
	
	public function __construct(& $subject, $config) {
		parent::__construct($subject, $config);
	}
	
	/**
	 * Display the button
	 *
	 * @return array A four element array of (article_id, article_title, category_id, object)
	 */
	public function onDisplay($name) {	
		if (!$this->canRun()) 
			return;
		
		$js = "function rsf_placeholder(what)
			{
				var text = '{rsfiles path=\"'+what+'\"}';
				jInsertEditorText(text, '".$name."');
				SqueezeBox.close();
				return false; 
			}
			";
		
		JFactory::getLanguage()->load('plg_editors-xtd_rsfiles', JPATH_ADMINISTRATOR);
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);

		JHtml::_('behavior.modal');

		/*
		 * Use the built-in element view to select the article.
		 * Currently uses blank class.
		 */
		$link = 'index.php?option=com_rsfiles&amp;view=files&amp;layout=modal&amp;from=editor&amp;tmpl=component&amp;'.JSession::getFormToken().'=1';

		$button = new JObject;
		$button->modal = true;
		$button->class = 'btn';
		$button->link = $link;
		$button->text = JText::_('PLG_RSFILES_BUTTON');
		$button->name = 'file-add';
		$button->options = "{handler: 'iframe', size: {x: 800, y: 500}}";

		return $button;
	}
	
	protected function canRun() {
		if (!JFactory::getApplication()->isAdmin())
			return false;
		
		if (file_exists(JPATH_SITE.'/components/com_rsfiles/rsfiles.php'))
			return true;
		
		return false;
	}
}