;(function($){
    //console.log('js loaded');
    $(document).on('click', '.add-to-lp', function(){
        //console.log('clicked button');
        var btn = $(this);
        var pathID = btn.attr('data-id');
        var nonceIn = btn.attr('data-nonce');
        var uID = btn.attr('data-user');
        var ajaxurl = window.location.href;
        data =  {'lp-ajax': 'learning_path_add_path_to_user',
                pathID: pathID,
                nonce: nonceIn,
                userID: uID};
        //console.log(data);
        $.post(ajaxurl, data).done(function (response) {
            // Response div goes here.
            window.location.reload();
        });
    });

})(jQuery);