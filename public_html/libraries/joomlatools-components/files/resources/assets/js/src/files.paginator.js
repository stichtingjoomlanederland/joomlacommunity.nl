/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */
Files.Paginator = new Class({
	Implements: [Options, Events],
	state: null,
	values: {
		total: 0,
		limit: 0,
		offset: 0,
		page_total: 0,
		page_current: 0
	},
	initialize: function(element, options) {
		if (options.state) {
			this.state = options.state;
			this.setData(this.state.getData());
		}

		this.setOptions(options);

		var element = document.id(element);

		this.element = element;
		this.elements = {
			page_total: element.getElement('.page-total'),
			page_current: element.getElement('.page-current'),
			page_start: element.getElement('.start a'),
			page_next: element.getElement('.next a'),
			page_prev: element.getElement('.prev a'),
			page_end: element.getElement('.end a'),
			page_container: element.getElement('.k-pagination__pages'),
			pages: {},
			limit_box: element.getElement('select')
		};
		this.setValues();

		this.element.addEvent('click:relay(a)', function(e) {
			e.stop();
			if (e.target.get('data-enabled') == '0') {
				return;
			}
			this.fireEvent('clickPage', e.target);
		}.bind(this));
		this.elements.limit_box.addEvent('change', function(e) {
			e.stop();
			this.fireEvent('changeLimit', this.elements.limit_box.get('value'));
		}.bind(this));

	},
	setValues: function() {
		this.fireEvent('beforeSetValues');

		var values = this.values, els = this.elements;

		this.setPageData(els.page_start, {offset: 0});

		this.setPageData(els.page_end, {offset: (values.page_total-1)*values.limit});

		this.setPageData(els.page_prev, {offset: Math.max(0, (values.page_current-2)*values.limit)});

		var offset = Math.min(((values.page_total-1)*values.limit),(values.page_current*values.limit));
		this.setPageData(els.page_next, {offset: offset});

		this.element.getElements('.k-js-page').dispose();
		var i = 1;
		while (i <= values.page_total) {
            var el = null,
				active = false;

			if (i == values.page_current) {
				active = true;
				el = new Element('span', {text: i});
			} else if (i < 3 || Math.abs(i-values.page_total) < 2 || Math.abs(i-(values.page_current)) < 2) {
                // Add a page link for the first and last two pages or a page around the current one
				el = new Element('a', {
					href: '#',
					text: i,
					'data-limit': values.limit,
					'data-offset': (i-1)*values.limit
				});
			} else if (Math.abs(i-values.page_current) < 3) {
                // Add an ellipsis for the gaps
                el = new Element('span', {html: '&hellip;'});
            }

            if (el) {
            	var li = new Element('li', {
            		'class': 'k-js-page ' + (active ? 'k-is-active': '')
				});
				el.inject(li);
                els.pages[i] = el;
                li.inject(els.page_container);
				els.page_next.getParent().inject(els.page_container);
            }

			i++;
		}

		els.limit_box.set('value', values.limit);

		this.fireEvent('afterSetValues');
	},
	setPageData: function(page, data) {
		this.fireEvent('beforeSetPageData', {page: page, data: data});

		var limit = data.limit || this.values.limit;
		page.set('data-limit', limit);
		page.set('data-offset', data.offset);

		var method = data.offset == this.values.offset ? 'addClass' : 'removeClass',
            wrap = page;
        if(wrap.getParent() !== this.elements.page_container && wrap.getParent() !== this.element && !wrap.getParent().match('ul')) {
            wrap = wrap.getParent();

            if(wrap.getParent() !== this.elements.page_container && wrap.getParent() !== this.element && !wrap.getParent().match('ul')) {
                wrap = wrap.getParent();
            }
        }
		wrap[method]('off disabled');
		page.set('data-enabled', (data.offset != this.values.offset)-0);

		this.fireEvent('afterSetPageData', {page: page, data: data});
	},
	setData: function(data) {
		this.fireEvent('beforeSetData', {data: data});

		var values = this.values;
		if (data.total == 0) {
			values.limit = this.state.get('limit');
			values.offset = this.state.get('offset');
			values.total = 0;
			values.page_total = 1;
			values.page_current = 1;
		} else {
            Object.each(data, function(value, key) {
				values[key] = value;
			});

			values.limit = Math.max(values.limit, 1);
			values.offset = Math.max(values.offset, 0);

			if (values.limit > values.total) {
				values.offset = 0;
			}

			if (!values.limit) {
				values.offset = 0;
				values.limit = values.total;
			}

			values.page_total = Math.ceil(values.total/values.limit);

			if (values.offset > values.total) {
				values.offset = (values.page_total-1)*values.limit;
			}
			values.page_current = Math.floor(values.offset/values.limit)+1;
		}

		this.fireEvent('afterSetData', {data: data});
	}
});
