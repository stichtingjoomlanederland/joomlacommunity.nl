<?php
/**
 * @package       RSEvents!Pro
 * @copyright (C) 2015 www.rsjoomla.com
 * @license       GPL, http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

defined('_JEXEC') or die('Restricted access');

JLoader::register('ContentHelperRoute', JPATH_SITE . '/components/com_content/helpers/route.php');

if ($this->category)
{
	$reports = null;
	$db      = Factory::getDbo();
	$query   = $db->getQuery(true)
		->select($db->quoteName('id'))
		->from($db->quoteName('#__categories'))
		->where($db->quoteName('alias') . " = " . $db->quote($this->category->alias))
		->where($db->quoteName('extension') . " = " . $db->quote('com_content'));
	$db->setQuery($query);
	$catid = $db->loadResult();

	if ($catid)
	{
		// Module params
		$params = array(
			'count'                      => 3,
			'layout'                     => 'jc:jugreports',
			'category_filtering_type'    => 1,
			'catid'                      => array($catid),
			'article_ordering'           => 'publish_up',
			'article_ordering_direction' => 'DESC',
			'show_author'                => 1,
			'show_introtext'             => 1,
			'introtext_limit'            => 140,
			'created_by_alias'           => array()
		);

		// Load module and add params
		$module         = ModuleHelper::getModule('mod_articles_category');
		$module->params = json_encode($params);

		// Render module
		$reports = Factory::getDocument()->loadRenderer('module')->render($module);
	}
}

$usergroup  = ($this->category) ? $this->category->metadata->get('author') : '';
$organisers = Access::getUsersByGroup($usergroup);
?>


<script type="text/javascript">
    var rseproMask = '<?php echo $this->escape($this->mask); ?>';
    var rseproCurrency = '<?php echo $this->escape($this->currency); ?>';
    var rseproDecimals = '<?php echo $this->escape($this->decimals); ?>';
    var rseproDecimal = '<?php echo $this->escape($this->decimal); ?>';
    var rseproThousands = '<?php echo $this->escape($this->thousands); ?>';
</script>

<?php if ($this->category): ?>
    <div class="well">

        <div class="row">
            <div class="col-md-<?php if ($organisers): ?>8<?php else: ?>12<?php endif; ?>">
                <div class="page-header">
                    <div class="pull-right">
						<?php if ($this->params->get('search', 1)) : ?>
                            <form method="post"
                                  action="<?php echo $this->escape(JRoute::_(JURI::getInstance(), false)); ?>"
                                  name="adminForm" id="adminForm">

                                <div class="rsepro-filter-container">


									<?php if (!empty($this->columns))
									{ ?>
										<?php for ($i = 0; $i < count($this->columns); $i++)
									{ ?>
										<?php $hash = sha1(@$this->columns[$i] . @$this->operators[$i] . @$this->values[$i]); ?>
                                        <div id="<?php echo $hash; ?>">
                                            <input type="hidden" name="filter_from[]"
                                                   value="<?php echo $this->escape($this->columns[$i]); ?>">
                                            <input type="hidden" name="filter_condition[]"
                                                   value="<?php echo $this->escape($this->operators[$i]); ?>">
                                            <input type="hidden" name="search[]"
                                                   value="<?php echo $this->escape($this->values[$i]); ?>">
                                            <div class="btn-group">
                                                <!--<span class="btn btn-default disabled btn-small"><?php echo $this->escape($this->values[$i]); ?></span>-->

                                                <a href="javascript:void(0)"
                                                   class="btn btn-sm btn-default rsepro-close">
                                                    <i class="icon-delete"></i> Toon alles
                                                </a>
                                            </div>
                                        </div>

                                        <li class="rsepro-filter-conditions" <?php echo $i == (count($this->columns) - 1) ? 'style="display: none;"' : ''; ?>>
                                            <a class="btn btn-small"><?php echo ucfirst(Text::_('COM_RSEVENTSPRO_GLOBAL_' . $this->operator)); ?></a>
                                        </li>

									<?php } ?>
									<?php } ?>


                                    <input type="hidden" name="filter_from[]" value="">
                                    <input type="hidden" name="filter_condition[]" value="">
                                    <input type="hidden" name="search[]" value="">
                                    <input type="hidden" name="filter_featured[]" value="">
                                    <input type="hidden" name="filter_price[]" value="">
                                </div>
                            </form>
						<?php else: ?>
							<?php if (!empty($this->columns)) : ?>
                                <!--<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=clear'); ?>" class="rs_filter_clear"><?php echo Text::_('COM_RSEVENTSPRO_GLOBAL_CLEAR_FILTER'); ?></a>-->
							<?php endif; ?>
						<?php endif; ?>
                    </div>

                    <h1><?php echo $this->category->title; ?></h1>

                </div>

                <div class="lead">
                    <p><?php echo HTMLHelper::_('content.prepare', $this->category->description, '', 'com_content.category'); ?></p>
	                <?php
	                $modules = ModuleHelper::getModules('gebruikersgroepen-lead');
	                $attribs = array(
		                'style' => 'tpl'
	                );

	                if (!empty($modules[0]))
	                {
		                foreach ($modules as $module)
		                {
			                echo ModuleHelper::renderModule($module, $attribs);
		                }
	                }
	                ?>
                </div>
            </div>

			<?php if ($organisers) : ?>
                <div class="col-md-4">
                    <!-- Show organizers -->
                    <div class="panel panel-agenda">
                        <div class="panel-heading">Organisatoren</div>
                        <ul class="list-group list-group-flush panel-agenda"><?php
                            foreach ($organisers as $organiser) :
                                echo '<li class="list-group-item list-group-item--inline">';
                                echo LayoutHelper::render('template.easydiscuss.profile', ['id' => $organiser, 'type' => 'avatar']);
                                echo LayoutHelper::render('template.easydiscuss.profile', ['id' => $organiser]);
                                echo '</li>';
                            endforeach;
                            ?></ul>
                    </div>
                    <!--//end Show organizers -->
                </div>
			<?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php if ($this->category && $this->category->level > 1): ?>
<div class="row">
    <div class="col-md-<?php if ($reports): ?>8<?php else: ?>12<?php endif; ?>">
		<?php endif; ?>
        <h2>Agenda</h2>
        <div class="well cards cards--media">
			<?php $count = count($this->events); ?>
			<?php if (!empty($this->events)) : ?>

				<?php $eventIds = rseventsproHelper::getEventIds($this->events, 'id'); ?>
				<?php $this->events = rseventsproHelper::details($eventIds); ?>
				<?php foreach ($this->events as $details)
				{ ?>
					<?php if (isset($details['event']) && !empty($details['event'])) $event = $details['event'];
				else continue; ?>
					<?php if (!rseventsproHelper::canview($event->id) && $event->owner != $this->user) continue; ?>
					<?php $full = rseventsproHelper::eventisfull($event->id); ?>
					<?php $ongoing = rseventsproHelper::ongoing($event->id); ?>
					<?php $categories = (isset($details['categories']) && !empty($details['categories'])) ? Text::_('COM_RSEVENTSPRO_GLOBAL_CATEGORIES') . ': ' . $details['categories'] : ''; ?>
					<?php $tags = (isset($details['tags']) && !empty($details['tags'])) ? Text::_('COM_RSEVENTSPRO_GLOBAL_TAGS') . ': ' . $details['tags'] : ''; ?>
					<?php $incomplete = !$event->completed ? ' rs_incomplete' : ''; ?>
					<?php $featured = $event->featured ? ' rs_featured' : ''; ?>
					<?php $repeats = rseventsproHelper::getRepeats($event->id); ?>
					<?php $lastMY = rseventsproHelper::showdate($event->start, 'mY'); ?>

					<?php if ($monthYear = rseventsproHelper::showMonthYear($event->start, 'events' . $this->fid))
				{ ?>
                    <br>
                    <div class="page-header"><h2><?php echo $monthYear; ?></h2></div>
				<?php } ?>
                    <div class="card__wrapper">
                        <div class="card">
                            <figure class="card__figure"><?php
								$src  = rseventsproHelper::thumb($event->id, $this->config->icon_small_width);
								$alt  = null;
								$data = array(
									'style' => 'width:' . $this->config->icon_small_width . 'px'
								);
								$href = rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id=' . rseventsproHelper::sef($event->id, $event->name), false, rseventsproHelper::itemid($event->id));
								$text = HTMLHelper::_('image', $src, $alt, $data);
								echo HTMLHelper::_('link', $href, $text);
								?>
                            </figure>
                            <div class="card__body">
                                <p class="text-muted"><em>
										<?php if ($event->allday)
										{ ?>
											<?php if (!empty($event->options['start_date_list']))
										{ ?>
											<?php echo rseventsproHelper::showdate($event->start, $this->config->global_date, true); ?>
										<?php } ?>
										<?php } else
										{ ?>

										<?php if (!empty($event->options['start_date_list']) || !empty($event->options['start_time_list']) || !empty($event->options['end_date_list']) || !empty($event->options['end_time_list']))
										{ ?>
										<?php if (!empty($event->options['start_date_list']) || !empty($event->options['start_time_list']))
										{ ?>
										<?php if ((!empty($event->options['start_date_list']) || !empty($event->options['start_time_list'])) && empty($event->options['end_date_list']) && empty($event->options['end_time_list']))
										{ ?>
                                        <span class="rsepro-event-starting-block">

						<?php }
						else
						{ ?>

						<?php } ?>
											<?php echo rseventsproHelper::showdate($event->start, rseventsproHelper::showMask('list_start', $event->options), true); ?>

											<?php } ?>
											<?php if (!empty($event->options['end_date_list']) || !empty($event->options['end_time_list']))
											{ ?>
												<?php if ((!empty($event->options['end_date_list']) || !empty($event->options['end_time_list'])) && empty($event->options['start_date_list']) && empty($event->options['start_time_list']))
											{ ?>

												<?php echo Text::_('COM_RSEVENTSPRO_EVENT_ENDING_ON'); ?>
											<?php }
											else
											{ ?>

												<?php echo Text::_('COM_RSEVENTSPRO_EVENT_UNTIL'); ?>
											<?php } ?>
												<?php if (strtotime($event->end) - strtotime($event->start) > 86400): ?>

												<?php echo rseventsproHelper::showdate($event->end, rseventsproHelper::showMask('list_start', $event->options), true); ?> uur
											<?php else: ?>
												<?php echo rseventsproHelper::showdate($event->end, 'H:i', true); ?> uur
											<?php endif; ?>

											<?php } ?>
											<?php } ?>

											<?php } ?>

											<?php if ($this->category && $this->category->level > 1): ?>
											<?php else: ?>
                                                -
												<?php if (!empty($event->options['show_categories_list']))
												{ ?>
													<?php echo $details['categories']; ?>
												<?php } ?>
											<?php endif; ?>
                                    </em></p>
                                <h3 class="media-heading"><?php
									$href = rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id=' . rseventsproHelper::sef($event->id, $event->name), false, rseventsproHelper::itemid($event->id));
									$text = $event->name;
									echo HTMLHelper::_('link', $href, $text);
									?></h3>
                                <p><?php echo HTMLHelper::_('string.truncate', $event->description, 210, true, false); ?></p>
                            </div>
                        </div>
                    </div>
				<?php } ?>


				<?php rseventsproHelper::clearMonthYear('events' . $this->fid, @$lastMY); ?>
                <div class="rs_loader" id="rs_loader" style="display:none;">
                    <img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/loader.gif" alt=""/>
                </div>
				<?php if ($this->total > $count)
				{ ?>
                    <!--                    <a class="rs_read_more" id="rsepro_loadmore">--><?php //echo Text::_('COM_RSEVENTSPRO_GLOBAL_LOAD_MORE');
					?><!--</a>-->
				<?php } ?>
                <div class="hidden">
                    <span id="total" class="rs_hidden"><?php echo $this->total; ?></span>
                    <span id="Itemid"
                          class="rs_hidden"><?php echo Factory::getApplication()->input->getInt('Itemid'); ?></span>
                    <span id="langcode" class="rs_hidden"><?php echo rseventsproHelper::getLanguageCode(); ?></span>
                    <span id="parent"
                          class="rs_hidden"><?php echo Factory::getApplication()->input->getInt('parent'); ?></span>
                    <span id="rsepro-prefix" class="rs_hidden"><?php echo 'events' . $this->fid; ?></span>
                </div>
			<?php else : ?>
				<?php echo Text::_('Er zijn momenteel geen bijeenkomsten gepland'); ?>
			<?php endif; ?>
        </div>
		<?php if ($this->category && $this->category->level > 1): ?>
    </div>
	<?php if ($reports) : ?>
        <div class="col-md-4">
			<?php echo $reports; ?>
        </div>
	<?php endif; ?>
</div>
<?php endif; ?>

<script type="text/javascript">
    jQuery(document).ready(function () {
		<?php if ($this->total > $count) { ?>
        jQuery('#rsepro_loadmore').on('click', function () {
            rspagination('events', jQuery('#rs_events_container > li[class!="rsepro-month-year"]').length);
        });
		<?php } ?>

		<?php if (!empty($count)) { ?>
        jQuery('#rs_events_container li[class!="rsepro-month-year"]').on({
            mouseenter: function () {
                jQuery(this).find('div.rs_options').css('display', '');
            },
            mouseleave: function () {
                jQuery(this).find('div.rs_options').css('display', 'none');
            }
        });
		<?php } ?>

		<?php if ($this->params->get('search', 1)) { ?>
        var options = {};
        options.condition = '.rsepro-filter-operator';
        options.events = [{'#rsepro-filter-from': 'rsepro_select'}];
        jQuery().rsjoomlafilter(options);
		<?php } ?>
    });
</script>
