window.addEventListener('DOMContentLoaded', function(){
	if (typeof jQuery !== 'undefined' && typeof bootstrap !== 'undefined') {
		jQuery.fn.modal = function() {
			var element = this[0];
			var modal = bootstrap.Modal.getInstance(element);

			if (!modal) {
				modal = new bootstrap.Modal(element);
			}

			switch (arguments[0]) {
				case 'show':
					modal.show();
				break;
				case 'hide':
					modal.hide();
				break;
			}
		}
	}
});