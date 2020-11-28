define([
    'jquery',
    'Common_Blog/js/comment',
    'Common_Blog/js/model/post',
    'Common_Blog/js/action/post-comment'
], function ($, Comment, post, postComment) {
    'use strict';

    return Comment.extend({

        defaults: {
            template: 'Common_Blog/comments'
        },

        buildComponent: function (opts) {
            /**
             * Blow config must be set before building comment elements,
             *   otherwise the settings could not pass correctly.
             */
            post.allowGuestReply = opts.allowGuestReply;
            post.defaultAuthor = opts.defaultAuthor;
            post.defaultEmail = opts.defaultEmail;
            post.postCommentUrl = opts.postCommentUrl;

            for (let c = 0; c < opts['comments'].length; c++) {
                this.comments[c] = new Comment({
                    comment: opts['comments'][c],
                    replies: opts['comments'][c].children
                });
            }
        }

    });
});
