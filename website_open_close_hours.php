<?php
/*
Plugin Name: Conditional redirect based on time
Description: Plugin is used to set opening and closing hours of website.
Plugin URI: https://wordpress.org/plugins/website-open-close-hours
Version: 1.5
Text Domain: woch
Author: Galaxy Weblinks
Author URI: https://www.galaxyweblinks.com/
License:GPL2
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/* Register activation hook. */
register_activation_hook( __FILE__, 'gwl_webopenclose_site_redirect_activate' );

/**
 * Runs only when the plugin is activated.
 */
function gwl_webopenclose_site_redirect_activate() 
{
    update_option( 'woch_version','1.3');
    add_option( 'woch_mon_web_status', 'full_open' );
    add_option( 'woch_tues_web_status', 'full_open' );
    add_option( 'woch_wed_web_status', 'full_open' );
    add_option( 'woch_thurs_web_status', 'full_open' );
    add_option( 'woch_fri_web_status', 'full_open' );
    add_option( 'woch_sat_web_status', 'full_open' );
    add_option( 'woch_sun_web_status', 'full_open' );
    set_transient( 'gwl-webopenclose-admin-notice-activation', true, 5 );
}

/**
 * Admin Notice on Activation.
 */
add_action( 'admin_notices', 'gwl_webopenclose_site_redirect_activation_notice' );

function gwl_webopenclose_site_redirect_activation_notice()
{
  if( get_transient( 'gwl-webopenclose-admin-notice-activation' ) )
  {
    ?>
    <div class="updated notice is-dismissible">
    <p><span><?php _e("To set opening and closing hours of website.","woch"); ?><a href="<?php echo admin_url('admin.php?page=openclosehours'); ?>"><?php _e("Click here","woch"); ?></a></span></p>
    </div>
    <?php
    delete_transient( 'gwl-webopenclose-admin-notice-activation' );
  }
}

/**
 * Register a custom menu page.
 */
function gwl_webopenclose_add_hours_option()
{
  add_menu_page( 
      __( 'Open & Close Hours', 'openclosehours' ),
      'Open & Close Hours',
      'manage_options',
      'openclosehours',
      'gwl_open_close_custom_code'
  );
}
add_action( 'admin_menu', 'gwl_webopenclose_add_hours_option' );

/**
 * Register jquery and style on initialization.
 */

function gwl_webopenclose_site_redirect_register_script() 
{
  $current_screen = get_current_screen();
  if (is_admin() && $current_screen->id === 'toplevel_page_openclosehours') 
  {
    wp_register_script( 'gwl_custom_jquery', plugins_url('/js/custom-jquery.js', __FILE__), array('jquery'), '1.0' );
    wp_register_script( 'gwl_timepicker_jquery', plugins_url('/js/jquery.timepicker.min.js', __FILE__), array('jquery'), '1.0' );
    wp_register_style ( 'gwl_new_style', plugins_url('/css/new-style.css', __FILE__), false, '1.0', 'all' );
    wp_register_style ( 'gwl_timepicker_style', plugins_url('/css/jquery.timepicker.min.css', __FILE__), false, '1.0', 'all' );
    wp_enqueue_script ( 'gwl_custom_jquery' );
    wp_enqueue_script ( 'gwl_timepicker_jquery' );
    wp_enqueue_style  ( 'gwl_new_style' );
    wp_enqueue_style  ( 'gwl_timepicker_style' );
  }
}
add_action('admin_enqueue_scripts', 'gwl_webopenclose_site_redirect_register_script');

/**
 * Get WordPress Time Zone Setting
 */
function gwl_webopenclose_getWpTimeZone() 
{
  $timezone_string = get_option( 'timezone_string' );

  if ( ! empty( $timezone_string ) ) {
      return ( $timezone_string );
  }

  $offset  = get_option( 'gmt_offset' );
  $hours   = (int) $offset;
  $minutes = ( $offset - floor( $offset ) ) * 60;
  $offset  = sprintf( 'UTC%+03d:%02d', $hours, $minutes );
  
  return ( $offset );
}

/**
 * Display a backend custom menu page
 */
function gwl_open_close_custom_code()
{
  $mon_open_time    = get_option( 'woch_mon_open_time');
  $mon_close_time   = get_option( 'woch_mon_close_time');
  $mon_web_status   = get_option( 'woch_mon_web_status');

  $tues_open_time   = get_option( 'woch_tues_open_time');
  $tues_close_time  = get_option( 'woch_tues_close_time');
  $tues_web_status  = get_option( 'woch_tues_web_status');

  $wed_open_time    = get_option( 'woch_wed_open_time');
  $wed_close_time   = get_option( 'woch_wed_close_time');
  $wed_web_status   = get_option( 'woch_wed_web_status');

  $thurs_open_time  = get_option( 'woch_thurs_open_time');
  $thurs_close_time = get_option( 'woch_thurs_close_time');
  $thurs_web_status = get_option( 'woch_thurs_web_status');

  $fri_open_time    = get_option( 'woch_fri_open_time');
  $fri_close_time   = get_option( 'woch_fri_close_time');
  $fri_web_status   = get_option( 'woch_fri_web_status');

  $sat_open_time    = get_option( 'woch_sat_open_time');
  $sat_close_time   = get_option( 'woch_sat_close_time');
  $sat_web_status   = get_option( 'woch_sat_web_status');

  $sun_open_time    = get_option( 'woch_sun_open_time');
  $sun_close_time   = get_option( 'woch_sun_close_time');
  $sun_web_status   = get_option( 'woch_sun_web_status');

  $RefererValue     = get_option( 'woch_RefererValue');
  $RefererValue_page= get_option( 'woch_RefererValue_page');

  $redirect_type    = get_option( 'woch_redirect_type');
  $get_wp_timezone  = get_option( 'timezone_string');
?>
<div class="wrap">
<h1><?php _e("Open Close Hours Settings","woch"); ?></h1>
  <form method="post" id="ajx_opncloseform">
      <table>
          <tr>
            <div class="website_error_message error"><?php _e("Please enter correct information","woch"); ?></div>
          </tr>
          <tr>
              <th></th>
              <th><strong><?php _e("Select an option","woch"); ?></strong></th>
              <th><strong><?php _e("Opening Hours","woch"); ?></strong></th>
              <th><strong><?php _e("Closing Hours","woch"); ?></strong></th>
          </tr>
          <tr>
              <td><strong><?php _e("Monday","woch"); ?></strong></td>
              <td><select name="woch_mon_web_status" class="woch_web_status">
                <option <?php if(!empty($mon_web_status) && ($mon_web_status == 'full_open')){ echo "selected"; } ?> value="full_open"><?php _e("Full Day Open","woch"); ?></option>
                <option <?php if(!empty($mon_web_status) && ($mon_web_status == 'full_close')){ echo "selected"; } ?> value="full_close"><?php _e("Full Day Close","woch"); ?></option>
                <option <?php if(!empty($mon_web_status) && ($mon_web_status == 'custom')){ echo "selected"; } ?> value="custom"><?php _e("Custom","woch"); ?></option>
              </select></td>
              <td><input type="text" class="timepicker readonly-cls" value="<?php if(!empty($mon_open_time)){ echo $mon_open_time; } ?>" name="mon_open_time" id="mon_open_time"></td>
              <td><input type="text" class="timepicker readonly-cls" value="<?php if(!empty($mon_close_time)){ echo $mon_close_time; } ?>" name="mon_close_time"></td>
              <td><span class="error_message" id="monday"></span></td>
          </tr>
          <tr>
              <td><strong><?php _e("Tuesday","woch"); ?></strong></td>
              <td><select name="woch_tues_web_status" class="woch_web_status">
                <option <?php if(!empty($tues_web_status) && ($tues_web_status == 'full_open')){ echo "selected"; } ?> value="full_open"><?php _e("Full Day Open","woch"); ?></option>
                <option <?php if(!empty($tues_web_status) && ($tues_web_status == 'full_close')){ echo "selected"; } ?> value="full_close"><?php _e("Full Day Close","woch"); ?></option>
                <option <?php if(!empty($tues_web_status) && ($tues_web_status == 'custom')){ echo "selected"; } ?> value="custom"><?php _e("Custom","woch"); ?></option>
              </select></td>
              <td><input type="text" class="timepicker readonly-cls" id="" value="<?php if(!empty($tues_open_time)){ echo $tues_open_time; } ?>" name="tues_open_time"></td>
              <td><input type="text" class="timepicker readonly-cls" id="" value="<?php if(!empty($tues_close_time)){ echo $tues_close_time; } ?>" name="tues_close_time"></td>
              <td><span class="error_message" id="tuesday"></span></td>
          </tr>
          <tr>
              <td><strong><?php _e("Wednesday","woch"); ?></strong></td>
              <td><select name="woch_wed_web_status" class="woch_web_status">
                <option <?php if(!empty($wed_web_status) && ($wed_web_status == 'full_open')){ echo "selected"; } ?> value="full_open"><?php _e("Full Day Open","woch"); ?></option>
                <option <?php if(!empty($wed_web_status) && ($wed_web_status == 'full_close')){ echo "selected"; } ?> value="full_close"><?php _e("Full Day Close","woch"); ?></option>
                <option <?php if(!empty($wed_web_status) && ($wed_web_status == 'custom')){ echo "selected"; } ?> value="custom"><?php _e("Custom","woch"); ?></option>
              </select></td>
              <td><input type="text" class="timepicker readonly-cls" id="" value="<?php if(!empty($wed_open_time)){ echo $wed_open_time; } ?>" name="wed_open_time"></td>
              <td><input type="text" class="timepicker readonly-cls" id="" value="<?php if(!empty($wed_close_time)){ echo $wed_close_time; } ?>" name="wed_close_time"></td>
              <td><span class="error_message" id="wednesday"></span></td>
          </tr>
          <tr>
              <td><strong><?php _e("Thursday","woch"); ?></strong></td>
              <td><select name="woch_thurs_web_status" class="woch_web_status">
                <option <?php if(!empty($thurs_web_status) && ($thurs_web_status == 'full_open')){ echo "selected"; } ?> value="full_open"><?php _e("Full Day Open","woch"); ?></option>
                <option <?php if(!empty($thurs_web_status) && ($thurs_web_status == 'full_close')){ echo "selected"; } ?> value="full_close"><?php _e("Full Day Close","woch"); ?></option>
                <option <?php if(!empty($thurs_web_status) && ($thurs_web_status == 'custom')){ echo "selected"; } ?> value="custom"><?php _e("Custom","woch"); ?></option>
              </select></td>
              <td><input type="text" class="timepicker readonly-cls" id="" value="<?php if(!empty($thurs_open_time)){ echo $thurs_open_time; } ?>" name="thurs_open_time"></td>
              <td><input type="text" class="timepicker readonly-cls" id="" value="<?php if(!empty($thurs_close_time)){ echo $thurs_close_time; } ?>" name="thurs_close_time"></td>
              <td><span class="error_message" id="thursday"></span></td>
          </tr>
          <tr>
              <td><strong><?php _e("Friday","woch"); ?></strong></td>
              <td><select name="woch_fri_web_status" class="woch_web_status">
                <option <?php if(!empty($fri_web_status) && ($fri_web_status == 'full_open')){ echo "selected"; } ?> value="full_open"><?php _e("Full Day Open","woch"); ?></option>
                <option <?php if(!empty($fri_web_status) && ($fri_web_status == 'full_close')){ echo "selected"; } ?> value="full_close"><?php _e("Full Day Close","woch"); ?></option>
                <option <?php if(!empty($fri_web_status) && ($fri_web_status == 'custom')){ echo "selected"; } ?> value="custom"><?php _e("Custom","woch"); ?></option>
              </select></td>
              <td><input type="text" class="timepicker readonly-cls" id="" value="<?php if(!empty($fri_open_time)){ echo $fri_open_time; } ?>" name="fri_open_time"></td>
              <td><input type="text" class="timepicker readonly-cls" id="" value="<?php if(!empty($fri_close_time)){ echo $fri_close_time; } ?>" name="fri_close_time"></td>
              <td><span class="error_message" id="friday"></span></td>
          </tr>
          <tr>
              <td><strong><?php _e("Saturday","woch"); ?></strong></td>
              <td><select name="woch_sat_web_status" class="woch_web_status">
                <option <?php if(!empty($sat_web_status) && ($sat_web_status == 'full_open')){ echo "selected"; } ?> value="full_open"><?php _e("Full Day Open","woch"); ?></option>
                <option <?php if(!empty($sat_web_status) && ($sat_web_status == 'full_close')){ echo "selected"; } ?> value="full_close"><?php _e("Full Day Close","woch"); ?></option>
                <option <?php if(!empty($sat_web_status) && ($sat_web_status == 'custom')){ echo "selected"; } ?> value="custom"><?php _e("Custom","woch"); ?></option>
              </select></td>
              <td><input type="text" class="timepicker readonly-cls" id="" value="<?php if(!empty($sat_open_time)){ echo $sat_open_time; } ?>" name="sat_open_time"></td>
              <td><input type="text" class="timepicker readonly-cls" id="" value="<?php if(!empty($sat_close_time)){ echo $sat_close_time; } ?>" name="sat_close_time"></td>
              <td><span class="error_message" id="saturday"></span></td>
          </tr>
          <tr>
              <td><strong><?php _e("Sunday","woch"); ?></strong></td>
              <td><select name="woch_sun_web_status" class="woch_web_status">
                <option <?php if(!empty($sun_web_status) && ($sun_web_status == 'full_open')){ echo "selected"; } ?> value="full_open"><?php _e("Full Day Open","woch"); ?></option>
                <option <?php if(!empty($sun_web_status) && ($sun_web_status == 'full_close')){ echo "selected"; } ?> value="full_close"><?php _e("Full Day Close","woch"); ?></option>
                <option <?php if(!empty($sun_web_status) && ($sun_web_status == 'custom')){ echo "selected"; } ?> value="custom"><?php _e("Custom","woch"); ?></option>
              </select></td>
              <td><input type="text" class="timepicker readonly-cls" id="" value="<?php if(!empty($sun_open_time)){ echo $sun_open_time; } ?>" name="sun_open_time"></td>
              <td><input type="text" class="timepicker readonly-cls" id="" value="<?php if(!empty($sun_close_time)){ echo $sun_close_time; } ?>" name="sun_close_time"></td>
              <td><span class="error_message" id="sunday"></span></td>
          </tr>          
          <tr>
              <td><strong><?php _e("Select Redirection","woch"); ?></strong></td>              
              <td colspan="3" >
                <?php
                  $redirect_type = get_option('woch_redirect_type');
                  if(!empty($redirect_type))
                  {
                    if ($redirect_type=='custom_url') 
                    {
                      $page_redirect_type = 'custom_url';
                    }
                    else
                    {
                      $page_redirect_type = 'wp_page';
                    }
                  }
                  else
                  {
                    $page_redirect_type='';
                  }
                ?>
                <ul id="redirection_options">
                    <li <?php if($page_redirect_type=='custom_url'){ ?> class="active" <?php } ?> for="custom_url"><?php _e("URL","woch"); ?></li>
                    <li <?php if($page_redirect_type=='wp_page'){ ?> class="active" <?php } ?> for="wp_page"><?php _e("Page","woch"); ?></li>
                    <input type="hidden" name="redirect_type" id="redirect_type" value="<?php if(!empty($redirect_type)) { echo $redirect_type; } ?>">
                </ul>
                <div class="redirect_option_Section <?php if($page_redirect_type=='custom_url'){ ?>active_section<?php } ?>" id="custom_url">
                  <input type="text" name="RefererValue" placeholder="For example: http://www.example.com" value="<?php echo get_option( 'woch_RefererValue');?>" id="RefererValue">
                </div>
                <div class="redirect_option_Section <?php if($page_redirect_type=='wp_page'){ ?>active_section<?php } ?>" id="wp_page">
                  <?php $page_ids = get_all_page_ids(); ?>
                  <select name="RefererValue_page">
                    <option value=""><?php _e("Select page","woch"); ?></option>
                    <?php 
                    if(!empty($page_ids)) {
                    foreach($page_ids as $page_data){ ?>
                      <option <?php if(!empty($RefererValue_page) && ($RefererValue_page == $page_data)){ ?> selected="selected" <?php } ?> value="<?php if(!empty($page_data)){ echo $page_data; } ?>"><?php echo get_the_title($page_data); ?></option>
                    <?php } } ?>
                  </select>
                </div>
              </td>
              <td><span id="redirection_required"></span><span id="same_url_msg"></span><span id="invalid_url_msg"></span></td>
          </tr>
          <tr class="time_zone_wrapper">
              <td><strong><?php _e("Timezone","woch"); ?></strong></td>
              <td colspan="3"><?php _e("Please update timezone from","woch"); ?> <a href="<?php echo admin_url('options-general.php');?>"><?php _e("Here","woch"); ?></a> <br/>
                <span class="current_timezone"><?php _e("Current Timezone:","woch"); ?> <?php if(!empty(gwl_webopenclose_getWpTimeZone())){ echo gwl_webopenclose_getWpTimeZone(); } ?> | <?php _e("Current Time:","woch"); ?> <?php echo current_time("D d M Y, H:i (h:i a)"); ?></span>
              </td>
          </tr>
          <tr>
              <td><input type="button" name="save_time" value="Save Time" class="btn-open-close" id="savetime"></td>
              <td colspan="2"><span id="updated"></span></td>
          </tr>
      </table>
  </form>
</div>  
<?php
}

/**
 * Saved time intervals in database
 */

function gwl_saveopen_close_timehours()
{
    $params = array();
    parse_str($_POST['data'], $params);
    $anArray = array();
    if(isset($_POST['data'])) 
    { 
        $mon_open_time      = $params['mon_open_time'];
        $mon_open_time_c    = strtotime(date('H:i', strtotime($mon_open_time)));
        $mon_close_time     = $params['mon_close_time'];
        $mon_close_time_c   = strtotime(date('H:i', strtotime($mon_close_time)));

        $tues_open_time     = $params['tues_open_time'];
        $tues_open_time_c   = strtotime(date('H:i', strtotime($tues_open_time)));
        $tues_close_time    = $params['tues_close_time'];
        $tues_close_time_c  = strtotime(date('H:i', strtotime($tues_close_time)));

        $wed_open_time      = $params['wed_open_time'];
        $wed_open_time_c    = strtotime(date('H:i', strtotime($wed_open_time)));
        $wed_close_time     = $params['wed_close_time'];
        $wed_close_time_c   = strtotime(date('H:i', strtotime($wed_close_time)));

        $thurs_open_time    = $params['thurs_open_time'];
        $thurs_open_time_c  = strtotime(date('H:i', strtotime($thurs_open_time)));
        $thurs_close_time   = $params['thurs_close_time'];
        $thurs_close_time_c = strtotime(date('H:i', strtotime($thurs_close_time)));

        $fri_open_time      = $params['fri_open_time'];
        $fri_open_time_c    = strtotime(date('H:i', strtotime($fri_open_time)));
        $fri_close_time     = $params['fri_close_time'];
        $fri_close_time_c   = strtotime(date('H:i', strtotime($fri_close_time)));

        $sat_open_time      = $params['sat_open_time'];
        $sat_open_time_c    = strtotime(date('H:i', strtotime($sat_open_time)));      
        $sat_close_time     = $params['sat_close_time'];
        $sat_close_time_c   = strtotime(date('H:i', strtotime($sat_close_time)));

        $sun_open_time      = $params['sun_open_time'];
        $sun_open_time_c    = strtotime(date('H:i', strtotime($sun_open_time)));
        $sun_close_time     = $params['sun_close_time'];
        $sun_close_time_c   = strtotime(date('H:i', strtotime($sun_close_time)));
        $redirect_type      = $params['redirect_type'];
        
        
        if ($redirect_type == 'custom_url') 
        {
          $RefererValue_page = '';
          $RefererValue      = $params['RefererValue'];
        }
        else
        {
          $RefererValue      = '';
          $RefererValue_page = $params['RefererValue_page'];
        }
  
        if(!empty($redirect_type) && (!empty($RefererValue) || !empty($RefererValue_page))) 
        {
          if($redirect_type == 'custom_url') 
          {
            $url_parse = wp_parse_url($RefererValue);
            $url_host  = $url_parse['host'];
            $siteURL   = wp_parse_url(get_site_url());
            $site_host = $siteURL['host'];

            if($url_host == $site_host) 
            {
              $val = $anArray['same_url'] = 'false';
              array_push($anArray,$val);
            }
            elseif(!wp_http_validate_url($RefererValue)) 
            {
              $val = $anArray['invalid_url'] = 'false';
              array_push($anArray,$val);
            }
            else
            {
              update_option( 'woch_redirect_type', $redirect_type ); 
              update_option( 'woch_RefererValue', $RefererValue ); 
              update_option( 'woch_RefererValue_page', $RefererValue_page );
            }
          }
          else
          {
            update_option( 'woch_redirect_type', $redirect_type ); 
            update_option( 'woch_RefererValue', $RefererValue ); 
            update_option( 'woch_RefererValue_page', $RefererValue_page );
          } 
        }
        else
        {
          $val = $anArray['red_type']= 'false';
          array_push($anArray,$val);
        }

        if(!empty($params['woch_mon_web_status']) && ($params['woch_mon_web_status'] == 'custom')) 
        {
          if(!empty($mon_open_time_c) && !empty($mon_close_time_c) && ($mon_open_time_c < $mon_close_time_c)) 
          {
            update_option( 'woch_mon_open_time', $mon_open_time );
            update_option( 'woch_mon_close_time', $mon_close_time );
          }
          else
          {
            $val = $anArray['mon']= 'false';
            array_push($anArray,$val);
          }
        }
        if(!empty($params['woch_tues_web_status']) && ($params['woch_tues_web_status'] == 'custom')) 
        {
          if(!empty($tues_open_time_c) && !empty($tues_close_time_c) && ($tues_open_time_c < $tues_close_time_c))
          {
            update_option( 'woch_tues_open_time', $tues_open_time );
            update_option( 'woch_tues_close_time', $tues_close_time );
          }
          else
          {
            $val1 = $anArray['tue']= 'false';
            array_push($anArray,$val1);
          }
        }
        if(!empty($params['woch_wed_web_status']) && ($params['woch_wed_web_status'] == 'custom')) 
        {
          if(!empty($wed_open_time_c) && !empty($wed_close_time_c) && ($wed_open_time_c < $wed_close_time_c)) 
          {
            update_option( 'woch_wed_open_time', $wed_open_time );
            update_option( 'woch_wed_close_time', $wed_close_time );
          }
          else
          {
            $val2 = $anArray['wed']= 'false';
            array_push($anArray,$val2);
          }
        }
        if(!empty($params['woch_thurs_web_status']) && ($params['woch_thurs_web_status'] == 'custom')) 
        {
          if(!empty($thurs_open_time_c) && !empty($thurs_close_time_c) && ($thurs_open_time_c< $thurs_close_time_c))
          {
            update_option( 'woch_thurs_open_time', $thurs_open_time );
            update_option( 'woch_thurs_close_time', $thurs_close_time );
          }
          else
          {
            $val3 =  $anArray['thus']= 'false';
            array_push($anArray,$val3);
          }
        }
        if(!empty($params['woch_fri_web_status']) && ($params['woch_fri_web_status'] == 'custom')) 
        {
          if(!empty($fri_open_time_c) && !empty($fri_close_time_c) && ($fri_open_time_c < $fri_close_time_c))
          {
            update_option( 'woch_fri_open_time', $fri_open_time );
            update_option( 'woch_fri_close_time', $fri_close_time );
          }
          else
          {
            $val4 =  $anArray['fri']= 'false';
            array_push($anArray,$val4);
          }
        }
        if(!empty($params['woch_sat_web_status']) && ($params['woch_sat_web_status'] == 'custom')) 
        {
          if(!empty($sat_open_time_c) && !empty($sat_close_time_c) && ($sat_open_time_c < $sat_close_time_c))
          {
            update_option( 'woch_sat_open_time', $sat_open_time );
            update_option( 'woch_sat_close_time', $sat_close_time );
          }
          else
          {
            $val5 =  $anArray['sat']= 'false';
            array_push($anArray,$val5);
          }
        }
        if(!empty($params['woch_sun_web_status']) && ($params['woch_sun_web_status'] == 'custom')) 
        {
          if(!empty($sun_open_time_c) && !empty($sun_close_time_c) && ($sun_open_time_c < $sun_close_time_c)) 
          {
            update_option( 'woch_sun_open_time', $sun_open_time );
            update_option( 'woch_sun_close_time', $sun_close_time );
          }
          else
          {
            $val6 = $anArray['sun']= 'false';
            array_push($anArray,$val6);
          }
        }  
        
        update_option( 'woch_mon_web_status', $params['woch_mon_web_status'] );

        update_option( 'woch_tues_web_status', $params['woch_tues_web_status'] );

        update_option( 'woch_wed_web_status', $params['woch_wed_web_status'] );

        update_option( 'woch_thurs_web_status', $params['woch_thurs_web_status'] );

        update_option( 'woch_fri_web_status', $params['woch_fri_web_status'] );

        update_option( 'woch_sat_web_status', $params['woch_sat_web_status'] );

        update_option( 'woch_sun_web_status', $params['woch_sun_web_status'] );
    }
    wp_send_json( $anArray );
}
add_action( 'wp_ajax_gwl_saveopen_close_timehours', 'gwl_saveopen_close_timehours' );            /**** AJAX for logged in    ****/
add_action( 'wp_ajax_nopriv_gwl_saveopen_close_timehours', 'gwl_saveopen_close_timehours' );     /**** AJAX for non-logged in    ****/

function gwl_webopenclose_site_redirect()
{
   global $wp;
   if(is_user_logged_in() || is_admin()) 
   {
     /**** LoggedIn user is excluded from redirection  ****/
     return false;
   }
   /** Get RefererValue from database. **/
   
  $RefererDomainValue = get_option( 'woch_RefererValue');
  $RefererDomainValue_page = get_option( 'woch_RefererValue_page'); 
  if(!empty($RefererDomainValue) || !empty($RefererDomainValue_page))
  {
    /** Get current time & day. **/

    $today = current_time( "Y-m-d H:i:s");
    $day   = current_time('l');
    $today = strtotime($today);

    /** According to day timeinterval is fetched from database. **/
    switch ($day) 
    {
        case "Monday":
            $mon_open_time    = get_option('woch_mon_open_time');
            $mon_close_time   = get_option('woch_mon_close_time');
            $today_web_status = get_option('woch_mon_web_status');
            $today_db_open    = strtotime($mon_open_time, current_time('timestamp'));
            $today_db_close   = strtotime($mon_close_time, current_time('timestamp'));
            break;
        case "Tuesday":
            $tues_open_time   = get_option('woch_tues_open_time');
            $tues_close_time  = get_option('woch_tues_close_time');
            $today_web_status = get_option('woch_tues_web_status');
            $today_db_open    = strtotime($tues_open_time, current_time('timestamp'));
            $today_db_close   = strtotime($tues_close_time, current_time('timestamp'));
            break;
        case "Wednesday":
            $wed_open_time    = get_option('woch_wed_open_time');
            $wed_close_time   = get_option('woch_wed_close_time');
            $today_web_status = get_option('woch_wed_web_status');
            $today_db_open    = strtotime($wed_open_time, current_time('timestamp'));
            $today_db_close   = strtotime($wed_close_time, current_time('timestamp'));
            break;
        case "Thursday":
            $thurs_open_time  = get_option('woch_thurs_open_time');
            $thurs_close_time = get_option('woch_thurs_close_time');
            $today_web_status = get_option('woch_thurs_web_status');
            $today_db_open    = strtotime($thurs_open_time, current_time('timestamp'));
            $today_db_close   = strtotime($thurs_close_time, current_time('timestamp'));
            break;
        case "Friday":
            $fri_open_time    = get_option('woch_fri_open_time');
            $fri_close_time   = get_option('woch_fri_close_time');
            $today_web_status = get_option('woch_fri_web_status');
            $today_db_open    = strtotime($fri_open_time, current_time('timestamp'));
            $today_db_close   = strtotime($fri_close_time, current_time('timestamp'));
            break;
        case "Saturday":
            $sat_open_time    = get_option('woch_sat_open_time');
            $sat_close_time   = get_option('woch_sat_close_time');
            $today_web_status = get_option('woch_sat_web_status');
            $today_db_open    = strtotime($sat_open_time, current_time('timestamp'));
            $today_db_close   = strtotime($sat_close_time, current_time('timestamp'));
            break;    
        default:
            $sun_open_time    = get_option('woch_sun_open_time');
            $sun_close_time   = get_option('woch_sun_close_time');
            $today_web_status = get_option('woch_sun_web_status');
            $today_db_open    = strtotime($sun_open_time, current_time('timestamp'));
            $today_db_close   = strtotime($sun_close_time, current_time('timestamp'));
    }
    
    if(!empty($today_web_status) && ($today_web_status == 'full_open')) 
    {
      /**** website open ****/
      return false;
    }
    elseif(!empty($today_web_status) && ($today_web_status == 'full_close')) 
    {
      /**** website close  ****/
      $redirect_type = get_option('woch_redirect_type');
      if(!empty($redirect_type) && $redirect_type == 'custom_url') 
      {
        wp_redirect($RefererDomainValue);
        exit;
      }
      else 
      {
        $page = get_page_by_path($wp->request);
        $current_page_id = $page->ID;
        $current_url =get_permalink($current_page_id);

        if(!empty($RefererDomainValue_page) && ($current_page_id != $RefererDomainValue_page) && ($current_url != wp_login_url()))
        {
          $redirection_url = get_permalink($RefererDomainValue_page);
          wp_redirect( $redirection_url);
          exit;
        }
      }
    }
    else
    {
      if((!empty($today_db_open) && ($today_db_open <= $today)) && (!empty($today_db_close) && ($today_db_close >= $today))) 
      {
        /**** website open  ****/  
      }
      else
      {
        /**** website close  ****/
        $redirect_type = get_option('woch_redirect_type');
        if(!empty($redirect_type) && $redirect_type == 'custom_url') 
        {
          wp_redirect($RefererDomainValue);
          exit;
        }
        else 
        {
          $page = get_page_by_path($wp->request);
          $current_page_id = $page->ID;
          $current_url =get_permalink($current_page_id);

          if(!empty($RefererDomainValue_page) && ($current_page_id != $RefererDomainValue_page) && ($current_url != wp_login_url()))
          {
            $redirection_url = get_permalink($RefererDomainValue_page);
            wp_redirect( $redirection_url);
            exit;
          }
        }
      }
    }
  }   
}
add_action( 'wp', 'gwl_webopenclose_site_redirect' );