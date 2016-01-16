/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2014 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */
 
if (typeof(Aclmanager) === 'undefined') {
	var Aclmanager = {};
}

window.addEvent('domready', function() {
	$$('.aclmanager a').each(function(el) {
		el.addEvent('click', function() {
			// Not-set -> Allowed
			if((this.getParent().getChildren('input').get('data-assetcheck') == '') 
				&& (this.getParent().getChildren('input').get('value') == '')
				) {
				this.getParent().getChildren('input').set('data-assetcheck', '1');
				this.getParent().getChildren('input').set('value', '1');
				this.getParent().set('class', 'rule allowed');
				this.set('class', 'icon link allow');
				Aclmanager.toChilds(this.getParent().get('data-action'), this.getParent().get('data-assetid'), '1');
			} else {
				// Allowed -> Denied
				if((this.getParent().getChildren('input').get('data-assetcheck') == '1') 
					&& (this.getParent().getChildren('input').get('value') == '1')
					) {
					this.getParent().getChildren('input').set('data-assetcheck', '0');
					this.getParent().getChildren('input').set('value', '0');
					this.getParent().set('class', 'rule denied');
					this.set('class', 'icon link deny');
					Aclmanager.toChilds(this.getParent().get('data-action'), this.getParent().get('data-assetid'), '0');
				} else {
					// Not-set -> Allowed (third)
					if((this.getParent().getChildren('input').get('data-assetcheck') == '0') 
						&& (this.getParent().getChildren('input').get('value') == '0') 
						&& (this.getParent().get('data-third') == '1') 
						) {
						this.getParent().getChildren('input').set('data-assetcheck', '');
						this.getParent().getChildren('input').set('value', '');
						this.getParent().set('class', 'rule');
						this.set('class', 'icon link notset');
						Aclmanager.toChilds(this.getParent().get('data-action'), this.getParent().get('data-assetid'), '');
					} else {
						// Denied -> Not-set (allowed) when parent is allowed
						if((this.getParent().getChildren('input').get('data-assetcheck') == '0') 
							&& (this.getParent().getChildren('input').get('value') == '0') 
							&& (Aclmanager.getParentValue(this.getParent().get('data-groupid'),this.getParent().get('data-action'),this.getParent().get('data-parentid')) =='1')
							) {
							this.getParent().getChildren('input').set('data-assetcheck', '1');
							this.getParent().getChildren('input').set('value', '');
							this.getParent().set('class', 'rule');
							this.set('class', 'icon link allowinactive');
							Aclmanager.toChilds(this.getParent().get('data-action'), this.getParent().get('data-assetid'), '1');
						} else {
							// Denied -> Not-set (denied) when parent is denied
							if((this.getParent().getChildren('input').get('data-assetcheck') == '0') 
								&& (this.getParent().getChildren('input').get('value') == '0') 
								&& (Aclmanager.getParentValue(this.getParent().get('data-groupid'),this.getParent().get('data-action'),this.getParent().get('data-parentid')) =='0')
								) {
								this.getParent().getChildren('input').set('data-assetcheck', '0');
								this.getParent().getChildren('input').set('value', '');
								this.getParent().set('class', 'rule');
								this.set('class', 'icon locked');
								Aclmanager.toChilds(this.getParent().get('data-action'), this.getParent().get('data-assetid'), '0');
							} else {
								// Denied -> Not-set when parent is Not-set
								if((this.getParent().getChildren('input').get('data-assetcheck') == '0') 
									&& (this.getParent().getChildren('input').get('value') == '0') 
									&& (Aclmanager.getParentValue(this.getParent().get('data-groupid'),this.getParent().get('data-action'),this.getParent().get('data-parentid')) =='')
									) {
									this.getParent().getChildren('input').set('data-assetcheck', '');
									this.getParent().getChildren('input').set('value', '');
									this.getParent().set('class', 'rule');
									this.set('class', 'icon link notset');
									Aclmanager.toChilds(this.getParent().get('data-action'), this.getParent().get('data-assetid'), '');
								} else {
									// Not-set (allowed) -> Denied
									if((this.getParent().getChildren('input').get('data-assetcheck') == '1') 
										&& (this.getParent().getChildren('input').get('value') == '') 
										&& (this.getParent().get('data-su') != '1') 
										) {
										this.getParent().getChildren('input').set('data-assetcheck', '0');
										this.getParent().getChildren('input').set('value', '0');
										this.getParent().set('class', 'rule denied');
										this.set('class', 'icon link deny');
										Aclmanager.toChilds(this.getParent().get('data-action'), this.getParent().get('data-assetid'), '0');
									} else {
										// Conflict -> Not-set (denied) when parent is denied
										if((this.getParent().getChildren('input').get('data-assetcheck') == '0') 
											&& (this.getParent().getChildren('input').get('value') == '1') 
											&& (Aclmanager.getParentValue(this.getParent().get('data-groupid'),this.getParent().get('data-action'),this.getParent().get('data-parentid')) =='0')
											) {
											this.getParent().getChildren('input').set('data-assetcheck', '0');
											this.getParent().getChildren('input').set('value', '');
											this.getParent().set('class', 'rule');
											this.set('class', 'icon locked');
											Aclmanager.toChilds(this.getParent().get('data-action'), this.getParent().get('data-assetid'), '0');
										}								
									}
								}
							}
						}
					}
				}
			}
		});
	});
});

Aclmanager.toChilds = function(action, parent, accessright) {
	this.action = action;
	this.accessright = accessright;
	$$('.aclmanager td[data-parentid="'+parent+'"][data-action="'+action+'"]').each(function(el) {
		if(this.accessright == '1' && el.getChildren('input').get('value') != '0') {
			el.getChildren('a').set('class', 'icon link allowinactive');
			el.getChildren('input').set('value', '');
			el.getChildren('input').set('data-assetcheck', '1');
			el.set('class', 'rule');
			Aclmanager.toChilds(this.action, el.get('data-assetid'), this.accessright);
		} else {
			if(this.accessright == '0') {
				el.getChildren('a').set('class', 'icon locked');
				el.getChildren('input').set('value', '');
				el.getChildren('input').set('data-assetcheck', '0');
				el.set('class', 'rule');
				Aclmanager.toChilds(this.action, el.get('data-assetid'), this.accessright);
			} else {
				if(this.accessright == '') {
					el.getChildren('a').set('class', 'icon link notset');
					el.getChildren('input').set('value', '');
					el.getChildren('input').set('data-assetcheck', '');
					el.set('class', 'rule');
					Aclmanager.toChilds(this.action, el.get('data-assetid'), this.accessright);
				}				
			}
		}

	}, this);
};

Aclmanager.getParentValue = function(group, action, parent) {
	var value = '';
	if(parent > 0 && action.search('core') != -1) {
		value = document.id('jformrules_'+parent+'_'+action+'_'+group).get('data-assetcheck');
	}
	return value;
};