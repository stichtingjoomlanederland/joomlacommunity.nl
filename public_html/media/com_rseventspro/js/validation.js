Joomla.submitbutton = function (task, formSelector, validate) {
	var form = document.querySelector(formSelector || 'form.form-validate');
	var newValidate = validate;

	if (typeof formSelector === 'string' && form === null) {
		form = document.querySelector("#".concat(formSelector));
	}

	if (form) {
		if (newValidate === undefined || newValidate === null) {
			var pressbutton = task.split('.');
			var cancelTask = form.getAttribute('data-cancel-task');

			if (!cancelTask) {
				cancelTask = "".concat(pressbutton[0], ".cancel");
			}

			newValidate = task !== cancelTask;
		}

		if (!newValidate || document.formvalidator.isValid(form)) {
			Joomla.submitform(task, form);
		}
	} else {
		Joomla.submitform(task);
	}
}