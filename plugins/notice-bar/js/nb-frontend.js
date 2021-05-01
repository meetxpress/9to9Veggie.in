(function ($) {
    function nb_setCookie(cookieName, cookieValue, nDays) {
        var today = new Date();
        var expire = new Date();
        if (nDays == null || nDays == 0)
            nDays = 1;
        expire.setTime(today.getTime() + 3600000 * 24 * nDays);
        document.cookie = cookieName + "=" + escape(cookieValue) + ";expires=" + expire.toGMTString();
    }
    
    $(function () {
        if ($('.nb-slider-wrap').length > 0) {
            var slide_duration = $('.nb-slider-wrap').data('slide-duration');
            var controls = $('.nb-slider-wrap').data('show-controls');
            var auto_start = $('.nb-slider-wrap').data('auto-start');

            $('.nb-slider-wrap').bxSlider({
                auto: (auto_start == 1) ? true : false,
                pager: false,
                controls: (controls == 1) ? true : false,
                speed: slide_duration,
                autoHover:true,
                pause:5000
            });
        }

        if ($('.nb-news-ticker-wrap').length > 0) {
            var ticker_label = $('.nb-news-ticker-wrap').data('ticker-label');
            var ticker_direction = $('.nb-news-ticker-wrap').data('ticker-direction');
            var ticker_speed = $('.nb-news-ticker-wrap').data('ticker-speed');
            var ticker_pause_duration = $('.nb-news-ticker-wrap').data('ticker-pause-duration');

            $('#nb-news-ticker').ticker({
                speed: ticker_speed, // The speed of the reveal
                debugMode: false, // Show some helpful errors in the console or as alerts
                // SHOULD BE SET TO FALSE FOR PRODUCTION SITES!
                controls: false, // Whether or not to show the jQuery News Ticker controls
                titleText: ticker_label, // To remove the title set this to an empty String
                direction: ticker_direction, // Ticker direction - current options are 'ltr' or 'rtl'
                pauseOnItems: ticker_pause_duration // The pause on a news item before being replaced

            });
            //$('#js-news').ticker();
        }

        // Add default class
        $("body").addClass("notice-bar-open");

        $('.nb-toggle-action').click(function () {
            $('.nb-notice-wrap').slideUp(500, function () {
                $('body').removeClass( 'notice-bar-open' ).addClass( 'notice-bar-close' );
                $('.nb-toggle-outer').show();
            });
        });

        $('.nb-toggle-outer').click(function () {
            $(this).hide();
            $('body').removeClass( 'notice-bar-close' ).addClass( 'notice-bar-open' );
            $('.nb-notice-wrap').slideDown(500);
        });

        $('.nb-close-action').click(function () {
            $('.nb-notice-wrap').slideUp(500,function(){
                nb_setCookie('nb_notice_flag','yes',1);
            });
        });
        
        if($('#wpadminbar').length>0 && $('#wpadminbar').is(':visible')){
            $('.nb-top-fixed, .nb-top-absolute, .nb-toggle-outer').css('margin-top','32px');
//            $('.nb-top-absolute').css('margin-top','32px');
//            $('.a.nb-toggle-outer').css('margin-top','32px');
        }

        if($('.ticker-title').length>0)
        {
            var label_width = $('.ticker-title').width();
            var mrg = label_width+5;
            $('.ticker-content').css('left', mrg+'px');
            // alert(label_width);
        }

    });//document.ready close
}(jQuery));