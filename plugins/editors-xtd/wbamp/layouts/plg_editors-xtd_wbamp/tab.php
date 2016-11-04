<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.6.0.607
 * @date        2016-10-31
 */

// no direct access
defined('_JEXEC') or die;

$tabId = 'wbamp-editor-tab-' . $displayData['id'];
?>
<div
	class="wbl-theme-default wbamp-editor-tab hide"
	id="<?php echo $tabId; ?>">

	<div class="wbamp-editor-form form-horizontal">
		<?php
		if (!empty($displayData['params']['content']))
		{
			echo $displayData['params']['content'];
		}
		if (!empty($displayData['form']))
		{
			$fieldsets = $displayData['form']->getFieldsets('params');

			foreach ($fieldsets as $name => $fieldset)
			{
				if (!isset($fieldset->repeat) || isset($fieldset->repeat) && $fieldset->repeat == false)
				{

					if (isset($fieldset->description) && trim($fieldset->description))
					{
						echo '<p class="hasTooltip">' . $this->escape(JText::_($fieldset->description)) . '</p>';
					}

					$hidden_fields = '';

					foreach ($displayData['form']->getFieldset($name) as $field)
					{
						if (!$field->hidden)
						{
							?>
							<div class="control-group">
								<div class="control-label">
									<?php echo $field->label; ?>
								</div>
								<div class="controls">
									<?php echo $field->input; ?>
								</div>
							</div>
							<?php
						}
						else
						{
							$hidden_fields .= $field->input;
						}
					}
					echo $hidden_fields;
				}
			}
		}
		?>

	</div>
</div>
