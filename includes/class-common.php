<?php

namespace WP_RolesEM\Includes;

use WP_RolesEM\controller\RolesData;

trait Common
{
    function table_name()
    {
        return 'emroles';
    }

    function user_meta()
    {
        return 'user_role_em';
    }

    function get_default_role()
    {
        return 'rolesDefault_em_option';
    }

    // memeber default role
    function user_member_default_meta()
    {
        return 'user_member_default_role_em';
    }

    function get_member_default_role()
    {
        return 'rolesDefaultMember_em_option';
    }
    
}
