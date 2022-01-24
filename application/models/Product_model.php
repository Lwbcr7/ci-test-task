<?php

/**
 * 
 */
class Product_model extends CI_model
{
    public $title;
    public $description;
    public $image;
    public $status;
    public $started_at;
    public $ended_at;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function all()
    {
        $query = $this->db->get('products');
        return $query->result_array();
    }

    public function getByID($id)
    {
        $query = $this->db->get_where('products', ['id' => $id]);

        return $query->row_array();
    }

    public function store($title, $desc, $imagePath, $status)
    {
        $this->title        = $title;
        $this->description  = $desc;
        $this->image        = $imagePath;
        $this->status       = $status;
        $this->started_at   = date('Y-m-d H:i:s');
        $this->ended_at     = date('Y-m-d H:i:s', strtotime('+7 days'));

        return $this->db->insert('products', $this);
    }

    public function updateByID($id, $title, $desc, $imagePath, $status)
    {
        $this->db->set('title', $title);
        $this->db->set('description', $desc);
        $this->db->set('image', $imagePath);
        $this->db->set('status', $status);
        $this->db->where('id', $id);
        $this->db->update('products');
    }

    public function deleteByID($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('products');
    }
}