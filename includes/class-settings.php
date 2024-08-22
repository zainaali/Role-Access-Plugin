<?php

namespace WP_RolesEM\Includes;

use stdClass;
use WP_RolesEM\controller\RolesData;

class Settings
{

    private $pages;
    private $controller;
    private $data;

    function __construct()
    {
        $this->pages = $this->get_pages();
        // echo json_encode($this->pages);
        $this->controller = new RolesData();
        // echo json_encode($this->pages);
        // echo json_encode($this->get_data());
        // $this->data = $this->get_data();
        $this->data = $this->get_data();
        // echo json_encode($this->controller->read($_GET["id"]));
    }

    function get_data()
    {
        if (isset($_GET["id"])) {
            $data = $this->controller->read($_GET["id"]);
        } else {
            $data = new stdClass();
            $data->name = '';
            $data->show_pages = [];
        }
        return $data;
    }

    function render()
    {
?>
        <div class="role-settings-container">
            <form action="<?php echo admin_url('admin-post.php') ?>" method="POST">
                <input type="text" name="action" value="save_role" hidden>
                <?php if (isset($_GET["id"])) : ?>
                    <input type="text" name="id" value="<?php echo $_GET["id"] ?>" hidden>
                <?php endif; ?>
                <h1>User Roles</h1>
                <div>
                    <label for="">Role name</label>
                    <input type="text" name="name" value="<?php echo $this->data->name ?>">
                    <button type="submit">Save</button>
                </div>
                <div>
                    <input style="font-size: 16px;" type="checkbox" value="0" onClick="settingToggle(this)" />
                    <label for="">Select Pages</label><br>
                    <hr/>
                   <?php
                        $pages = $this->get_pages();
                        if ($pages) {
                            $processedPages = []; // Array to keep track of processed pages

                            foreach ($pages as $page) {
                                if ($page->post_parent == 0) {
                                    // Check if the page has not been processed already
                                    if (!in_array($page->ID, $processedPages)) {
                                        $processedPages[] = $page->ID; // Mark the page as processed
                                        ?>
                                        <input <?php if (in_array($page->ID, $this->data->show_pages)) echo 'checked' ?> type="checkbox" id="chck_<?php echo $page->ID ?>" name="pages[]" value="<?php echo $page->ID ?>"> 
                                        <label style="font-size: 16px;"><?php echo $page->post_title ?> </label><br/>
                                        <?php
                                    }

                                    $sub_pages = get_pages(array('child_of' => $page->ID, "post_status" => "publish", "post_type" => "page", "sort_column" => "menu_order"));  //Root Level
                                    foreach ($sub_pages as $sub_page) {
                                        // Check if the sub-page has not been processed already
                                        if (!in_array($sub_page->ID, $processedPages)) {
                                            $processedPages[] = $sub_page->ID; // Mark the sub-page as processed
                                            ?>
                                            <input <?php if (in_array($sub_page->ID, $this->data->show_pages)) echo 'checked' ?> type="checkbox" id="chck_<?php echo $sub_page->ID ?>" name="pages[]" value="<?php echo $sub_page->ID ?>"> 
                                            <label style="font-size: 16px;"><?php echo ' —— ' . $sub_page->post_title ?> </label><br/>
                                            <?php
                                        }

                                        $sub_pages2 = get_pages(array('child_of' => $sub_page->ID, "post_status" => "publish", "post_type" => "page", "sort_column" => "menu_order"));    //First Level
                                        if ($sub_pages2) {
                                            foreach ($sub_pages2 as $sub_page2) {
                                                // Check if the sub-page2 has not been processed already
                                                if (!in_array($sub_page2->ID, $processedPages)) {
                                                    $processedPages[] = $sub_page2->ID; // Mark the sub-page2 as processed
                                                    ?>
                                                    <input <?php if (in_array($sub_page2->ID, $this->data->show_pages)) echo 'checked' ?> type="checkbox" id="chck_<?php echo $sub_page2->ID ?>" name="pages[]" value="<?php echo $sub_page2->ID ?>"> 
                                                    <label style="font-size: 16px;"><?php echo ' —— —— ' . $sub_page2->post_title ?> </label><br/>
                                                    <?php
                                                }

                                                $sub_pages3 = get_pages(array('child_of' => $sub_page2->ID, "post_status" => "publish", "post_type" => "page", "sort_column" => "menu_order")); //Second Level
                                                if ($sub_pages3) {
                                                    foreach ($sub_pages3 as $sub_page3) {
                                                        // Check if the sub-page3 has not been processed already
                                                        if (!in_array($sub_page3->ID, $processedPages)) {
                                                            $processedPages[] = $sub_page3->ID; // Mark the sub-page3 as processed
                                                            ?>
                                                            <input <?php if (in_array($sub_page3->ID, $this->data->show_pages)) echo 'checked' ?> type="checkbox" id="chck_<?php echo $sub_page3->ID ?>" name="pages[]" value="<?php echo $sub_page3->ID ?>"> 
                                                            <label style="font-size: 16px;"><?php echo ' —— —— ' . $sub_page3->post_title ?> </label><br/>
                                                            <?php
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    ?>
                    
                </div>
                <div>
                    <button type="submit">Save</button>
                </div>
            </form>
        </div>
<?php
    }

    function get_pages()
    {
        $args = [
            "post_status" => "publish",
            "post_type" => "page",
            "sort_column" => "menu_order"
        ];

        return get_pages($args);
    }

}
