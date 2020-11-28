define([
    'jquery',
    'mage/translate',
    'ko',
    'uiComponent',
    'Common_Blog/js/model/post',
    'Common_Blog/js/action/post-comment'
], function ($, $t, ko, Component, post, postComment) {
    'use strict';

    return Component.extend({

        defaults: {
            template: 'Common_Blog/comment',
            replyContent: ko.observable('')
        },

        initialize: function (opts) {
            this._super();
            this.buildComponent(opts);
            return this;
        },

        buildComponent: function (opts) {
            this.allowGuestReply = post.allowGuestReply;
            this.defaultAuthor = post.defaultAuthor;
            this.defaultEmail = post.defaultEmail;
            for (let r = 0; r < opts['replies'].length; r++) {
                this.replies[r] = new this.constructor({
                    comment: opts['replies'][r],
                    replies: opts['replies'][r].children
                });
            }
        },

        openedReply: function () {
            return post.openedReply();
        },

        openReply: function (component, evt) {
            post.openedReply($(evt.currentTarget).data('id'));
        },

        closeReply: function () {
            post.openedReply(0);
        },

        postComment: function (component, evt) {
            postComment($(evt.currentTarget)).then(function () {
                this.replyContent('');
            }.bind(this));
        }

    });
});
