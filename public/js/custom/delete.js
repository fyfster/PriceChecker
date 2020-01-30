jQuery(document).ready(function() {
    $("#datatable_ajax, #datatable_ajax1, #datatable_ajax2").on("click", ".delete-button",function(){
        if (confirm($(this).data('delete-message'))) {
            toggleLoading(true);
            var postUrl = $(this).data('url');
            var id = $(this).data('id');
            $.post(postUrl, {
                id: id,
                _token: $('meta[name="csrf-token"]').attr('content')
            }).done(function( data ) {
                if (typeof data.success != 'undefined' && data.success == 1) {
                    location.reload();
                } else {
                    $( "#message-box" ).remove()
                    var usage = '';
                    if((typeof data.data != 'undefined') && (typeof data.data.usage != 'undefined')) {
                        var i;
                        for (i = 0; i < data.data.usage.length; i++) {
                            usage += '<br>' + data.data.usage[i].name;
                        }
                        usage = '<br>'+usedByTrans+': ' + usage + '<br>';
                    }
                    $( ".delete-box-info" ).prepend('<div class="col-md-12 margin-top-15" id="message-box"><div class="panel panel-danger"><div class="panel-heading"><h3 class="panel-title">'+data.errorMessage+ usage +'</h3><button type="button" class="close message-close" data-target="#message-box" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></div></div></div>');
                    window.scrollTo(0, 0);
                    toggleLoading(false);
                }
            });
        }
    });

    $("#datatable_ajax").on("click", ".enable-button",function(){
        if (confirm($(this).data('enable-message'))) {
            toggleLoading(true);
            var postUrl = $(this).data('url');
            var id = $(this).data('id');
            $.post(postUrl, {
                id: id,
                _token: $('meta[name="csrf-token"]').attr('content')
            }).done(function( data ) {
                if (typeof data.success != 'undefined' && data.success == 1) {
                    location.reload();
                } else {
                    $( "#message-box" ).remove()
                    $( ".delete-box-info" ).prepend('<div class="col-md-12 margin-top-15" id="message-box"><div class="panel panel-danger"><div class="panel-heading"><h3 class="panel-title">'+data.errorMessage+'</h3><button type="button" class="close message-close" data-target="#message-box" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></div></div></div>');
                    toggleLoading(false);
                }
            });
        }
    });

    $(".delete-button-normal").on("click",function(){
        if (confirm($(this).data('delete-message'))) {
            toggleLoading(true);
            let postUrl = $(this).data('url');
            let id = $(this).data('id');
            $.post(postUrl, {
                id: id,
                _token: $('meta[name="csrf-token"]').attr('content')
            }).done(function( data ) {
                if (typeof data.success != 'undefined' && data.success == 1) {
                    if (typeof data.data.redirect_url != 'undefined') {
                        location.replace(data.data.redirect_url);
                    } else {
                        location.reload();
                    }
                } else {
                    $( "#message-box" ).remove();
                    $( ".delete-box-info" ).prepend('<div class="col-md-12 margin-top-15" id="message-box"><div class="panel panel-danger"><div class="panel-heading"><h3 class="panel-title">'+data.errorMessage+'</h3><button type="button" class="close message-close" data-target="#message-box" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></div></div></div>');
                    toggleLoading(false);
                }
            });
        }
    });

    $(".delete-image-button").on("click",function(){
        if (confirm($(this).data('delete-message'))) {
            toggleLoading(true);
            var postUrl = $(this).data('url');
            $.post(postUrl, {
                fid_id: $(this).data('fid-id'),
                file_id: $(this).data('file-id'),
                file_child_id: $(this).data('child-id'),
                _token: $('meta[name="csrf-token"]').attr('content')
            }).done(function( data ) {
                if (typeof data.success != 'undefined' && data.success == 1) {
                    if (data.data.doReload == 1) {
                        location.reload();
                    } else {
                        location.replace($('#success-location').val());
                    }
                } else {
                    $( "#message-box" ).remove()
                    $( ".delete-box-info" ).prepend('<div class="col-md-12 margin-top-15" id="message-box"><div class="panel panel-danger"><div class="panel-heading"><h3 class="panel-title">'+data.errorMessage+'</h3><button type="button" class="close message-close" data-target="#message-box" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></div></div></div>');
                    toggleLoading(false);
                }
            });
        }
    });

    //toggle if loading gif is visible
    function toggleLoading(show) {
        if (show == true) {
            $('.main_object').css({'display': 'none'});
            $('#loading').css({'display': 'block'});
        } else {
            $('.main_object').css({'display': 'block'});
            $('#loading').css({'display':'none'});
        }
    }
});
