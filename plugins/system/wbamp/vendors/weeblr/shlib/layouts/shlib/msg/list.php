<?php
/**
 * Shlib - programming library
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier 2015
 * @package      shlib
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      0.3.1.540
 */

/**
 * This layout displays a message list, obtained from the message manager
 *
 * $displayData elements:
 *
 * @param string $id   An optional unique id for the message container
 * @param array  $msgs The list of messages object
 *
 */

defined('_JEXEC') or die;

$id = empty($displayData['id']) ? 'wb-lib-msgs-container' : $displayData['id'];
$displayData['msgs'] = empty($displayData['msgs']) || !is_array($displayData['msgs']) ? array() : $displayData['msgs'];
?>

<!-- wbLib messages -->
<section id="<?php echo $id; ?>" class="wbl-container-msg-all">
	<?php
	foreach ($displayData['msgs'] as $msg)
	{
		echo ShlMvcLayout_Helper::render('shlib.msg.msg', array('msg' => $msg, 'container_id' => $id), SHLIB_LAYOUTS_PATH);
	}
	?>
</section>
<!-- wbLib messages -->
