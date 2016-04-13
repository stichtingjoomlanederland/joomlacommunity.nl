ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

function appendLog(type, message) {
	EasyDiscuss.$( '#migrator-' + type + '-log' ).append( '<li>' + message + '</li>');
}

function runMigration(type) {
	// Hide migration button.
	EasyDiscuss.$('.migrator-button').hide();

	disjax.load('migrators', type);
}

function runMigrationCategory(type, categories) {
	
	if (categories === 'done') {
		disjax.load('migrators' , type + 'CategoryItem' , current , categories );
		return;
	}

	// Removes the first element
	var current	= categories.shift();

	if (categories.length == 0 && !current) {
		return;
	}

	if (categories.length == 0) {
		categories	= 'done';
	}

	disjax.load( 'migrators' , type + 'CategoryItem' , current , categories );
}

function runMigrationItem(type, itemstr) {

	if (itemstr == 'done') {
		disjax.load( 'migrators' , type + 'PostItem' , 'done' , itemstr );
		return;
	}

	var items 	= itemstr.split( '|' );
	var current	= items.shift();
	var nextstr = items.join( '|' );

	if (items.length == 0) {
		nextstr	= 'done';
	}

	disjax.load( 'migrators' , type + 'PostItem' , current , nextstr );
}


function runMigrationReplies(type) {
	disjax.load( 'migrators' , type + 'PostReplies' );
}

});