<?php
session_start();

class Base extends CI_Controller
{
    public function responseJson($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        return;
    }

    public function currentUser()
    {
        if (empty($_SESSION['user_id']) and empty($_SESSION['user_role'])) {
            return null;
        }

        $this->load->model('user_model');
        $user = $this->user_model->getByID($_SESSION['user_id']);

        return $user;
    }

    public function setSession($key, $value)
    {
        return $_SESSION[$key] = $value;
    }
}