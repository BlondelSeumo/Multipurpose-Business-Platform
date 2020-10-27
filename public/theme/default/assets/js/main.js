$(function() {
    
    "use strict";
    
    //===== Prealoder
    
    $(window).on('load', function(event) {
        $('#preloader').delay(500).fadeOut(500);
    });
    
    
    //===== Mobile Menu 
    
    $(".navbar-toggler").on('click', function() {
        $(this).toggleClass('active');
    });
    
    $(".navbar-nav a").on('click', function() {
        $(".navbar-toggler").removeClass('active');
    });
    
    
    //===== close navbar-collapse when a  clicked
    
    $(".navbar-nav a").on('click', function () {
        $(".navbar-collapse").removeClass("show");
    });
    
    
    //===== Sticky
    
    $(window).on('scroll',function(event) {    
        var scroll = $(window).scrollTop();
        if (scroll < 10) {
            $(".navgition").removeClass("sticky");
        }else{
            $(".navgition").addClass("sticky");
        }
    });
      
    
    //====== Magnific Popup
    
    $('.video-popup').magnificPopup({
        type: 'iframe'
        // other options
    });
    
    
    //===== Back to top
    
    // Show or hide the sticky footer button
    $(window).on('scroll', function(event) {
        if($(this).scrollTop() > 600){
            $('.back-to-top').fadeIn(200)
        } else{
            $('.back-to-top').fadeOut(200)
        }
    });
    
    
    //Animate the scroll to yop
    $('.back-to-top').on('click', function(event) {
        event.preventDefault();
        
        $('html, body').animate({
            scrollTop: 0,
        }, 1500);
    });
    
    
    //===== 
    
    //Submit Contact Form
    $(document).on('submit', '#contact-form', function( event ){
        
        event.preventDefault();

        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data: $(this).serialize(),
            beforeSend: function(){
                $("#preloader").fadeIn();
                $("#contact-form .main-btn").prop('disabled',true);
            },
            success : function(data){
                $("#preloader").fadeOut();
                $("#contact-form .main-btn").prop('disabled',false);           
                var json = JSON.parse(data);
                
                if (json['result'] == true){
                    $("#contact-message").removeClass('alert-danger').addClass('alert-success');
                    $("#contact-message").html('<p>'+ json['message'] +'</p>');
                    $("#contact-message").removeClass('d-none');
					$("#contact-form")[0].reset();
                } else {
                    $("#contact-message").removeClass('alert-success').addClass('alert-danger');
                    $("#contact-message").html('<p>'+ json['message'] +'</p>');
                    $("#contact-message").removeClass('d-none');
                }
            },
            complete: function(request, status, error){
               $("#preloader").fadeOut();
               $("#contact-form .main-btn").prop('disabled',false);
            }
        });
    });

    $(document).on('click','#btn-monthly',function(){
        $(".monthly-package").fadeIn(800);
        $(".yearly-package").css('display','none');
        $(this).removeClass('btn-outline-info').addClass('btn-primary');
        $('#btn-yearly').removeClass('btn-primary').addClass('btn-outline-info');
    });

    $(document).on('click','#btn-yearly',function(){
        $(".yearly-package").fadeIn(800);
        $(".monthly-package").css('display','none');
        $(this).removeClass('btn-outline-info').addClass('btn-primary');
        $('#btn-monthly').removeClass('btn-primary').addClass('btn-outline-info');
    });
    
});