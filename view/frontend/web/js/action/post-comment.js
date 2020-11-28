define([
    'jquery',
    'Common_Blog/js/model/post',
    'validation'
], function ($, post) {
    'use strict';

    return function (formElement) {
        return new Promise(function (resolve) {
            if (formElement.validation() && formElement.validation('isValid')) {
                $('body').trigger('processStart');

                let data = formElement.serializeArray();
                data.push({name: 'form_key', value: $.mage.cookies.get('form_key')});

                $.ajax({
                    url: post.postCommentUrl,
                    data: data,
                    dataType: 'json',
                    type: 'post',
                    success: function (response) {
                        if (response.success) {
                            resolve();
                        } else {
                            console.error(response);
                        }
                    },
                    complete: function () {
                        $('body').trigger('processStop');
                    }
                });
            }
        });
    };
});
