<?php

namespace WP_RolesEM\Includes;

use WP_RolesEM\controller\RolesData;

class RolesEm
{
    use Common;
    private $version = '1.0.0';
    private $controller;
    function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'public_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'public_scripts']);
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_post_save_role', [$this, 'save_role']);
        add_action('admin_post_save_setting', [$this, 'save_setting']);
        //add_shortcode('rolesem-login', [$this, '_shortcode']);
        // add_action('wp_login_failed', [$this, 'my_front_end_login_fail']);
        add_action('template_redirect', [$this, 'test_run']);

        $this->controller = new RolesData();
    }

    function test_run()
    {
        if (is_user_logged_in() && !is_admin()) {
            global $current_user; //get the current user
            $role = current( $current_user->roles );
            $current_role = $this->controller->read(get_option($role));
            // echo $current_role->show_pages;
            if (isset($current_role->show_pages)) {
                if (!in_array(get_queried_object_id(), $current_role->show_pages)) {
                    wp_die("Not Allowed to access the page");
                }
            }
        } elseif (!is_admin()) {
            $current_role = $this->controller->read(get_option($this->get_default_role()));
            if (!in_array(get_queried_object_id(), $current_role->show_pages)) {
                wp_redirect( home_url('/access-denied/') ); exit;
            }
        }
    }

    function public_scripts()
    {
        wp_register_style('em-roles-css', REM2_ASSETS_URL . 'style.css', array(), $this->version, 'all');
        wp_register_script('em-roles-js', REM2_ASSETS_URL . 'script.js', ['jquery'], $this->version, true);

        // Localize the script with the nonce and AJAX URL
        wp_localize_script('em-roles-js', 'lmic_ajax_obj', array(
            'site_lmic_url' => site_url(),
            'ajax_url' => admin_url('admin-ajax.php'),
        ));

        wp_enqueue_style('em-roles-css');

        $pages = [];

        if (is_user_logged_in() && !is_admin()) {
            global $current_user; //get the current user
            $role = current( $current_user->roles );
            $current_role = $this->controller->read(get_option($role));
            // echo $current_role->show_pages;
            if (isset($current_role->show_pages)) $pages = $current_role->show_pages;
        } else {
            $pages = $this->controller->read(get_option($this->get_default_role()));
        }

        wp_localize_script('em-roles-js', 'arr', ["pages" => $pages]);

        wp_enqueue_script('em-roles-js');

    }




    function register_menu()
    {
        //dashicons-schedule
        //add_menu_page('User Roles', 'User Roles', 'manage_options', 'em-roles', [$this, 'dealer'], REM2_ASSETS_URL . '/images/management.png', null);
        add_menu_page('lmic-access', 'LMIC Access', 'manage_options', 'em-roles', [$this, 'dealer'], 'dashicons-schedule', null);
        //add_submenu_page('em-roles', 'Roles List', 'Roles List', 'manage_options', 'access-roles-list', [$this, 'dealer'], null);
        add_submenu_page('em-roles', 'Add Roles', 'Add Roles', 'manage_options', 'em-roles-settings', [$this, 'dealer'], null);
        add_submenu_page('em-roles', 'Settings Role', 'Settings', 'manage_options', 'em-roles-settings2', [$this, 'dealer'], null);
    }

    function dealer()
    {
        switch ($_GET["page"]) {
            case "em-roles":
                if (isset($_GET["action"]) && $_GET["action"] == "delete" && isset($_GET["id"])) {
                    $this->controller->delete($_GET["id"]);
                    wp_safe_redirect(wp_get_referer());
                }
                new ListEm();
                break;
            case "em-roles-settings":
                $s = new Settings();
                $s->render();
                break;
            case "em-roles-settings2":
                new Settings2();
                break;
        }
    }

    function save_role()
    {
        // wp_send_json($_POST);
        if (isset($_POST["id"])) {
            $this->controller->update($_POST);
        } else {
            $this->controller->create($_POST);
        }

        // redirect to LMIC Access page
        $redirect_to = home_url() . "/wp-admin/admin.php?page=em-roles";
        wp_safe_redirect( $redirect_to );
        
    }

    function save_setting()
    {
        if ( $_POST[ "role" ] == '0' ) wp_safe_redirect(wp_get_referer());
        if ( $_POST[ "memebersRole" ] == '0' ) wp_safe_redirect(wp_get_referer());

        update_option( $this->get_default_role(), $_POST[ "role" ] );
        wp_safe_redirect(wp_get_referer());

        update_option($this->get_member_default_role(), $_POST["membersRole"]);

        update_option('tamp_pass_member', $_POST["tamp_pass_member"]);
		update_option('username_sub_member', $_POST["username_sub_member"]);
		update_option('pass_sub_member', $_POST["pass_sub_member"]);
        wp_safe_redirect(wp_get_referer());

    }

    function _shortcode()
    {
        $redirect_login = get_home_url() . '/example.php';
        $redirect_logout = get_home_url();

        if (!is_user_logged_in()) :
            $args = array(
                'echo'           => true,
                // 'redirect'       => get_option('bet_match_page'),
                'label_log_in'   => __('Log in'),
                'form_id'        => 'seminar-login',
                'label_username' => __('User'),
                'label_password' => __('Password'),
                'label_remember' => __('Remember Me'),
            );
            // show errors
            if (isset($_GET['login']) == 'failed') {
                echo '<div style="margin-bottom:15px;"><strong>Error: </strong> Invalid information</div>';
            }
            return wp_login_form($args);
        else :
?>
            <div>
                <h2>Welcome <?php echo wp_get_current_user()->user_login ?></h2>
            </div>
<?php
        endif;
    }

    function my_front_end_login_fail($username)
    {
        $referrer = $_SERVER['HTTP_REFERER'];  // where did the post submission come from?
        // if there's a valid referrer, and it's not the default log-in screen
        if (!empty($referrer) && !strstr($referrer, 'wp-login') && !strstr($referrer, 'wp-admin')) {
            // wp_redirect($referrer . '?login=failed');  // let's append some information (login=failed) to the URL for the theme to use
            wp_safe_redirect(
                esc_url_raw(
                    add_query_arg('login', 'failed', $referrer)
                )
            );
            exit;
        }
    }
}
