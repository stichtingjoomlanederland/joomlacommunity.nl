<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html');
jimport('joomla.form.formfield');

require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php');

class JFormFieldModal_Categories extends JFormField
{
	protected $type = 'Modal_Categories';

	protected function getInput()
	{
		$containersOnly = isset($this->element['containersonly']) ? true : false;
		$multiple = isset($this->element['multipleitems']) ? true : false;

		$model = ED::model('Categories');
		$categories = $containersOnly ? $model->getCatContainer() : $model->getAllCategories();

		if (!is_array($this->value)) {
			$this->value = array($this->value);
		}

		ob_start();
		?>
		<select name="<?php echo $this->name;?><?php echo $multiple ? '[]' : '';?>" id="<?php echo $this->name;?>"<?php echo $multiple == 'true' ? ' multiple="multiple"' :'';?>>
			<option value="0"> Please select your category </option>
			<?php if ($categories) { ?>
				<?php foreach ($categories as $category) { ?>
				<option value="<?php echo $category->id;?>"<?php echo in_array($category->id, $this->value) ? ' selected="selected"' : '';?>><?php echo JText::_($category->title); ?></option>
				<?php } ?>
			<?php } ?>
		</select>
		<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
