<?php
//$this->print_array( $nb_settings );
$position_class = 'nb-' . esc_attr( $nb_settings['display']['display_position'] );
$layout_class = 'nb-' . esc_attr( $nb_settings['layout'] );
foreach ( $nb_settings['layout_1']['middle'] as $key => $val ) {
    $$key = $val;
}
?>
<div class="nb-notice-outer-wrap <?php echo $position_class . '-outer'; ?> <?php echo $layout_class . '-outer'; ?>">
    <div class="nb-notice-wrap <?php echo $position_class; ?> <?php echo $layout_class; ?>">
        <?php
        switch ( $notice_type ) {
            case 'plain-text':
                ?>
                <div class="nb-plain-text-wrap">
                    <?php echo $notice_text; ?>
                </div>
                <?php
                break;
            case 'slider':
                foreach ( $slider as $key => $val ) {
                    $$key = $val;
                }
                ?>
                <div class="nb-slider-wrap" data-auto-start="<?php echo isset( $auto_start ) ? esc_attr( $auto_start ) : 0 ?>" data-slide-duration="<?php echo ($slide_duration == '') ? 2000 : esc_attr( $slide_duration ); ?>" data-show-controls="<?php echo (isset( $show_controls )) ? esc_attr( $show_controls ) : 0; ?>">
                    <?php
                    if ( count( $slides ) ) {
                        foreach ( $slides as $slide ) {
                            if ( $slide != '' ) {
                                ?>
                                <div class="nb-each-slide"><?php echo $slide; ?></div>
                                <?php
                            }
                        }
                    }
                    ?>
                </div>
                <?php
                break;
            case 'news-ticker':
                foreach ( $ticker as $key => $val ) {
                    $$key = $val;
                }
                ?>
                <div class="nb-news-ticker-wrap" data-ticker-label="<?php echo esc_attr( $ticker_label ); ?>" data-ticker-direction="<?php echo esc_attr( $ticker_direction ); ?>" data-ticker-speed="<?php echo ($ticker_speed == '') ? 0.10 : esc_attr( $ticker_speed ); ?>" data-ticker-pause-duration="<?php echo ($ticker_pause == '') ? 2000 : esc_attr( $ticker_pause ); ?>">
                    <ul id="nb-news-ticker">
                        <?php
                        if ( count( $ticker_items ) ) {
                            foreach ( $ticker_items as $ticker_item ) {
                                ?>
                                <li class="nb-each-ticker">
                                   <?php 
                                    $allowed_tags = '<a>';  
                                    $ticker_item = strip_tags($ticker_item, $allowed_tags);
                                    echo $ticker_item; ?>
                                </li>
                                <?php
                            }
                        }
                        ?>  
                    </ul>
                </div>
                <?php
                break;
            case 'social-icons':
                ?>
                <div class="nb-social-icons-wrap">
                    <?php if ( $social_icons['label'] != '' ) { ?><span class="nb-social-icons-label"><?php echo esc_attr( $social_icons['label'] ); ?></span><?php } ?>
                    <div class="nb-social-icons">
                        <?php
                        foreach ( $social_icons['icons'] as $icon_name => $icon_detail ) {
                            if ( isset( $icon_detail['status'] ) && $icon_detail['url'] != '' ) {
                                ?>

                                <a href="<?php echo esc_url( $icon_detail['url'] ); ?>" target="_blank" class="nb-each-icon nb-<?php echo $icon_name ?>" rel="nofollow">
                                    <span class="nb-social-icon"><i class="fa fa-<?php echo $icon_name; ?>"></i></span>
                                </a>

                                <?php
                            }
                            ?>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <?php
                break;
            default:
                break;
        }
        ?>
        <?php if ( $nb_settings['display']['close_action'] != 'disable' ) {
            ?>
            <a href="javascript:void(0);" class="nb-action nb-<?php echo $nb_settings['display']['close_action'] . '-action'; ?>">
                <?php echo $nb_settings['display']['close_action']; ?>
            </a>
            <?php
        }
        ?>
    </div>
    <a href="javascript:void(0);" class="nb-toggle-outer" style="display:none;"><span>Toogle Down</span></a>
    <?php include(NB_BASE_PATH . '/css/dynamic-css.php'); ?>
</div>