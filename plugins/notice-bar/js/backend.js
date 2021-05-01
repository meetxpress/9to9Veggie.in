(function ($) {
    $(function () {
        $('.nb-new-slide-trigger').click(function () {
            var input_name = $(this).data('slide-name');
            var slide_html = '<div class="nb-each-slide"><textarea name="nb_settings' + input_name + '"></textarea><a href="javascript:void(0);" title="Delete Slide" class="nb-remove-slide">x</a></div>';
            $(this).closest('.nb-option-field').find('.nb-slides-append').append(slide_html);
            $(this).closest('.nb-option-field').find('.nb-slides-append .nb-each-slide textarea').last().focus();
        });

        $('body').on('click', '.nb-remove-slide', function () {
            $(this).parent().fadeOut(300, function () {
                $(this).remove();
            });

        });
        
        $('.nb-new-ticker-trigger').click(function () {
            var input_name = $(this).data('ticker-name');
            var slide_html = '<div class="nb-each-slide"><input type="text" name="nb_settings' + input_name + '"/><a href="javascript:void(0);" title="Delete Slide" class="nb-remove-slide">x</a></div>';
            $(this).closest('.nb-option-field').find('.nb-ticker-append').append(slide_html);
            $(this).closest('.nb-option-field').find('.nb-ticker-append .nb-each-slide input[type="text"]').last().focus();
        });

        $('.nb-sortable-icons').sortable({
            handle: '.nb-drag-icon'
        });

        $('.nb-colorpicker').wpColorPicker();


        $('.nb-notice-type').change(function () {
            var notice_type = $(this).val();
            $('.nb-display-ref').hide();
            $('.nb-'+notice_type+'-ref').show();
            $('.nb-notice-type-options').hide();
            $('.nb-'+notice_type+'-options').show();
        });
        if($('.nb-notice-type').length>0){
            
            var notice_type_init = $('.nb-notice-type:checked').val();
            $('.nb-display-ref').hide();
            $('.nb-' + notice_type_init + '-ref').show();
        }
        
        $('.nb-tab-trigger').click(function(){
           $('.nb-tab-trigger').removeClass('nav-tab-active');
           $(this).addClass('nav-tab-active');
           var configuration = $(this).data('configuration');
           $('.nb-configurations').hide();
           $('.nb-'+configuration+'-configurations').show();
        });
            

    });//document.ready close
}(jQuery));