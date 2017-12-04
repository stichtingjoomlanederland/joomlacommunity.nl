
var COM_EASYDISCUSS_RANKING_DELETE = '<?php echo JText::_('COM_EASYDISCUSS_RANKING_DELETE'); ?>';
var COM_EASYDISCUSS_RANKING_ERR_ENTER_TITLE = '<?php echo JText::_('COM_EASYDISCUSS_RANKING_ERR_ENTER_TITLE'); ?>';
var COM_EASYDISCUSS_RANKING_ERR_ONLY_NUMBER = '<?php echo JText::_('COM_EASYDISCUSS_RANKING_ERR_ONLY_NUMBER'); ?>';
var COM_EASYDISCUSS_RANKING_ERR_GREATER_THAN_ZERO = '<?php echo JText::_('COM_EASYDISCUSS_RANKING_ERR_GREATER_THAN_ZERO'); ?>';
var COM_EASYDISCUSS_RANKING_ERR_END_CANNOT_SMALLER_THAN_START = '<?php echo JText::_('COM_EASYDISCUSS_RANKING_ERR_END_CANNOT_SMALLER_THAN_START'); ?>';
var COM_EASYDISCUSS_RANKING_ERR_CANNOT_HAVE_GAPS = '<?php echo JText::_('COM_EASYDISCUSS_RANKING_ERR_CANNOT_HAVE_GAPS'); ?>';
var COM_EASYDISCUSS_RANKING_ERR_ALL_VALUE_IS_CORRECT = '<?php echo JText::_('COM_EASYDISCUSS_RANKING_ERR_ALL_VALUE_IS_CORRECT'); ?>';

function showDescription( id )
{
	EasyDiscuss.$( '.rule-description' ).hide();
	EasyDiscuss.$( '#rule-' + id ).show();
}

ed.require(['edq'], function($) {

	$.Joomla( 'submitbutton' , function(action){
		if ( action != 'cancel' ) {
			window.location.href = 'index.php?option=com_easydiscuss&view=ranks';
		}
		$.Joomla( 'submitform' , [action] );
	});

	function clearerrors() {
		$('input.input-error').removeClass('input-error');
		$('#sys-msg').html('');
	}

	function sort() {

		startItems  = $('input[name="start[]"]');
		endItems    = $('input[name="end[]"]');
		errorMessage    = '';

		if (startItems.length > 0) {
			for (i = 0; i < startItems.length; i++) {
				if ((i + 1) <= startItems.length) {
					var nextStart   = startItems[i + 1];
					var curStart    = startItems[i];

					var nextEnd = endItems[i + 1];
					var curEnd  = endItems[i];

					var nextStartVal    = parseInt($(nextStart).val() , 10);
					var nextEndVal  = parseInt($(nextEnd).val() , 10);

					var curEndVal   = parseInt($(curEnd).val(), 10);

					if ((curEndVal + 1) != nextStartVal)
					{
						var interval = nextEndVal - nextStartVal;

						var newNextStartVal = curEndVal + 1;
						var newNextEndVal = newNextStartVal + interval;

						$(nextStart).val(newNextStartVal);
						$(nextEnd).val(newNextEndVal);
					}
				}//end if
			}
		}//end if

	}

	function checkvalue(ele) {

		var val = $(ele).val();
		var intRegex = /^\d+$/;

		if (!intRegex.test(val)) {
			setTimeout(function()
			{
				ele.focus();
				ele.select();
			},200);

			$('#sys-msg').html(COM_EASYDISCUSS_RANKING_ERR_ONLY_NUMBER);
			$(ele).addClass('input-error');

			return;
		}

		if (parseInt(val, 10) <= 0) {
			setTimeout(function()
			{
				ele.focus();
				ele.select();
			},200);

			$('#sys-msg').html(COM_EASYDISCUSS_RANKING_ERR_GREATER_THAN_ZERO);
			$(ele).addClass('input-error');

			return;
		}

		// now we check if all the pair value entered are valid or not.
		validate();
	}

	function validate() {

		//clear error styling first
		$('input[name="start[]"]').removeClass('input-error');
		$('input[name="end[]"]').removeClass('input-error');

		startItems  = $('input[name="start[]"]');
		endItems    = $('input[name="end[]"]');
		errorMessage    = '';

		if (startItems.length > 0) {
			for (i = 0; i < startItems.length; i++) {

				var curStart    = startItems[i];
				var curEnd  = endItems[i];

				var curStartVal = parseInt($(curStart).val(), 10);
				var curEndVal   = parseInt($(curEnd).val(), 10);

				if (curStartVal >= curEndVal)
				{

					$('#sys-msg').html(COM_EASYDISCUSS_RANKING_ERR_END_CANNOT_SMALLER_THAN_START);
					$(curEnd).addClass('input-error');
					return;
				}

				if (i != 0)
				{
					var prevStart   = startItems[i - 1];
					var prevEnd = endItems[i - 1];

					var prevEndVal  = parseInt($(prevEnd).val() , 10);

					if ((prevEndVal + 1) != curStartVal)
					{
						$('#sys-msg').html(COM_EASYDISCUSS_RANKING_ERR_CANNOT_HAVE_GAPS);
						$(curStart).addClass('input-error');
						$(prevEnd).addClass('input-error');
						return;
					}
				}
			}
		}//end if

		//clear all errors
		clearerrors();
	}


	$('[data-rank-add]').click(function() {

		var newtitle = $('#newtitle').val();
		if (newtitle.length == 0) {
			$('#newtitle').addClass('input-error');
			$('#newtitle').focus();
			$('#sys-msg').html(COM_EASYDISCUSS_RANKING_ERR_ENTER_TITLE);
			return;
		}

		var itemCnt = $('#itemCnt').val();
		var itemCnt = parseInt(itemCnt, 10);

		var items   = $('input[name="id[]"]');
		var endVal  = '';

		if (items.length > 0) {
			endVal = $('input[name="end[]"]').last().val();

			if (endVal == '') {
				$('#sys-msg').html(COM_EASYDISCUSS_RANKING_ERR_ALL_VALUE_IS_CORRECT);
				$('input[name="end[]"]').last().focus();

				return;
			}

			endVal = parseInt(endVal, 10);
		}


		var newStartValue = 1 + endVal;

		var input = '<tr id="rank-' + itemCnt + '">';
			input += '  <td>' + (items.length + 1) + '<input type="hidden" name="id[]" value="0" /></td>';
			input += '  <td style="text-align: center;"><input data-title-text type="text" name="title[]" value="' + newtitle + '" class="input-full inputbox"/></td>';
			input += '  <td style="text-align: center;"><input data-start-text style="text-align: center;" type="text" name="start[]" value="' + newStartValue + '" class="input-full inputbox"/></td>';
			input += '  <td style="text-align: center;"><input data-end-text style="text-align: center;" type="text" name="end[]" value="" class="input-full inputbox"/></td>';
			input += '  <td style="text-align: center;"><a href="javascript:void(0);" class="btn btn-danger" data-remove-button data-id="' + itemCnt + '">' + COM_EASYDISCUSS_RANKING_DELETE + '</a></td>';
			input += '</tr>';

			$('#rank-list')
				.append(
					input
				);

		//set the focus here.
		$('input[name="end[]"]').last().focus();

		// update the counter
		$('#itemCnt').val(itemCnt + 1);

		//clear text box
		$('#newtitle').val('');

		clearerrors();

	});

	$('[data-remove-button]').live( 'click', function(){

		var eleId = $(this).data('id');
		var rankId = $('#rank-' + eleId).children().first().children('input[name="id[]"]').val();

		if (rankId != '0') {
			if ($('#itemRemove').val() == '') {
				$('#itemRemove').val(rankId);
			} else {
				var rankIds = $('#itemRemove').val() + ',' + rankId;
				$('#itemRemove').val(rankIds);
			}
		}

		$('#rank-' + eleId).remove();
		sort();
	});

	$('[data-title-text]').live('change', function() {

		var ele = $(this);

		var val = ele.val();

		ele.removeClass('input-error');

		if (val == '') {
			setTimeout(function() {
				ele.focus();
				ele.select();
			},200);

			ele.addClass('input-error');
			$('#sys-msg').html(COM_EASYDISCUSS_RANKING_ERR_ENTER_TITLE);
			return;
		}

	});

	$('[data-start-text]').live('change', function() {
		checkvalue(this);
	});

	$('[data-end-text]').live('change', function() {
		checkvalue(this);
	});


});




