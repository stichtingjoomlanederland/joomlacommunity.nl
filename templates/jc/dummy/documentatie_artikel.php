<div class="row">
	<div class="content-8">
		<div class="well">
			<div class="page-header">
				<div class="pull-right">
					<span class="label label-joomla2"><span class="jc-joomla"></span> Joomla 2.5</span>
					<span class="label label-joomla3"><span class="jc-joomla"></span> Joomla 3.0</span>
				</div>
				<h1>Menu's maken</h1>
			</div>
			<p class="lead">In dit artikel vind je tips voor het maken van een menu. De beginnende Joomlagebruiker wordt geholpen met het maken van navigatie binnen de website. Met deze tutorial maak je een menu met een menu-item naar één artikel.</p>

			<p>Menu's en menu-items worden gebruikt om de belangrijkste navigatielinks op de pagina's van de website te creëren. Je kunt navigatie maken naar alle artikelen binnen een bepaalde categorie of één artikel, een speciaal artikel of een van de vele andere menu-opties.</p>

			<p>Er is veel mogelijk in Joomla, dus zorg dat je een
				<b>plan</b> hebt gemaakt voordat je menu-items gaat aanmaken. Zo'n plan kan een eenvoudig blaadje in je kladblok zijn, waar je opsomt welke items je in je navigatie wil hebben.
			</p>

			<p>Menu-items zijn gegroepeerd onder de menu's en worden getoond op de website. Sommige sites gebruiken een aantal hoofdmenu's en veel menu-items. Andere sites zetten de structuur op in groepen. Je kunt met Joomla eenvoudig submenu's maken, die je in je template kunt opmaken met CSS. In kant en klare templates zullen submenu's in de CSS gedefinieerd zijn en waarschijnlijk met de gewenste opmaak vanzelf verschijnen.</p>

			<h2>Stap 1 Menubeheer</h2>

			<p>Ga naar het beheergedeelte van de Joomla!-site en log in. Zoek in de Administratie naar <b>Menubeheer</b>.
			</p>

			<p>
				<img class="img-thumbnail" width="263" height="290" alt="Menubeheer" src="http://help.joomlacommunity.eu/images/stories/screenshots-25/menu/menubeheer.jpg" mce_src="http://help.joomlacommunity.eu/images/stories/screenshots-25/menu/menubeheer.jpg">
			</p>

			<p>Wanneer je geen voorbeelddata geïnstalleerd hebt, zie je hier alleen Hoofdmenu. Als je wel voorbeelddata hebt geïnstalleerd zie je meerdere menu's.</p>

			<p>
				<img class="img-thumbnail" width="580" alt="Menubeheer: Menu's" src="http://help.joomlacommunity.eu/images/stories/screenshots-25/menu/menubeheer_menus.jpg" mce_src="http://help.joomlacommunity.eu/images/stories/screenshots-25/menu/menubeheer_menus.jpg">
			</p>

			<p>Je kunt&nbsp;één van de bestaande menu's gebruiken, maar in dit artikel maken we een geheel nieuw menu. Klik op de knop
				<span class="label">Nieuw</span> en vul de gegevens in van het menu in het scherm dat zichtbaar wordt:
			</p>

			<dl class="dl-horizontal">
				<dt>Titel</dt>
				<dd>een passende titel voor het menu</dd>
				<dt>Menutype</dt>
				<dd>de systeemnaam van het menu</dd>
				<dt>Beschrijving</dt>
				<dd>Optioneel kun je hier een beschrijving typen.</dd>
			</dl>

			<p class="alert alert-info">Tip: ga met de muis over het woord en je ziet een tooltip met nadere uitleg.&nbsp;<br>
			</p>

			<p>
				<img class="img-thumbnail" width="441" height="271" style="" alt="Menu toevoegen" src="http://help.joomlacommunity.eu/images/stories/screenshots-25/menu/menubeheer_toevoegen_menu.jpg" mce_src="http://help.joomlacommunity.eu/images/stories/screenshots-25/menu/menubeheer_toevoegen_menu.jpg"><br>
			</p>

			<p>Wanneer je de velden hebt ingevuld, klik je op de knop
				<span class="label">Opslaan</span> rechtsboven. Je menu staat nu in de lijst onder
				<b>Menubeheer</b>. Het menu is nog niet gepubliceerd.&nbsp;We gaan straks menu-items in het menu aanmaken.
			</p>

			<h2>Stap 2 Module toevoegen</h2>

			<p>Een menu heeft een positie nodig in je template, anders wordt het niet getoond.</p>

			<p>Ga nu naar
				<code>Menubeheer &gt; Menu's</code> en je ziet dat je menu is aangemaakt. Achter de menunaam zie je onder
				<b>Modules die gelinkt zijn aan het menu</b>&nbsp;een link:&nbsp;<b>Voeg een module toe voor dit menutype</b>. We gaan de module toevoegen.
			</p>

			<p>Het Hoofdmenu wordt standaard geplaatst op <b>Positie</b>
				<code>position-7</code>. Een modulepositie is een 'blokje' op je site, om inhoud van de site een plaatsje in de site te geven.
			</p>

			<p>
				<img class="img-thumbnail" width="580" height="261" alt="Modulebeheer Hoofdmenu" src="http://help.joomlacommunity.eu/images/stories/screenshots-25/menu/modulebeheer.jpg" mce_src="http://help.joomlacommunity.eu/images/stories/screenshots-25/menu/modulebeheer.jpg">
			</p>

			<p>Je kunt bij
				<code>Extensies &gt; Modulebeheer: Modules</code> bestaande modules bewerken.&nbsp;Klik op de modulenaam en in het volgende scherm zie je de blokken&nbsp;<b>Gegevens</b>,&nbsp;<b>Menutoewijzing</b>,
				<b>Basisopties</b> en
				<b>Geavanceerde opties</b>. Merk op dat je bij de Basisopties een submenu aan of uit kunt zetten. Bij Menutoewijzing vind je instellingen om je menu op bepaalde pagina's wel of niet te tonen. Je kunt deze opties uitproberen.
			</p>

			<p>Let op dat je in het vakje Gegevens,
				<b>Toon titel</b> op Ja zet en een&nbsp;<b>Positie</b> kiest die je template ook echt bevat, anders wordt je menu niet getoond op je website.
			</p>

			<p>We gaan naar de laatste stap, menu-items maken.</p>

			<h2>Stap 3 Menu-item</h2>

			<p>We gaan in het hoofdmenu een nieuw menu-item aanmaken.</p>

			<p>Je zet eerst een pagina klaar om te koppelen, bijvoorbeeld voor de pagina 'Over ons'. Maak daarvoor bij
				<b>Artikelbeheer</b> een artikel aan.</p>

			<p>Je bent nog steeds in de Administratie, het beheergedeelte. Klik in het menu bovenin naar
				<b>Menu's &gt; Hoofdmenu &gt; Nieuw menu-item toevoegen</b>.</p>

			<p>Klik op <i>Selecteren</i> achter Menu-itemtype. Een pop-up verschijnt. Kies daar voor
				<i>Individueel artikel</i>.</p>

			<p>
				<img class="img-thumbnail" width="560" height="419" style="" title="menu-item selecteren" alt="menu-item selecteren" src="http://help.joomlacommunity.eu/images/stories/screenshots-25/menu/menu-item-toevoegen.png" mce_src="http://help.joomlacommunity.eu/images/stories/screenshots-25/menu/menu-item-toevoegen.png">
			</p>

			<p>Bij
				<b>Menutitel</b> vul je de titel in, dit wordt de naam van je menu-item. Wil je dit navigeren naar bijvoorbeeld die pagina 'Over ons' dan vul je daar 'Over ons' in.&nbsp;
			</p>

			<p>Kies bij aan de rechterkant bij
				<b>Selecteer artikel</b>. Zoek daarmee je artikel 'Over ons' wat je net al klaargezet hebt.</p>

			<p>Je menu-item is nu klaar. Je kunt aan de voorkant van je website bekijken of je menu te zien is.<br>
				Gelukt? Dan kun je nu -volgens je plan- je menu's met menu-items gaan vullen.</p>

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
<div class="modal fade" id="verbetering">
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