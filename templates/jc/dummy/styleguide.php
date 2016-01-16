<div class="row">
	<div class="content-8">
		<div class="well">
			<div class="page-header">

				<h1>JoomlaCommunity stijlgids</h1>
			</div>
			<p class="lead">Op deze pagina vind je alle opmaakmogelijkheden voor teksten op JoomlaCommunity met een voorbeeld van hoe je de weergave kunt toepassen.</p>
			<h2>Typography</h2>
			<div class="jc-voorbeeld type">
				<h1>Kop H1</h1>
				<h2>Kop H2</h2>
				<h3>Kop H3</h3>
			</div>
			<div class="jc-code">
			    <pre class="prettyprint linenums">&lt;h1&gt;Kop H1&lt;/h1&gt;
&lt;h2&gt;Kop H2&lt;/h2&gt;
&lt;h3&gt;Kop H3&lt;/h3&gt;</pre>
			</div>

			<h2>Introductie paragraaf</h2>
			<div class="jc-voorbeeld">
				<p class="lead">Deze weergave kan gebruikt worden voor de introductie alinea van een artikel. Bij voorkeur niet elders in een artikel gebruiken.</p>
			</div>
			<div class="jc-code">
				<pre class="prettyprint linenums">&lt;p class=&quot;lead&quot;&gt;Deze weergave kan gebruikt worden voor de introductie alinea van een artikel. Bij voorkeur niet elders in een artikel gebruiken.&lt;/p&gt;</pre>
			</div>

			<h2>Paragraaf</h2>
			<div class="jc-voorbeeld">
				<p>Dit is de normale weergave van paragraven. Dit is de normale weergave van paragraven. Dit is de normale weergave van paragraven.</p>
			</div>
			<div class="jc-code">
				<pre class="prettyprint linenums">&lt;p&gt;Dit is de normale weergave van paragraven. Dit is de normale weergave van paragraven. Dit is de normale weergave van paragraven.&lt;/p&gt;</pre>
			</div>

			<h2>Dikgedrukte tekst</h2>
			<div class="jc-voorbeeld">
				<p>Dit is de normale weergave van paragraven.
					<strong>Met dikgedrukte tekst.</strong> Dit is de normale weergave van paragraven.</p>
			</div>
			<div class="jc-code">
				<pre class="prettyprint linenums">&lt;p&gt;Dit is de normale weergave van paragraven. &lt;strong&gt;Met dikgedrukte tekst.&lt;/strong&gt; Dit is de normale weergave van paragraven.&lt;/p&gt;</pre>
			</div>

			<h2>Definitie lijst
				<small>bijvoorbeeld voor beschrijving Joomla velden</small>
			</h2>
			<div class="jc-voorbeeld">
				<dl class="dl-horizontal">
					<dt>Titel</dt>
					<dd>een passende titel voor het menu</dd>
					<dt>Menutype</dt>
					<dd>de systeemnaam van het menu</dd>
					<dt>Beschrijving</dt>
					<dd>optioneel kun je hier een beschrijving typen</dd>
				</dl>
			</div>
			<div class="jc-code">
			    <pre class="prettyprint linenums">&lt;dl class=&quot;dl-horizontal&quot;&gt;
 &lt;dt&gt;Titel&lt;/dt&gt;
 &lt;dd&gt;een passende titel voor het menu&lt;/dd&gt;
 &lt;dt&gt;Menutype&lt;/dt&gt;
 &lt;dd&gt;de systeemnaam van het menu&lt;/dd&gt;
 &lt;dt&gt;Beschrijving&lt;/dt&gt;
 &lt;dd&gt;optioneel kun je hier een beschrijving typen&lt;/dd&gt;
&lt;/dl&gt;</pre>
			</div>

			<h2>Inline-code
				<small>bijvoorbeeld voor Joomla-menu navigatie</small>
			</h2>
			<div class="jc-voorbeeld">
				Ga nu naar
				<code>Menubeheer &gt; Menu's</code> en je ziet dat je menu is aangemaakt. Het Hoofdmenu wordt standaard geplaatst op
				<strong>Positie</strong> <code>position-7</code>.
			</div>
			<div class="jc-code">
				<pre class="prettyprint linenums">Ga nu naar &lt;code&gt;Menubeheer &amp;gt; Menu's&lt;/code&gt; en je ziet dat je menu is aangemaakt. Het Hoofdmenu wordt standaard geplaatst op &lt;strong&gt;Positie&lt;/strong&gt; &lt;code&gt;position-7&lt;/code&gt;.</pre>
			</div>


			<h2>Labels</h2>
			<div class="jc-voorbeeld">
				<span class="label label-default">Default</span>
				<span class="label label-primary">Primary</span>
				<span class="label label-success">Success</span>
				<span class="label label-info">Info</span>
				<span class="label label-warning">Warning</span>
				<span class="label label-danger">Danger</span>
			</div>
			<div class="jc-code">
			    <pre class="prettyprint linenums">&lt;span class=&quot;label label-default&quot;&gt;Default&lt;/span&gt;
&lt;span class=&quot;label label-primary&quot;&gt;Primary&lt;/span&gt;
&lt;span class=&quot;label label-success&quot;&gt;Success&lt;/span&gt;
&lt;span class=&quot;label label-info&quot;&gt;Info&lt;/span&gt;
&lt;span class=&quot;label label-warning&quot;&gt;Warning&lt;/span&gt;
&lt;span class=&quot;label label-danger&quot;&gt;Danger&lt;/span&gt;</pre>
			</div>

			<h2>Joomla!-versie labels</h2>
			<div class="jc-voorbeeld">
				<span class="label label-joomla1"><span class="jc-joomla"></span> Joomla 1.5</span>
				<span class="label label-joomla2"><span class="jc-joomla"></span> Joomla 2.5</span>
				<span class="label label-joomla3"><span class="jc-joomla"></span> Joomla 3</span>
				<br/>TODO: Ombouwen naar plugin zodat {joomla1}, {joomla2} en {joomla3} gebruikt kan worden
			</div>
			<div class="jc-code">
			    <pre class="prettyprint linenums">&lt;span class=&quot;label label-joomla1&quot;&gt;&lt;span class=&quot;jc-joomla&quot;&gt;&lt;/span&gt; Joomla 1.5&lt;/span&gt;
&lt;span class=&quot;label label-joomla2&quot;&gt;&lt;span class=&quot;jc-joomla&quot;&gt;&lt;/span&gt; Joomla 2.5&lt;/span&gt;
&lt;span class=&quot;label label-joomla3&quot;&gt;&lt;span class=&quot;jc-joomla&quot;&gt;&lt;/span&gt; Joomla 3&lt;/span&gt;</pre>
			</div>

			<h2>Buttons</h2>
			<div class="jc-voorbeeld">
				<button type="button" class="btn btn-default">Default</button>
				<button type="button" class="btn btn-primary">Primary</button>
				<button type="button" class="btn btn-success">Success</button>
				<button type="button" class="btn btn-info">Info</button>
				<button type="button" class="btn btn-warning">Warning</button>
				<button type="button" class="btn btn-danger">Danger</button>
				<button type="button" class="btn btn-link">Link</button>
			</div>
			<div class="jc-code">
			    <pre class="prettyprint linenums">&lt;button type=&quot;button&quot; class=&quot;btn btn-default&quot;&gt;Default&lt;/button&gt;
&lt;button type=&quot;button&quot; class=&quot;btn btn-primary&quot;&gt;Primary&lt;/button&gt;
&lt;button type=&quot;button&quot; class=&quot;btn btn-success&quot;&gt;Success&lt;/button&gt;
&lt;button type=&quot;button&quot; class=&quot;btn btn-info&quot;&gt;Info&lt;/button&gt;
&lt;button type=&quot;button&quot; class=&quot;btn btn-warning&quot;&gt;Warning&lt;/button&gt;
&lt;button type=&quot;button&quot; class=&quot;btn btn-danger&quot;&gt;Danger&lt;/button&gt;
&lt;button type=&quot;button&quot; class=&quot;btn btn-link&quot;&gt;Link&lt;/button&gt;</pre>
			</div>

			<h2>Meldingen</h2>
			<div class="jc-voorbeeld">
				<div class="alert alert-success">
					<strong>Let op!</strong> Te gebruiken voor - nog vaststellen -
				</div>
				<div class="alert alert-info">
					<strong>Let op!</strong> Te gebruiken voor - nog vaststellen -
				</div>
				<div class="alert alert-warning">
					<strong>Let op!</strong> Te gebruiken voor - nog vaststellen -
				</div>
				<div class="alert alert-danger">
					<strong>Let op!</strong> Te gebruiken voor - nog vaststellen -
				</div>
			</div>
			<div class="jc-code">
			    <pre class="prettyprint linenums">&lt;div class=&quot;alert alert-success&quot;&gt;
	&lt;strong&gt;Let op!&lt;/strong&gt; Te gebruiken voor - nog vaststellen -
&lt;/div&gt;
&lt;div class=&quot;alert alert-info&quot;&gt;
	&lt;strong&gt;Let op!&lt;/strong&gt; Te gebruiken voor - nog vaststellen -
&lt;/div&gt;
&lt;div class=&quot;alert alert-warning&quot;&gt;
	&lt;strong&gt;Let op!&lt;/strong&gt; Te gebruiken voor - nog vaststellen -
&lt;/div&gt;
&lt;div class=&quot;alert alert-danger&quot;&gt;
	&lt;strong&gt;Let op!&lt;/strong&gt; Te gebruiken voor - nog vaststellen -
&lt;/div&gt;</pre>
			</div>


		</div>
	</div>
	<div class="content-4">
		<div class="panel panel-home">
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