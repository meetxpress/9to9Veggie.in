<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!!' );

$background_color = ($nb_settings['display']['background_color'] == '') ? '#dd3333' : esc_attr( $nb_settings['display']['background_color'] );
$font_color = ($nb_settings['display']['font_color'] == '') ? '#ffffff' : esc_attr( $nb_settings['display']['font_color'] );
$icon_background = ($nb_settings['display']['social_icon_background'] == '') ? '#222' : esc_attr( $nb_settings['display']['social_icon_background'] );
$icon_color = ($nb_settings['display']['social_icon_color'] == '') ? '#fff' : esc_attr( $nb_settings['display']['social_icon_color'] );
$icon_hover_background = (isset( $nb_settings['display']['social_icon_hover_background'] ) && ($nb_settings['display']['social_icon_hover_background'] != '')) ? esc_attr( $nb_settings['display']['social_icon_hover_background'] ) : $icon_background;
$top_bottom = (isset( $nb_settings['display']['top_bottom'] ) && $nb_settings['display']['top_bottom'] != '') ? esc_attr( $nb_settings['display']['top_bottom'] ) : '0';
$anchor_link_color = (isset( $nb_settings['display']['anchor_link_color'] )) ? esc_attr( $nb_settings['display']['anchor_link_color'] ) : '';
$link_hover_color = (isset( $nb_settings['display']['link_hover_color'] )) ? esc_attr( $nb_settings['display']['link_hover_color'] ) : '';
$ticker_label_background = (isset( $nb_settings['display']['ticker_label_background'] ) && $nb_settings['display']['ticker_label_background'] != '') ? esc_attr( $nb_settings['display']['ticker_label_background'] ) : $background_color;
?>
<style>
    .nb-notice-wrap,.nb-notice-wrap .bx-viewport, .nb-notice-wrap .ticker-swipe,.nb-notice-wrap .ticker-swipe span, .nb-notice-wrap .ticker-content,.nb-notice-wrap .ticker-wrapper.has-js, .nb-notice-wrap .ticker{
        background:<?php echo $background_color; ?>;
        color:<?php echo $font_color; ?>;
    }
    .nb-plain-text-wrap a,.nb-slider-wrap a, .nb-notice-wrap .ticker-content a {color:<?php echo $anchor_link_color; ?>;}
    .nb-plain-text-wrap a:hover,.nb-slider-wrap a:hover, .nb-notice-wrap .ticker-content a:hover{color:<?php echo $link_hover_color; ?>;}
    .nb-each-icon{background:<?php echo $icon_background; ?> !important;color:<?php echo $icon_color; ?> !important;}
    .nb-each-icon:hover,.nb-each-icon:active,.nb-each-icon:focus{background:<?php echo $icon_hover_background; ?> !important;color:<?php echo $icon_color; ?> !important;}
    .nb-top-fixed {top: <?php echo $top_bottom; ?>px;}
    .nb-top-fixed-outer .nb-toggle-outer {top: <?php echo $top_bottom + 24; ?>px;}
    .nb-top-absolute{top:<?php echo $top_bottom; ?>px;}
    .nb-top-absolute-outer .nb-toggle-outer{top:<?php echo $top_bottom + 24; ?>px;}
    .nb-bottom{bottom:<?php echo $top_bottom; ?>px;}
    .nb-bottom-outer .nb-toggle-outer{bottom:<?php echo $top_bottom + 24; ?>px;}
    .nb-notice-wrap .ticker-title { color: <?php echo $font_color; ?>;background-color: <?php echo $ticker_label_background; ?> ;}
    .nb-notice-wrap .left .nb-right-arw:after{border-left-color:<?php echo $ticker_label_background; ?>;}
    .nb-notice-wrap .right .nb-right-arw:before{border-right-color:<?php echo $ticker_label_background; ?>;}
    .nb-notice-wrap .nb-right-arw{box-shadow:0 0 2px <?php echo $ticker_label_background; ?>;}
    <?php
    /**
     * We are adding this to hide the ticker label element when label text is blank
     */
    if ( $nb_settings['layout_1']['middle']['ticker']['ticker_label'] == '' ) {
        ?>
        .nb-notice-wrap .ticker-title{visibility:hidden;width:0;}
        <?php
    }

    if ( isset( $nb_settings['display']['disable_for_mobile'] ) && $nb_settings['display']['disable_for_mobile'] == 1 ) {
        ?>
        @media (max-width: 767px) { 

            .nb-notice-wrap{
                display:none;
                
            }
         } 
            <?php
        }
        ?>




    </style>

