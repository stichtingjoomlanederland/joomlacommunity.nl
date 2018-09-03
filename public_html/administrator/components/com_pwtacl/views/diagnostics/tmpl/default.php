<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Uri\Uri;

// No direct access.
defined('_JEXEC') or die;
?>

<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
    <div id="pwtacl" class="diagnostics bootstrap">

		<?php if ($this->issues): ?>

            <div class="quickscan quickscan-issues well well-large">
                <legend><?php echo Text::_('COM_PWTACL_DIAGNOSTICS_ISSUES_DETECTED'); ?></legend>
                <p class="lead"><?php echo Text::_('COM_PWTACL_DIAGNOSTICS_ISSUES_DETECTED_DESC'); ?></p>
                <p>
                    <button class="btn btn-large btn-block btn-success js--start" type="button">
						<?php echo Text::_('COM_PWTACL_DIAGNOSTICS_FIX'); ?>
                    </button>
                </p>
                <div class="progress progress-striped active hidden">
                    <div class="bar bar-success" style="width:0%"></div>
                </div>
            </div>
		<?php endif ?>

        <div class="quickscan quickscan-noissues well well-large<?php if ($this->issues): ?> hidden<?php endif; ?>">
            <legend><?php echo Text::_('COM_PWTACL_DIAGNOSTICS_NO_ISSUES_DETECTED'); ?></legend>
            <p class="lead"><?php echo Text::_('COM_PWTACL_DIAGNOSTICS_NO_ISSUES_DETECTED_DESC'); ?></p>
            <p>
                <button class="btn btn-large btn-success btn-block js--rebuild" type="button" onclick="Joomla.submitbutton('diagnostics.rebuild');">
					<?php echo Text::_('COM_PWTACL_DIAGNOSTICS_STEP_REBUILD'); ?>
                </button>
            </p>
        </div>

        <div class="accordion" id="diagnosticsteps">
			<?php foreach ($this->steps as $key => $step): ?>
                <div class="accordion-group <?php echo 'step' . $key; ?>">
                    <div class="accordion-heading">
                        <a class="accordion-toggle nopointer" data-toggle="collapse" data-parent="#diagnosticsteps">
                            <h3 class="<?php if ($this->issues): ?> muted<?php else: ?>text-success<?php endif; ?>">
                                <span class="js-step-done badge badge-success pull-right <?php if ($this->issues): ?> hidden<?php endif; ?>">
                                    <i class="icon-ok icon-white"></i>
                                </span>
                                <span class="js-assets-fixed-number badge badge-warning pull-right"></span>
								<?php echo Text::sprintf('COM_PWTACL_DIAGNOSTICS_STEP', $key); ?>
								<?php if (strpos($step, 'GENERAL') === false) : ?>
									<?php echo Text::_('COM_PWTACL_DIAGNOSTICS_STEP_' . $step); ?>
                                    <small>
										<?php echo Text::_('COM_PWTACL_DIAGNOSTICS_STEP_' . $step . '_DESC'); ?>
                                    </small>
								<?php else: ?>
									<?php $steptitle = Text::_('COM_PWTACL_DIAGNOSTICS_STEP_' . $step); ?>
									<?php echo Text::sprintf('COM_PWTACL_DIAGNOSTICS_STEP_GENERAL_TITLE', $steptitle); ?>
                                    <small>
										<?php echo Text::sprintf('COM_PWTACL_DIAGNOSTICS_STEP_GENERAL_DESC', $steptitle, $steptitle); ?>
                                    </small>
								<?php endif; ?>
                            </h3>
                        </a>
                    </div>
                    <div id="step<?php echo $key; ?>" class="accordion-body collapse">
                        <div class="accordion-inner">
                            <div class="alert alert-success">
								<?php if (strpos($step, 'GENERAL') === false) : ?>
									<?php echo Text::_('COM_PWTACL_DIAGNOSTICS_STEP_' . $step . '_SUCCESS'); ?>
								<?php else: ?>
									<?php echo Text::sprintf('COM_PWTACL_DIAGNOSTICS_STEP_GENERAL_SUCCESS', $steptitle); ?>
								<?php endif; ?>
                                <span class="js-assets-fixed hidden">
                                    <strong>
                                        <span class="js-assets-fixed-number"></span> <?php echo Text::sprintf('COM_PWTACL_DIAGNOSTICS_RESULTS_ITEMS_FIXED', null); ?>.
                                    </strong>
                                </span>
                            </div>

                            <table class="table table-striped table-bordered js-results-table hidden">
                                <thead>
                                <tr>
                                    <th width="6%"></th>
                                    <th width="10%"><?php echo Text::_('COM_PWTACL_DIAGNOSTICS_RESULTS_TYPE'); ?></th>
                                    <th><?php echo Text::_('COM_PWTACL_DIAGNOSTICS_RESULTS_TITLE'); ?></th>
                                    <th width="35%"><?php echo Text::_('COM_PWTACL_DIAGNOSTICS_RESULTS_CHANGES'); ?></th>
                                    <th width="30px"><?php echo Text::_('COM_PWTACL_DIAGNOSTICS_RESULTS_ID'); ?></th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
			<?php endforeach; ?>

            <div class="accordion-group completed hidden">
                <div class="accordion-heading">
                    <span class="accordion-toggle nopointer" data-parent="#diagnosticsteps">
                        <h3 class="text-success text-center"><?php echo Text::_('COM_PWTACL_DIAGNOSTICS_COMPLETED'); ?></h3>
                    </span>
                </div>
            </div>
        </div>

        <!-- Begin rebuild -->
        <form action="index.php" method="post" id="adminForm">
            <input type="hidden" name="option" value="com_pwtacl"/>
            <input type="hidden" name="task" value="diagnostics.rebuild"/>
			<?php echo HTMLHelper::_('form.token'); ?>
        </form>
        <!-- End rebuild -->
    </div>
</div>