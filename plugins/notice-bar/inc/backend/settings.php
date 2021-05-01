<?php
$nb_settings = get_option( 'nb_new_settings' );
//Notice_bar_main::print_array( $nb_settings );
?>
<div class="wrap">
    <div class="nb-header">
        <h3><?php _e( 'Notice Bar', 'notice-bar' ); ?></h3>
        <span class="nb-version">V: <?php echo NB_VERSION; ?></span>
        <div class="nb-clear"></div>
    </div>
    <h3><?php _e( 'Notice Bar Settings', 'notice-bar' ); ?></h3>
    <?php if ( isset( $_GET['success'] ) && 'true' == $_GET['success'] ) : ?>
        <div id="notice-bar-settings_updated" class="updated settings-error notice is-dismissible"> 
            <p>
                <strong><?php echo ($_GET['msg'] == 1) ? __( 'Settings saved.', 'notice-bar' ) : __( 'Default settings restored successfully.', 'notice-bar' ); ?></strong>
            </p>
            <button type="button" class="notice-dismiss">
                <span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'notice-bar' ); ?></span>
            </button>
        </div>
    <?php endif; ?>
    <h2 class="nav-tab-wrapper">
        <a href="javascript:void(0);" class="nav-tab nav-tab-active nb-tab-trigger" data-configuration="basic"><?php _e( 'Basic Configurations', 'notice-bar' ); ?></a>
        <a href="javascript:void(0);" class="nav-tab nb-tab-trigger" data-configuration="notice"><?php _e( 'Notice Configurations', 'notice-bar' ); ?></a>
        <a href="javascript:void(0);" class="nav-tab nb-tab-trigger" data-configuration="display"><?php _e( 'Display Configurations', 'notice-bar' ); ?></a>
    </h2>
    <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>" class="nb-settings-form">
        <input type="hidden" name="action" value="nb_settings_save"/>
        <?php wp_nonce_field( 'nb-settings-nonce', 'nb_settings_nonce_field' ); ?>
        <div class="nb-new-settings-wrap">
            <div class="nb-basic-configurations nb-configurations">
                <h4><?php _e( 'Basic Configurations', 'notice-bar' ); ?></h4>
                <div class="nb-option-field-wrap">
                    <label><?php _e( 'Enable Notice', 'notice-bar' ); ?></label>
                    <div class="nb-option-field">
                        <label class="nb-plain-label">
                            <input type="checkbox" value="1" name="nb_settings[enable]" <?php
                            if ( isset( $nb_settings['enable'] ) ) {
                                checked( $nb_settings['enable'], true );
                            }
                            ?>>
                            <div class="nb-option-side-note"><?php _e( 'Check if you want to enable notice in frontend.', 'notice-bar' ); ?></div>
                        </label>
                    </div>
                </div>
                <div class="nb-option-field-wrap">
                    <label><?php _e( 'Enable Debug Mode', 'notice-bar' ); ?></label>
                    <div class="nb-option-field">
                        <label class="nb-plain-label">
                            <input type="checkbox" value="1" name="nb_settings[debug_mode]" <?php
                            if ( isset( $nb_settings['debug_mode'] ) ) {
                                checked( $nb_settings['debug_mode'], true );
                            }
                            ?>>
                            <div class="nb-option-side-note"><?php _e( 'Check if you want to enable debug mode in frontend.', 'notice-bar' ); ?></div>
                            <div class="nb-option-note"><?php _e( 'Enabling debug mode will use uncompressed css and js files in frontend which can be used to debug the css and js conflicts.', 'notice-bar' ); ?></div>
                        </label>
                    </div>
                </div>

                <div class="nb-option-field-wrap">
                    <label><?php _e( 'Notice Bar Layout', 'notice-bar' ); ?></label>
                    <div class="nb-option-field">
                        <select name="nb_settings[layout]">

                            <option value="layout-1" <?php selected( $nb_settings['layout'], 'layout-1' ); ?>><?php _e( 'Layout 1 (Single Column)', 'notice-bar' ); ?>
                                <?php /*
                                 * <label class="nb-plain-label nb-block"><input type="radio" value="layout-2" name="nb_settings[layout]" <?php checked( $nb_settings['layout'], 'layout-2' ); ?> disabled="disabled"><?php _e( 'Layout 2', 'notice-bar' ); ?> - 2 Column (Left 30 - Right 70)</label>
                                  <label class="nb-plain-label nb-block"><input type="radio" value="layout-3" name="nb_settings[layout]" <?php checked( $nb_settings['layout'], 'layout-3' ); ?> disabled="disabled"><?php _e( 'Layout 3', 'notice-bar' ); ?> - 2 Column (Left 70 - Right 30)</label>
                                 */ ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="nb-notice-configurations nb-configurations" style="display:none">
                <h4><?php _e( 'Notice configurations', 'notice-bar' ); ?></h4>
                <div class="nb-components-wrap">

                    <div class="nb-layout-1-component nb-layout-component">
                        <div class="nb-option-field-wrap">
                            <label><?php _e( 'Notice Type', 'notice-bar' ); ?></label>
                            <div class="nb-option-field">
                                <label class="nb-plain-label"><input type="radio" name="nb_settings[layout_1][middle][notice_type]" value="plain-text" class="nb-notice-type" <?php checked( $nb_settings['layout_1']['middle']['notice_type'], 'plain-text' ); ?>/><?php _e( 'Plain Text', 'notice-bar' ); ?></label>
                                <label class="nb-plain-label"><input type="radio" name="nb_settings[layout_1][middle][notice_type]" value="slider" class="nb-notice-type" <?php checked( $nb_settings['layout_1']['middle']['notice_type'], 'slider' ); ?>/><?php _e( 'Slider', 'notice-bar' ); ?></label>
                                <label class="nb-plain-label"><input type="radio" name="nb_settings[layout_1][middle][notice_type]" value="news-ticker" class="nb-notice-type" <?php checked( $nb_settings['layout_1']['middle']['notice_type'], 'news-ticker' ); ?>/><?php _e( 'News Ticker', 'notice-bar' ); ?></label>
                                <label class="nb-plain-label"><input type="radio" name="nb_settings[layout_1][middle][notice_type]" value="social-icons" class="nb-notice-type" <?php checked( $nb_settings['layout_1']['middle']['notice_type'], 'social-icons' ); ?>/><?php _e( 'Social Icons', 'notice-bar' ); ?></label>
                            </div>
                        </div>
                        <div class="nb-notice-type-options nb-plain-text-options" <?php if ( $nb_settings['layout_1']['middle']['notice_type'] != 'plain-text' ) { ?>style="display:none;"<?php } ?>>
                            <div class="nb-option-field-wrap">
                                <label><?php _e( 'Notice Text', 'notice-bar' ); ?></label>
                                <div class="nb-option-field">
                                    <textarea name="nb_settings[layout_1][middle][notice_text]" rows="5"><?php echo esc_attr( $nb_settings['layout_1']['middle']['notice_text'] ); ?></textarea>
                                    <div class="nb-option-note">
                                        <?php _e( 'Allowed HTML Tags are : a, button, em, br, strong', 'notice-bar' ); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="nb-notice-type-options nb-slider-options" <?php if ( $nb_settings['layout_1']['middle']['notice_type'] != 'slider' ) { ?>style="display:none;"<?php } ?>>
                            <div class="nb-option-field-wrap">
                                <label><?php _e( 'Slides', 'notice-bar' ) ?></label>
                                <div class="nb-option-field">

                                    <div class="nb-slides-append">
                                        <?php
                                        $slide_count = 0;
                                        $slides = array();
                                        if ( isset( $nb_settings['layout_1']['middle']['slider']['slides'] ) ) {
                                            $slides = $nb_settings['layout_1']['middle']['slider']['slides'];
                                        }
                                        if ( count( $slides ) > 0 ) :
                                            foreach ( $slides as $slide ) {
                                                $slide_count++;
                                                ?>
                                                <div class="nb-each-slide">
                                                    <textarea name="nb_settings[layout_1][middle][slider][slides][]"><?php echo $slide; ?></textarea>
                                                    <?php if ( $slide_count != 1 ) {
                                                        ?>
                                                        <a href="javascript:void(0);" title="Delete Slide" class="nb-remove-slide">x</a>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                <?php
                                            }
                                        endif;
                                        ?>

                                    </div>
                                    <input type="button" class="button-primary nb-new-slide-trigger" value="<?php _e( 'Add New Slide', 'notice-bar' ); ?>" data-slide-name="[layout_1][middle][slider][slides][]"/>
                                </div>
                            </div>
                            <div class="nb-option-field-wrap">
                                <label><?php _e( 'Auto Slide', 'notice-bar' ); ?></label>
                                <div class="nb-option-field">
                                    <label class="nb-plain-label">
                                        <?php
                                        $auto_start = isset( $nb_settings['layout_1']['middle']['slider']['auto_start'] ) ? esc_attr( $nb_settings['layout_1']['middle']['slider']['auto_start'] ) : 0;
                                        ?>
                                        <input type="checkbox" name="nb_settings[layout_1][middle][slider][auto_start]" value="1" <?php checked( $auto_start, true ); ?>/>
                                        <div class="nb-option-side-note"><?php _e( 'Check if you want to auto start the slider', 'notice-bar' ); ?></div>
                                    </label>
                                </div>
                            </div>
                            <div class="nb-option-field-wrap">
                                <label><?php _e( 'Show Controls', 'notice-bar' ); ?></label>
                                <div class="nb-option-field">
                                    <label class="nb-plain-label">
                                        <?php
                                        $show_controls = isset( $nb_settings['layout_1']['middle']['slider']['show_controls'] ) ? esc_attr( $nb_settings['layout_1']['middle']['slider']['show_controls'] ) : 0;
                                        ?>
                                        <input type="checkbox" name="nb_settings[layout_1][middle][slider][show_controls]" value="1" <?php checked( $show_controls, true ); ?>/>
                                        <div class="nb-option-side-note"><?php _e( 'Check if you want to show slider controls', 'notice-bar' ); ?></div>
                                    </label>
                                </div>
                            </div>
                            <div class="nb-option-field-wrap">
                                <label><?php _e( 'Slide Duration', 'notice-bar' ); ?></label>
                                <div class="nb-option-field">
                                    <input type="number" name="nb_settings[layout_1][middle][slider][slide_duration]" placeholder="1000" value="<?php echo esc_attr( $nb_settings['layout_1']['middle']['slider']['slide_duration'] ); ?>" min="0" step="100"/>
                                    <div class="nb-option-note"><?php _e( 'Please enter the slide duration in milliseconds. Default duration is 1000', 'notice-bar' ); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="nb-notice-type-options nb-news-ticker-options" <?php if ( $nb_settings['layout_1']['middle']['notice_type'] != 'news-ticker' ) { ?>style="display:none;"<?php } ?>>
                            <div class="nb-option-field-wrap">
                                <label><?php _e( 'Ticker Label', 'notice-bar' ); ?></label>
                                <div class="nb-option-field">
                                    <input type="text" name="nb_settings[layout_1][middle][ticker][ticker_label]" placeholder="<?php _e( 'Latest News', 'notice-bar' ); ?>" value="<?php echo esc_attr( $nb_settings['layout_1']['middle']['ticker']['ticker_label'] ); ?>"/>
                                    <div class="nb-option-note"><?php _e( 'Please enter the ticker label. Leave blank if you don\'t want to display ticker label.', 'notice-bar' ); ?></div>
                                </div>
                            </div>
                            <div class="nb-option-field-wrap">
                                <label><?php _e( 'Ticker Items', 'notice-bar' ); ?></label>
                                <div class="nb-option-field">
                                    <div class="nb-ticker-append">
                                        <?php
                                        $ticker_count = 0;
                                        $tickers = array();
                                        if ( isset(  $nb_settings['layout_1']['middle']['ticker']['ticker_items'] ) ) {
                                            $tickers =  $nb_settings['layout_1']['middle']['ticker']['ticker_items'];
                                        }
                                        if ( count( $tickers ) > 0 ) :
                                            foreach ( $tickers as $ticker ) {
                                                $ticker_count++;
                                                ?>
                                                <div class="nb-each-slide">
                                                    <input type="text" name="nb_settings[layout_1][middle][ticker][ticker_items][]" value="<?php echo esc_attr( $ticker ); ?>"/>
                                                    <?php if ( $ticker_count != 1 ) {
                                                        ?>
                                                        <a href="javascript:void(0);" title="Delete Slide" class="nb-remove-slide">x</a>
                                                    <?php }
                                                    ?>
                                                </div>
                                            <?php
                                            }
                                        endif;
                                        ?>
                                    </div>
                                    <input type="button" class="button-primary nb-new-ticker-trigger" value="<?php _e( 'Add New Item', 'notice-bar' ); ?>" data-ticker-name="[layout_1][middle][ticker][ticker_items][]"/>
                                    <div class="nb-option-note"><?php _e( 'Please add the ticker text which will fit in the width of the notice bar. Else ticker text may be overlapped.', 'notice-bar' ) ?></div>
                                </div>
                            </div>
                            <div class="nb-option-field-wrap">
                                <label><?php _e( 'Ticker Direction', 'notice-bar' ); ?></label>
                                <div class="nb-option-field">
                                    <select name="nb_settings[layout_1][middle][ticker][ticker_direction]">
                                        <?php $ticker_direction = $nb_settings['layout_1']['middle']['ticker']['ticker_direction']; ?>
                                        <option value="ltr" <?php selected( $ticker_direction, 'ltr' ); ?>><?php _e( 'Left to right', 'notice-bar' ); ?></option>
                                        <option value="rtl" <?php selected( $ticker_direction, 'rtl' ); ?>><?php _e( 'Right to left', 'notice-bar' ); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="nb-option-field-wrap">
                                <label><?php _e( 'Ticker Speed', 'notice-bar' ); ?></label>
                                <div class="nb-option-field">
                                    <input type="text" name="nb_settings[layout_1][middle][ticker][ticker_speed]" placeholder="0.10" value="<?php echo esc_attr( $nb_settings['layout_1']['middle']['ticker']['ticker_speed'] ); ?>"/>
                                    <div class="nb-option-note"><?php _e( 'Please enter the reveal speed of ticker. Default value is 0.10 ', 'notice-bar' ); ?></div>
                                </div>
                            </div>
                            <div class="nb-option-field-wrap">
                                <label><?php _e( 'Ticker Pause Duration', 'notice-bar' ); ?></label>
                                <div class="nb-option-field">
                                    <input type="text" name="nb_settings[layout_1][middle][ticker][ticker_pause]" placeholder="2000"  value="<?php echo esc_attr( $nb_settings['layout_1']['middle']['ticker']['ticker_pause'] ); ?>"/>
                                    <div class="nb-option-note"><?php _e( 'Please enter the pause duration between each ticker item in milliseconds. Default value is 2000', 'notice-bar' ); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="nb-notice-type-options nb-social-icons-options" <?php if ( $nb_settings['layout_1']['middle']['notice_type'] != 'social-icons' ) { ?>style="display:none;"<?php } ?>>
                            <div class="nb-option-field-wrap">
                                <label><?php _e( 'Social Icons Label', 'notice-bar' ); ?></label>
                                <div class="nb-option-field">
                                    <input type="text" name="nb_settings[layout_1][middle][social_icons][label]" placeholder="<?php _e( 'Follow Us', 'notice-bar' ); ?>" value="<?php echo esc_attr( $nb_settings['layout_1']['middle']['social_icons']['label'] ); ?>"/>
                                    <div class="nb-option-note"><?php _e( 'This Label will show just before social icons. Please leave blank if you don\'t want to display the label', 'notice-bar' ); ?></div>
                                </div>
                            </div>
                            <div class="nb-option-field-wrap">
                                <label><?php _e( 'Social Icons', 'notice-bar' ); ?></label>
                                <div class="nb-option-field nb-sortable-icons">
                                    <?php
                                    $social_icons = array();
                                    if ( isset( $nb_settings['layout_1']['middle']['social_icons']['icons'] ) ) {
                                        $social_icons = $nb_settings['layout_1']['middle']['social_icons']['icons'];
                                    }
                                    if ( count( $social_icons ) > 0 ) :
                                        foreach ( $social_icons as $icon => $icon_detail ) {
                                            $status = isset( $icon_detail['status'] ) ? $icon_detail['status'] : 0;
                                            ?>
                                            <div class="nb-each-social-icon">
                                                <span class="nb-drag-icon"><i class="fa fa-arrows"></i></span>
                                                <label class="nb-plain-label">
                                                    <span class="nb-icon"><i class="fa fa-<?php echo $icon; ?>"></i></span>
                                                    <div class="nb-inner-option-wrap">
                                                        <span class="nb-inner-label"><?php _e( 'Enable', 'notice-bar' ); ?></span>
                                                        <input type="checkbox" name="nb_settings[layout_1][middle][social_icons][icons][<?php echo $icon; ?>][status]" value="1" <?php checked( $status, true ); ?>/>
                                                    </div>
                                                    <div class="nb-inner-option-wrap">
                                                        <span class="nb-inner-label"><?php _e( 'URL', 'notice-bar' ); ?></span>
                                                        <input type="text" name="nb_settings[layout_1][middle][social_icons][icons][<?php echo $icon; ?>][url]" value="<?php echo esc_url( $icon_detail['url'] ); ?>"/>
                                                    </div>
                                                </label>
                                            </div>
                                            <?php
                                        }
                                    endif;
                                    ?>

                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="nb-layout-2-component nb-layout-component"></div>
                    <div class="nb-layout-3-component nb-layout-component"></div>
                </div>
            </div>
            <div class="nb-display-configurations nb-configurations" style="display:none">
                <h4><?php _e( 'Display configurations', 'notice-bar' ); ?></h4>
                <div class="nb-option-field-wrap">
                    <label><?php _e( 'Notice Bar Position', 'notice-bar' ); ?></label>
                    <div class="nb-option-field">
                        <select name="nb_settings[display][display_position]">
                            <option value="top-absolute" <?php selected( $nb_settings['display']['display_position'], 'top-absolute' ); ?>><?php _e( 'Top Absolute', 'notice-bar' ); ?></option>
                            <option value="top-fixed" <?php selected( $nb_settings['display']['display_position'], 'top-fixed' ); ?>><?php _e( 'Top Fixed', 'notice-bar' ); ?></option>
                            <option value="bottom" <?php selected( $nb_settings['display']['display_position'], 'bottom' ); ?>><?php _e( 'Bottom', 'notice-bar' ); ?></option>
                        </select>
                    </div>
                </div>
                <div class="nb-option-field-wrap">
                    <label><?php _e( 'Close Action', 'notice-bar' ); ?></label>
                    <div class="nb-option-field">
                        <?php $close_action = isset( $nb_settings['display']['close_action'] ) ? $nb_settings['display']['close_action'] : 0; ?>
                        <select name="nb_settings[display][close_action]">
                            <option value="disable" <?php selected( $close_action, 'disable' ); ?>><?php _e( 'Disable', 'notice-bar' ); ?></option>
                            <option value="close" <?php selected( $close_action, 'close' ); ?>><?php _e( 'Close Button', 'notice-bar' ); ?></option>
                            <option value="toggle" <?php selected( $close_action, 'toggle' ); ?>><?php _e( 'Toggle Button', 'notice-bar' ); ?></option>
                        </select>
                    </div>
                </div>
                <div class="nb-option-field-wrap">
                    <label><?php _e( 'Background Color', 'notice-bar' ); ?></label>
                    <div class="nb-option-field">
                        <input type="text" name="nb_settings[display][background_color]" class="nb-colorpicker" value="<?php echo esc_attr( $nb_settings['display']['background_color'] ); ?>"/>
                    </div>
                </div>
                <div class="nb-option-field-wrap">
                    <label><?php _e( 'Font Color', 'notice-bar' ); ?></label>
                    <div class="nb-option-field">
                        <input type="text" name="nb_settings[display][font_color]" class="nb-colorpicker" value="<?php echo esc_attr( $nb_settings['display']['font_color'] ); ?>"/>
                    </div>
                </div>
                <div class="nb-social-icons-ref nb-display-ref">
                    <div class="nb-option-field-wrap">
                        <label><?php _e( 'Social Icon Background', 'notice-bar' ); ?></label>
                        <div class="nb-option-field">
                            <input type="text" name="nb_settings[display][social_icon_background]" class="nb-colorpicker"  value="<?php echo esc_attr( $nb_settings['display']['social_icon_background'] ); ?>"/>
                        </div>
                    </div>
                    <div class="nb-option-field-wrap">
                        <label><?php _e( 'Social Icon Hover Background', 'notice-bar' ); ?></label>
                        <div class="nb-option-field">
                            <input type="text" name="nb_settings[display][social_icon_hover_background]" class="nb-colorpicker"  value="<?php echo isset( $nb_settings['display']['social_icon_hover_background'] ) ? esc_attr( $nb_settings['display']['social_icon_hover_background'] ) : ''; ?>"/>
                        </div>
                    </div>
                    <div class="nb-option-field-wrap">
                        <label><?php _e( 'Social Icon Color', 'notice-bar' ); ?></label>
                        <div class="nb-option-field">
                            <input type="text" name="nb_settings[display][social_icon_color]" class="nb-colorpicker" value="<?php echo esc_attr( $nb_settings['display']['social_icon_color'] ); ?>"/>
                        </div>
                    </div>
                </div>
                <div class="nb-option-field-wrap nb-display-ref nb-plain-text-ref nb-news-ticker-ref nb-slider-ref">
                    <label><?php _e( 'Anchor Link Color', 'notice-bar' ); ?></label>
                    <div class="nb-option-field">
                        <input type="text" name="nb_settings[display][anchor_link_color]" class="nb-colorpicker" value="<?php echo isset( $nb_settings['display']['anchor_link_color'] ) ? esc_attr( $nb_settings['display']['anchor_link_color'] ) : ''; ?>"/>
                        <div class="nb-option-note"><?php _e( 'Please choose the color of the link. It will also work as color for News Ticker. Leave blank if you want to assign active theme\'s default color.', 'notice-bar' ); ?></div>
                    </div>
                </div>
                <div class="nb-option-field-wrap  nb-display-ref nb-plain-text-ref nb-news-ticker-ref nb-slider-ref">
                    <label><?php _e( 'Anchor Link Hover Color', 'notice-bar' ); ?></label>
                    <div class="nb-option-field">
                        <input type="text" name="nb_settings[display][link_hover_color]" class="nb-colorpicker"  value="<?php echo isset( $nb_settings['display']['link_hover_color'] ) ? esc_attr( $nb_settings['display']['link_hover_color'] ) : ''; ?>"/>
                        <div class="nb-option-note"><?php _e( 'Please choose the color of the link on hover. It will also work as hover color for News Ticker. Leave blank if you want to assign active theme\'s default color.', 'notice-bar' ); ?></div>
                    </div>
                </div>
                <div class="nb-option-field-wrap  nb-display-ref nb-news-ticker-ref">
                    <label><?php _e( 'Ticker Label Background', 'notice-bar' ); ?></label>
                    <div class="nb-option-field">
                        <input type="text" name="nb_settings[display][ticker_label_background]" class="nb-colorpicker"  value="<?php echo isset( $nb_settings['display']['ticker_label_background'] ) ? esc_attr( $nb_settings['display']['ticker_label_background'] ) : ''; ?>"/>
                        <div class="nb-option-note"><?php _e( 'Please choose the background color of the ticker label. Leave blank if you want to assign the same background color of whole notice bar.', 'notice-bar' ); ?></div>
                    </div>
                </div>
                <div class="nb-option-field-wrap">
                    <label><?php _e( 'Margin from Bottom or Top', 'notice-bar' ); ?></label>
                    <div class="nb-option-field">
                        <input type="number" name="nb_settings[display][top_bottom]" value="<?php echo isset( $nb_settings['display']['top_bottom'] ) ? esc_attr( $nb_settings['display']['top_bottom'] ) : ''; ?>"  min="0"/>
                        <div class="nb-option-note"><?php _e( 'Please enter the margin in px if you require to shift the notice bar from top or bottom. Default is 0px.', 'notice-bar' ); ?></div>
                    </div>
                </div>
                <div class="nb-option-field-wrap">
                    <label><?php _e( 'Disable For Mobile', 'notice-bar' ); ?></label>
                    <div class="nb-option-field">
                        <label class="nb-plain-label">
                            <?php $disable_for_mobile = (isset( $nb_settings['display']['disable_for_mobile'] )) ? esc_attr( $nb_settings['display']['disable_for_mobile'] ) : 0; ?>
                            <input type="checkbox" name="nb_settings[display][disable_for_mobile]" value="1" <?php checked( $disable_for_mobile, true ); ?>/>
                            <div class="nb-option-side-note"><?php _e( 'Check if you want to disable notice bar for mobile devices', 'notice-bar' ); ?></div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="nb-option-field-wrap">
                <label></label>
                <div class="nb-option-field">
                    <input type="submit" name="nb_settings_save_submit" value="<?php _e( 'Save Changes', 'notice-bar' ); ?>" class="button button-primary"/>
                    <?php $restore_nonce = wp_create_nonce( 'nb-restore-nonce' ); ?>
                    <a href="<?php echo admin_url( 'admin-post.php?action=nb_restore_default_action&_wpnonce=' . $restore_nonce ); ?>" onclick="return confirm('Are you sure you want to restore default settings?', 'notice-bar');">
                        <input type="button" value="<?php _e( 'Restore Default Settings', 'notice-bar' ); ?>" class="button button-primary"/>
                    </a>
                </div>
            </div>
        </div>
        <?php include(NB_BASE_PATH . '/inc/backend/sidebar.php'); ?>
    </form>
</div>