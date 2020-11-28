define([
    'ko'
], function (ko) {
    'use strict';

    return {
        openedReply: ko.observable(0),
        defaultAuthor: null,
        defaultEmail: null,
        postCommentUrl: null
    };
});
