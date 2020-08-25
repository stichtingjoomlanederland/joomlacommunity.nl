function checkResult(id, token)
{
	jQuery.ajax({
		async: true,
		url: 'index.php?option=com_jdidealgateway',
		dataType: 'json',
		cache: false,
		data: 'task=logs.checkstatus&format=json&id=' + id + '&' + token + '=1',
		success: function(response)
		{
			if (response.success)
			{
				// Render the new status
				jQuery('#paymentResult' + id).html(response.data);

				var msg = new Object();
				msg.success = new Array();

				// Add the regular message
				msg.success[0] = response.message;

				// Add any enqueued messages if they exist
				if (null !== response.messages)
				{
					for (index = 0; index < response.messages.message.length; ++index)
					{
						msg.success[index + 1] = response.messages.message[index];
					}
				}

				Joomla.renderMessages(msg);
			}
			else if (response.success == false)
			{
				var msg = new Object();
				msg.error = new Array();

				// Add the regular message
				msg.error[0] = response.message;

				// Add any enqueued messages if they exist
				if (null !== response.messages)
				{
					for (index = 0; index < response.messages.message.length; ++index)
					{
						msg.error[index + 1] = response.messages.message[index];
					}
				}

				Joomla.renderMessages(msg);
			}
		},
		error:function (request, status, error)
		{
			var msg = new Object();
			msg.error = new Array();
			msg.error[0] = request.responseText;
			Joomla.renderMessages(msg);
		}
	});
}
