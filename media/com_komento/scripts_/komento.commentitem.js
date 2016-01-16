Komento.module('komento.commentitem', function($) {
var module = this;

$.fn.itemset = function(options) {
	var el = $(this);
	var data = $(el).parents('.kmt-item').data();

	if(!data.item) {
		var item = {};

		item.mine = $(el).parents('.kmt-item');
		item.commentid = item.mine.attr('id');
		item.both = $('.' + item.commentid);
		item.id = item.commentid.split('-')[1];
		item.parentid = item.mine.attr('parentid');
		item.depth = item.mine.attr('depth');
		item.childs = item.mine.attr('childs');
		item.published = item.mine.attr('published');

		// declare object
		item.element = {};
		item.element.mine = {};
		item.element.both = {};

		// affects single self
		item.element.mine.commentText = item.mine.find(options['{commentText}']);
		item.element.mine.commentInfo = item.mine.find(options['{commentInfo}']);
		item.element.mine.commentForm = item.mine.find(options['{commentForm}']);
		item.element.mine.stickButton = item.mine.find(options['{stickButton}']);
		item.element.mine.replyButton = item.mine.find(options['{replyButton}']);
		item.element.mine.reportButton = item.mine.find(options['{reportButton}']);
		item.element.mine.likeButton = item.mine.find(options['{likeButton}']);
		item.element.mine.likesCounter = item.mine.find(options['{likesCounter}']);
		item.element.mine.editButton = item.mine.find(options['{editButton}']);
		item.element.mine.saveEditButton = item.mine.find(options['{saveEditButton}']);
		// item.element.mine.editForm = item.mine.find(options['{editForm}']);
		// item.element.mine.editInput = item.mine.find(options['{editInput}']);
		item.element.mine.deleteButton = item.mine.find(options['{deleteButton}']);
		item.element.mine.publishButton = item.mine.find(options['{publishButton}']);
		item.element.mine.unpublishButton = item.mine.find(options['{unpublishButton}']);
		item.element.mine.parentLink = item.mine.find(options['{parentLink}']);
		item.element.mine.parentContainer = item.mine.find(options['{parentContainer}']);
		item.element.mine.attachmentWrap = item.mine.find(options['{attachmentWrap}']);
		item.element.mine.attachmentFile = item.mine.find(options['{attachmentFile}']);

		// affects all
		item.element.both.commentText = item.both.find(options['{commentText}']);
		item.element.both.commentInfo = item.both.find(options['{commentInfo}']);
		item.element.both.stickButton = item.both.find(options['{stickButton}']);
		item.element.both.replyButton = item.both.find(options['{replyButton}']);
		item.element.both.reportButton = item.both.find(options['{reportButton}']);
		item.element.both.likeButton = item.both.find(options['{likeButton}']);
		item.element.both.likesCounter = item.both.find(options['{likesCounter}']);
		item.element.both.parentLink = item.both.find(options['{parentLink}']);
		item.element.both.parentContainer = item.both.find(options['{parentContainer}']);
		item.element.both.attachmentWrap = item.both.find(options['{attachmentWrap}']);
		item.element.both.attachmentFile = item.both.find(options['{attachmentFile}']);


		item.mine.data('item', item);
		data.item = item;
	}

	return data.item;
};

module.resolve();
});
