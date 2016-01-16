jQuery.noConflict();
jQuery(document).ready(function (){
	jQuery("#rs_tags").autoSuggest('index.php', {
		minChars: 2,
		startText: '',
		emptyText: Joomla.JText._('COM_RSEVENTSPRO_NO_RESULTS'),
		limitText: '',
		queryParam: 'search',
		extraParams: '&option=com_rseventspro&task=filter&type=tags&condition=contains&method=json',
		neverSubmit: true,
		retrieveComplete: function(data) {
			if (data) {
				newdata = [];
				for (i=0; i<data.length; i++)
					newdata.push({'value': data[i]});
				return newdata;
			}
			return data;
		}
	});
	
	var data = [{name: ""}];
	jQuery("#meta_key").autoSuggest(data, {
		startText: '',
		emptyText: '',
		limitText: '',
		neverSubmit: true
	});
});