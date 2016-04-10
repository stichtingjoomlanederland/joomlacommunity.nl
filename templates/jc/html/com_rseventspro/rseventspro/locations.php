<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="Content-Style-Type" content="text/css">
  <title></title>
  <meta name="Generator" content="Cocoa HTML Writer">
  <meta name="CocoaVersion" content="1404.46">
  <style type="text/css">
    p.p1 {margin: 0.0px 0.0px 0.0px 0.0px; line-height: 14.0px; font: 12.0px Times; color: #000000; -webkit-text-stroke: #000000}
    p.p2 {margin: 0.0px 0.0px 0.0px 0.0px; line-height: 14.0px; font: 12.0px Times; color: #000000; -webkit-text-stroke: #000000; min-height: 14.0px}
    span.s1 {font-kerning: none}
    span.Apple-tab-span {white-space:pre}
  </style>
</head>
<body>
<p class="p1"><span class="s1">&lt;?php</span></p>
<p class="p1"><span class="s1">/**</span></p>
<p class="p1"><span class="s1">* @package RSEvents!Pro</span></p>
<p class="p1"><span class="s1">* @copyright (C) 2015 www.rsjoomla.com</span></p>
<p class="p1"><span class="s1">* @license GPL, http://www.gnu.org/copyleft/gpl.html</span></p>
<p class="p1"><span class="s1">*/</span></p>
<p class="p1"><span class="s1">defined( '_JEXEC' ) or die( 'Restricted access' );</span></p>
<p class="p1"><span class="s1">$count = count($this-&gt;locations); ?&gt;</span></p>
<p class="p2"><span class="s1"></span><br></p>
<p class="p1"><span class="s1">&lt;?php if ($this-&gt;params-&gt;get('show_page_heading', 1)) { ?&gt;</span></p>
<p class="p1"><span class="s1">&lt;?php $title = $this-&gt;params-&gt;get('page_heading', ''); ?&gt;</span></p>
<p class="p1"><span class="s1">&lt;h1&gt;&lt;?php echo !empty($title) ? $this-&gt;escape($title) : JText::_('COM_RSEVENTSPRO_LOCATIONS'); ?&gt;&lt;/h1&gt;</span></p>
<p class="p1"><span class="s1">&lt;?php } ?&gt;</span></p>
<p class="p2"><span class="s1"></span><br></p>
<p class="p1"><span class="s1">&lt;?php if (!empty($this-&gt;locations)) { ?&gt;</span></p>
<p class="p1"><span class="s1">&lt;ul class="rs_events_container rsepro-locations-list" id="rs_events_container"&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span>&lt;?php foreach($this-&gt;locations as $location) { ?&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span>&lt;li&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;div class="well"&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;div class="rs_options" style="display:none;"&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;?php if ((!empty($this-&gt;permissions['can_edit_locations']) || $this-&gt;admin) &amp;&amp; !empty($this-&gt;user)) { ?&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;a href="&lt;?php echo rseventsproHelper::route('index.php?option=com_rseventspro&amp;layout=editlocation&amp;id='.rseventsproHelper::sef($location-&gt;id,$location-&gt;name)); ?&gt;"&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;i class="fa fa-pencil"&gt;&lt;/i&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;/a&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;?php } ?&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;a href="&lt;?php echo rseventsproHelper::route('index.php?option=com_rseventspro&amp;layout=location&amp;id='.rseventsproHelper::sef($location-&gt;id,$location-&gt;name)); ?&gt;"&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;i class="fa fa-eye"&gt;&lt;/i&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;/a&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;/div&gt;</span></p>
<p class="p2"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span></span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;div class="rs_heading"&gt;</span></p>
<p class="p2"><span class="s1"></span><br></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;H2&gt;&lt;?php echo $location-&gt;name; ?&gt;&lt;/H2&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;?php if ($this-&gt;params-&gt;get('events',0)) { ?&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;?php $events = (int) $this-&gt;getNumberEvents($location-&gt;id,'locations'); ?&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;?php if (!empty($events)) { ?&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;small&gt;(&lt;?php echo $this-&gt;getNumberEvents($location-&gt;id,'locations'); ?&gt;)&lt;/small&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;?php } ?&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;?php } ?&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;/a&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;/div&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;div class="rs_description"&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;?php echo rseventsproHelper::shortenjs($location-&gt;description,$location-&gt;id,255,$this-&gt;params-&gt;get('type', 1)); ?&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;/div&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;/div&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span>&lt;/li&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span>&lt;?php } ?&gt;</span></p>
<p class="p1"><span class="s1">&lt;/ul&gt;</span></p>
<p class="p1"><span class="s1">&lt;?php } ?&gt;</span></p>
<p class="p2"><span class="s1"></span><br></p>
<p class="p1"><span class="s1">&lt;span id="total" class="rs_hidden"&gt;&lt;?php echo $this-&gt;total; ?&gt;&lt;/span&gt;</span></p>
<p class="p1"><span class="s1">&lt;span id="Itemid" class="rs_hidden"&gt;&lt;?php echo JFactory::getApplication()-&gt;input-&gt;getInt('Itemid'); ?&gt;&lt;/span&gt;</span></p>
<p class="p2"><span class="s1"></span><br></p>
<p class="p1"><span class="s1">&lt;div class="rs_loader" id="rs_loader" style="display:none;"&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span>&lt;img src="&lt;?php echo JURI::root(); ?&gt;components/com_rseventspro/assets/images/loader.gif" alt="" /&gt;</span></p>
<p class="p1"><span class="s1">&lt;/div&gt;</span></p>
<p class="p2"><span class="s1"></span><br></p>
<p class="p1"><span class="s1">&lt;?php if ($this-&gt;total &gt; $count) { ?&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span>&lt;a class="rs_read_more" id="rsepro_loadmore"&gt;&lt;?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_LOAD_MORE'); ?&gt;&lt;/a&gt;</span></p>
<p class="p1"><span class="s1">&lt;?php } ?&gt;</span></p>
<p class="p2"><span class="s1"></span><br></p>
<p class="p1"><span class="s1">&lt;script type="text/javascript"&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span>jQuery(document).ready(function() {</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;?php if ($this-&gt;total &gt; $count) { ?&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>jQuery('#rsepro_loadmore').on('click', function() {</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>rspagination('locations', jQuery('#rs_events_container &gt; li').length);</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>});</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;?php } ?&gt;</span></p>
<p class="p2"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span></span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;?php if (!empty($count)) { ?&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>jQuery('#rs_events_container li').on({</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>mouseenter: function() {</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>jQuery(this).find('div.rs_options').css('display','');</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>},</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>mouseleave: function() {</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>jQuery(this).find('div.rs_options').css('display','none');</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>}</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>});</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span><span class="Apple-tab-span">	</span>&lt;?php } ?&gt;</span></p>
<p class="p1"><span class="s1"><span class="Apple-tab-span">	</span>});</span></p>
<p class="p1"><span class="s1">&lt;/script&gt;</span></p>
</body>
</html>
