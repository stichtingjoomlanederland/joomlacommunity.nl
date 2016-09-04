<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.5.0.585
 * @date        2016-08-25
 */

// no direct access
defined('_JEXEC') or die;

$href = 'https://www.linkedin.com/shareArticle?url=' . $displayData['canonical'] . '&title=' . urlencode($displayData['metadata']['title']);
?>

<li class="">
	<a class="wbamp-social-buttons"
	   id="wbamp-button_linkedin_share_1"
	   href="<?php echo htmlspecialchars($href); ?>"
	   title="LinkedIn">
<span class="wbamp-social-icon wbamp-linked-in">
<svg width="30" height="30" viewBox="0 0 30 30"><path d="M10.576 7.985c.865 0 1.568.703 1.568 1.568 0 .866-.703 1.57-1.568 1.57-.867 0-1.568-.704-1.568-1.57 0-.865.7-1.568 1.568-1.568zm-1.353 4.327h2.706v8.704H9.222v-8.704zm4.403 0h2.595v1.19h.038c.36-.685 1.244-1.407 2.56-1.407 2.737 0 3.243 1.803 3.243 4.147v4.774h-2.7v-4.232c0-1.01-.02-2.308-1.407-2.308-1.408 0-1.623 1.1-1.623 2.235v4.306h-2.704v-8.704"/></svg>
</span>
	</a>
</li>
