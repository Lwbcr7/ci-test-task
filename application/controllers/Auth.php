<?php
require_once(APPPATH . "controllers/Base.php");

class Auth extends Base
{
    public function index()
    {
        header('Location: /auth/login');
        die();
    }

    public function registerPage()
    {
        // redirect if logged in
        $currentUser = $this->currentUser();
        if ($currentUser) {
            if ($currentUser['role'] == 'user') {
                header('Location: /user/products');
            } else {
                header('Location: /admin/dashboard');
            }
            die();
        }

        $this->load->view('layout/header');
        $this->load->view('auth/register');
        $this->load->view('layout/footer');
    }

    public function register()
    {
        $name       = $this->input->post('name');
        $email      = $this->input->post('email');
        $password   = $this->input->post('password');

        if (empty($name) || empty($email) || empty($password)) {
            return $this->responseJson([
                'code'      => -1,
                'message'   => 'Invalid parameters',
            ]);
        }

        if (!preg_match("/[a-zA-Z0-9_\-\.]+@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,8})/", $email)) {
            return $this->responseJson([
                'code'      => -2,
                'message'   => 'Invalid email',
            ]);
        }

        $this->load->model('user_model');
        // check user by email
        $existsUsers = $this->user_model->getByEmail($email);

        if (!is_null($existsUsers)) {
            return $this->responseJson([
                'code'      => -2,
                'message'   => 'Email had been used by other users',
            ]);
        }

        // success and redirect
        $this->user_model->store($name, $email, $password);
        $user = $this->user_model->getByEmail($email);

        $this->setSession('user_id', $user['id']);
        $this->setSession('user_role', $user['role']);

        return $this->responseJson([
            'code'      => 1,
            'message'   => '',
            'data' => [
                'redirect' => ($user['role'] == 'admin') ? '/admin/dashboard' : '/user/products',
            ],
        ]);
    }

    public function loginPage()
    {
        // redirect if logged in
        $currentUser = $this->currentUser();
        if ($currentUser) {
            if ($currentUser['role'] == 'user') {
                header('Location: /user/products');
            } else {
                header('Location: /admin/dashboard');
            }
            die();
        }

        $this->load->view('layout/header');
        $this->load->view('auth/login');
        $this->load->view('layout/footer');
    }

    public function login()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $this->load->model('user_model');

        $user = $this->user_model->getByEmail($email);

        if (is_null($user)) {
            return $this->responseJson([
                'code'      => -1,
                'message'   => 'Unable to find user by email:'.$email,
            ]);
        }

        if (md5($password) != $user['password']) {
            return $this->responseJson([
                'code'      => -2,
                'message'   => 'Incorrect password',
            ]);
        }

        $this->setSession('user_id', $user['id']);
        $this->setSession('user_role', $user['role']);

        return $this->responseJson([
            'code'      => 1,
            'message'   => '',
            'data' => [
                'redirect' => $user['role'] == 'admin' ? '/admin/dashboard' : '/user/products',
            ],
        ]);
    }

    public function logout()
    {
        session_destroy();

        header('Location: /auth/login');
        die();
    }

    public function emailVerify()
    {
        $currentUser = $this->currentUser();

        if (is_null($currentUser)) {
            return $this->responseJson([
                'code'      => -1,
                'message'   => 'There are currently no logged in user',
            ]);
        }

        if ($currentUser['email_is_verified'] == 1) {
            return $this->responseJson([
                'code'      => -2,
                'message'   => 'Email has been verified',
            ]);
        }

        // update user email_is_verified => 1
        $this->load->model('user_model');
        $this->user_model->setEmailIsVerified($currentUser['id']);

        return $this->responseJson(['code' => 1]);
    }
}
