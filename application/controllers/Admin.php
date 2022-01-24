<?php
require_once(APPPATH . "controllers/Base.php");

/**
 * 
 */
class Admin extends Base
{
    
    function __construct(argument)
    {
        # code...
    }

    public function dashboard()
    {
        echo 'admin/dashboard';
    }
}