<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.3.1.490
 * @date        2016-05-18
 */

// no direct access
defined('_JEXEC') or die;

// no text, no show
if (empty($displayData['user-notification']) || empty($displayData['user-notification']['text']))
{
	return '';
}

// data preparation
$buttonText = empty($displayData['user-notification']['button']) ? '' : $displayData['user-notification']['button'];
$id = sha1('amp-user-notification' . $displayData['user-notification']['text'] . $buttonText);

?>

<div class="wbamp-container wbamp-amp-tag wbamp-user-notification wbamp-notification-<?php echo $displayData['user-notification']['theme']; ?>">
	<amp-user-notification layout="nodisplay" id="wbamp-<?php echo $id; ?>"
	>
		<?php
		echo $displayData['user-notification']['text'];
		if (!empty($buttonText))
		{
			echo "\n<button on=\"tap:wbamp-" . $id . '.dismiss">' . $buttonText . '</button>';
		}
		?>

	</amp-user-notification>
</div>
