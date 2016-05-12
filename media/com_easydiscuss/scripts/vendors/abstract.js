ed.define('abstract', ['edq'], function($) {

	var Abstract = function(data) {

		// We just store the data into a temporary location until the execution happens
		this.data = data;

		if (typeof this.data == 'function') {
			this.data = this.data.call(this, this);
		}

		// Default properties
		this.element = null;
		this.options = {};
		this.definitions = {};

		// Allows child classes to attach definitionas
		this.attach = function(identifier, definition) {
			this.definitions["identifier"] = definition;
		};
	};

	Abstract.prototype.execute = function(wrapper, overrideOptions) {

		// Get the implemented item
		this.element = $(wrapper);

		// Scoping issues
		var self = this;

		// console.log(wrapper, this, self);

		// Extend the default options
		this.options = $.extend({}, this.data.opts, overrideOptions);

		// Register all events
		$.each(this.data, function(name, event) {
			self.register(name, event);
		});

		// Register all properties
		$.each(this.options, function(option, selector) {
			self.registerProperties(option, selector);
		});

		// Invoke the constructor at the end of this
		this.construct();

		// Once we are done with the temporary data, we should just remove it
		// delete this.data;

		return this;
	};

	// Internal method to invoke the child's constructor
	Abstract.prototype.construct = function() {

		var constructor = this["init"];

		if (typeof constructor == "function") {
			constructor.call(this, this.element);
		}
	};

	// Internal method to register all event bindings
	Abstract.prototype.register = function(name, event) {

		// Event augmentation by specifying "{option} click" on child object.
		if (name.match(/^\{.+\}/)) {
			this.registerEvent(name, event);
			return;
		}

		// For other events, we need to think of a way to handle it
		this[name] = event;
	};

	Abstract.prototype.registerProperties = function(name, selector) {

		if (!selector) {
			return;
		}

		// Get the function name so that caller can call it
		name = name.replace(/\{/, '');
		name = name.replace(/\}/, '');

		var self = this;

		this[name] = function() {
			var element = self.element.find(selector);

			return element;
		}
	};

	Abstract.prototype.registerEvent = function(name, event) {
		var self = this;

		// Get the element selector
		var selector = name.match(/^\{.+\}/);
		var selector = selector[0];

		// Get the element
		var element = this.element.find(this.options[selector]);

		// We need to know which event to trigger.
		var eventName = $.trim(name.replace(/^\{.+\}/, ''));
		var eventMethod = event;

		element.live(eventName, function(originalEvent) {

			// We do not want to mess up child's "this" keyword
			eventMethod.apply(self, [$(this), originalEvent]);
		});
	};

	return Abstract;
});
