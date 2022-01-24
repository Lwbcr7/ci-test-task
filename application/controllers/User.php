<?php
require_once(APPPATH . "controllers/Base.php");

/**
 * 
 */
class User extends Base
{
    public function productList()
    {
        $user = $this->currentUser();

        if (!$user) {
            header('Location: /auth/login');
            die();
        }

        if ($user['email_is_verified'] == 0) {
            // output email verify page
            $this->load->view('layout/header');
            $this->load->view('user/verify_email');
            $this->load->view('layout/footer');
            return;
        }

        echo 'product-list';
    }
}