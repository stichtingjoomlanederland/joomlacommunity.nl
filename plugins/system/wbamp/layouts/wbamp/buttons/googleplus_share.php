<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.4.2.551
 * @date        2016-07-19
 */

// no direct access
defined('_JEXEC') or die;

$href = 'https://plus.google.com/share?url=' . $displayData['canonical'];
?>


<li class="">
	<a class="wbamp-social-buttons"
	   id="wbamp-button_googleplus_share_1"
	   href="<?php echo htmlspecialchars($href); ?>"
	   title="Google +">
<span class="wbamp-social-icon wbamp-google-plus">
<svg width="32" height="32" viewBox="4 -4 32 32"><path d="M8.8 12c-.1 2.9 2 5.7 4.7 6.6 2.6.9 5.8.2 7.5-2 1.3-1.6 1.6-3.6 1.4-5.6h-6.7v2.4h4c-.3 1.2-1.1 2.2-2.3 2.7-2.3 1-5.1-.3-5.8-2.7-.9-2.3.5-5 2.9-5.7 1.4-.5 2.9.1 4.1.8l1.8-1.8c-1.4-1.2-3.2-1.9-5-1.7-3.6.1-6.8 3.4-6.6 7zm18-3v2h-2v2h2v2h2v-2h2v-2h-2V9h-2z"/></svg>
</span>
	</a>
</li>
