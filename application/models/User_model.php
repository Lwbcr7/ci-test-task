<?php

/**
 * 
 */
class User_model extends CI_model
{
    public $name;
    public $email;
    public $password;
    public $created_at;
    public $updated_at;
    public $role;
    public $email_is_verified;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getByID($id)
    {
        $query = $this->db->get_where('users', ['id' => $id]);

        return $query->row_array();
    }

    public function getByEmail($email)
    {
        $query = $this->db->get_where('users', array('email' => $email));

        return $query->row_array();
    }

    public function store($name, $email, $password, $role = 'user')
    {
        $this->name         = $name;
        $this->email        = $email;
        $this->password     = md5($password); // use other algorithum
        $this->role         = $role;
        $this->created_at   = date('Y-m-d H:i:s');
        $this->updated_at   = date('Y-m-d H:i:s');

        return $this->db->insert('users', $this);
    }

    public function setEmailIsVerified($id)
    {
        $this->db->set('email_is_verified', 1);
        $this->db->where('id', $id);
        $this->db->update('users');
    }
}
