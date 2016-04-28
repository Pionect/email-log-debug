jQuery(document).ready(function ($) {

    $(".email_response").click(function () {
        var w = window.open('', 'new_window', 'width=650,height=500'),
            data = {
                action: 'show_smtp_response',
                email_id: $(this).data('email_id')
            };

        $.post(ajaxurl, data, function (response) {
            $(w.document.body).html(response);
        });
    });

});
