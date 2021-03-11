<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

$data      = $displayData['data'];
$seo       = $displayData['seo'];
$config    = $displayData['config'];
$component = $displayData['component'];

$contacts = array();

foreach ($data->contacts as $item)
{
	$contacts[] = (object) array(
		'@type'       => 'ContactPoint',
		'telephone'   => $item->telephone,
		'contactType' => $item->type,
	);
}
?>
{
	"@context": "https://schema.org",
	"@type": "Organization",
	"url": "<?php echo Uri::base() ?>",
	"contactPoint": <?php echo json_encode($contacts) ?>
}
