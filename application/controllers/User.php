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

        $this->load->model('product_model');

        $products       = $this->product_model->allPublish();
        $pickedProducts = $this->product_model->pickedByUserID($user['id']);

        $viewData = [
            'user' => [
                'name' => $user['name'],
            ],
            'products'          => $products ? $products : [],
            'pickedProducts'    => $pickedProducts ? $pickedProducts : [],
        ];

        $this->load->view('layout/header');
        $this->load->view('user/pick_product', $viewData);
        $this->load->view('layout/footer');
    }

    public function pickProduct()
    {
        $user = $this->currentUser();

        if (!$user) {
            return $this->responseJson([
                'code'      => -1,
                'message'   => 'There are currently no logged in user',
            ]);
        }

        if ($user['email_is_verified'] == 0) {
            return $this->responseJson([
                'code'      => -1,
                'message'   => 'only verified user can do this',
            ]);
        }

        $productID = $this->input->post('product_id');
        if (empty($productID)) {
            return $this->responseJson([
                'code'      => -2,
                'message'   => 'product_id is required',
            ]);
        }

        $this->load->model('product_model');
        $product = $this->product_model->getByID($productID);
        if (empty($product)) {
            return $this->responseJson([
                'code'      => -3,
                'message'   => 'product does not exists anymore',
            ]);
        }

        $this->load->model('user_product_model');
        $relation = $this->user_product_model->getByUserIDProductID($user['id'], $productID);
        if (!empty($relation)) {
            return $this->responseJson([
                'code'      => -4,
                'message'   => 'This product has already been attached',
            ]);
        }

        $price = $this->input->post('price');
        if (!is_numeric($price)) {
            return $this->responseJson([
                'code'      => -5,
                'message'   => 'price value is not a number',
            ]);
        }

        $count = $this->input->post('count');
        if ($count != intval($count)) {
            return $this->responseJson([
                'code'      => -6,
                'message'   => 'count value is not an integer',
            ]);
        }

        $this->user_product_model->attach($user['id'], $product['id'], $price, $count);

        return $this->responseJson(['code' => 1]);
    }

    public function unpickProduct()
    {
        $user = $this->currentUser();

        if (!$user) {
            return $this->responseJson([
                'code'      => -1,
                'message'   => 'There are currently no logged in user',
            ]);
        }

        if ($user['email_is_verified'] == 0) {
            return $this->responseJson([
                'code'      => -2,
                'message'   => 'only verified user can do this',
            ]);
        }

        $productID = $this->input->post('product_id');
        if (empty($productID)) {
            return $this->responseJson([
                'code'      => -3,
                'message'   => 'product id is required',
            ]);
        }

        // check relation
        $this->load->model('user_product_model');
        $relation = $this->user_product_model->getByUserIDProductID($user['id'], $productID);
        if (empty($relation)) {
            return $this->responseJson([
                'code'      => -3,
                'message'   => 'This product is not yet attach',
            ]);
        }

        $this->user_product_model->unattach($user['id'], $productID);

        return $this->responseJson(['code' => 1]);
    }
}
