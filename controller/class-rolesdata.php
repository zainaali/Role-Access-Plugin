<?php

namespace WP_RolesEM\controller;

use stdClass;
use WP_RolesEM\Includes\Common;

class RolesData
{
    use Common;
    private $db;
    private $table;
    function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->table = $this->db->prefix . $this->table_name();
    }
    function create(array $array)
    {
        add_role(
            $array["name"],
            $array["name"],
            [
                'read'         => true,            // todo - change to false this way the roles creaded not allow to read pages in the back end.
                'show_pages' => $array["pages"]
            ]
        );
        return true;
    }
    function read( String $role = '' )
    {
        global $wp_roles;
        $roles = $wp_roles->roles;
        if ($role != '') {
            $p = new stdClass();
            if (isset($roles[$role])) {
                $p->key = $role;
                $p->name = $roles[$role]["name"];
                $p->show_pages = isset($roles[$role]["capabilities"]["show_pages"]) ? $roles[$role]["capabilities"]["show_pages"] : [];
            }
            return $p;
        } else {
            $ar = [];
            foreach ($roles as $key => $value) {
                $o["key"] = $key;
                $o["name"] = $value["name"];
                $o["show_pages"] =  isset($value["capabilities"]["show_pages"]) ? $value["capabilities"]["show_pages"] : [];
                array_push($ar, $o);
            }
            return $ar;
        }
    }

    function update( array $array )
    {
        $role = get_role($array["id"]);
        $role->add_cap("show_pages", $array["pages"]);
        return true;
    }

    function delete( String $role )
    {
        remove_role($role);
        return true;
    }
}
