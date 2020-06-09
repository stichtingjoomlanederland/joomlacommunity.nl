var RSFormProhCaptcha = {
	forms: {},
	loaders: [],
	onLoad: function() {
		window.setTimeout(function(){
			for (var i = 0; i < RSFormProhCaptcha.loaders.length; i++) {
				var func = RSFormProhCaptcha.loaders[i];
				if (typeof func === 'function') {
					try {
						func();
					} catch (err) {
						if (console && typeof console.log === 'function') {
							console.log(err);
						}
					}
				}
			}
		}, 500);
	}
};

RSFormProUtils.addEvent(window, 'load', RSFormProhCaptcha.onLoad);

function ajaxValidationhCaptcha(task, formId, data, componentId)
{
	switch (task)
	{
		case 'beforeSend':
			if (data.params.indexOf('h-captcha-response=&') > -1 && data.params.indexOf('&page=') === -1)
			{
				RSFormPro.Ajax.Wait = true;
				
				window['RSFormProInvisiblehCaptchaCallback' + formId] = function(token)
				{
					RSFormPro.Ajax.Params = RSFormPro.Ajax.Params.replace('h-captcha-response=&', 'h-captcha-response=' + encodeURIComponent(token) + '&');
					RSFormPro.Ajax.Wait = false;
					RSFormPro.Ajax.xmlHttp.send(RSFormPro.Ajax.Params);
				};

				var foundhCaptchaEvent = false;
				for (var i = 0; i < RSFormPro.formEvents[formId].length; i++)
				{
					if (typeof RSFormPro.formEvents[formId][i] === 'function' && RSFormPro.formEvents[formId][i].toString().indexOf('hcaptcha.execute(id)') > -1)
					{
                        foundhCaptchaEvent = true;
					}
				}

				if (!foundhCaptchaEvent)
				{
					RSFormPro.addFormEvent(formId, function() { var id = RSFormProhCaptcha.forms[formId]; hcaptcha.execute(id); } )
				}

				RSFormPro.submitForm(RSFormPro.getForm(formId));
			}
			else
			{
				RSFormPro.Ajax.Wait = false;
			}
		break;
	}
}