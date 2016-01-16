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


<script type="text/javascript">
EasyDiscuss.ready(function($){
	EasyDiscuss.ajax( 'admin.views.discuss.getnews' ,
		{},
		function( html ){
			$( '#recentNews' ).append( html );
		});
})
</script>

<div class="widget accordion-group">
	<div class="whead accordion-heading">
		<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#newsItems">
		<h6><?php echo JText::_( 'COM_EASYDISCUSS_RECENT_NEWS' );?></h6>
		<i class="icon-chevron-down"></i>
		</a>
	</div>
	<div id="newsItems" class="accordion-body collapse in">
		<ul id="recentNews" class="unstyled updates">
		</ul>
	</div>
</div>
