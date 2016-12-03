/*!
 * Dispatch
 * Mediator of Foundry libraries.
 *
 * Copyright (c) 2012 Jason Ramos
 * www.stackideas.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

(function(window, undefined) {

	// Prevent any secondary dispatch overriding existing one.
	if (window.dispatch!==undefined) return;

	var uid = function(p,s) {
		return ((p) ? p : '') + Math.random().toString().replace('.','') + ((s) ? s : '');
	}

	var dispatch = window.dispatch = function(manifest, exports) {

		var setter = false;

		// If it's an object, it is a setter operation.
		if (typeof manifest==="object") {

			if (typeof manifest.name===undefined) {
				return;
			}

			setter = true;
		}

		// If it's a string, it could be a getter or a setter.
		if (typeof manifest==="string") {

			manifest = {
				name: manifest
			}
		}

		// Find the parcel in our registry
		var parcel = dispatch.parcels[manifest.name];

		// If this is a new parcel
		if (parcel===undefined) {

			// Create the parcel and put it in our registry
			parcel = dispatch.parcels[manifest.name] = new Parcel(manifest.name);

			// Add a variation of the parcel
			parcel.add(manifest, exports);

		// If it's a setter operation, add a variation of the parcel.
		} else if (setter) {

			parcel.add(manifest, exports);
		}

		// If it's a setter operation, the parcel is always the latest one.
		// If it's a getter operation, automatically get the latest variation.
		return parcel.get();
	}

	// Parcel registry
	dispatch.parcels = {};

	// Dropsite registry
	dispatch.dropsite = {};

	dispatch.delivering = false;

	dispatch.queue = [];

	// Internal function. Do not use.
	dispatch.deliver = function(parcel, dropsite, forceDeliver) {

		if (dispatch.delivering) {
			if (!forceDeliver) {
				dispatch.queue.push(arguments);
				return;
			}
		}

		dispatch.delivering = true;

		// We will always keep a copy of the parcel
		// for current or future dropsites.
		dropsite.addParcel(parcel);

		// If parcel does not contain exports, don't do anything.
		// When the parcel has exports, it will call for delivery again.
		if (parcel.exports===undefined) {
			return;
		}

		// Go through every dropsite
		var i;
		for (i=0; i<dropsite.sites.length; i++) {

			var site = dropsite.sites[i];

			if (parcel.sentTo[site.id]) continue;

			// If dropsite exists
			if (site.target) {

				// Deliver parcel
				site.target.apply(window, [parcel.exports, parcel.manifest]);

				parcel.sentTo[site.id] = true;
			}
		}

		if (dispatch.queue.length > 0) {

			var args = dispatch.queue.shift();
			dispatch.deliver.apply(this, [args[0], args[1], true]);

		} else {

			dispatch.delivering = false;
		}
	}

	// Getting a dropsite.
	dispatch.to = function(name) {

		if (name===undefined) return;

		return dispatch.dropsite[name] || (dispatch.dropsite[name] = new Dropsite(name));
	}

	// Dropsite class

	var Dropsite = function(name) {
		this.name = name;
		this.sites = [];
		this.parcels = [];
	};

	// Dispatch.to("name").at(dropsite);
	// Adds a new dropsite to a recipient.
	// One recipient can have many dropsites.

	Dropsite.prototype.at = function(site) {

		var dropsite = this;

		if (typeof site === "function") {

			var siteId = uid(this.name+"/");

			// Create a new target to the dropsite
			this.sites.push({
				id: siteId,
				target: site
			});

			// Go through every existing parcel for this dropsite,
			// and drop it at the new target.
			var i;
			for (i=0; i<this.parcels.length; i++) {

				var parcel = this.parcels[i];

				dispatch.deliver(parcel, dropsite);
			}
		}

		return this;
	}

	Dropsite.prototype.addParcel = function(newParcel) {

		var parcelExists = false;

		var i;
		for (i=0; i<this.parcels.length; i++) {

			var parcel = this.parcels[i];

			if (parcel == newParcel) {
				parcelExists = true;
				break;
			}
		}

		if (!parcelExists) {
			this.parcels.push(newParcel);
		}
	}

	// Parcel class

	var Parcel = function(manifest) {

		this.name = manifest.name;
		this.dropsites = [];
		this.parcels = [];

		// The rest of the operation is done in .add() called manually.
	}

	Parcel.prototype.add = function(manifest, exports) {

		return this.parcels.push({
			manifest: manifest,
			exports: exports,
			sentTo: {}
		});
	}

	Parcel.prototype.get = function() {

		// Reset lock
		this.indexLocked = false;

		this.index = this.parcels.length - 1;

		return this;
	}

	// Getter
	Parcel.prototype.intendedFor = function(name) {

		var parcel;

		for (i in this.parcels) {

			parcel = this.parcels[i];

			if (parcel.manifest.recipient==name) {

				this.index = i;

				this.indexLocked = true;

				return this;
			}
		}
	}

	// Setter for parcel exports
	Parcel.prototype.containing = function(exports) {

		// .intendedFor() was called before this,
		// it is a getter not setter operation.
		if (this.indexLocked) {

			return this;
		}

		var parcel = this.parcels[this.index];

		// If a dispatch already contains export
		if (parcel.exports!==undefined) {

			// Create another variation instead,
			// also set the index to this variation.
			this.index = this.add({name: this.name}, exports) - 1;

		} else {

			// Else add to the current variation.
			parcel.exports = exports;
		}

		return this;
	}

	// Getter or setter
	Parcel.prototype.to = function(dropsite, forceDeliver) {

		// Don't do anything when no dropsite is given
		if (dropsite===undefined) {
			return this;
		}

		// Get the right parcel
		parcel = this.parcels[this.index];

		// If a dropsite name is given
		if (typeof dropsite==="string") {

			// Resolve dropsite
			var name = dropsite;

			// If a recipient has already been registered,
			// anymore incoming recipient will be assigned
			// only as a secondary dropsite.

			// intendedFor(secondRecipientName) will not work.
			// If you're doing intendedFor() just to get the parcel for the
			// secondary dropsite, you probably need to rethink your logic.

			if (parcel.manifest.recipient!==undefined) {

				parcel.manifest.recipient = name;
			}

			dropsite = dispatch.to(name);
		}

		// If a dropsite target is given
		if (typeof dropsite==="function") {

			// Create a dropsite entry first
			dropsite = dispatch.to("Custom dropsite for " + parcel.manifest.name).at(dropsite);
		}

		// Keep a copy of the dropsite
		this.dropsites.push(dropsite);

		// If the parcel is locked, don't deliver,
		// except if being forced.
		if (this.dropsiteLocked) {

			// Attempt to deliver
			if (forceDeliver) {
				dispatch.deliver(parcel, dropsite);
			}

		} else {

			// This will ensure previous undelivered dropsite
			// gets delivered first before the current dropsite.
			this.toAll();

		}

		return this;
	}

	Parcel.prototype.onlyTo = function(dropsite) {

		this.dropsiteLocked = true;

		this.to(dropsite, true);

		return this;
	}

	Parcel.prototype.toAll = function() {

		this.dropsiteLocked = false;

		var parcel = this.parcels[this.index],
			dropsites = this.dropsites;

		// Go through every parcel and deliver its content
		var i;
		for (i=0; i<dropsites.length; i++) {

			var dropsite = dropsites[i];

			dispatch.deliver(parcel, dropsite);
		}

		return this;
	}

}(window));
