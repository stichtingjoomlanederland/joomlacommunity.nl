<?php
/**
 * @package       RSEvents!Pro
 * @copyright (C) 2015 www.rsjoomla.com
 * @license       GPL, http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.modal', '.rs_modal');

$details     = rseventsproHelper::details($this->event->id);
$event       = $details['event'];
$categories  = $details['categories'];
$tags        = $details['tags'];
$files       = $details['files'];
$repeats     = $details['repeats'];
$full        = rseventsproHelper::eventisfull($this->event->id);
$ongoing     = rseventsproHelper::ongoing($this->event->id);
$featured    = $event->featured ? ' rs_featured_event' : '';
$description = empty($event->description) ? $event->small_description : $event->description;

// Get organizers of event, JC custom
require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php');
$profile = DiscussHelper::getTable('Profile');

$categoriesinfo = rseventsproHelper::categories($this->event->id);

$db    = JFactory::getDbo();
$query = $db->getQuery(true);

$query->clear()
	->select($db->qn('c.metadata'))
	->from($db->qn('#__categories', 'c'))
	->where($db->qn('c.id') . ' = ' . (int) $categoriesinfo[0]->id);

$db->setQuery($query);
$categorymeta = $db->loadResult();
$categorymeta = json_decode($categorymeta);
$organisers   = ($categorymeta->author) ? explode(',', $categorymeta->author) : null;
// End organizers

$links = rseventsproHelper::getConfig('modal', 'int');
$class = '';
$rel_s = '';
$rel_i = '';
$rel_g = '';
if ($links == 2) $class = ' rs_modal';
if ($links == 1) $rel_s = ' rel="rs_subscribe"';
else if ($links == 2) $rel_s = ' rel="{handler: \'iframe\',size: {x:' . $this->modal_width . ',y:' . $this->modal_height . '}}"';
if ($links == 1) $rel_i = ' rel="rs_invite"';
else if ($links == 2) $rel_i = ' rel="{handler: \'iframe\',size: {x:' . $this->modal_width . ',y:' . $this->modal_height . '}}"';
if ($links == 1) $rel_g = ' rel="rs_message"';
else if ($links == 2) $rel_g = ' rel="{handler: \'iframe\',size: {x:' . $this->modal_width . ',y:' . $this->modal_height . '}}"';
$tmpl = $links == 0 ? '' : '&tmpl=component';
?>
<?php JFactory::getApplication()->triggerEvent('rsepro_onBeforeEventDisplay', array(array('event' => &$event, 'categories' => &$categories, 'tags' => &$tags))); ?>
<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "Event",
  "name": "<?php echo $this->escape($event->name); ?>",
  "startDate" : "<?php echo rseventsproHelper::showdate($event->start, 'Y-m-d H:i:s'); ?>",
  <?php if (!$event->allday)
	{ ?>"endDate" : "<?php echo rseventsproHelper::showdate($event->end, 'Y-m-d H:i:s'); ?>",<?php echo "\n";
	} ?>
  "url" : "<?php echo $this->root . rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id=' . rseventsproHelper::sef($event->id, $event->name), false, rseventsproHelper::itemid($event->id)); ?>",
  "image" : "<?php echo $this->escape($this->root . $details['image_b']); ?>",
  "description": "<?php echo strip_tags($description); ?>",
  "location" :
  {
    "@type" : "Place",
    "name" : "<?php echo $this->escape($event->location); ?>",
    "address" :
    {
      "@type" : "PostalAddress",
      "name" : "<?php echo $this->escape($event->address); ?>"
    }
  }

</script>
<div class="row">
	<div class="content-8">
		<div class="well<?php if ($details['image_b']): ?> photoheader<?php endif; ?>">
			<?php if ($details['image_b']): ?>
				<div class="photobox">
					<img src="<?php echo $details['image_b']; ?>"/>
				</div>
			<?php endif; ?>

			<div class="row">
				<div class="col-md-12">
					<div class="item">
						<div class="page-header">
							<h1>
								<?php echo $this->escape($event->name); ?>
							</h1>
						</div>
						<div class="item-content">
							<?php echo $description; ?>

							<?php if ($event->ldescription): ?>
								<h3>Over de locatie</h3>
								<?php echo $event->ldescription; ?>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="content-4">
		<div class="panel panel-bijeenkomsten">
			<div class="panel-heading">Details</div>
			<div class="panel-body">
				<?php if (!($this->admin || $event->owner == $this->user || $event->sid == $this->user) && $this->permissions['can_edit_events'])
				{ ?>
					<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=edit&id=' . rseventsproHelper::sef($event->id, $event->name)); ?>" class="btn btn-default btn-block">
						<i class="fa fa-pencil fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_EDIT'); ?>
					</a>
				<?php } ?>


				<!-- Categories -->
				<?php if (!empty($categories) && !empty($this->options['show_categories']))
				{ ?>
					<p>
						<strong><i class="fa fa-folder fa-fw"></i> Categorie</strong><br>
						<?php echo $categories; ?>
					</p>

				<?php } ?>

				<!-- Start / End date -->
				<?php if ($event->allday): ?>
					<?php if (!empty($this->options['start_date'])): ?>
						<p>
							<strong><i class="fa fa-calendar fa-fw"></i> Datum</strong><br>
							<?php echo rseventsproHelper::showdate($event->start, 'l j F Y', false); ?>
						</p>
					<?php endif; ?>
				<?php else : ?>
					<?php if (!empty($this->options['start_date']) || !empty($this->options['start_time']) || !empty($this->options['end_date']) || !empty($this->options['end_time'])) : ?>

						<?php if (rseventsproHelper::showdate($event->start, 'j F Y', false) != rseventsproHelper::showdate($event->end, 'j F Y', false)): ?>
							<p>
								<strong><i class="fa fa-calendar fa-fw"></i> Datum</strong><br>
								<?php echo rseventsproHelper::showdate($event->start, 'l j F', false); ?> - <?php echo rseventsproHelper::showdate($event->end, 'l j F Y', false); ?>
							</p>
						<?php else: ?>
							<?php if (!empty($this->options['start_date']) || !empty($this->options['start_time'])): ?>
								<p>
									<strong><i class="fa fa-calendar fa-fw"></i> Datum</strong><br>
									<?php echo rseventsproHelper::showdate($event->start, 'l j F Y', false); ?>
								</p>
							<?php endif; ?>
							<?php if (!empty($this->options['start_time']) && !empty($this->options['end_time'])) : ?>
								<p>
									<strong><i class="fa fa-clock-o fa-fw"></i> Tijd</strong><br>
									<?php echo rseventsproHelper::showdate($event->start, 'H:i', true); ?> - <?php echo rseventsproHelper::showdate($event->end, 'H:i', true); ?> uur
								</p>
							<?php endif ?>
						<?php endif ?>

					<?php endif; ?>

				<?php endif; ?>
				<!--//end Start / End date -->

				<!-- Location -->
				<?php if (!empty($event->lpublished) && !empty($this->options['show_location']))
				{ ?>
					<p>
						<strong><i class="fa fa-map-marker fa-fw"></i> Locatie</strong><br>
						<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_AT'); ?>
						<a href="<?php echo $event->locationlink; ?>" target="_blank"><?php echo $event->location; ?></a><br>
						<?php echo $event->address; ?><br>
						<a href="http://maps.google.nl/maps?daddr=<?php echo $event->address; ?>" target="_blank">Open in Google Maps</a>
					</p>
				<?php } ?>
				<!--//end Location -->

				<!-- Invite/Join/Unsubscribe -->
				<!-- Changed If Statement to make sure subscribe button disappeared when user was subscribed ::ConConNL-->
				<?php if (!$this->issubscribed && $this->cansubscribe['status']) : ?>
					<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=subscribe&id=' . rseventsproHelper::sef($event->id, $event->name) . $tmpl); ?>" class="btn btn-success btn-block"<?php echo $rel_s; ?> >
						<i class="fa fa-check fa-fw"></i> <?php echo JText::_('Aanmelden'); ?>
					</a>
				<?php endif; ?>

				<?php if (!$this->eventended) : ?>
					<?php if ($this->issubscribed) : ?>
						<?php if ($this->canunsubscribe) : ?>
							<?php if ($this->issubscribed == 1) : ?>
								<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.unsubscribe&id=' . rseventsproHelper::sef($event->id, $event->name)); ?>" class="btn btn-danger btn-block ">
									<i class="fa fa-times fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_UNSUBSCRIBE'); ?>
								</a>
							<?php else : ?>
								<?php $Uclass = $links == 0 || $links == 2 ? 'rs_modal' : ''; ?>
								<?php $Urel = $links == 0 || $links == 2 ? 'rel="{handler: \'iframe\'}"' : 'rel="rs_unsubscribe"'; ?>
								<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=unsubscribe&id=' . rseventsproHelper::sef($event->id, $event->name) . '&tmpl=component'); ?>" class="btn btn-default btn-block <?php echo $Uclass; ?>" <?php echo $Urel; ?>>
									<i class="fa fa-times fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_UNSUBSCRIBE'); ?>
								</a>
							<?php endif; ?>
						<?php endif; ?>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>

		<!-- Show organizers -->
		<?php if (!empty($organisers)) : ?>
			<div class="panel panel-bijeenkomsten">
				<div class="panel-heading">Organisatoren</div>
				<div class="list-group list-group-flush panel-bijeenkomsten">
					<?php foreach ($organisers as $organiser) : ?>
						<?php $profile->load($organiser); ?>

						<a class="list-group-item" href="<?php echo $profile->getLink(); ?>">
							<img class="img-circle" src="<?php echo $profile->getAvatar(); ?>" width="50px" height="50px"/>
							<?php echo $profile->nickname; ?>
						</a>

					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
		<!--//end Show organizers -->


		<!-- Show subscribers -->
		<?php if ($event->show_registered) : ?>
			<?php if (!empty($this->guests)) : ?>
				<div class="panel panel-bijeenkomsten">
					<div class="panel-heading">Wij gaan!</div>
					<div class="list-group list-group-flush panel-bijeenkomsten">
						<?php foreach ($this->guests as $guest) : ?>

							<?php if (!empty($guest->url)) : ?>
								<a class="list-group-item" href="<?php echo $guest->url; ?>">
									<?php echo $guest->avatar; ?>
									<?php echo $guest->name; ?>
								</a>
							<?php else: ?>
								<a class="list-group-item" href="#">
									<?php echo $guest->avatar; ?>
									<?php echo $guest->name; ?>
								</a>
							<?php endif; ?>

						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<!--//end Show subscribers -->

	</div>
</div>

<?php JFactory::getApplication()->triggerEvent('rsepro_onAfterEventDisplay', array(array('event' => $event, 'categories' => $categories, 'tags' => $tags))); ?>
