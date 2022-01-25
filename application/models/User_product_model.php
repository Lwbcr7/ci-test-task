<?php

/**
 * 
 */
class User_product_model extends CI_model
{
    public $user_id;
    public $product_id;
    public $price;
    public $count;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getByUserIDProductID($userID, $productID)
    {
        $this->db->where('user_id', $userID);
        $this->db->where('product_id', $productID);

        return $this->db->get('user_product_relation')->row_array();
    }

    public function attach($userID, $productID, $price, $count)
    {
        $this->user_id      = $userID;
        $this->product_id   = $productID;
        $this->price        = floatval($price);
        $this->count        = intval($count);

        return $this->db->insert('user_product_relation', $this);
    }

    public function unattach($userID, $productID)
    {
        $this->db->where('user_id', $userID);
        $this->db->where('product_id', $productID);
        $this->db->delete('user_product_relation');
    }

    public function deleteByProductID($productID)
    {
        $this->db->where('product_id', $productID);
        $this->db->delete('user_product_relation');
    }
}