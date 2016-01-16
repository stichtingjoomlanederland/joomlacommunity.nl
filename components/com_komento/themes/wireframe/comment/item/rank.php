<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

if( $system->config->get( 'enable_rank_bar' ) ) {
$userRank = ( $row->author->getCommentCount() / Komento::getModel( 'comments' )->getTotalComment() ) * 100;

if( !$row->author->guest ) { ?>
<div class="kmt-rank">
	<div class="kmt-rank-bar">
		<div class="kmt-rank-progress" style="width: <?php echo $userRank; ?>%;"></div>
	</div>
</div>
<?php }
}
