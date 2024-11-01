<?php
/*
Plugin Name: WP Comsoon
Plugin URI: http://www.postkartengeschichten.de/wp-comsoon
Description: Displays scheduled posts in a widget in the sidebar
Author: Marc Migge
Version: 1.2.1
Author URI: http://www.marcmigge.de

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation Version 2.0

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

*/


Class Comsoon_Widget Extends WP_Widget {


         function Comsoon_Widget() {

                $comsoon_locale = get_locale();

                $comsoon_domain = 'wp-comsoon';

                if (function_exists('load_plugin_textdomain'))
                $comsoon_mofile = WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/lang/'.$comsoon_domain.'-'.$comsoon_locale.'.mo';
                load_textdomain($comsoon_domain, $comsoon_mofile);

                $comsoon_widget_ops = array('classname' => 'comsoon_widget', 'description' => __('Displays scheduled posts in the sidebar','wp-comsoon'));
                $this->WP_Widget ( 'comsoon_widget', 'Coming soon', $comsoon_widget_ops);
         }



         function widget($args, $instance) {

                extract($args);

                echo $before_widget;

                $title = empty($instance['title']) ? 'Coming soon' : apply_filters('widget_title', $instance['title']);
                if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };

                $comsoon_nopostmsg = strip_tags($instance['comsoon_nopostmsg']);
                $comsoon_showdate = $instance['comsoon_showdate'] ? '1' : '0';
                $comsoon_sortorder = $instance['comsoon_sortorder'] ? '1' : '0';
                $comsoon_hidepower = $instance['comsoon_hidepower'] ? '1' : '0';
                $comsoon_showtime = $insctance['comsoon_showtime'] ? '1' : '0';
                $comsoon_hidenopost = $instance['comsoon_hidenopost'] ? '1' : '0';

                if ( empty($comsoon_nopostmsg) ) $comsoon_nopostmsg = __('No scheduled posts', 'wp-comsoon');

                if ( !$comsoon_postnrs = (int) $instance['comsoon_postnrs'] )
                        $comsoon_postnrs = 5;
                elseif ( $comsoon_postnrs < 1 )
                        $comsoon_postnrs = 1;
                elseif ( $comsoon_postnrs >30 )
                        $comsoon_postnrs = 30;

                show_comsoon( $instance );

                echo $after_widget;
        }


        function update($new_instance, $old_instance) {
                $instance = $old_instance;
                $new_instance = wp_parse_args( (array) $new_instance, array(
                        'title' => '',
                        'comsoon_showdate' => 0,
                        'comsoon_sortorder' => 0,
                        'comsoon_postnrs' => 5,
                        'comsoon_nopostmsg' => '',
                        'comsoon_hidepower' => 0,
                        'comsoon_showtime' => 0,
                        'comsoon_hidenopost' => 0 ) );
                $instance['title'] = strip_tags($new_instance['title']);
                $instance['comsoon_nopostmsg'] = strip_tags($new_instance['comsoon_nopostmsg']);
                $instance['comsoon_showdate'] = $new_instance['comsoon_showdate'] ? 1 : 0;
                $instance['comsoon_sortorder'] = $new_instance['comsoon_sortorder'] ? 1 : 0;
                $instance['comsoon_postnrs'] = (int) $new_instance['comsoon_postnrs'];
                $instance['comsoon_hidepower'] = $new_instance['comsoon_hidepower'] ? 1 : 0;
                $instance['comsoon_showtime'] = $new_instance['comsoon_showtime'] ? 1 : 0;
                $instance['comsoon_hidenopost'] = $new_instance['comsoon_hidenopost'] ? 1 : 0;

                return $instance;
        }


        function form($instance) {
                $instance = wp_parse_args( (array) $instance, array(
                        'title' => '',
                        'comsoon_showdate' => 0,
                        'comsoon_sortorder' => 0,
                        'comsoon_postnrs' => 5,
                        'comsoon_nopostmsg' => '',
                        'comsoon_hidepower' => 0,
                        'comsoon_showtime' => 0,
                        'comsoon_hidenopost' => 0 ) );
                $title = strip_tags($instance['title']);
                $comsoon_nopostmsg = strip_tags($instance['comsoon_nopostmsg']);
                $comsoon_showdate = $instance['comsoon_showdate'] ? 'checked="checked"' : '';
                $comsoon_sortorder = $instance['comsoon_sortorder'] ? 'checked="checked"' : '';
                $comsoon_hidepower = $instance['comsoon_hidepower'] ? 'checked="checked"' : '';
                $comsoon_showtime = $instance['comsoon_showtime'] ? ' checked="checked"' : '';
                $comsoon_hidenopost = $instance['comsoon_hidenopost'] ? 'checked="checked"' : '';

                if ( empty($comsoon_nopostmsg) ) $comsoon_nopostmsg = __('No scheduled posts','wp-comsoon');

                if ( !$comsoon_postnrs = (int) $instance['comsoon_postnrs'] )
                        $comsoon_postnrs = 5;
                elseif ( $comsoon_postnrs < 1 )
                        $comsoon_postnrs = 5;
                elseif ( $comsoon_postnrs > 30 )
                        $comsoon_postnrs = 30;

?>
                <p>
                        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp-comsoon'); ?></label>
                        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
                </p>

                <p>
                        <label for="<?php echo $this->get_field_id('comsoon_nopostmsg'); ?>"><?php _e('No scheduled posts text:', 'wp-comsoon');?></label>
                        <input class="widefat" id="<?php echo $this->get_field_id('comsoon_nopostmsg'); ?>" name="<?php echo $this->get_field_name('comsoon_nopostmsg'); ?>" type="text" value="<?php echo $comsoon_nopostmsg; ?>"/>
                </p>

                <p>
                        <input id="<?php echo $this->get_field_id('comsoon_postnrs'); ?>" name="<?php echo $this->get_field_name('comsoon_postnrs'); ?>" type="text" value="<?php echo $comsoon_postnrs; ?>" size="2" />
                        <label for="<?php echo $this->get_field_id('comsoon_postnrs'); ?>"><?php _e('Posts are displayed', 'wp-comsoon');?></label>
                        <small>(1-30)</small>
                </p>

                <p>
                        <input class="checkbox" type="checkbox" <?php echo $comsoon_showdate; ?> id="<?php echo $this->get_field_id( 'comsoon_showdate' ); ?>" name="<?php echo $this->get_field_name( 'comsoon_showdate' ); ?>" />
                        <label for="<?php echo $this->get_field_id( 'comsoon_showdate' ); ?>"><?php _e('Show Date ?', 'wp-comsoon'); ?></label>
                </p>

                <p>
                        <input class="checkbox" type="checkbox" <?php echo $comsoon_showtime; ?> id="<?php echo $this->get_field_id( 'comsoon_showtime' ); ?>" name="<?php echo $this->get_field_name( 'comsoon_showtime' ); ?>" />
                        <label for="<?php echo $this->get_field_id( 'comsoon_showtime' ); ?>"><?php _e('Show Time ?', 'wp-comsoon'); ?></label>
                </p>

                <p>
                        <input class="checkbox" type="checkbox" <?php echo $comsoon_hidenopost; ?> id="<?php echo $this->get_field_id( 'comsoon_hidenopost' ); ?>" name="<?php echo $this->get_field_name( 'comsoon_hidenopost' ); ?>" />
                        <label for="<?php echo $this->get_field_id( 'comsoon_hidenopost' ); ?>"><?php _e('Hide No Posts scheduled text ?', 'wp-comsoon'); ?></label>
                </p>

                <p>
                        <input class="checkbox" type="checkbox" <?php echo $comsoon_sortorder; ?> id="<?php echo $this->get_field_id( 'comsoon_sortorder' ); ?>" name="<?php echo $this->get_field_name( 'comsoon_sortorder' ); ?>" />
                        <label for="<?php echo $this->get_field_id( 'comsoon_sortorder' ); ?>"><?php _e('Sort posts descending ?', 'wp-comsoon'); ?></label>
                </p>

                <p>
                        <input class="checkbox" type="checkbox" <?php echo $comsoon_hidepower; ?> id="<?php echo $this->get_field_id( 'comsoon_hidepower' ); ?>" name="<?php echo $this->get_field_name( 'comsoon_hidepower' ); ?>" />
                        <label for="<?php echo $this->get_field_id( 'comsoon_hidepower' ); ?>"><?php _e('Hide "powered by WP Comsoon"', 'wp-comsoon'); ?></label>
                </p>


<?php

        }
}


add_action('widgets_init', create_function('', 'return register_widget("Comsoon_Widget");'));


function show_comsoon ( $args = array() ) {
        $default_args = array ( 'comsoon_postnrs' => 5, 'comsoon_showdate' => 0, 'comsoon_sortorder' => 0, 'comsoon_nopostmsg' => __('No scheduled posts','wp-comsoon'), 'comsoon_hidepower' => 0, 'comsoon_showtime' => 0, 'comsoon_hidenopost' => 0 );
        $args = wp_parse_args( $args, $default_args );
        extract( $args );

        $comsoon_showdate = (int) $comsoon_showdate;
        $comsoon_sortorder = (int) $comsoon_sortorder;
        $comsoon_postnrs = (int) $comsoon_postnrs;
        $comsoon_hidepower = (int) $comsoon_hidepower;
        $comsoon_showtime = (int) $comsoon_showtime;
        $comsoon_hidenopost = (int) $comsoon_hidenopost;
        $comsoon_postshow = (int) $comsoon_postshown;

        if ( empty($comsoon_nopostmsg) ) $comsoon_nopostmsg = __('No scheduled posts','wp-comsoon');

        $comsoon_today = strtotime(current_time('mysql'));

        echo '<div class="comsoon-list">';
        echo '<ul>';

        if ($comsoon_sortorder == 0)
                 $my_query = new WP_Query('post_status=future&orderby=date&order=ASC&posts_per_page='.$comsoon_postnrs);
           else
                 $my_query = new WP_Query('post_status=future&orderby=date&order=DESC&posts_per_page='.$comsoon_postnrs);

        $comsoon_postshown = 0;

        if ($my_query->have_posts()) {
                 while ($my_query->have_posts()) : $my_query->the_post(); $do_not_duplicate = $post->ID;
                         $comsoon_post_date = strtotime(get_the_time('Y-m-d H:i:s'));
                         if ($comsoon_post_date > $comsoon_today) {
                                 $comsoon_postshown = 1;
                                 echo '<li>';
                                 if ($comsoon_showdate == 1) {
                                         the_date();
                                 }
                                 if (($comsoon_showdate == 1) AND ($comsoon_showtime == 1)) {
                                         echo ' - ';
                                 }
                                 if ($comsoon_showtime == 1) {
                                         the_time('G:i');
                                 }
                                 if (($comsoon_showdate == 1) OR ($comsoon_showtime == 1)) {
                                         echo '<br/>';
                                 }
                                 the_title();
                                 echo '</li>';
                         } // end if
                  endwhile;
//                  }
         } // endif

         echo '</ul>';
         if (($comsoon_postshown == 0) AND ($comsoon_hidenopost == 0)) echo $comsoon_nopostmsg;
         if (!$comsoon_hidepower) echo '<div style="text-align:center;"><span style="font-size:xx-small;">powered by <a href="http://www.postkartengeschichten.de/wp-comsoon">WP Comsoon</a></span></div>';
         echo '</div>';

} // end function

?>