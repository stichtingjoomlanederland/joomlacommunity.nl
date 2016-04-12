<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<h1><?php echo $this->escape($this->filename); ?></h1>
<?php if ($this->status) { ?>
	<h2><?php echo $this->escape($this->status['reason']); ?></h2>
<?php } ?>
<pre><?php
	if ($this->status) {
		$contents = str_replace($this->escape($this->status['match']), '<strong class="com-rsfirewall-level-high">'.$this->escape($this->status['match']).'</strong>', $this->escape($this->contents));
	} else {
		$contents = $this->escape($this->contents);
	}
	echo $contents;
?></pre>