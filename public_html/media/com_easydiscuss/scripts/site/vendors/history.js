ed.define('historyjs', ['edjquery'], function($) {

var jQuery = $;

/**
 * EDHistory.js jQuery Adapter
 * @author Benjamin Arthur Lupton <contact@balupton.com>
 * @copyright 2010-2011 Benjamin Arthur Lupton <contact@balupton.com>
 * @license New BSD License <http://creativecommons.org/licenses/BSD/>
 */

// Closure
(function(window,undefined){
	"use strict";

	// Localise Globals
	var History = window.EDHistory = window.EDHistory||{};

	// Check Existence
	if ( typeof EDHistory.Adapter !== 'undefined' ) {
		throw new Error('EDHistory.js Adapter has already been loaded...');
	}

	// Add the Adapter
	EDHistory.Adapter = {
		/**
		 * EDHistory.Adapter.bind(el,event,callback)
		 * @param {Element|string} el
		 * @param {string} event - custom and standard events
		 * @param {function} callback
		 * @return {void}
		 */
		bind: function(el,event,callback){
			jQuery(el).bind(event,callback);
		},

		/**
		 * EDHistory.Adapter.trigger(el,event)
		 * @param {Element|string} el
		 * @param {string} event - custom and standard events
		 * @param {Object=} extra - a object of extra event data (optional)
		 * @return {void}
		 */
		trigger: function(el,event,extra){
			jQuery(el).trigger(event,extra);
		},

		/**
		 * EDHistory.Adapter.extractEventData(key,event,extra)
		 * @param {string} key - key for the event data to extract
		 * @param {string} event - custom and standard events
		 * @param {Object=} extra - a object of extra event data (optional)
		 * @return {mixed}
		 */
		extractEventData: function(key,event,extra){
			// jQuery Native then jQuery Custom
			var result = (event && event.originalEvent && event.originalEvent[key]) || (extra && extra[key]) || undefined;

			// Return
			return result;
		},

		/**
		 * EDHistory.Adapter.onDomLoad(callback)
		 * @param {function} callback
		 * @return {void}
		 */
		onDomLoad: function(callback) {
			jQuery(callback);
		}
	};

	// Try and Initialise History
	if ( typeof EDHistory.init !== 'undefined' ) {
		EDHistory.init();
	}

})(window);

/**
 * EDHistory.js HTML4 Support
 * Depends on the HTML5 Support
 * @author Benjamin Arthur Lupton <contact@balupton.com>
 * @copyright 2010-2011 Benjamin Arthur Lupton <contact@balupton.com>
 * @license New BSD License <http://creativecommons.org/licenses/BSD/>
 */

(function(window,undefined){
	"use strict";

	// ========================================================================
	// Initialise

	// Localise Globals
	var
		document = window.document, // Make sure we are using the correct document
		setTimeout = window.setTimeout||setTimeout,
		clearTimeout = window.clearTimeout||clearTimeout,
		setInterval = window.setInterval||setInterval,
		History = window.EDHistory = window.EDHistory||{}; // Public History Object

	// Check Existence
	if ( typeof EDHistory.initHtml4 !== 'undefined' ) {
		throw new Error('EDHistory.js HTML4 Support has already been loaded...');
	}


	// ========================================================================
	// Initialise HTML4 Support

	// Initialise HTML4 Support
	EDHistory.initHtml4 = function(){
		// Initialise
		if ( typeof EDHistory.initHtml4.initialized !== 'undefined' ) {
			// Already Loaded
			return false;
		}
		else {
			EDHistory.initHtml4.initialized = true;
		}


		// ====================================================================
		// Properties

		/**
		 * EDHistory.enabled
		 * Is History enabled?
		 */
		EDHistory.enabled = true;


		// ====================================================================
		// Hash Storage

		/**
		 * EDHistory.savedHashes
		 * Store the hashes in an array
		 */
		EDHistory.savedHashes = [];

		/**
		 * EDHistory.isLastHash(newHash)
		 * Checks if the hash is the last hash
		 * @param {string} newHash
		 * @return {boolean} true
		 */
		EDHistory.isLastHash = function(newHash){
			// Prepare
			var oldHash = EDHistory.getHashByIndex(),
				isLast;

			// Check
			isLast = newHash === oldHash;

			// Return isLast
			return isLast;
		};

		/**
		 * EDHistory.saveHash(newHash)
		 * Push a Hash
		 * @param {string} newHash
		 * @return {boolean} true
		 */
		EDHistory.saveHash = function(newHash){
			// Check Hash
			if ( EDHistory.isLastHash(newHash) ) {
				return false;
			}

			// Push the Hash
			EDHistory.savedHashes.push(newHash);

			// Return true
			return true;
		};

		/**
		 * EDHistory.getHashByIndex()
		 * Gets a hash by the index
		 * @param {integer} index
		 * @return {string}
		 */
		EDHistory.getHashByIndex = function(index){
			// Prepare
			var hash = null;

			// Handle
			if ( typeof index === 'undefined' ) {
				// Get the last inserted
				hash = EDHistory.savedHashes[EDHistory.savedHashes.length-1];
			}
			else if ( index < 0 ) {
				// Get from the end
				hash = EDHistory.savedHashes[EDHistory.savedHashes.length+index];
			}
			else {
				// Get from the beginning
				hash = EDHistory.savedHashes[index];
			}

			// Return hash
			return hash;
		};


		// ====================================================================
		// Discarded States

		/**
		 * EDHistory.discardedHashes
		 * A hashed array of discarded hashes
		 */
		EDHistory.discardedHashes = {};

		/**
		 * EDHistory.discardedStates
		 * A hashed array of discarded states
		 */
		EDHistory.discardedStates = {};

		/**
		 * EDHistory.discardState(State)
		 * Discards the state by ignoring it through History
		 * @param {object} State
		 * @return {true}
		 */
		EDHistory.discardState = function(discardedState,forwardState,backState){
			//EDHistory.debug('EDHistory.discardState', arguments);
			// Prepare
			var discardedStateHash = EDHistory.getHashByState(discardedState),
				discardObject;

			// Create Discard Object
			discardObject = {
				'discardedState': discardedState,
				'backState': backState,
				'forwardState': forwardState
			};

			// Add to DiscardedStates
			EDHistory.discardedStates[discardedStateHash] = discardObject;

			// Return true
			return true;
		};

		/**
		 * EDHistory.discardHash(hash)
		 * Discards the hash by ignoring it through History
		 * @param {string} hash
		 * @return {true}
		 */
		EDHistory.discardHash = function(discardedHash,forwardState,backState){
			//EDHistory.debug('EDHistory.discardState', arguments);
			// Create Discard Object
			var discardObject = {
				'discardedHash': discardedHash,
				'backState': backState,
				'forwardState': forwardState
			};

			// Add to discardedHash
			EDHistory.discardedHashes[discardedHash] = discardObject;

			// Return true
			return true;
		};

		/**
		 * EDHistory.discardState(State)
		 * Checks to see if the state is discarded
		 * @param {object} State
		 * @return {bool}
		 */
		EDHistory.discardedState = function(State){
			// Prepare
			var StateHash = EDHistory.getHashByState(State),
				discarded;

			// Check
			discarded = EDHistory.discardedStates[StateHash]||false;

			// Return true
			return discarded;
		};

		/**
		 * EDHistory.discardedHash(hash)
		 * Checks to see if the state is discarded
		 * @param {string} State
		 * @return {bool}
		 */
		EDHistory.discardedHash = function(hash){
			// Check
			var discarded = EDHistory.discardedHashes[hash]||false;

			// Return true
			return discarded;
		};

		/**
		 * EDHistory.recycleState(State)
		 * Allows a discarded state to be used again
		 * @param {object} data
		 * @param {string} title
		 * @param {string} url
		 * @return {true}
		 */
		EDHistory.recycleState = function(State){
			//EDHistory.debug('EDHistory.recycleState', arguments);
			// Prepare
			var StateHash = EDHistory.getHashByState(State);

			// Remove from DiscardedStates
			if ( EDHistory.discardedState(State) ) {
				delete EDHistory.discardedStates[StateHash];
			}

			// Return true
			return true;
		};


		// ====================================================================
		// HTML4 HashChange Support

		if ( EDHistory.emulated.hashChange ) {
			/*
			 * We must emulate the HTML4 HashChange Support by manually checking for hash changes
			 */

			/**
			 * EDHistory.hashChangeInit()
			 * Init the HashChange Emulation
			 */
			EDHistory.hashChangeInit = function(){
				// Define our Checker Function
				EDHistory.checkerFunction = null;

				// Define some variables that will help in our checker function
				var lastDocumentHash = '',
					iframeId, iframe,
					lastIframeHash, checkerRunning;

				// Handle depending on the browser
				if ( EDHistory.isInternetExplorer() ) {
					// IE6 and IE7
					// We need to use an iframe to emulate the back and forward buttons

					// Create iFrame
					iframeId = 'historyjs-iframe';
					iframe = document.createElement('iframe');

					// Adjust iFarme
					iframe.setAttribute('id', iframeId);
					iframe.style.display = 'none';

					// Append iFrame
					document.body.appendChild(iframe);

					// Create initial history entry
					iframe.contentWindow.document.open();
					iframe.contentWindow.document.close();

					// Define some variables that will help in our checker function
					lastIframeHash = '';
					checkerRunning = false;

					// Define the checker function
					EDHistory.checkerFunction = function(){
						// Check Running
						if ( checkerRunning ) {
							return false;
						}

						// Update Running
						checkerRunning = true;

						// Fetch
						var documentHash = EDHistory.getHash()||'',
							iframeHash = EDHistory.unescapeHash(iframe.contentWindow.document.location.hash)||'';

						// The Document Hash has changed (application caused)
						if ( documentHash !== lastDocumentHash ) {
							// Equalise
							lastDocumentHash = documentHash;

							// Create a history entry in the iframe
							if ( iframeHash !== documentHash ) {
								//EDHistory.debug('hashchange.checker: iframe hash change', 'documentHash (new):', documentHash, 'iframeHash (old):', iframeHash);

								// Equalise
								lastIframeHash = iframeHash = documentHash;

								// Create History Entry
								iframe.contentWindow.document.open();
								iframe.contentWindow.document.close();

								// Update the iframe's hash
								iframe.contentWindow.document.location.hash = EDHistory.escapeHash(documentHash);
							}

							// Trigger Hashchange Event
							EDHistory.Adapter.trigger(window,'hashchange');
						}

						// The iFrame Hash has changed (back button caused)
						else if ( iframeHash !== lastIframeHash ) {
							//EDHistory.debug('hashchange.checker: iframe hash out of sync', 'iframeHash (new):', iframeHash, 'documentHash (old):', documentHash);

							// Equalise
							lastIframeHash = iframeHash;

							// Update the Hash
							EDHistory.setHash(iframeHash,false);
						}

						// Reset Running
						checkerRunning = false;

						// Return true
						return true;
					};
				}
				else {
					// We are not IE
					// Firefox 1 or 2, Opera

					// Define the checker function
					EDHistory.checkerFunction = function(){
						// Prepare
						var documentHash = EDHistory.getHash();

						// The Document Hash has changed (application caused)
						if ( documentHash !== lastDocumentHash ) {
							// Equalise
							lastDocumentHash = documentHash;

							// Trigger Hashchange Event
							EDHistory.Adapter.trigger(window,'hashchange');
						}

						// Return true
						return true;
					};
				}

				// Apply the checker function
				EDHistory.intervalList.push(setInterval(EDHistory.checkerFunction, EDHistory.options.hashChangeInterval));

				// Done
				return true;
			}; // EDHistory.hashChangeInit

			// Bind hashChangeInit
			EDHistory.Adapter.onDomLoad(EDHistory.hashChangeInit);

		} // EDHistory.emulated.hashChange


		// ====================================================================
		// HTML5 State Support

		// Non-Native pushState Implementation
		if ( EDHistory.emulated.pushState ) {
			/*
			 * We must emulate the HTML5 State Management by using HTML4 HashChange
			 */

			/**
			 * EDHistory.onHashChange(event)
			 * Trigger HTML5's window.onpopstate via HTML4 HashChange Support
			 */
			EDHistory.onHashChange = function(event){
				//EDHistory.debug('EDHistory.onHashChange', arguments);

				// Prepare
				var currentUrl = ((event && event.newURL) || document.location.href),
					currentHash = EDHistory.getHashByUrl(currentUrl),
					currentState = null,
					currentStateHash = null,
					currentStateHashExits = null,
					discardObject;

				// Check if we are the same state
				if ( EDHistory.isLastHash(currentHash) ) {
					// There has been no change (just the page's hash has finally propagated)
					//EDHistory.debug('EDHistory.onHashChange: no change');
					EDHistory.busy(false);
					return false;
				}

				// Reset the double check
				EDHistory.doubleCheckComplete();

				// Store our location for use in detecting back/forward direction
				EDHistory.saveHash(currentHash);

				// Expand Hash
				if ( currentHash && EDHistory.isTraditionalAnchor(currentHash) ) {
					//EDHistory.debug('EDHistory.onHashChange: traditional anchor', currentHash);
					// Traditional Anchor Hash
					EDHistory.Adapter.trigger(window,'anchorchange');
					EDHistory.busy(false);
					return false;
				}

				// Create State
				currentState = EDHistory.extractState(EDHistory.getFullUrl(currentHash||document.location.href,false),true);

				// Check if we are the same state
				if ( EDHistory.isLastSavedState(currentState) ) {
					//EDHistory.debug('EDHistory.onHashChange: no change');
					// There has been no change (just the page's hash has finally propagated)
					EDHistory.busy(false);
					return false;
				}

				// Create the state Hash
				currentStateHash = EDHistory.getHashByState(currentState);

				// Check if we are DiscardedState
				discardObject = EDHistory.discardedState(currentState);
				if ( discardObject ) {
					// Ignore this state as it has been discarded and go back to the state before it
					if ( EDHistory.getHashByIndex(-2) === EDHistory.getHashByState(discardObject.forwardState) ) {
						// We are going backwards
						//EDHistory.debug('EDHistory.onHashChange: go backwards');
						EDHistory.back(false);
					} else {
						// We are going forwards
						//EDHistory.debug('EDHistory.onHashChange: go forwards');
						EDHistory.forward(false);
					}
					return false;
				}

				// Push the new HTML5 State
				//EDHistory.debug('EDHistory.onHashChange: success hashchange');
				EDHistory.pushState(currentState.data,currentState.title,currentState.url,false);

				// End onHashChange closure
				return true;
			};
			EDHistory.Adapter.bind(window,'hashchange',EDHistory.onHashChange);

			/**
			 * EDHistory.pushState(data,title,url)
			 * Add a new State to the history object, become it, and trigger onpopstate
			 * We have to trigger for HTML4 compatibility
			 * @param {object} data
			 * @param {string} title
			 * @param {string} url
			 * @return {true}
			 */
			EDHistory.pushState = function(data,title,url,queue){
				//EDHistory.debug('EDHistory.pushState: called', arguments);

				// Check the State
				if ( EDHistory.getHashByUrl(url) ) {
					throw new Error('EDHistory.js does not support states with fragement-identifiers (hashes/anchors).');
				}

				// Handle Queueing
				if ( queue !== false && EDHistory.busy() ) {
					// Wait + Push to Queue
					//EDHistory.debug('EDHistory.pushState: we must wait', arguments);
					EDHistory.pushQueue({
						scope: History,
						callback: EDHistory.pushState,
						args: arguments,
						queue: queue
					});
					return false;
				}

				// Make Busy
				EDHistory.busy(true);

				// Fetch the State Object
				var newState = EDHistory.createStateObject(data,title,url),
					newStateHash = EDHistory.getHashByState(newState),
					oldState = EDHistory.getState(false),
					oldStateHash = EDHistory.getHashByState(oldState),
					html4Hash = EDHistory.getHash();

				// Store the newState
				EDHistory.storeState(newState);
				EDHistory.expectedStateId = newState.id;

				// Recycle the State
				EDHistory.recycleState(newState);

				// Force update of the title
				EDHistory.setTitle(newState);

				// Check if we are the same State
				if ( newStateHash === oldStateHash ) {
					//EDHistory.debug('EDHistory.pushState: no change', newStateHash);
					EDHistory.busy(false);
					return false;
				}

				// Update HTML4 Hash
				if ( newStateHash !== html4Hash && newStateHash !== EDHistory.getShortUrl(document.location.href) ) {
					//EDHistory.debug('EDHistory.pushState: update hash', newStateHash, html4Hash);
					EDHistory.setHash(newStateHash,false);
					return false;
				}

				// Update HTML5 State
				EDHistory.saveState(newState);

				// Fire HTML5 Event
				//EDHistory.debug('EDHistory.pushState: trigger popstate');
				EDHistory.Adapter.trigger(window,'statechange');
				EDHistory.busy(false);

				// End pushState closure
				return true;
			};

			/**
			 * EDHistory.replaceState(data,title,url)
			 * Replace the State and trigger onpopstate
			 * We have to trigger for HTML4 compatibility
			 * @param {object} data
			 * @param {string} title
			 * @param {string} url
			 * @return {true}
			 */
			EDHistory.replaceState = function(data,title,url,queue){
				//EDHistory.debug('EDHistory.replaceState: called', arguments);

				// Check the State
				if ( EDHistory.getHashByUrl(url) ) {
					throw new Error('EDHistory.js does not support states with fragement-identifiers (hashes/anchors).');
				}

				// Handle Queueing
				if ( queue !== false && EDHistory.busy() ) {
					// Wait + Push to Queue
					//EDHistory.debug('EDHistory.replaceState: we must wait', arguments);
					EDHistory.pushQueue({
						scope: History,
						callback: EDHistory.replaceState,
						args: arguments,
						queue: queue
					});
					return false;
				}

				// Make Busy
				EDHistory.busy(true);

				// Fetch the State Objects
				var newState        = EDHistory.createStateObject(data,title,url),
					oldState        = EDHistory.getState(false),
					previousState   = EDHistory.getStateByIndex(-2);

				// Discard Old State
				EDHistory.discardState(oldState,newState,previousState);

				// Alias to PushState
				EDHistory.pushState(newState.data,newState.title,newState.url,false);

				// End replaceState closure
				return true;
			};

		} // EDHistory.emulated.pushState



		// ====================================================================
		// Initialise

		// Non-Native pushState Implementation
		if ( EDHistory.emulated.pushState ) {
			/**
			 * Ensure initial state is handled correctly
			 */
			if ( EDHistory.getHash() && !EDHistory.emulated.hashChange ) {
				EDHistory.Adapter.onDomLoad(function(){
					EDHistory.Adapter.trigger(window,'hashchange');
				});
			}

		} // EDHistory.emulated.pushState

	}; // EDHistory.initHtml4

	// Try and Initialise History
	if ( typeof EDHistory.init !== 'undefined' ) {
		EDHistory.init();
	}

})(window);
/**
 * EDHistory.js Core
 * @author Benjamin Arthur Lupton <contact@balupton.com>
 * @copyright 2010-2011 Benjamin Arthur Lupton <contact@balupton.com>
 * @license New BSD License <http://creativecommons.org/licenses/BSD/>
 */

(function(window,undefined){
	"use strict";

	// ========================================================================
	// Initialise

	// Localise Globals
	var
		console = window.console||undefined, // Prevent a JSLint complain
		document = window.document, // Make sure we are using the correct document
		navigator = window.navigator, // Make sure we are using the correct navigator
		sessionStorage = window.sessionStorage||false, // sessionStorage
		setTimeout = window.setTimeout,
		clearTimeout = window.clearTimeout,
		setInterval = window.setInterval,
		clearInterval = window.clearInterval,
		JSON = window.JSON,
		alert = window.alert,
		History = window.EDHistory = window.EDHistory||{}, // Public History Object
		history = window.history; // Old History Object

	// MooTools Compatibility
	JSON.stringify = JSON.stringify||JSON.encode;
	JSON.parse = JSON.parse||JSON.decode;

	// Check Existence
	if ( typeof EDHistory.init !== 'undefined' ) {
		throw new Error('EDHistory.js Core has already been loaded...');
	}

	// Initialise History
	EDHistory.init = function(){
		// Check Load Status of Adapter
		if ( typeof EDHistory.Adapter === 'undefined' ) {
			return false;
		}

		// Check Load Status of Core
		if ( typeof EDHistory.initCore !== 'undefined' ) {
			EDHistory.initCore();
		}

		// Check Load Status of HTML4 Support
		if ( typeof EDHistory.initHtml4 !== 'undefined' ) {
			EDHistory.initHtml4();
		}

		// Return true
		return true;
	};


	// ========================================================================
	// Initialise Core

	// Initialise Core
	EDHistory.initCore = function(){
		// Initialise
		if ( typeof EDHistory.initCore.initialized !== 'undefined' ) {
			// Already Loaded
			return false;
		}
		else {
			EDHistory.initCore.initialized = true;
		}


		// ====================================================================
		// Options

		/**
		 * EDHistory.options
		 * Configurable options
		 */
		EDHistory.options = EDHistory.options||{};

		/**
		 * EDHistory.options.hashChangeInterval
		 * How long should the interval be before hashchange checks
		 */
		EDHistory.options.hashChangeInterval = EDHistory.options.hashChangeInterval || 100;

		/**
		 * EDHistory.options.safariPollInterval
		 * How long should the interval be before safari poll checks
		 */
		EDHistory.options.safariPollInterval = EDHistory.options.safariPollInterval || 500;

		/**
		 * EDHistory.options.doubleCheckInterval
		 * How long should the interval be before we perform a double check
		 */
		EDHistory.options.doubleCheckInterval = EDHistory.options.doubleCheckInterval || 500;

		/**
		 * EDHistory.options.storeInterval
		 * How long should we wait between store calls
		 */
		EDHistory.options.storeInterval = EDHistory.options.storeInterval || 1000;

		/**
		 * EDHistory.options.busyDelay
		 * How long should we wait between busy events
		 */
		EDHistory.options.busyDelay = EDHistory.options.busyDelay || 250;

		/**
		 * EDHistory.options.debug
		 * If true will enable debug messages to be logged
		 */
		EDHistory.options.debug = EDHistory.options.debug || false;

		/**
		 * EDHistory.options.initialTitle
		 * What is the title of the initial state
		 */
		EDHistory.options.initialTitle = EDHistory.options.initialTitle || document.title;


		// ====================================================================
		// Interval record

		/**
		 * EDHistory.intervalList
		 * List of intervals set, to be cleared when document is unloaded.
		 */
		EDHistory.intervalList = [];

		/**
		 * EDHistory.clearAllIntervals
		 * Clears all setInterval instances.
		 */
		EDHistory.clearAllIntervals = function(){
			var i, il = EDHistory.intervalList;
			if (typeof il !== "undefined" && il !== null) {
				for (i = 0; i < il.length; i++) {
					clearInterval(il[i]);
				}
				EDHistory.intervalList = null;
			}
		};


		// ====================================================================
		// Debug

		/**
		 * EDHistory.debug(message,...)
		 * Logs the passed arguments if debug enabled
		 */
		EDHistory.debug = function(){
			if ( (EDHistory.options.debug||false) ) {
				EDHistory.log.apply(History,arguments);
			}
		};

		/**
		 * EDHistory.log(message,...)
		 * Logs the passed arguments
		 */
		EDHistory.log = function(){
			// Prepare
			var
				consoleExists = !(typeof console === 'undefined' || typeof console.log === 'undefined' || typeof console.log.apply === 'undefined'),
				textarea = document.getElementById('log'),
				message,
				i,n,
				args,arg
				;

			// Write to Console
			if ( consoleExists ) {
				args = Array.prototype.slice.call(arguments);
				message = args.shift();
				if ( typeof console.debug !== 'undefined' ) {
					console.debug.apply(console,[message,args]);
				}
				else {
					console.log.apply(console,[message,args]);
				}
			}
			else {
				message = ("\n"+arguments[0]+"\n");
			}

			// Write to log
			for ( i=1,n=arguments.length; i<n; ++i ) {
				arg = arguments[i];
				if ( typeof arg === 'object' && typeof JSON !== 'undefined' ) {
					try {
						arg = JSON.stringify(arg);
					}
					catch ( Exception ) {
						// Recursive Object
					}
				}
				message += "\n"+arg+"\n";
			}

			// Textarea
			if ( textarea ) {
				textarea.value += message+"\n-----\n";
				textarea.scrollTop = textarea.scrollHeight - textarea.clientHeight;
			}
			// No Textarea, No Console
			else if ( !consoleExists ) {
				alert(message);
			}

			// Return true
			return true;
		};


		// ====================================================================
		// Emulated Status

		/**
		 * EDHistory.getInternetExplorerMajorVersion()
		 * Get's the major version of Internet Explorer
		 * @return {integer}
		 * @license Public Domain
		 * @author Benjamin Arthur Lupton <contact@balupton.com>
		 * @author James Padolsey <https://gist.github.com/527683>
		 */
		EDHistory.getInternetExplorerMajorVersion = function(){
			var result = EDHistory.getInternetExplorerMajorVersion.cached =
					(typeof EDHistory.getInternetExplorerMajorVersion.cached !== 'undefined')
				?   EDHistory.getInternetExplorerMajorVersion.cached
				:   (function(){
						var v = 3,
								div = document.createElement('div'),
								all = div.getElementsByTagName('i');
						while ( (div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->') && all[0] ) {}
						return (v > 4) ? v : false;
					})()
				;
			return result;
		};

		/**
		 * EDHistory.isInternetExplorer()
		 * Are we using Internet Explorer?
		 * @return {boolean}
		 * @license Public Domain
		 * @author Benjamin Arthur Lupton <contact@balupton.com>
		 */
		EDHistory.isInternetExplorer = function(){
			var result =
				EDHistory.isInternetExplorer.cached =
				(typeof EDHistory.isInternetExplorer.cached !== 'undefined')
					?   EDHistory.isInternetExplorer.cached
					:   Boolean(EDHistory.getInternetExplorerMajorVersion())
				;
			return result;
		};

		/**
		 * EDHistory.emulated
		 * Which features require emulating?
		 */
		EDHistory.emulated = {
			pushState: !Boolean(
				window.history && window.history.pushState && window.history.replaceState
				&& !(
					(/ Mobile\/([1-7][a-z]|(8([abcde]|f(1[0-8]))))/i).test(navigator.userAgent) /* disable for versions of iOS before version 4.3 (8F190) */
					|| (/AppleWebKit\/5([0-2]|3[0-2])/i).test(navigator.userAgent) /* disable for the mercury iOS browser, or at least older versions of the webkit engine */
				)
			),
			hashChange: Boolean(
				!(('onhashchange' in window) || ('onhashchange' in document))
				||
				(EDHistory.isInternetExplorer() && EDHistory.getInternetExplorerMajorVersion() < 8)
			)
		};

		/**
		 * EDHistory.enabled
		 * Is History enabled?
		 */
		EDHistory.enabled = !EDHistory.emulated.pushState;

		/**
		 * EDHistory.bugs
		 * Which bugs are present
		 */
		EDHistory.bugs = {
			/**
			 * Safari 5 and Safari iOS 4 fail to return to the correct state once a hash is replaced by a `replaceState` call
			 * https://bugs.webkit.org/show_bug.cgi?id=56249
			 */
			setHash: Boolean(!EDHistory.emulated.pushState && navigator.vendor === 'Apple Computer, Inc.' && /AppleWebKit\/5([0-2]|3[0-3])/.test(navigator.userAgent)),

			/**
			 * Safari 5 and Safari iOS 4 sometimes fail to apply the state change under busy conditions
			 * https://bugs.webkit.org/show_bug.cgi?id=42940
			 */
			safariPoll: Boolean(!EDHistory.emulated.pushState && navigator.vendor === 'Apple Computer, Inc.' && /AppleWebKit\/5([0-2]|3[0-3])/.test(navigator.userAgent)),

			/**
			 * MSIE 6 and 7 sometimes do not apply a hash even it was told to (requiring a second call to the apply function)
			 */
			ieDoubleCheck: Boolean(EDHistory.isInternetExplorer() && EDHistory.getInternetExplorerMajorVersion() < 8),

			/**
			 * MSIE 6 requires the entire hash to be encoded for the hashes to trigger the onHashChange event
			 */
			hashEscape: Boolean(EDHistory.isInternetExplorer() && EDHistory.getInternetExplorerMajorVersion() < 7)
		};

		/**
		 * EDHistory.isEmptyObject(obj)
		 * Checks to see if the Object is Empty
		 * @param {Object} obj
		 * @return {boolean}
		 */
		EDHistory.isEmptyObject = function(obj) {
			for ( var name in obj ) {
				return false;
			}
			return true;
		};

		/**
		 * EDHistory.cloneObject(obj)
		 * Clones a object and eliminate all references to the original contexts
		 * @param {Object} obj
		 * @return {Object}
		 */
		EDHistory.cloneObject = function(obj) {
			var hash,newObj;
			if ( obj ) {
				hash = JSON.stringify(obj);
				newObj = JSON.parse(hash);
			}
			else {
				newObj = {};
			}
			return newObj;
		};


		// ====================================================================
		// URL Helpers

		/**
		 * EDHistory.getRootUrl()
		 * Turns "http://mysite.com/dir/page.html?asd" into "http://mysite.com"
		 * @return {String} rootUrl
		 */
		EDHistory.getRootUrl = function(){
			// Create
			var rootUrl = document.location.protocol+'//'+(document.location.hostname||document.location.host);
			if ( document.location.port||false ) {
				rootUrl += ':'+document.location.port;
			}
			rootUrl += '/';

			// Return
			return rootUrl;
		};

		/**
		 * EDHistory.getBaseHref()
		 * Fetches the `href` attribute of the `<base href="...">` element if it exists
		 * @return {String} baseHref
		 */
		EDHistory.getBaseHref = function(){
			// Create
			var
				baseElements = document.getElementsByTagName('base'),
				baseElement = null,
				baseHref = '';

			// Test for Base Element
			if ( baseElements.length === 1 ) {
				// Prepare for Base Element
				baseElement = baseElements[0];
				baseHref = baseElement.href.replace(/[^\/]+$/,'');
			}

			// Adjust trailing slash
			baseHref = baseHref.replace(/\/+$/,'');
			if ( baseHref ) baseHref += '/';

			// Return
			return baseHref;
		};

		/**
		 * EDHistory.getBaseUrl()
		 * Fetches the baseHref or basePageUrl or rootUrl (whichever one exists first)
		 * @return {String} baseUrl
		 */
		EDHistory.getBaseUrl = function(){
			// Create
			var baseUrl = EDHistory.getBaseHref()||EDHistory.getBasePageUrl()||EDHistory.getRootUrl();

			// Return
			return baseUrl;
		};

		/**
		 * EDHistory.getPageUrl()
		 * Fetches the URL of the current page
		 * @return {String} pageUrl
		 */
		EDHistory.getPageUrl = function(){
			// Fetch
			var
				State = EDHistory.getState(false,false),
				stateUrl = (State||{}).url||document.location.href,
				pageUrl;

			// Create
			pageUrl = stateUrl.replace(/\/+$/,'').replace(/[^\/]+$/,function(part,index,string){
				return (/\./).test(part) ? part : part+'/';
			});

			// Return
			return pageUrl;
		};

		/**
		 * EDHistory.getBasePageUrl()
		 * Fetches the Url of the directory of the current page
		 * @return {String} basePageUrl
		 */
		EDHistory.getBasePageUrl = function(){
			// Create
			var basePageUrl = document.location.href.replace(/[#\?].*/,'').replace(/[^\/]+$/,function(part,index,string){
				return (/[^\/]$/).test(part) ? '' : part;
			}).replace(/\/+$/,'')+'/';

			// Return
			return basePageUrl;
		};

		/**
		 * EDHistory.getFullUrl(url)
		 * Ensures that we have an absolute URL and not a relative URL
		 * @param {string} url
		 * @param {Boolean} allowBaseHref
		 * @return {string} fullUrl
		 */
		EDHistory.getFullUrl = function(url,allowBaseHref){
			// Prepare
			var fullUrl = url, firstChar = url.substring(0,1);
			allowBaseHref = (typeof allowBaseHref === 'undefined') ? true : allowBaseHref;

			// Check
			if ( /[a-z]+\:\/\//.test(url) ) {
				// Full URL
			}
			else if ( firstChar === '/' ) {
				// Root URL
				fullUrl = EDHistory.getRootUrl()+url.replace(/^\/+/,'');
			}
			else if ( firstChar === '#' ) {
				// Anchor URL
				fullUrl = EDHistory.getPageUrl().replace(/#.*/,'')+url;
			}
			else if ( firstChar === '?' ) {
				// Query URL
				fullUrl = EDHistory.getPageUrl().replace(/[\?#].*/,'')+url;
			}
			else {
				// Relative URL
				if ( allowBaseHref ) {
					fullUrl = EDHistory.getBaseUrl()+url.replace(/^(\.\/)+/,'');
				} else {
					fullUrl = EDHistory.getBasePageUrl()+url.replace(/^(\.\/)+/,'');
				}
				// We have an if condition above as we do not want hashes
				// which are relative to the baseHref in our URLs
				// as if the baseHref changes, then all our bookmarks
				// would now point to different locations
				// whereas the basePageUrl will always stay the same
			}

			// Return
			return fullUrl.replace(/\#$/,'');
		};

		/**
		 * EDHistory.getShortUrl(url)
		 * Ensures that we have a relative URL and not a absolute URL
		 * @param {string} url
		 * @return {string} url
		 */
		EDHistory.getShortUrl = function(url){
			// Prepare
			var shortUrl = url, baseUrl = EDHistory.getBaseUrl(), rootUrl = EDHistory.getRootUrl();

			// Trim baseUrl
			if ( EDHistory.emulated.pushState ) {
				// We are in a if statement as when pushState is not emulated
				// The actual url these short urls are relative to can change
				// So within the same session, we the url may end up somewhere different
				shortUrl = shortUrl.replace(baseUrl,'');
			}

			// Trim rootUrl
			shortUrl = shortUrl.replace(rootUrl,'/');

			// Ensure we can still detect it as a state
			if ( EDHistory.isTraditionalAnchor(shortUrl) ) {
				shortUrl = './'+shortUrl;
			}

			// Clean It
			shortUrl = shortUrl.replace(/^(\.\/)+/g,'./').replace(/\#$/,'');

			// Return
			return shortUrl;
		};


		// ====================================================================
		// State Storage

		/**
		 * EDHistory.store
		 * The store for all session specific data
		 */
		EDHistory.store = {};

		/**
		 * EDHistory.idToState
		 * 1-1: State ID to State Object
		 */
		EDHistory.idToState = EDHistory.idToState||{};

		/**
		 * EDHistory.stateToId
		 * 1-1: State String to State ID
		 */
		EDHistory.stateToId = EDHistory.stateToId||{};

		/**
		 * EDHistory.urlToId
		 * 1-1: State URL to State ID
		 */
		EDHistory.urlToId = EDHistory.urlToId||{};

		/**
		 * EDHistory.storedStates
		 * Store the states in an array
		 */
		EDHistory.storedStates = EDHistory.storedStates||[];

		/**
		 * EDHistory.savedStates
		 * Saved the states in an array
		 */
		EDHistory.savedStates = EDHistory.savedStates||[];

		/**
		 * EDHistory.noramlizeStore()
		 * Noramlize the store by adding necessary values
		 */
		EDHistory.normalizeStore = function(){
			EDHistory.store.idToState = EDHistory.store.idToState||{};
			EDHistory.store.urlToId = EDHistory.store.urlToId||{};
			EDHistory.store.stateToId = EDHistory.store.stateToId||{};
		};

		/**
		 * EDHistory.getState()
		 * Get an object containing the data, title and url of the current state
		 * @param {Boolean} friendly
		 * @param {Boolean} create
		 * @return {Object} State
		 */
		EDHistory.getState = function(friendly,create){
			// Prepare
			if ( typeof friendly === 'undefined' ) { friendly = true; }
			if ( typeof create === 'undefined' ) { create = true; }

			// Fetch
			var State = EDHistory.getLastSavedState();

			// Create
			if ( !State && create ) {
				State = EDHistory.createStateObject();
			}

			// Adjust
			if ( friendly ) {
				State = EDHistory.cloneObject(State);
				State.url = State.cleanUrl||State.url;
			}

			// Return
			return State;
		};

		/**
		 * EDHistory.getIdByState(State)
		 * Gets a ID for a State
		 * @param {State} newState
		 * @return {String} id
		 */
		EDHistory.getIdByState = function(newState){

			// Fetch ID
			var id = EDHistory.extractId(newState.url),
				str;
			
			if ( !id ) {
				// Find ID via State String
				str = EDHistory.getStateString(newState);
				if ( typeof EDHistory.stateToId[str] !== 'undefined' ) {
					id = EDHistory.stateToId[str];
				}
				else if ( typeof EDHistory.store.stateToId[str] !== 'undefined' ) {
					id = EDHistory.store.stateToId[str];
				}
				else {
					// Generate a new ID
					while ( true ) {
						id = (new Date()).getTime() + String(Math.random()).replace(/\D/g,'');
						if ( typeof EDHistory.idToState[id] === 'undefined' && typeof EDHistory.store.idToState[id] === 'undefined' ) {
							break;
						}
					}

					// Apply the new State to the ID
					EDHistory.stateToId[str] = id;
					EDHistory.idToState[id] = newState;
				}
			}

			// Return ID
			return id;
		};

		/**
		 * EDHistory.normalizeState(State)
		 * Expands a State Object
		 * @param {object} State
		 * @return {object}
		 */
		EDHistory.normalizeState = function(oldState){
			// Variables
			var newState, dataNotEmpty;

			// Prepare
			if ( !oldState || (typeof oldState !== 'object') ) {
				oldState = {};
			}

			// Check
			if ( typeof oldState.normalized !== 'undefined' ) {
				return oldState;
			}

			// Adjust
			if ( !oldState.data || (typeof oldState.data !== 'object') ) {
				oldState.data = {};
			}

			// ----------------------------------------------------------------

			// Create
			newState = {};
			newState.normalized = true;
			newState.title = oldState.title||'';
			newState.url = EDHistory.getFullUrl(EDHistory.unescapeString(oldState.url||document.location.href));
			newState.hash = EDHistory.getShortUrl(newState.url);
			newState.data = EDHistory.cloneObject(oldState.data);

			// Fetch ID
			newState.id = EDHistory.getIdByState(newState);

			// ----------------------------------------------------------------

			// Clean the URL
			newState.cleanUrl = newState.url.replace(/\??\&_suid.*/,'');
			newState.url = newState.cleanUrl;

			// Check to see if we have more than just a url
			dataNotEmpty = !EDHistory.isEmptyObject(newState.data);

			// Apply
			if ( newState.title || dataNotEmpty ) {
				// Add ID to Hash
				newState.hash = EDHistory.getShortUrl(newState.url).replace(/\??\&_suid.*/,'');
				if ( !/\?/.test(newState.hash) ) {
					newState.hash += '?';
				}
				newState.hash += '&_suid='+newState.id;
			}

			// Create the Hashed URL
			newState.hashedUrl = EDHistory.getFullUrl(newState.hash);

			// ----------------------------------------------------------------

			// Update the URL if we have a duplicate
			if ( (EDHistory.emulated.pushState || EDHistory.bugs.safariPoll) && EDHistory.hasUrlDuplicate(newState) ) {
				newState.url = newState.hashedUrl;
			}

			// ----------------------------------------------------------------

			// Return
			return newState;
		};

		/**
		 * EDHistory.createStateObject(data,title,url)
		 * Creates a object based on the data, title and url state params
		 * @param {object} data
		 * @param {string} title
		 * @param {string} url
		 * @return {object}
		 */
		EDHistory.createStateObject = function(data,title,url){
			// Hashify
			var State = {
				'data': data,
				'title': title,
				'url': url
			};

			// Expand the State
			State = EDHistory.normalizeState(State);

			// Return object
			return State;
		};

		/**
		 * EDHistory.getStateById(id)
		 * Get a state by it's UID
		 * @param {String} id
		 */
		EDHistory.getStateById = function(id){
			// Prepare
			id = String(id);

			// Retrieve
			var State = EDHistory.idToState[id] || EDHistory.store.idToState[id] || undefined;

			// Return State
			return State;
		};

		/**
		 * Get a State's String
		 * @param {State} passedState
		 */
		EDHistory.getStateString = function(passedState){
			// Prepare
			var State, cleanedState, str;

			// Fetch
			State = EDHistory.normalizeState(passedState);

			// Clean
			cleanedState = {
				data: State.data,
				title: passedState.title,
				url: passedState.url
			};

			// Fetch
			str = JSON.stringify(cleanedState);

			// Return
			return str;
		};

		/**
		 * Get a State's ID
		 * @param {State} passedState
		 * @return {String} id
		 */
		EDHistory.getStateId = function(passedState){
			// Prepare
			var State, id;
			
			// Fetch
			State = EDHistory.normalizeState(passedState);

			// Fetch
			id = State.id;

			// Return
			return id;
		};

		/**
		 * EDHistory.getHashByState(State)
		 * Creates a Hash for the State Object
		 * @param {State} passedState
		 * @return {String} hash
		 */
		EDHistory.getHashByState = function(passedState){
			// Prepare
			var State, hash;
			
			// Fetch
			State = EDHistory.normalizeState(passedState);

			// Hash
			hash = State.hash;

			// Return
			return hash;
		};

		/**
		 * EDHistory.extractId(url_or_hash)
		 * Get a State ID by it's URL or Hash
		 * @param {string} url_or_hash
		 * @return {string} id
		 */
		EDHistory.extractId = function ( url_or_hash ) {
			// Prepare
			var id,parts,url;

			// Extract
			parts = /(.*)\&_suid=([0-9]+)$/.exec(url_or_hash);
			url = parts ? (parts[1]||url_or_hash) : url_or_hash;
			id = parts ? String(parts[2]||'') : '';

			// Return
			return id||false;
		};

		/**
		 * EDHistory.isTraditionalAnchor
		 * Checks to see if the url is a traditional anchor or not
		 * @param {String} url_or_hash
		 * @return {Boolean}
		 */
		EDHistory.isTraditionalAnchor = function(url_or_hash){
			// Check
			var isTraditional = !(/[\/\?\.]/.test(url_or_hash));

			// Return
			return isTraditional;
		};

		/**
		 * EDHistory.extractState
		 * Get a State by it's URL or Hash
		 * @param {String} url_or_hash
		 * @return {State|null}
		 */
		EDHistory.extractState = function(url_or_hash,create){
			// Prepare
			var State = null, id, url;
			create = create||false;

			// Fetch SUID
			id = EDHistory.extractId(url_or_hash);
			if ( id ) {
				State = EDHistory.getStateById(id);
			}

			// Fetch SUID returned no State
			if ( !State ) {
				// Fetch URL
				url = EDHistory.getFullUrl(url_or_hash);

				// Check URL
				id = EDHistory.getIdByUrl(url)||false;
				if ( id ) {
					State = EDHistory.getStateById(id);
				}

				// Create State
				if ( !State && create && !EDHistory.isTraditionalAnchor(url_or_hash) ) {
					State = EDHistory.createStateObject(null,null,url);
				}
			}

			// Return
			return State;
		};

		/**
		 * EDHistory.getIdByUrl()
		 * Get a State ID by a State URL
		 */
		EDHistory.getIdByUrl = function(url){
			// Fetch
			var id = EDHistory.urlToId[url] || EDHistory.store.urlToId[url] || undefined;

			// Return
			return id;
		};

		/**
		 * EDHistory.getLastSavedState()
		 * Get an object containing the data, title and url of the current state
		 * @return {Object} State
		 */
		EDHistory.getLastSavedState = function(){
			return EDHistory.savedStates[EDHistory.savedStates.length-1]||undefined;
		};

		/**
		 * EDHistory.getLastStoredState()
		 * Get an object containing the data, title and url of the current state
		 * @return {Object} State
		 */
		EDHistory.getLastStoredState = function(){
			return EDHistory.storedStates[EDHistory.storedStates.length-1]||undefined;
		};

		/**
		 * EDHistory.hasUrlDuplicate
		 * Checks if a Url will have a url conflict
		 * @param {Object} newState
		 * @return {Boolean} hasDuplicate
		 */
		EDHistory.hasUrlDuplicate = function(newState) {
			// Prepare
			var hasDuplicate = false,
				oldState;

			// Fetch
			oldState = EDHistory.extractState(newState.url);

			// Check
			hasDuplicate = oldState && oldState.id !== newState.id;

			// Return
			return hasDuplicate;
		};

		/**
		 * EDHistory.storeState
		 * Store a State
		 * @param {Object} newState
		 * @return {Object} newState
		 */
		EDHistory.storeState = function(newState){
			// Store the State
			EDHistory.urlToId[newState.url] = newState.id;

			// Push the State
			EDHistory.storedStates.push(EDHistory.cloneObject(newState));

			// Return newState
			return newState;
		};

		/**
		 * EDHistory.isLastSavedState(newState)
		 * Tests to see if the state is the last state
		 * @param {Object} newState
		 * @return {boolean} isLast
		 */
		EDHistory.isLastSavedState = function(newState){
			// Prepare
			var isLast = false,
				newId, oldState, oldId;

			// Check
			if ( EDHistory.savedStates.length ) {
				newId = newState.id;
				oldState = EDHistory.getLastSavedState();
				oldId = oldState.id;

				// Check
				isLast = (newId === oldId);
			}

			// Return
			return isLast;
		};

		/**
		 * EDHistory.saveState
		 * Push a State
		 * @param {Object} newState
		 * @return {boolean} changed
		 */
		EDHistory.saveState = function(newState){
			// Check Hash
			if ( EDHistory.isLastSavedState(newState) ) {
				return false;
			}

			// Push the State
			EDHistory.savedStates.push(EDHistory.cloneObject(newState));

			// Return true
			return true;
		};

		/**
		 * EDHistory.getStateByIndex()
		 * Gets a state by the index
		 * @param {integer} index
		 * @return {Object}
		 */
		EDHistory.getStateByIndex = function(index){
			// Prepare
			var State = null;

			// Handle
			if ( typeof index === 'undefined' ) {
				// Get the last inserted
				State = EDHistory.savedStates[EDHistory.savedStates.length-1];
			}
			else if ( index < 0 ) {
				// Get from the end
				State = EDHistory.savedStates[EDHistory.savedStates.length+index];
			}
			else {
				// Get from the beginning
				State = EDHistory.savedStates[index];
			}

			// Return State
			return State;
		};


		// ====================================================================
		// Hash Helpers

		/**
		 * EDHistory.getHash()
		 * Gets the current document hash
		 * @return {string}
		 */
		EDHistory.getHash = function(){
			var hash = EDHistory.unescapeHash(document.location.hash);
			return hash;
		};

		/**
		 * EDHistory.unescapeString()
		 * Unescape a string
		 * @param {String} str
		 * @return {string}
		 */
		EDHistory.unescapeString = function(str){
			// Prepare
			var result = str,
				tmp;

			// Unescape hash
			while ( true ) {
				tmp = window.unescape(result);
				if ( tmp === result ) {
					break;
				}
				result = tmp;
			}

			// Return result
			return result;
		};

		/**
		 * EDHistory.unescapeHash()
		 * normalize and Unescape a Hash
		 * @param {String} hash
		 * @return {string}
		 */
		EDHistory.unescapeHash = function(hash){
			// Prepare
			var result = EDHistory.normalizeHash(hash);

			// Unescape hash
			result = EDHistory.unescapeString(result);

			// Return result
			return result;
		};

		/**
		 * EDHistory.normalizeHash()
		 * normalize a hash across browsers
		 * @return {string}
		 */
		EDHistory.normalizeHash = function(hash){
			// Prepare
			var result = hash.replace(/[^#]*#/,'').replace(/#.*/, '');

			// Return result
			return result;
		};

		/**
		 * EDHistory.setHash(hash)
		 * Sets the document hash
		 * @param {string} hash
		 * @return {History}
		 */
		EDHistory.setHash = function(hash,queue){
			// Prepare
			var adjustedHash, State, pageUrl;

			// Handle Queueing
			if ( queue !== false && EDHistory.busy() ) {
				// Wait + Push to Queue
				//EDHistory.debug('EDHistory.setHash: we must wait', arguments);
				EDHistory.pushQueue({
					scope: History,
					callback: EDHistory.setHash,
					args: arguments,
					queue: queue
				});
				return false;
			}

			// Log
			//EDHistory.debug('EDHistory.setHash: called',hash);

			// Prepare
			adjustedHash = EDHistory.escapeHash(hash);

			// Make Busy + Continue
			EDHistory.busy(true);

			// Check if hash is a state
			State = EDHistory.extractState(hash,true);
			if ( State && !EDHistory.emulated.pushState ) {
				// Hash is a state so skip the setHash
				//EDHistory.debug('EDHistory.setHash: Hash is a state so skipping the hash set with a direct pushState call',arguments);

				// PushState
				EDHistory.pushState(State.data,State.title,State.url,false);
			}
			else if ( document.location.hash !== adjustedHash ) {
				// Hash is a proper hash, so apply it

				// Handle browser bugs
				if ( EDHistory.bugs.setHash ) {
					// Fix Safari Bug https://bugs.webkit.org/show_bug.cgi?id=56249

					// Fetch the base page
					pageUrl = EDHistory.getPageUrl();

					// Safari hash apply
					EDHistory.pushState(null,null,pageUrl+'#'+adjustedHash,false);
				}
				else {
					// Normal hash apply
					document.location.hash = adjustedHash;
				}
			}

			// Chain
			return History;
		};

		/**
		 * EDHistory.escape()
		 * normalize and Escape a Hash
		 * @return {string}
		 */
		EDHistory.escapeHash = function(hash){
			// Prepare
			var result = EDHistory.normalizeHash(hash);

			// Escape hash
			result = window.escape(result);

			// IE6 Escape Bug
			if ( !EDHistory.bugs.hashEscape ) {
				// Restore common parts
				result = result
					.replace(/\%21/g,'!')
					.replace(/\%26/g,'&')
					.replace(/\%3D/g,'=')
					.replace(/\%3F/g,'?');
			}

			// Return result
			return result;
		};

		/**
		 * EDHistory.getHashByUrl(url)
		 * Extracts the Hash from a URL
		 * @param {string} url
		 * @return {string} url
		 */
		EDHistory.getHashByUrl = function(url){
			// Extract the hash
			var hash = String(url)
				.replace(/([^#]*)#?([^#]*)#?(.*)/, '$2')
				;

			// Unescape hash
			hash = EDHistory.unescapeHash(hash);

			// Return hash
			return hash;
		};

		/**
		 * EDHistory.setTitle(title)
		 * Applies the title to the document
		 * @param {State} newState
		 * @return {Boolean}
		 */
		EDHistory.setTitle = function(newState){
			// Prepare
			var title = newState.title,
				firstState;

			// Initial
			if ( !title ) {
				firstState = EDHistory.getStateByIndex(0);
				if ( firstState && firstState.url === newState.url ) {
					title = firstState.title||EDHistory.options.initialTitle;
				}
			}

			// Apply
			try {
				document.getElementsByTagName('title')[0].innerHTML = title.replace('<','&lt;').replace('>','&gt;').replace(' & ',' &amp; ');
			}
			catch ( Exception ) { }
			document.title = title;

			// Chain
			return History;
		};


		// ====================================================================
		// Queueing

		/**
		 * EDHistory.queues
		 * The list of queues to use
		 * First In, First Out
		 */
		EDHistory.queues = [];

		/**
		 * EDHistory.busy(value)
		 * @param {boolean} value [optional]
		 * @return {boolean} busy
		 */
		EDHistory.busy = function(value){
			// Apply
			if ( typeof value !== 'undefined' ) {
				//EDHistory.debug('EDHistory.busy: changing ['+(EDHistory.busy.flag||false)+'] to ['+(value||false)+']', EDHistory.queues.length);
				EDHistory.busy.flag = value;
			}
			// Default
			else if ( typeof EDHistory.busy.flag === 'undefined' ) {
				EDHistory.busy.flag = false;
			}

			// Queue
			if ( !EDHistory.busy.flag ) {
				// Execute the next item in the queue
				clearTimeout(EDHistory.busy.timeout);
				var fireNext = function(){
					var i, queue, item;
					if ( EDHistory.busy.flag ) return;
					for ( i=EDHistory.queues.length-1; i >= 0; --i ) {
						queue = EDHistory.queues[i];
						if ( queue.length === 0 ) continue;
						item = queue.shift();
						EDHistory.fireQueueItem(item);
						EDHistory.busy.timeout = setTimeout(fireNext,EDHistory.options.busyDelay);
					}
				};
				EDHistory.busy.timeout = setTimeout(fireNext,EDHistory.options.busyDelay);
			}

			// Return
			return EDHistory.busy.flag;
		};

		/**
		 * EDHistory.busy.flag
		 */
		EDHistory.busy.flag = false;

		/**
		 * EDHistory.fireQueueItem(item)
		 * Fire a Queue Item
		 * @param {Object} item
		 * @return {Mixed} result
		 */
		EDHistory.fireQueueItem = function(item){
			return item.callback.apply(item.scope||History,item.args||[]);
		};

		/**
		 * EDHistory.pushQueue(callback,args)
		 * Add an item to the queue
		 * @param {Object} item [scope,callback,args,queue]
		 */
		EDHistory.pushQueue = function(item){
			// Prepare the queue
			EDHistory.queues[item.queue||0] = EDHistory.queues[item.queue||0]||[];

			// Add to the queue
			EDHistory.queues[item.queue||0].push(item);

			// Chain
			return History;
		};

		/**
		 * EDHistory.queue (item,queue), (func,queue), (func), (item)
		 * Either firs the item now if not busy, or adds it to the queue
		 */
		EDHistory.queue = function(item,queue){
			// Prepare
			if ( typeof item === 'function' ) {
				item = {
					callback: item
				};
			}
			if ( typeof queue !== 'undefined' ) {
				item.queue = queue;
			}

			// Handle
			if ( EDHistory.busy() ) {
				EDHistory.pushQueue(item);
			} else {
				EDHistory.fireQueueItem(item);
			}

			// Chain
			return History;
		};

		/**
		 * EDHistory.clearQueue()
		 * Clears the Queue
		 */
		EDHistory.clearQueue = function(){
			EDHistory.busy.flag = false;
			EDHistory.queues = [];
			return History;
		};


		// ====================================================================
		// IE Bug Fix

		/**
		 * EDHistory.stateChanged
		 * States whether or not the state has changed since the last double check was initialised
		 */
		EDHistory.stateChanged = false;

		/**
		 * EDHistory.doubleChecker
		 * Contains the timeout used for the double checks
		 */
		EDHistory.doubleChecker = false;

		/**
		 * EDHistory.doubleCheckComplete()
		 * Complete a double check
		 * @return {History}
		 */
		EDHistory.doubleCheckComplete = function(){
			// Update
			EDHistory.stateChanged = true;

			// Clear
			EDHistory.doubleCheckClear();

			// Chain
			return History;
		};

		/**
		 * EDHistory.doubleCheckClear()
		 * Clear a double check
		 * @return {History}
		 */
		EDHistory.doubleCheckClear = function(){
			// Clear
			if ( EDHistory.doubleChecker ) {
				clearTimeout(EDHistory.doubleChecker);
				EDHistory.doubleChecker = false;
			}

			// Chain
			return History;
		};

		/**
		 * EDHistory.doubleCheck()
		 * Create a double check
		 * @return {History}
		 */
		EDHistory.doubleCheck = function(tryAgain){
			// Reset
			EDHistory.stateChanged = false;
			EDHistory.doubleCheckClear();

			// Fix IE6,IE7 bug where calling history.back or history.forward does not actually change the hash (whereas doing it manually does)
			// Fix Safari 5 bug where sometimes the state does not change: https://bugs.webkit.org/show_bug.cgi?id=42940
			if ( EDHistory.bugs.ieDoubleCheck ) {
				// Apply Check
				EDHistory.doubleChecker = setTimeout(
					function(){
						EDHistory.doubleCheckClear();
						if ( !EDHistory.stateChanged ) {
							//EDHistory.debug('EDHistory.doubleCheck: State has not yet changed, trying again', arguments);
							// Re-Attempt
							tryAgain();
						}
						return true;
					},
					EDHistory.options.doubleCheckInterval
				);
			}

			// Chain
			return History;
		};


		// ====================================================================
		// Safari Bug Fix

		/**
		 * EDHistory.safariStatePoll()
		 * Poll the current state
		 * @return {History}
		 */
		EDHistory.safariStatePoll = function(){
			// Poll the URL

			// Get the Last State which has the new URL
			var
				urlState = EDHistory.extractState(document.location.href),
				newState;

			// Check for a difference
			if ( !EDHistory.isLastSavedState(urlState) ) {
				newState = urlState;
			}
			else {
				return;
			}

			// Check if we have a state with that url
			// If not create it
			if ( !newState ) {
				//EDHistory.debug('EDHistory.safariStatePoll: new');
				newState = EDHistory.createStateObject();
			}

			// Apply the New State
			//EDHistory.debug('EDHistory.safariStatePoll: trigger');
			EDHistory.Adapter.trigger(window,'popstate');

			// Chain
			return History;
		};


		// ====================================================================
		// State Aliases

		/**
		 * EDHistory.back(queue)
		 * Send the browser history back one item
		 * @param {Integer} queue [optional]
		 */
		EDHistory.back = function(queue){
			//EDHistory.debug('EDHistory.back: called', arguments);

			// Handle Queueing
			if ( queue !== false && EDHistory.busy() ) {
				// Wait + Push to Queue
				//EDHistory.debug('EDHistory.back: we must wait', arguments);
				EDHistory.pushQueue({
					scope: History,
					callback: EDHistory.back,
					args: arguments,
					queue: queue
				});
				return false;
			}

			// Make Busy + Continue
			EDHistory.busy(true);

			// Fix certain browser bugs that prevent the state from changing
			EDHistory.doubleCheck(function(){
				EDHistory.back(false);
			});

			// Go back
			history.go(-1);

			// End back closure
			return true;
		};

		/**
		 * EDHistory.forward(queue)
		 * Send the browser history forward one item
		 * @param {Integer} queue [optional]
		 */
		EDHistory.forward = function(queue){
			//EDHistory.debug('EDHistory.forward: called', arguments);

			// Handle Queueing
			if ( queue !== false && EDHistory.busy() ) {
				// Wait + Push to Queue
				//EDHistory.debug('EDHistory.forward: we must wait', arguments);
				EDHistory.pushQueue({
					scope: History,
					callback: EDHistory.forward,
					args: arguments,
					queue: queue
				});
				return false;
			}

			// Make Busy + Continue
			EDHistory.busy(true);

			// Fix certain browser bugs that prevent the state from changing
			EDHistory.doubleCheck(function(){
				EDHistory.forward(false);
			});

			// Go forward
			history.go(1);

			// End forward closure
			return true;
		};

		/**
		 * EDHistory.go(index,queue)
		 * Send the browser history back or forward index times
		 * @param {Integer} queue [optional]
		 */
		EDHistory.go = function(index,queue){
			//EDHistory.debug('EDHistory.go: called', arguments);

			// Prepare
			var i;

			// Handle
			if ( index > 0 ) {
				// Forward
				for ( i=1; i<=index; ++i ) {
					EDHistory.forward(queue);
				}
			}
			else if ( index < 0 ) {
				// Backward
				for ( i=-1; i>=index; --i ) {
					EDHistory.back(queue);
				}
			}
			else {
				throw new Error('EDHistory.go: EDHistory.go requires a positive or negative integer passed.');
			}

			// Chain
			return History;
		};


		// ====================================================================
		// HTML5 State Support

		// Non-Native pushState Implementation
		if ( EDHistory.emulated.pushState ) {
			/*
			 * Provide Skeleton for HTML4 Browsers
			 */

			// Prepare
			var emptyFunction = function(){};
			EDHistory.pushState = EDHistory.pushState||emptyFunction;
			EDHistory.replaceState = EDHistory.replaceState||emptyFunction;
		} // EDHistory.emulated.pushState

		// Native pushState Implementation
		else {
			/*
			 * Use native HTML5 History API Implementation
			 */

			/**
			 * EDHistory.onPopState(event,extra)
			 * Refresh the Current State
			 */
			EDHistory.onPopState = function(event,extra){
				// Prepare
				var stateId = false, newState = false, currentHash, currentState;

				// Reset the double check
				EDHistory.doubleCheckComplete();

				// Check for a Hash, and handle apporiatly
				currentHash = EDHistory.getHash();
				if ( currentHash ) {
					// Expand Hash
					currentState = EDHistory.extractState(currentHash||document.location.href,true);
					if ( currentState ) {
						// We were able to parse it, it must be a State!
						// Let's forward to replaceState
						//EDHistory.debug('EDHistory.onPopState: state anchor', currentHash, currentState);
						EDHistory.replaceState(currentState.data, currentState.title, currentState.url, false);
					}
					else {
						// Traditional Anchor
						//EDHistory.debug('EDHistory.onPopState: traditional anchor', currentHash);
						EDHistory.Adapter.trigger(window,'anchorchange');
						EDHistory.busy(false);
					}

					// We don't care for hashes
					EDHistory.expectedStateId = false;
					return false;
				}

				// Ensure
				stateId = EDHistory.Adapter.extractEventData('state',event,extra) || false;

				// Fetch State
				if ( stateId ) {
					// Vanilla: Back/forward button was used
					newState = EDHistory.getStateById(stateId);
				}
				else if ( EDHistory.expectedStateId ) {
					// Vanilla: A new state was pushed, and popstate was called manually
					newState = EDHistory.getStateById(EDHistory.expectedStateId);
				}
				else {
					// Initial State
					newState = EDHistory.extractState(document.location.href);
				}

				// The State did not exist in our store
				if ( !newState ) {
					// Regenerate the State
					newState = EDHistory.createStateObject(null,null,document.location.href);
				}

				// Clean
				EDHistory.expectedStateId = false;

				// Check if we are the same state
				if ( EDHistory.isLastSavedState(newState) ) {
					// There has been no change (just the page's hash has finally propagated)
					//EDHistory.debug('EDHistory.onPopState: no change', newState, EDHistory.savedStates);
					EDHistory.busy(false);
					return false;
				}

				// Store the State
				EDHistory.storeState(newState);
				EDHistory.saveState(newState);

				// Force update of the title
				EDHistory.setTitle(newState);

				// Fire Our Event
				EDHistory.Adapter.trigger(window,'statechange');
				EDHistory.busy(false);

				// Return true
				return true;
			};
			EDHistory.Adapter.bind(window,'popstate',EDHistory.onPopState);

			/**
			 * EDHistory.pushState(data,title,url)
			 * Add a new State to the history object, become it, and trigger onpopstate
			 * We have to trigger for HTML4 compatibility
			 * @param {object} data
			 * @param {string} title
			 * @param {string} url
			 * @return {true}
			 */
			EDHistory.pushState = function(data,title,url,queue){
				//EDHistory.debug('EDHistory.pushState: called', arguments);

				// Check the State
				if ( EDHistory.getHashByUrl(url) && EDHistory.emulated.pushState ) {
					throw new Error('EDHistory.js does not support states with fragement-identifiers (hashes/anchors).');
				}

				// Handle Queueing
				if ( queue !== false && EDHistory.busy() ) {
					// Wait + Push to Queue
					//EDHistory.debug('EDHistory.pushState: we must wait', arguments);
					EDHistory.pushQueue({
						scope: History,
						callback: EDHistory.pushState,
						args: arguments,
						queue: queue
					});
					return false;
				}

				// Make Busy + Continue
				EDHistory.busy(true);

				// Create the newState
				var newState = EDHistory.createStateObject(data,title,url);

				// Check it
				if ( EDHistory.isLastSavedState(newState) ) {
					// Won't be a change
					EDHistory.busy(false);
				}
				else {
					// Store the newState
					EDHistory.storeState(newState);
					EDHistory.expectedStateId = newState.id;

					// Push the newState
					history.pushState(newState.id,newState.title,newState.url);

					// Fire HTML5 Event
					EDHistory.Adapter.trigger(window,'popstate');
				}

				// End pushState closure
				return true;
			};

			/**
			 * EDHistory.replaceState(data,title,url)
			 * Replace the State and trigger onpopstate
			 * We have to trigger for HTML4 compatibility
			 * @param {object} data
			 * @param {string} title
			 * @param {string} url
			 * @return {true}
			 */
			EDHistory.replaceState = function(data,title,url,queue){
				//EDHistory.debug('EDHistory.replaceState: called', arguments);

				// Check the State
				if ( EDHistory.getHashByUrl(url) && EDHistory.emulated.pushState ) {
					throw new Error('EDHistory.js does not support states with fragement-identifiers (hashes/anchors).');
				}

				// Handle Queueing
				if ( queue !== false && EDHistory.busy() ) {
					// Wait + Push to Queue
					//EDHistory.debug('EDHistory.replaceState: we must wait', arguments);
					EDHistory.pushQueue({
						scope: History,
						callback: EDHistory.replaceState,
						args: arguments,
						queue: queue
					});
					return false;
				}

				// Make Busy + Continue
				EDHistory.busy(true);

				// Create the newState
				var newState = EDHistory.createStateObject(data,title,url);

				// Check it
				if ( EDHistory.isLastSavedState(newState) ) {
					// Won't be a change
					EDHistory.busy(false);
				}
				else {
					// Store the newState
					EDHistory.storeState(newState);
					EDHistory.expectedStateId = newState.id;

					// Push the newState
					history.replaceState(newState.id,newState.title,newState.url);

					// Fire HTML5 Event
					EDHistory.Adapter.trigger(window,'popstate');
				}

				// End replaceState closure
				return true;
			};

		} // !EDHistory.emulated.pushState


		// ====================================================================
		// Initialise

		/**
		 * Load the Store
		 */
		if ( sessionStorage ) {
			// Fetch
			try {
				EDHistory.store = JSON.parse(sessionStorage.getItem('EDHistory.store'))||{};
			}
			catch ( err ) {
				EDHistory.store = {};
			}

			// Normalize
			EDHistory.normalizeStore();
		}
		else {
			// Default Load
			EDHistory.store = {};
			EDHistory.normalizeStore();
		}

		/**
		 * Clear Intervals on exit to prevent memory leaks
		 */
		EDHistory.Adapter.bind(window,"beforeunload",EDHistory.clearAllIntervals);
		EDHistory.Adapter.bind(window,"unload",EDHistory.clearAllIntervals);

		/**
		 * Create the initial State
		 */
		EDHistory.saveState(EDHistory.storeState(EDHistory.extractState(document.location.href,true)));

		/**
		 * Bind for Saving Store
		 */
		if ( sessionStorage ) {
			// When the page is closed
			EDHistory.onUnload = function(){
				// Prepare
				var currentStore, item;

				// Fetch
				try {
					currentStore = JSON.parse(sessionStorage.getItem('EDHistory.store'))||{};
				}
				catch ( err ) {
					currentStore = {};
				}

				// Ensure
				currentStore.idToState = currentStore.idToState || {};
				currentStore.urlToId = currentStore.urlToId || {};
				currentStore.stateToId = currentStore.stateToId || {};

				// Sync
				for ( item in EDHistory.idToState ) {
					if ( !EDHistory.idToState.hasOwnProperty(item) ) {
						continue;
					}
					currentStore.idToState[item] = EDHistory.idToState[item];
				}
				for ( item in EDHistory.urlToId ) {
					if ( !EDHistory.urlToId.hasOwnProperty(item) ) {
						continue;
					}
					currentStore.urlToId[item] = EDHistory.urlToId[item];
				}
				for ( item in EDHistory.stateToId ) {
					if ( !EDHistory.stateToId.hasOwnProperty(item) ) {
						continue;
					}
					currentStore.stateToId[item] = EDHistory.stateToId[item];
				}

				// Update
				EDHistory.store = currentStore;
				EDHistory.normalizeStore();

				// Store
				sessionStorage.setItem('EDHistory.store',JSON.stringify(currentStore));
			};

			// For Internet Explorer
			EDHistory.intervalList.push(setInterval(EDHistory.onUnload,EDHistory.options.storeInterval));
			
			// For Other Browsers
			EDHistory.Adapter.bind(window,'beforeunload',EDHistory.onUnload);
			EDHistory.Adapter.bind(window,'unload',EDHistory.onUnload);
			
			// Both are enabled for consistency
		}

		// Non-Native pushState Implementation
		if ( !EDHistory.emulated.pushState ) {
			// Be aware, the following is only for native pushState implementations
			// If you are wanting to include something for all browsers
			// Then include it above this if block

			/**
			 * Setup Safari Fix
			 */
			if ( EDHistory.bugs.safariPoll ) {
				EDHistory.intervalList.push(setInterval(EDHistory.safariStatePoll, EDHistory.options.safariPollInterval));
			}

			/**
			 * Ensure Cross Browser Compatibility
			 */
			if ( navigator.vendor === 'Apple Computer, Inc.' || (navigator.appCodeName||'') === 'Mozilla' ) {
				/**
				 * Fix Safari HashChange Issue
				 */

				// Setup Alias
				EDHistory.Adapter.bind(window,'hashchange',function(){
					EDHistory.Adapter.trigger(window,'popstate');
				});

				// Initialise Alias
				if ( EDHistory.getHash() ) {
					EDHistory.Adapter.onDomLoad(function(){
						EDHistory.Adapter.trigger(window,'hashchange');
					});
				}
			}

		} // !EDHistory.emulated.pushState


	}; // EDHistory.initCore

	// Try and Initialise History
	EDHistory.init();

})(window);
var timestamps = []; // Array of unique timestamps.

$.fn.route = function(options) {

	if (this.is("a")) {

		var title = this.attr('title');
		//     appendTitle = $.joomla.appendTitle;

		// if (appendTitle==="before") {
		//     title = $.joomla.sitename + ((title) ? " - " + title : "");
		// }

		// if (appendTitle==="after") {
		//     title = ((title) ? title + " - " : "") + $.joomla.sitename;
		// }

		// Creating a unique timestamp that will be associated with the state.
		var t = new Date().getTime();
		timestamps[t] = t;

		EDHistory.pushState($.extend({timestamp: t, refresh: true}, options), title , this.attr("href"));
	}

	return this;
}

EDHistory.Adapter.bind(window,'statechange',function(){

	var state = EDHistory.getState();

	// Fixed back button not refreshing when
	// state is in the first state in navigation history.
	if (state.id===EDHistory.savedStates[0].id) {
		window.location = state.url;
	}

	if(state.data.timestamp in timestamps) {
		// Deleting the unique timestamp associated with the state
		delete timestamps[state.data.timestamp];
	}
	else{
		if (state.data.refresh) {
			window.location = state.url;
		}
	}
});



});