<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
	// Load the Visualization API and the piechart package.
	google.load( 'visualization' , '1.0' , {
		packages : [ 'corechart' ]
	});

	// Set callback
	google.setOnLoadCallback( drawCategoryChart );

	function drawCategoryChart()
	{
		var data 	= new google.visualization.DataTable();

		data.addColumn( 'string', 'Category' );
		data.addColumn( 'number', 'Posts' );

		data.addRows([

			<?php for( $i = 0; $i < count( $this->categories );$i++ ){ ?>
				<?php
				$total = $this->categories[ $i ]->getPostCount();
				?>
				['<?php echo $this->escape(JText::_($this->categories[ $i ]->title, true));?> (<?php echo $total;?>)', <?php echo $total;?>]
				<?php if( next( $this->categories ) !== false ){ ?>, <?php } ?>
			<?php } ?>
		]);

		var chart 	= new google.visualization.PieChart( document.getElementById( 'categoryChart' ) );

		chart.draw( data , {
			width	: '100%',
			height 	: 250
		});
	}
</script>
<div class="widget">
	<div class="whead"><h6><?php echo JText::_( 'COM_EASYDISCUSS_POSTS_BY_CATEGORY' );?></h6>
	</div>

	<div id="categoryChart"></div>

</div>
