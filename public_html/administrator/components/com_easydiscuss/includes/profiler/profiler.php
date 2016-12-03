<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussProfiler extends EasyDiscuss
{
	private $start = null;
	private $end = null;
	private $peak = null;
	private $queries = null;

	public function start()
	{
		if (!$this->config->get('system_db_profiling')) {
			return false;
		}

		$db = ED::db();
		$db->setDebug(true);

		$this->start = memory_get_usage(true);

		return $this;
	}

	public function end()
	{
		if (!$this->config->get('system_db_profiling')) {
			return false;
		}

		$db = ED::db();

		$this->queries = $db->getLog();

		$this->end = memory_get_usage(true);

		$db->setDebug(false);

		return $this;
	}

	private function convert($unit)
	{
		$units = array('B', 'KB', 'MB', 'GB');

		return round( $unit / pow ( 1024 , ( $i = floor( log( $unit , 1024 ) ) ) ) , 2 ) . ' ' . $units[ $i ];
	}

	/**
	 * Generates the HTML block for profiling
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function html()
	{
		// Only super admins are allowed to view
		if (!$this->isSiteAdmin) {
			return;
		}

		if (!$this->config->get('system_db_profiling')) {
			return;
		}

		$this->end();

		$db = ED::db();

		$start 	= $this->start / 1024 / 1024;
		$end 	= $this->end / 1024 / 1024;

		ob_start();
	?>
	<script type="text/javascript">
	ed.require(['edq'], function($) {

		$('[data-profiler-expand]').click(function(){

			if (!$(this).hasClass("expanded")) {

				$('[data-profiler-box]')
					.css({
						width: "100%",
						height: "100%"
					});

				$('[data-profiler-queries]').show();

				$('body').css("overflow", "hidden");

				$(this).addClass("expanded");

			} else {

				$('[data-profiler-box]')
					.css({
						width: "auto",
						height: "auto"
					});

				$('[data-profiler-queries]').hide();

				$('body').css("overflow", "auto");

				$(this).removeClass("expanded");
			}
		});
	});
	</script>
	<section style="border: 1px solid #e6e6e6;margin-top: 20px;position:fixed;right: 0;bottom: 0;overflow:hidden;z-index: 9999;background:rgba(255,255,255,0.8)" data-profiler-box>
	<div class="row-fluid" style="background: #fff; border-bottom: 1px solid #eee;">
		<h6 class="pull-left" style="margin: 5px;">Debug information</h6>
		<a href="javascript:void(0);" data-profiler-expand class="pull-right" style="font-size: 14px; padding-right: 5px; padding-top: 5px;">Expand</a>
	</div>
	<div style="padding: 5px;">
		Total SQL queries executed by: <strong style="<?php echo count($this->queries) > 80 ? 'color:red;' : '';?>"><?php echo count($this->queries); ?></strong><br />
		Before application load usage: <strong><?php echo $start; ?>MB</strong><br />
		After application load usage: <strong><?php echo $end; ?>MB</strong><br />

		<ul class="list-unstyled" data-profiler-queries style="display: none; overflow: scroll; border: 1px solid #CCCCCC; margin-top: 5px; overflow-y: scroll; overflow-x: hidden; padding: 5px; height: -moz-calc(100% - 90px); height: -webkit-calc(100% - 90px); height: calc(100% - 90px); position: absolute; margin-right: 5px;">
		<?php if( $this->queries ){ ?>
			<?php foreach( $this->queries as $query ){ ?>
				<li style="margin-bottom: 5px;">
					<code style="max-width:400px; word-wrap: break-word; white-space: normal;"><?php echo $query;?></code>
				</li>
			<?php } ?>
		<?php } ?>
		</ul>
	</div>
	</section>
	<?php
		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
