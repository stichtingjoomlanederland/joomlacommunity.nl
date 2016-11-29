<?php
/**
 * @package       RSEvents!Pro
 * @copyright (C) 2015 www.rsjoomla.com
 * @license       GPL, http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
$count = count($this->categories); ?>

<?php if ($this->params->get('show_page_heading', 1))
{ ?>
	<?php $title = $this->params->get('page_heading', ''); ?>
	<h1><?php echo !empty($title) ? $this->escape($title) : JText::_('COM_RSEVENTSPRO_CATEGORIES_TITLE'); ?></h1>
<?php } ?>

<?php if (!empty($this->categories)) : ?>
	<?php foreach ($this->categories as $category): ?>
		<?php if ($category->level == 2): ?>
			<h1><?php echo $category->title; ?></h1>
		<?php else: ?>
			<div class="well">
				<div class="page-header">
				<h2>
					<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&category=' . rseventsproHelper::sef($category->id, $category->title)); ?>">
						<?php echo $category->title; ?>
					</a>
				</h2>
				</div>

				<?php echo rseventsproHelper::shortenjs($category->description, $category->id, 255, $this->params->get('type', 1)); ?>

				<a class="btn btn-bijeenkomsten" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&category=' . rseventsproHelper::sef($category->id, $category->title)); ?>">
					Bekijk alle bijeenkomsten
				</a>
<!--				@TODO: komende bijeenkomst-->
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>