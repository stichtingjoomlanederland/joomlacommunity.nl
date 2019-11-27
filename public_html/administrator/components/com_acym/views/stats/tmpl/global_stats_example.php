<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.5.2
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA SAS - All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<div class="cell grid-x acym__stats__empty acym__content">
	<h2 class="acym__listing__empty__title text-center cell">
        <?php echo acym_translation('ACYM_DONT_HAVE_STATS_CAMPAIGN'); ?>
		<a href="<?php echo acym_completeLink('campaigns&task=edit&step=chooseTemplate'); ?>"><?php echo acym_translation('ACYM_CREATE_ONE'); ?></a>
	</h2>

	<h2 class="acym__listing__empty__subtitle text-center cell"><?php echo acym_translation('ACYM_LOOK_AT_THESE_AMAZING_DONUTS'); ?></h2>
    <?php echo $data['example_round_chart']; ?>

	<h2 class="acym__listing__empty__subtitle text-center cell"><?php echo acym_translation('ACYM_OR_THIS_AWESOME_CHART_LINE'); ?></h2>
    <?php echo $data['example_line_chart']; ?>
</div>

