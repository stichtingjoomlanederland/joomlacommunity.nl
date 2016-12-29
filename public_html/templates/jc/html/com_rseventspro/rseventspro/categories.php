<?php
/**
 * @package       RSEvents!Pro
 * @copyright (C) 2015 www.rsjoomla.com
 * @license       GPL, http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
$count = count($this->categories);

// Get organizers of event, JC custom
require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php');
$profile = DiscussHelper::getTable('Profile');
?>

<?php if ($this->params->get('show_page_heading', 1))
{ ?>
	<?php $title = $this->params->get('page_heading', ''); ?>
    <h1><?php echo !empty($title) ? $this->escape($title) : JText::_('COM_RSEVENTSPRO_CATEGORIES_TITLE'); ?></h1>
<?php } ?>

<?php if (!empty($this->categories)) : ?>
	<?php foreach ($this->categories as $category): ?>
		<?php if ($category->level == 2): ?>
            <h1><?php echo $category->title; ?></h1>
		<?php else: ?>
            <div class="well">

                <div class="row">
                    <div class="col-md-8">
                        <div class="page-header">
                            <h2>
                                <a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&category=' . rseventsproHelper::sef($category->id, $category->title)); ?>">
									<?php echo $category->title; ?>
                                </a>
                            </h2>
                        </div>
                        <div class="lead">
							<?php // echo rseventsproHelper::shortenjs($category->description, $category->id, 255, $this->params->get('type', 1)); ?>

							<?php
							// Module params
							$params = array(
								'limit'           => 3,
								'layout'          => 'jc:jug',
								'ordering'        => 'start',
								'order'           => 'asc',
								'archived'        => 0,
								'categories'      => array($category->id),
								'moduleclass_sfx' => 'panel-agenda',
							);

							// Load module and add params
							$module         = JModuleHelper::getModule('mod_rseventspro_upcoming');
							$module->params = json_encode($params);

							// Render module
							echo JFactory::getDocument()->loadRenderer('module')->render($module);
							?>

                            <a class="btn btn-agenda" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&category=' . rseventsproHelper::sef($category->id, $category->title)); ?>">
                                Bekijk alle bijeenkomsten
                            </a>

                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Show organizers -->
						<?php
						$categorymeta = null;
						$organisers   = null;
						$categorymeta = json_decode($category->metadata);
						$organisers   = ($categorymeta->author) ? explode(',', $categorymeta->author) : null;
						?>
						<?php if (!empty($organisers)) : ?>
                            <div class="panel panel-agenda">
                                <div class="panel-heading">Organisatoren</div>
                                <div class="list-group list-group-flush panel-agenda">
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
                    </div>
                </div>
            </div>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>