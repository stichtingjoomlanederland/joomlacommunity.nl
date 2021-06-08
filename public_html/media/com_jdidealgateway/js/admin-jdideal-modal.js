/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
(function() {
	"use strict";

	window.jdidealButton = function()
	{
		if (!Joomla.getOptions('xtd-jdideal')) {
			// Something went wrong!
			window.parent.jModalClose();
			return false;
		}

		var tag = '{jdidealpaymentlink', editor;

		editor = Joomla.getOptions('xtd-jdideal').editor;

		// Get the title
		var title = document.getElementById('title').value;

		if (title) {
			tag += ' title="' + title + '"';
		}

		// Get the amount
		var amount = document.getElementById('amount').value;

		if (amount) {
			tag += ' amount="' + amount + '"';
		}

		// Get the email
		var email = document.getElementById('email').value;

		if (email) {
			tag += ' email="' + email + '"';
		}

		// Get the remark
		var remark = document.getElementById('remark').value;

		if (remark) {
			tag += ' remark="' + document.getElementById('remark').value + '"';
		}

		// Get the order number
		var orderNumber = document.getElementById('order_number').value;

		if (orderNumber) {
			tag += ' order_number="' + document.getElementById('order_number').value + '"';
		}

		// Get the silent status
		var silent = document.getElementById('silent').value;

		if (silent) {
			tag += ' silent="' + document.getElementById('silent').value + '"';
		}

		// Close the tag
		tag += '}';

		/** Use the API, if editor supports it **/
		if (window.parent.Joomla && window.parent.Joomla.editors && window.parent.Joomla.editors.instances && window.parent.Joomla.editors.instances.hasOwnProperty(editor)) {
			window.parent.Joomla.editors.instances[editor].replaceSelection(tag)
		} else {
			window.parent.jInsertEditorText(tag, editor);
		}

		window.parent.Joomla.Modal && window.parent.Joomla.Modal.getCurrent().close();
		return false;
	};
})();
