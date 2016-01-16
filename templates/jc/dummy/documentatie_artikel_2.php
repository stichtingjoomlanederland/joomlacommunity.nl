<div class="row">
	<div class="content-8">
		<div class="well">
			<div class="page-header">
				<div class="pull-right">
					<span class="label label-joomla1"><span class="jc-joomla"></span> Joomla 1.5</span>
					<span class="label label-joomla2"><span class="jc-joomla"></span> Joomla 2.5</span>
				</div>
				<h1>Template 1.5.x aanpassen naar 2.5.x </h1>
			</div>
			<p class="alert alert-info">Deze tips zijn afkomstig van
				<a mce_href="http://www.web-effect.nl" href="http://www.web-effect.nl">Anja Hage van Web-effect</a>.</p>

			<p class="lead">Templates voor Joomla 1.5 zullen niet zonder aanpassing werken in Joomla versie 2.5.&nbsp;In deze tutorial vind je de veranderingen die voor de nieuwe versie nodig zijn en kun je vinden hoe je je template weer kunt laten werken, als je bent overgegaan naar de nieuwste versie van Joomla.&nbsp;</p>

			<p class="alert">Bij uitgebreide templates die je niet zelfgemaakt hebt, kun je beter wachten op de aanpassing van de maker. Dit artikel informeert je over de meest belangrijke wijzigingen.</p>

			<h2>Stap 1 Veranderingen in de index.php</h2>

			<p>In de template index.php moeten een paar regels code aangepast worden.</p>

			<p>Maak een kopie van je index.php van je template voor Joomla 1.5. Sla de kopie op in een map waar je al je bestanden voor je aangepaste template voor versie 2.5 terug kunt vinden.</p>

			<p>Open nu de nieuwe index.php in een html editor.&nbsp;Daarin zie je als eerste regel staan:</p>

			<pre class="prettyprint linenums">&lt;?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?&gt;</pre>

			<p>Deze regel moet voor Joomla 2.5 aangepast worden naar:</p>

			<pre class="prettyprint linenums">&lt;?php defined('_JEXEC') or die; ?&gt;</pre>

			<p>Er is een nieuwe
				<b>extra regel code</b> voor het weergeven van de website naam en url in de Joomla 2.5 template. Voeg deze regel toe:
			</p>

			<pre class="prettyprint linenums">&lt;?php $app = JFactory::getApplication(); ?&gt;</pre>

			<p>De Joomla 1.5 template code voor het weergeven van de website naam is:</p>

			<pre class="prettyprint linenums">&lt;?php echo $mainframe-&gt;getCfg( 'sitename' ); ?&gt;</pre>

			<p>Deze code moet voor Joomla 2.5 aangepast worden naar:</p>

			<pre class="prettyprint linenums">&lt;?php echo $app-&gt;getCfg('sitename'); ?&gt;</pre>

			<p>De Joomla 1.5 template code voor het weergeven van de website url is:</p>

			<pre class="prettyprint linenums">&lt;?php echo $mainframe-&gt;getCfg('live_site'); ?&gt;</pre>

			<p>Deze code moet voor Joomla 2.5 aangepast worden naar:</p>

			<pre class="prettyprint linenums">&lt;?php echo $app-&gt;getCfg('live_site'); ?&gt;</pre>

			<p>Sla nu je index.php voor Joomla versie 2.5 weer op.</p>

			<h2>Stap 2 Nieuwe namen voor de module posities&nbsp;</h2>

			<p>In de standaard Joomla 2.5 voorbeelddata bestaan geen standaard module posities meer zoals left, right, user1 etc. Dit is vooral een aanmoediging om in Joomla 2.5 templates meer logische module posities te gebruiken. Een nieuwe module zal ook niet meer automatisch aan module positie left toegewezen worden, maar zal er een keuze gemaakt moeten worden uit de moduleposities.</p>

			<p>Voorbeeld Joomla 1.5:</p>

			<pre class="prettyprint linenums">&lt;jdoc:include type="modules" name="left" style="xhtml"/&gt;
&lt;jdoc:include type="modules" name="user1" style="xhtml"/&gt;
&lt;jdoc:include type="modules" name="user2" style="xhtml"/&gt;</pre>

			<p>Voorbeeld Joomla 2.5:&nbsp;</p>

			<pre class="prettyprint linenums">&lt;jdoc:include type="modules" name="navigation-left" style="xhtml"/&gt;
&lt;jdoc:include type="modules" name="sidebar-left" style="xhtml"/&gt;
&lt;jdoc:include type="modules" name="sidebar-left-bottom" style="xhtml"/&gt;</pre>

			<h2>Stap 3 Aanpassen van templateDetails.xml</h2>

			<p>In de Joomla templateDetails.xml moeten ook een aantal regels code aangepast worden. Kopieer weer uit je map van je oude template het benodigde bestand en geef het dezelfde naam templateDetails.xml. Sla de kopie op in de map waar je zojuist je index.php hebt opgeslagen.&nbsp;</p>

			<p>Open de gekopieerde templateDetails.xml in een html editor. Daarin zie je als eerste regel staan:</p>

			<pre class="prettyprint linenums">&lt;? xml version="1.0" encoding="utf-8" ?&gt;</pre>

			<p>Deze code blijft hetzelfde, maar daaronder komt <b>een nieuwe extra regel code</b> voor Joomla 2.5:</p>

			<pre class="prettyprint linenums">&lt;!DOCTYPE install PUBLIC "-//Joomla! 1.6//DTD template 1.0//EN" "http://www.joomla.org/xml/dtd/1.6/template-install.dtd"&gt;</pre>

			<p>Daaronder staat de Joomla 1.5 code:</p>

			<pre class="prettyprint linenums">&lt;install version="1.5" type="template"&gt;</pre>

			<p>Deze beginregel moet voor Joomla 2.5 aangepast worden naar:</p>

			<pre class="prettyprint linenums">&lt;extension version="2.5" type="template" client="site"&gt;</pre>

			<p>Vergeet ook niet af te sluiten, in plaats van &lt;/install&gt; gebruik je nu &lt;/extension&gt; .</p>

			<h2>Stap 4 Aanpassen van de stylesheet css_template.css</h2>

			<p>We bevelen je aan je Joomla 1.5 stylesheet aan te vullen met wat extra code, om het te laten werken in Joomla 2.5.&nbsp;Met aanpassingen in je css is het mogelijk om je basis Joomla 1.5 template geschikt te maken voor Joomla 2.5.</p>

			<p>Kopieer weer uit de map van je oude template het benodigde bestand en geef het dezelfde naam css_template.css. Sla de kopie op in de map waar je zojuist je index.php hebt opgeslagen.&nbsp;</p>

			<h3>Print, pdf en e-mail buttons</h3>

			<p>Voor de print, pdf en e-mail buttons werd in Joomla 1.5 td class="buttonheading" gebruikt. Voor Joomla 2.5 wordt nu div class="actions" gebruikt met een unordered list. Voeg de volgende css code toe voor de uitlijning van de buttons in Joomla 2.5:</p>

<pre class="prettyprint linenums lang-css">.actions {
	margin:0;
}

.actions li {
	list-style: none;
	display:inline;
	float:right;
}</pre>

			<h3>Sectie- en categorieblog layout</h3>

			<p>Voor de weergave van de sectie- en categorieblog werd in Joomla 1.5 tabellen gebruikt, in Joomla 2.5 is dat veranderd en wordt XHTML (met div's) gebruikt. Je kunt de div's netjes op hun plaats zetten met float, margin en padding en width.&nbsp;</p>

			<h3>Inlogformulier</h3>

			<p>De code van de module voor het inlogformulier is voor Joomla 2.5 aangepast, je hebt extra code nodig om het formulier netjes uit te lijnen.
				Voor de specifieke aanpassingen heb je kennis nodig van CSS. Voor vragen kun je altijd terecht
				<a style="" mce_href="http://forum.joomlacommunity.eu/forumdisplay.php?f=113" href="http://forum.joomlacommunity.eu/forumdisplay.php?f=113">op het forum</a>.
			</p>

			<h2>Stap 5 Parameters aanpassen in de&nbsp;templateDetails.xml</h2>

			<p>In Joomla 2.5 templates is het mogelijk om parameters te groeperen in diverse fieldsets. Dat is vooral handig voor templates met veel parameters.</p>

			<p>Voorbeeld Joomla 1.5:</p>

			<pre class="prettyprint linenums">&lt;params&gt;
&nbsp;&lt;param name="templateLayout" type="list" default="layout1" label="Template Layout" description="Kies een template layout"&gt;
&nbsp;&nbsp; &lt;option value="layout1"&gt;layout1&lt;/option&gt;
&nbsp;&nbsp; &lt;option value="layout2"&gt;layout2&lt;/option&gt;
&nbsp;&nbsp; &lt;option value="layout3"&gt;layout3&lt;/option&gt;
&nbsp;&lt;/param&gt;
&lt;/params&gt;</pre>

			<p>Voor Joomla 2.5 ga je dat aanpassen:</p>

<pre class="prettyprint linenums">&lt;config&gt;
&nbsp;&lt;fields name="params"&gt;
&nbsp;&nbsp;&lt;fieldset name="advanced"&gt;
&nbsp;&nbsp;&nbsp;&lt;field name="templateLayout" type="list" default="layout1" label="Template Layout" description="Kies een template layout"&gt;
&nbsp;&nbsp;&nbsp;&nbsp;&lt;option value="layout1"&gt;layout1&lt;/option&gt;
&nbsp;&nbsp;&nbsp;&nbsp;&lt;option value="layout2"&gt;layout2&lt;/option&gt;
&nbsp;&nbsp;&nbsp;&nbsp;&lt;option value="layout3"&gt;layout3&lt;/option&gt;
&nbsp;&nbsp;&nbsp;&lt;/field&gt;
&nbsp;&nbsp;&lt;/fieldset&gt;
&nbsp;&lt;/fields&gt;
&lt;/config&gt;</pre>

			<p class="alert alert-info">Engelstalige uitleg:
				<a target="_blank" mce_href="http://docs.joomla.org/Upgrading_a_Joomla_1.5_template_to_Joomla_1.6" href="http://docs.joomla.org/Upgrading_a_Joomla_1.5_template_to_Joomla_1.6">http://docs.joomla.org/Upgrading_a_Joomla_1.5_template_to_Joomla_1.6</a>
			</p>

			<div class="articleinfo">
				<a class="btn btn-small btn-danger pull-right" data-toggle="modal" href="#verbetering">
					<span class="glyphicon glyphicon-warning-sign"></span> Verbetering doorgeven
				</a>
				<p class="text-muted"><strong>Gemaakt:</strong> 8 mei 2012,
					<strong>Bijgewerkt:</strong> 11 mei 2013<br/>
					Aan dit artikel hebben bijgedragen: <a href="#">Sander</a>, <a href="#">Julianne</a></p>
			</div>
		</div>
		<ul class="pager">
			<li class="previous"><a href="#">&larr; Vorige</a></li>
			<li class="next"><a href="#">Volgende &rarr;</a></li>
		</ul>
	</div>
	<div class="content-4">
		<div class="panel panel-documentatie">
			<div class="panel-heading">Templates</div>
			<div class="list-group list-group-flush">
				<a href="#" class="list-group-item">
					<span class="badge badge-joomla3" rel="tooltip" title="Joomla 3.0"><span class="jc-joomla"></span></span>
					Template 1.5.x aanpassen naar 2.5.x
				</a>
				<a href="#" class="list-group-item">
					<span class="badge badge-joomla1" rel="tooltip" title="Joomla 1.5"><span class="jc-joomla"></span></span>
					<span class="badge badge-joomla2" rel="tooltip" title="Joomla 2.5"><span class="jc-joomla"></span></span>
					Introductie in Joomla! Templates
				</a>
				<a href="#" class="list-group-item">
					<span class="badge badge-joomla2" rel="tooltip" title="Joomla 2.5"><span class="jc-joomla"></span></span>
					<span class="badge badge-joomla3" rel="tooltip" title="Joomla 3.0"><span class="jc-joomla"></span></span>
					Module posities
				</a>
				<a href="#" class="list-group-item">
					<span class="badge badge-joomla1" rel="tooltip" title="Joomla 1.5"><span class="jc-joomla"></span></span>
					<span class="badge badge-joomla2" rel="tooltip" title="Joomla 2.5"><span class="jc-joomla"></span></span>
					Vaststelling van een parameter in templateDetails.xml
				</a>
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal" id="verbetering">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Verbetering / aanvulling doorgeven</h4>
			</div>
			<div class="modal-body">
				<p>Fout in het artikel? Aanvullingen of verbeteringen? Geef het door via onderstaand formulier. Hartelijk dank!</p>
				<form class="form-horizontal">
					<div class="row">
						<label for="inputEmail" class="col-lg-2 row-label">Artikel</label>
						<div class="col-lg-10">
							<input id="disabledInput" type="text" placeholder="Template 1.5.x aanpassen naar 2.5.x" disabled>

						</div>
					</div>
					<div class="row">
						<label for="inputEmail" class="col-lg-2 row-label">Naam</label>
						<div class="col-lg-10">
							<input type="text" id="inputNaam" placeholder="Naam">
						</div>
					</div>
					<div class="row">
						<label for="inputEmail" class="col-lg-2 row-label">Email</label>
						<div class="col-lg-10">
							<input type="text" id="inputEmail" placeholder="Email">
						</div>
					</div>
					<div class="row">
						<label for="inputEmail" class="col-lg-2 row-label">Bericht</label>
						<div class="col-lg-10">
							<textarea rows="8"></textarea>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn btn-default" data-dismiss="modal">Sluit</a>
				<a href="#" class="btn btn-primary">Verstuur</a>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dalog -->
</div><!-- /.modal -->