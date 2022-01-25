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

    public function allVerified()
    {
        $this->db->where('email_is_verified', 1);

        return $this->db->count_all_results('users');
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

    public function allHadAttachedProductAmount()
    {
        $this->db->select('users.id, name, email, role');
        $this->db->from('users');
        $this->db->join('user_product_relation', 'users.id = user_product_relation.user_id', 'left');
        $this->db->where('user_product_relation.user_id is not NULL');

        $result = $this->db->get()->result_array();

        return $result ? count($result) : 0;
    }

    public function allHadAttached()
    {
        $this->db->select('users.id, name, email, price, count');
        $this->db->from('users');
        $this->db->join('user_product_relation', 'users.id = user_product_relation.user_id', 'left');
        $this->db->where('user_product_relation.user_id is not NULL');

        $result = $this->db->get()->result_array();

        $data = [];
        foreach ($result as $key => $value) {
            if (isset($data[$value['id']])) {
                $data[$value['id']]['total'] += $value['price'] * $value['count'];
                $data[$value['id']]['origin'] += $value['price'] * $value['count'];
            } else {
                $data[$value['id']]['id'] = $value['id'];
                $data[$value['id']]['name'] = $value['name'];
                $data[$value['id']]['total'] = $value['price'] * $value['count'];
                $data[$value['id']]['origin'] = $value['price'] * $value['count'];
            }
        }

        return $data;
    }
}
