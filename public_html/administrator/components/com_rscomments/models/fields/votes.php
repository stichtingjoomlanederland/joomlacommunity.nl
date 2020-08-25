<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');

class JFormFieldVotes extends JFormField {
	
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Votes';

	/**
	 * Method to get the field input markup for a combo box field.
	 *
	 * @return  string   The field input markup.
	 *
	 * @since   11.1
	 */
	public function getInput() {
		$db 	= JFactory::getDbo();
		$app 	= JFactory::getApplication();
		$input 	= $app->input;

		$query = $db->getQuery(true);
		$query->select('COUNT('.$db->qn('IdVote').')')
			->from($db->qn('#__rscomments_votes'))
			->where($db->qn('IdComment').' = '.$input->getInt('IdComment', 0))
			->where($db->qn('value').' = '.$db->q('positive'));
		
		$db->setQuery($query);
		$positive_votes = $db->loadResult();

		$query = $db->getQuery(true);
		$query->select('COUNT('.$db->qn('IdVote').')')
			->from($db->qn('#__rscomments_votes'))
			->where($db->qn('IdComment').' = '.$input->getInt('IdComment', 0))
			->where($db->qn('value').' = '.$db->q('negative'));
		
		$db->setQuery($query);
		$negative_votes = $db->loadResult();
		
		return '<div class="btn-group"><button type="button" class="btn btn-success btn-sm"><i class="icon-thumbs-up"></i> '.$positive_votes.' </button> <button type="button" class="btn btn-danger btn-sm"><i class="icon-thumbs-down"></i> '.$negative_votes.' </button></div>';
	}
}