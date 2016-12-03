<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RSTabs
{
	protected $id		= null;
	protected $titles 	= array();
	protected $contents = array();
	
	public function __construct($id) {
		$this->id	   = preg_replace('#[^A-Z0-9_\. -]#i', '', $id);
	}
	
	public function addTitle($label, $id) {
		$this->titles[] = (object) array('label' => $label, 'id' => $id);
	}
	
	public function addContent($content) {
		$this->contents[] = $content;
	}
	
	public function render() {
		?>
		<ul class="nav nav-tabs" id="<?php echo $this->id; ?>">
		<?php foreach ($this->titles as $i => $title) { ?>
			<li<?php if ($i == 0) { ?> class="active"<?php } ?>><a href="#<?php echo $title->id; ?>" data-toggle="tab"><?php echo JText::_($title->label); ?></a></li>
		<?php } ?>
		</ul>
		<div class="tab-content">
		<?php foreach ($this->contents as $i => $content) { ?>
			<div class="tab-pane<?php if ($i == 0) { ?> active<?php } ?>" id="<?php echo $this->titles[$i]->id;?>">
			<?php echo $content; ?>
			</div>
		<?php } ?>
		</div>
		<?php
	}
}