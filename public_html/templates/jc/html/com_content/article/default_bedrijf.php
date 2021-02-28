<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

// Create shortcuts to some parameters.
$params = $this->item->params;
$images = json_decode($this->item->images);
$urls = json_decode($this->item->urls);
$canEdit = $params->get('access-edit');
$user = Factory::getUser();

foreach ($this->item->jcfields as $field) {
	switch ($field->name) {
		case 'certified':
		case 'portfolio':
			$this->item->customfields[$field->name] = json_decode($field->rawvalue);
			break;

		case 'bedrijfsgrootte':
			$this->item->customfields[$field->name] = $field->value;
			break;

		default:
			$this->item->customfields[$field->name] = $field->rawvalue;
			break;
	}
}

$src = 'templates/' . Factory::getApplication()->getTemplate() . '/images/jc-pattern.png';
$class = ' photobox--empty';

if ($this->item->customfields['foto']) {
	$src = $this->item->customfields['foto'];
	$class = null;
}
?>

	<div class="well photoheader">

		<div class="photobox<?php echo $class; ?>">
			<?php echo HTMLHelper::_('image', $src, ''); ?>
		</div>

		<div class="row">
			<div class="col-md-3">

				<div class="company-logo">
					<img src="<?php echo $this->item->customfields['logo']; ?>"
						 alt="<?php echo $this->item->title; ?>"/>
				</div>

				<div class="item-meta">
					<div class="article-meta auteur-info">
						<p class="article-meta-label">bezoekadres</p>
						<p>
							<?php echo $this->item->customfields['adres']; ?>
							<br><?php echo $this->item->customfields['postcode']; ?> <?php echo $this->item->customfields['plaats']; ?>
							<br><?php echo $this->item->customfields['provincie'][0]; ?>
						</p>
					</div>

					<div class="article-meta">
						<p class="article-meta-label">kvk-nummer</p>
						<p><?php echo $this->item->customfields['kvk']; ?></p>
					</div>

					<div class="article-meta">
						<p class="article-meta-label">bedrijfsgrootte</p>
						<p><?php echo $this->item->customfields['bedrijfsgrootte']; ?></p>
					</div>

					<div class="article-meta">
						<p class="article-meta-label">contact</p>
						<p>
							<?php if ($this->item->customfields['contactpersoon']): ?>
								<i class="fa fa-user" aria-hidden="true"></i>
								<?php echo $this->item->customfields['contactpersoon']; ?>
								<br>
							<?php endif; ?>
							<?php if ($this->item->customfields['telefoon']): ?>
								<i class="fa fa-phone" aria-hidden="true"></i>
								<?php echo $this->item->customfields['telefoon']; ?><br>
							<?php endif; ?>
							<?php if ($this->item->customfields['email']): ?>
								<i class="fa fa-envelope" aria-hidden="true"></i>
								<a href="mailto:<?php echo $this->item->customfields['email']; ?>"><?php echo $this->item->customfields['email']; ?></a>
								<br>
							<?php endif; ?>
							<i class="fa fa-globe" aria-hidden="true"></i>
							<a href="<?php echo $this->item->customfields['website']; ?>"><?php echo $this->item->customfields['website']; ?></a>
						</p>
					</div>

				</div>
			</div>

			<div class="col-md-8">
				<div class="item">
					<div class="page-header">
						<?php if ($canEdit) : ?>
							<?php echo HTMLHelper::_('icon.edit', $this->item, $params, ['class' => 'btn btn-aan-de-slag pull-right']); ?>
						<?php endif; ?>

						<h1>Over <?php echo $this->item->title; ?></h1>

						<?php if ($this->item->state == 0) : ?>
							<span class="label label-warning"><?php echo Text::_('JUNPUBLISHED'); ?></span>
						<?php endif; ?>
					</div>
					<div class="item-content">
						<?php echo $this->item->text; ?>

						<h3>Specialisaties van <?php echo $this->item->title; ?></h3>
						<div class="bedrijvengids__specialisaties">
							<?php foreach ($this->item->customfields['specialiteiten'] as $item): ?>
								<?php echo HTMLHelper::_('link',
									Route::_(ContentHelperRoute::getCategoryRoute($this->item->catslug)) . '#specialisms=' . rawurlencode($item) . '&',
									$item,
									[
										'class' => 'btn btn-default'
									]
								); ?>
							<?php endforeach; ?>
						</div>

						<?php if ($this->item->customfields['certified']): ?>
							<h3>Gecertificeerde Joomla-gebruikers</h3>
							<p>
								<img src="https://exam.joomla.org/images/badge.png"
									 class="certified"/>Bij <?php echo $this->item->title; ?> werken de volgende
								<a href="https://certification.joomla.org" target="_blank">gecertificeerde</a>
								Joomla-gebruikers:
							</p>
							<ul>
								<?php foreach ($this->item->customfields['certified'] as $item): ?>
									<li><?php echo HTMLHelper::_('link',
											$item->{'Link naar profiel op Certified User Directory'},
											$item->{'Naam'},
											[
												'target' => '_blank'
											]
										); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php if ($this->item->customfields['portfolio']): ?>
	<h2>Portfolio van <?php echo $this->item->title; ?></h2>
	<div class="row">
		<?php foreach ($this->item->customfields['portfolio'] as $item): ?>
			<div class=" col-md-4">
				<div class="panel panel-home"><?php
					echo HTMLHelper::_('link',
						$item->{'URL van website'},
						HTMLHelper::_('image',
							'https://image.thum.io/get/' . $item->{'URL van website'},
							'',
							[
								'class' => 'img-rounded img-responsive'
							]
						),
						[
							'target' => '_blank'
						]
					);
					?>
					<div class="panel-footer">
						<h3 class="panel-title"><?php echo $item->{'Naam van site'}; ?></h3>
						<?php echo HTMLHelper::_('link',
							$item->{'URL van website'},
							$item->{'URL van website'},
							[
								'target' => '_blank'
							]
						); ?></div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
