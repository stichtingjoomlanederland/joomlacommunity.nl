<?php
/**
 * @package AkeebaReleaseSystem
 * @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

$category_url = AKRouter::_('index.php?option=com_ars&view=category&id='.$item->id.'&Itemid=' . $Itemid);

$app 		= JFactory::getApplication();
$menu 		= $app->getMenu();
$menuItems 	= $menu->getItems( 'link', 'index.php?option=com_ars&view=latests&layout=latest' );

foreach($menuItems as $menuItem) {
	$params = $menu->getParams($menuItem->id);
	if($item->vgroup_id == $params->get('vgroupid')) {
		$route = $menuItem->route;
	}
}

// Get releases
$model = FOFModel::getTmpInstance('Releases','ArsModel')
	->published(1)
	->filter_order('created')
	->filter_order_Dir('DESC')
	->access_user(JFactory::getUser()->id)
	->category($item->id);
$allItems = $model->getItemList();

if($allItems) {

	$latest = $allItems[0];

	// Get files
	$model = FOFModel::getTmpInstance('Items','ArsModel')
		->published(1)
		->access_user(JFactory::getUser()->id)
		->release($latest->id);

	$allItems = $model->getItemList();
	$download = $allItems[0];
	$environments = json_decode($download->environments);

	// Get versions
	$model = FOFModel::getTmpInstance('Environments','ArsModel');
	$versions = $model->getItemList();

	if ($environments) {
		$joomlaversions = array();
		foreach($versions as $version) {
			if(in_array($version->id, $environments)) {
				$joomlaversions[] = $version;
			}
		}
	}

	// Correct title for Joomla core
	if($item->vgroup_id == 1) {
		$item->title = 'Joomla';
	}

	// New & updated badges
	$new 				= false;
	$new_datediff 		= time() - strtotime($item->created);
	$update 			= false;
	$update_datediff 	= time() - strtotime($latest->created);
	if(floor($new_datediff/(60*60*24)) < 7) {
		$new = true;
	}
	if(floor($update_datediff/(60*60*24)) < 7) {
		$update = true;
	}
}
?>

<?php if($allItems):?>
<a href="<?php echo $this->escape($route) ?>#<?php echo $this->escape($item->alias) ?>" class="list-group-item">
	<?php if($environments):?>
	<?php foreach($joomlaversions as $version):?>
		<span class="badge badge-<?php echo $version->xmltitle?>" data-toggle="tooltip" data-placement="top" data-html="false" title="<?php echo $version->title?>"><span class="icon-joomla"></span></span>
	<?php endforeach;?>
	<?php endif;?>
	<?php echo $this->escape($item->title) ?> <?php echo $this->escape($download->version) ?>
	<?php if($new):?>
		<span class="label label-success">Nieuw</span>
	<?php elseif($update):?>
		<span class="label label-warning">Update</span>
	<?php endif;?>
</a>
<?php else:?>
<a href="<?php echo htmlentities($category_url); ?>" class="list-group-item">
	<?php echo $this->escape($item->title) ?>
</a>
<?php endif;?>
