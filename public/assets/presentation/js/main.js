$(document).ready(function() {
    $("#dynamicDiv").load("mainpage");
    $("html").easeScroll({
        frameRate: 60,
        animationTime: 1000,
        stepSize: 120,
        pulseAlgorithm: 1,
        pulseScale: 8,
        pulseNormalize: 1,
        accelerationDelta: 20,
        accelerationMax: 1,
        keyboardSupport: true,
        arrowScroll: 50,
        touchpadSupport: true,
        fixedBackground: true
    });

    $("body").on("click", ".form-action-button",function(){
        $.ajax({
            url: $(this).data('url'),
            type: 'POST',
            data: $('form').serialize()
        }).done(function (data) {
            $('.meesage_from_contact').removeClass('alert-danger');
            $('.meesage_from_contact').removeClass('alert-success');
            if (data.success == 1) {
                $('.meesage_from_contact').addClass('alert-success');
                $('.meesage_from_contact').text(mailSuccess);
                $('.rd-mailform').hide();
                $('.contact-form-label').hide();
            } else {
                $('.meesage_from_contact').addClass('alert-danger');
                $('.meesage_from_contact').text(data.errorMessage);
            }
        });
    });
});

$(window).scroll(function() {    
    var scroll = $(window).scrollTop();
    if (scroll >= 50) {
        $("#topNav").addClass("fixed-top");
        $('#returnTop').fadeIn(200); 
    } else{
        $("#topNav").removeClass("fixed-top");
        $('#returnTop').fadeOut(200);
    }
});

$("#homeNavBtn").click(function() {
    $("#dynamicDiv").load("mainpage");
    activeNavBtn(0);
});

$("body").on("click", ".aboutNavBtn",function(){
    $("#dynamicDiv").load("about");
    activeNavBtn(1);
});

$("body").on("click", ".contactNavBtn",function(){
    $("#dynamicDiv").load("contact");
    activeNavBtn(2);
});

$("#contactNavBtn").click(function() {
    $("#dynamicDiv").load("contact");
    activeNavBtn(2);
});

$("#termsLink").click(function() {
    $("#dynamicDiv").load("terms");
});

$('#returnTop').click(function() {
    $('body,html').animate({
        scrollTop : 0                       
    }, 500);
});



function activeNavBtn(value) {
    $('.navbar-nav').find('.nav-item').each(function(idx) {
        $(this).removeClass("active");
        if(idx == value) {
            $(this).addClass("active");
        }
    });
}