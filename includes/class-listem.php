<?php

namespace WP_RolesEM\Includes;

use WP_RolesEM\controller\RolesData;

class ListEm extends \WP_List_Table
{
    private $controller;
    //private $pagesList;

    function __construct()
    {
        parent::__construct(array(
            'singular' => 'List',
            'plural' => 'Lists',
            'ajax' => false
        ));
        $this->controller = new RolesData();
        $this->render();

        //load list of pages on array key = ID and value = post_title
        //$pagesList = get_pages();

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


    function get_columns()
    {
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'role' => 'Role',
            'pages' => 'Pages Names'
        );
        return $columns;
    }
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="lists[]" value="%s" />',
            $item["key"]
        );
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'role':
                return $item["name"];
                break;
            case 'pages':
                //$pagesList = implode(' , ', $item["show_pages"]);
                //echo $this->get_pages_title( $item["show_pages"] );

                //return implode(' , ', $item["show_pages"]);     // todo - here need to load pages name not ids
                return $this->get_pages_title( $item["show_pages"] );

                break;
            default:
                return isset($item->$column_name) ? $item->$column_name : '';
        }
    }

    function get_pages_title( $pagesList )
    {
        $pagesTitles = '';

        foreach( $pagesList as $pageId ) {
            $pagesTitles .= get_the_title($pageId) . ', ';
        }

        return $pagesTitles;
    }

    function get_bulk_actions()
    {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }

    function column_role($item)
    {
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&id=%s">Edit</a>', 'em-roles-settings', $item["key"]),
            'delete'    => sprintf('<a href="?page=%s&action=delete&id=%s">Delete</a>', 'em-roles', $item["key"]),
        );

        return sprintf('%1$s %2$s', $item["name"], $this->row_actions($actions));
    }

    function no_items()
    {
        _e('No access list avaliable.');
    }

    function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $this->controller->read();
        //pagination

        $this->process_bulk_action();

        $current_page = $this->get_pagenum();
        $total_items = count($this->items);
        // only ncessary because we have sample data
        $found_data = array_slice($this->items, (($current_page - 1) * $this->per_page), $this->per_page);

        $this->set_pagination_args(array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $this->per_page                     //WE have to determine how many items to show on a page
        ));
        $this->items = $found_data;
    }

    function process_bulk_action()
    {
        if (isset($_POST['_wpnonce']) && !empty($_POST['_wpnonce'])) {
            $nonce  = sanitize_text_field($_POST['_wpnonce']);
            $action = 'bulk-' . $this->_args['plural'];

            if (!wp_verify_nonce($nonce, $action))
                wp_die('Nope! Security check failed!');
        }

        $action = $this->current_action();
        
        //echo $action;
        
        switch ($action) {
            case 'delete':
                if ( isset( $_POST["lists"] ) ) {
                    foreach ( $_POST["lists"] as $value ) {
                        $this->controller->delete($value);
                    }

                    // redirect to refresh
                    $redirect_to = home_url() . "/wp-admin/admin.php?page=em-roles";
                    wp_safe_redirect( $redirect_to );

                }
                break;
            default:
                // do nothing or something else
                return;
                break;
        }
        return;
    }

    function render()
    {
?>
        <div class="list-container">
            <form action="" method="post">
                <!-- <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" /> -->
                <h2>All User Roles</h2> <a class="button" href="<?php echo admin_url('admin.php?page=em-roles-settings') ?>">Add New Role</a>
                <?php
                $this->prepare_items();
                $this->display();
                ?>
            </form>
        </div>
<?php

    }
}