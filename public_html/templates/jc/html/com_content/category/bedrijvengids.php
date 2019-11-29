<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

HTMLHelper::_('script', 'templates/jc/js/filtercompanies.js', array('version' => 'auto'));

$companies = [];

foreach ($this->intro_items as $key => $item)
{
	$fields = [];

	foreach ($item->jcfields as $field)
	{
		$fields[$field->name] = $field;
	}

	if ($key === 0)
	{
		$provinces   = formatOptions($fields['provincie']->fieldparams->get('options'));
		$specialisms = formatOptions($fields['specialiteiten']->fieldparams->get('options'));
		$sizes       = formatOptions($fields['bedrijfsgrootte']->fieldparams->get('options'));
	}

	$company = (object) [
		'title'            => $item->title,
		'link'             => substr(Route::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid)), 1),
		'descriptionintro' => HTMLHelper::_('string.truncate', $item->text, 250, false, false),
		'logo'             => $fields['logo']->rawvalue,
		'city'             => $fields['plaats']->rawvalue,
		'province'         => valueToName($provinces, $fields['provincie']->rawvalue),
		'specialisms'      => valueToName($specialisms, $fields['specialiteiten']->rawvalue),
		'description'      => $item->text,
		'size'             => valueToName($sizes, $fields['bedrijfsgrootte']->rawvalue)
	];

	$companies[] = $company;
}

function formatOptions($rawDatas)
{
	$data = [];

	foreach ($rawDatas as $rawData)
	{
		if ($rawData->value)
		{
			$data[$rawData->value] = $rawData->name;
		}
	}

	return $data;
}

function valueToName($dataset, $items)
{
	$formatted = [];

	foreach ($items as $item)
	{
		$formatted[] = $dataset[$item];
	}

	return $formatted;
}

$companies = json_encode($companies, JSON_UNESCAPED_SLASHES);
?>
<div class="row">
	<div class="content-12">
		<div class="page-header">
			<h1>Joomla! bedrijvengids</h1>
		</div>
		<div class="lead">
			<p>Op zoek naar een Joomla-expert voor het bouwen van een site, advies, ontwikkeling van templates en/of extensies? In de Joomla bedrijvengids vind je de actieve Joomla-specialisten van Nederland en België. Zelf in de gids staan?
				<a href="<?php echo Route::_('index.php?Itemid=2871'); ?>">Meld je aan</a>.<br/>
			</p>
		</div>
	</div>
</div>

<div class="bedrijvengids bedrijvengids__wrapper">
	<div class="row">
		<div class="content-4">
			<dl id="narrow-mobile" class="bedrijvengids__filter bedrijvengids__filter--mobile">
				<dt class="bedrijvengids__filter-header bedrijvengids__filter-header--mobile">
					Verfijn resultaten
					<i class="fa fa-chevron-up pull-right"></i>
				</dt>
			</dl>

			<dl id="narrow-by-list" class="bedrijvengids__filter bedrijvengids__filter--filters">

				<dd class="list-group list-group-flush bedrijvengids__filter-content bedrijvengids__filter-content--trefwoord">
					<label class="sr-only" for="searchbox">Search</label>
					<input type="search" class="form-control" placeholder="Zoek in bedrijvengids..." id="searchbox"/>
				</dd>

				<div class="panel panel-home panel-filter">
					<dt class="panel-heading bedrijvengids__filter-header--specialisms">
						Specialiteiten <i class="fa fa-chevron-up pull-right"></i>
					</dt>
					<dd class="list-group list-group-flush bedrijvengids__filter-content bedrijvengids__filter-content--specialisms">
						<div class="panel-home js-specialisms" id="filter_specialisms">
							<?php foreach ($specialisms as $key => $value) : ?>
								<input type="checkbox" value="<?php echo $value; ?>" data-label="<?php echo $value; ?>">
								<label class="list-group-item"><?php echo $value; ?></label>
							<?php endforeach; ?>
						</div>
					</dd>
				</div>

				<div class="panel panel-home panel-filter">
					<dt class="panel-heading bedrijvengids__filter-header--province">
						Provincie <i class="fa fa-chevron-up pull-right"></i>
					</dt>
					<dd class="list-group list-group-flush bedrijvengids__filter-content bedrijvengids__filter-content--province">
						<div class="panel-home js-province" id="filter_province">
							<?php foreach ($provinces as $key => $value) : ?>
								<input type="checkbox" value="<?php echo $value; ?>" data-label="<?php echo $value; ?>">
								<label class="list-group-item"><?php echo $value; ?></label>
							<?php endforeach; ?>
						</div>
					</dd>
				</div>

				<div class="panel panel-home panel-filter">
					<dt class="panel-heading bedrijvengids__filter-header--size">
						Bedrijfsgrootte <i class="fa fa-chevron-up pull-right"></i>
					</dt>
					<dd class="list-group list-group-flush bedrijvengids__filter-content bedrijvengids__filter-content--size">
						<div class="panel-home js-size" id="filter_size">
							<?php foreach ($sizes as $key => $value) : ?>
								<input type="checkbox" value="<?php echo $value; ?>" data-label="<?php echo $value; ?>">
								<label class="list-group-item"><?php echo $value; ?></label>
							<?php endforeach; ?>
						</div>
					</dd>
				</div>

			</dl>
		</div>

		<div class="content-8 bedrijvengids__collection">
			<div class="row bedrijvengids__countsorting">
				<div class="col-xs-12 col-sm-6 bedrijvengids__count">
					<span id="total_partners">0</span> bedrijven gevonden
				</div>
				<div class="col-xs-12 col-sm-6 bedrijvengids__sorting">
					Sorteer:

					<label class="bedrijvengids__sorting-item radio-inline">
						<input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="title-asc">
						<span>A - Z</span>
					</label>
					<label class="bedrijvengids__sorting-item radio-inline">
						<input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="title-desc">
						<span>Z - A</span>
					</label>
					<label class="bedrijvengids__sorting-item radio-inline hidden">
						<input type="radio" name="inlineRadioOptions" id="inlineRadio3" value="title-rand" checked>
						<span>Willekeurig</span>
					</label>
				</div>
			</div>

			<div id="collection"></div>

			<div class="bedrijvengids__pagination">
				<div id="pagination"></div>
				<div id="per_page" class="hidden"></div>
			</div>

			<script>var collection =<?php echo $companies; ?>;</script>

			<script id="collection-template" type="text/html">
				<div class="bedrijvengids__item well well-sm">
					<div class="row">
						<div class="bedrijvengids__figwrap col-xs-8 col-xs-push-2 col-sm-3 col-sm-push-0 col-md-3">
							<figure class="bedrijvengids__figure ">
								<?php
								$img = '<img src="<%=logo%>" class="bedrijvengids__image img-responsive" alt="<%=title%>">';
								echo HTMLHelper::_('link', '<%=link%>', $img, array('class' => 'bedrijvengids__url partners_url--figure'));
								?>
							</figure>
						</div>
						<div class="bedrijvengids__content col-xs-12 col-sm-9 col-md-9 ">
							<header class="bedrijvengids__header row">
                                    <span class="bedrijvengids__title col-xs-12">
                                        <?php echo HTMLHelper::_('link', '<%=link%>', '<%=title%>', array('class' => 'bedrijvengids__url')); ?>
                                    </span>
							</header>
							<div class="bedrijvengids__text">
								<div class="bedrijvengids__location"><i class="fa fa-map-marker"></i> <%=city%>,
									<%=province%>
								</div>
								<div class="bedrijvengids__description">
									<%=descriptionintro%>
								</div>
								<div class="bedrijvengids__action">
									<?php
									echo HTMLHelper::_('link', '<%=link%>', '<i class="fa fa-chevron-right"></i><span class="hidden">Meer informatie over <%=title%></span>', array('class' => ''));
									?>
								</div>
							</div>
						</div>
					</div>
					<div class="row hidden-xs">
						<div class="col-xs-12 bedrijvengids__tags">
							<span>Specialiteiten:</span>
							<% for(var item in specialisms) { %>
							<?php
							$text = '<%= specialisms[item] %>';
							$url  = JRoute::_('index.php?option=com_content&view=category&id=374') . '#specialisms=' . $text . '&';
							?>
							<span class="tag" onclick="document.location.href='<?php echo $url; ?>'; window.location.reload(true)"><?php echo $text; ?></span>
							<% } %>
						</div>
					</div>
				</div>
			</script>

			<script>
				(function ($) {
					$('#narrow-mobile').click(function () {
						$(this).find('.fa').toggleClass('fa-chevron-up fa-chevron-down');
						$('#narrow-by-list').slideToggle('slow');
					});

					function toggleShow() {
						var opened = $(this).hasClass('less');
						$(this).text(opened ? 'Toon meer...' : 'Toon minder.').toggleClass('less', !opened);
						$(this).siblings('li.toggleable').slideToggle('slow');
					}

					$('#narrow-by-list dt').click(function () {
						$(this).find('.fa').toggleClass('fa-chevron-up fa-chevron-down');
						$(this).nextUntil('dt').slideToggle('slow');
					});
				})(jQuery);
			</script>
		</div>
	</div>
</div>
