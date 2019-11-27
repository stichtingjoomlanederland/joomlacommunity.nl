<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die;

$input   = Factory::getApplication()->input;
$path    = $input->getString('path', null);
$alt     = $input->getString('alt', null);
$caption = $input->getString('caption', null);
?>

<script type="text/javascript">
	window.parent.select_pwtimage_article('<?php echo $path; ?>', '<?php echo $alt; ?>', '<?php echo $caption; ?>');
</script>
