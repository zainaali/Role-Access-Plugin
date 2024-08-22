<?php

namespace WP_RolesEM\Includes;

use WP_RolesEM\controller\RolesData;

class Settings2
{
    use Common;
    private $roles;
    function __construct()
    {
        $controller = new RolesData();
        $this->roles = $controller->read();
        $this->render();
    }

    function render()
    {
         $tamp_pass_member = get_option('tamp_pass_member');
		 $username_sub_member = get_option('username_sub_member');
		 $pass_sub_member = get_option('pass_sub_member');
?>
        <div>
            <h1>Settings</h1>
            <form action="<?php echo admin_url('admin-post.php') ?>" method="POST">
                <input type="text" name="action" value="save_setting" hidden>
                <div style="margin-bottom: 15px;">
                <label for="">Default Role when Add New User:*</label> <br/>
                    <select name="membersRole" id="">
                        <option value="0">Select default</option>
                        <?php foreach ($this->roles as $key => $r) : ?>
                            <option <?php if ( get_option( $this->get_member_default_role() ) == $r["key"]) echo 'selected' ?> value="<?php echo $r["key"] ?>"><?php echo $r["name"] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <br/>
                    <br/>
                    <label for="">Default Role for No Member Users:*</label> <br/>
                    <select name="role" id="">
                        <option value="0">Select default</option>
                        <?php foreach ($this->roles as $key => $r) : ?>
                            <option <?php if ( get_option( $this->get_default_role() ) == $r["key"]) echo 'selected' ?> value="<?php echo $r["key"] ?>"><?php echo $r["name"] ?></option>
                        <?php endforeach; ?>
                    </select>

                    <div class="user_set">
                        <h2>Member Settings</h2>
                        <label for="">Temporal Password for all memebers:</label> <br/>
                        <input type="text" name="tamp_pass_member" value="<?php echo esc_attr($tamp_pass_member); ?>">
                    </div>
					
					  <div class="user_set user_sub">
                        <h2>Guest User Settings</h2>
						<label for="">username of guest user:</label> <br/>
                        <input type="text" name="username_sub_member" value="<?php echo esc_attr($username_sub_member); ?>">
					</div>
						   <div class="">
                        <label for="">Password of guest user:</label> <br/>
                        <input type="text" name="pass_sub_member" value="<?php echo esc_attr($pass_sub_member); ?>">
                    </div>
                    
                </div>
                <div>
                    <button>Save</button>
                </div>
            </form>
            <!-- <hr>
            <h2>Shortcode</h2>
            <em>[rolesem-login]</em> -->
        </div>

<?php
    }
}
