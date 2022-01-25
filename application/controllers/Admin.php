<?php
require_once(APPPATH . "controllers/Base.php");

/**
 * 
 */
class Admin extends Base
{
    public function dashboard()
    {
        $user = $this->currentUser();

        if (!$user) {
            header('Location: /auth/login');
            die();
        }

        if ($user['role'] != 'admin') {
            header('Location: /user/products');
            die();
        }

        $this->load->model('product_model');
        $this->load->model('user_model');
        $this->load->model('user_product_model');

        $viewData['user'] = [
            'name' => $user['name'],
        ];

        $viewData['data'] = [
            'users'                     => $this->user_model->allVerified(),
            'attached_product_users'    => $this->user_model->allHadAttachedProductAmount(),
            'products'                  => count($this->product_model->all()),
            'unattached_products'       => count($this->product_model->allUnattached()),
            'attached_products'         => $this->product_model->attachedAmount(),
            'total_price'               => $this->product_model->attachedPrice(),
            'detail_price'              => $this->user_model->allHadAttached(),
        ];
        $viewData['products'] = $this->product_model->all();

        !is_dir('./uploads') && mkdir('./uploads');

        $this->load->view('layout/header');
        $this->load->view('admin/dashboard', $viewData);
        $this->load->view('layout/footer');
    }

    public function storeProduct()
    {
        $user = $this->currentUser();

        if (!$user) {
            return $this->responseJson([
                'code'      => -1,
                'message'   => 'There are currently no logged in user',
            ]);
        }

        if ($user['role'] != 'admin') {
            return $this->responseJson([
                'code'      => -2,
                'message'   => 'Only admin is allow to do this',
            ]);
        }

        $title = $this->input->post('title');
        $desc = $this->input->post('description');
        $status = $this->input->post('status');

        if (empty($title)) {
            return $this->responseJson([
                'code'      => -3,
                'message'   => 'Title is required',
            ]);
        }

        if (empty($desc)) {
            return $this->responseJson([
                'code'      => -4,
                'message'   => 'Description is required',
            ]);
        }

        if (empty($status)) {
            return $this->responseJson([
                'code'      => -5,
                'message'   => 'Status is required',
            ]);
        }

        $config['upload_path']      = './uploads/';
        $config['allowed_types']    = 'gif|jpg|png|jpeg';
        $config['max_size']         = 2048; // kb

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('image')) {
            $error = $this->upload->display_errors();
            return $this->responseJson([
                'code'      => -6,
                'message'   => $error,
            ]);
        }

        $storageData = $this->upload->data();
        $imagePath = '/uploads/'.$storageData['file_name'];

        $this->load->model('product_model');
        $this->product_model->store($title, $desc, $imagePath, $status);

        return $this->responseJson(['code' => 1]);
    }

    public function updateProduct()
    {
        $user = $this->currentUser();

        if (!$user) {
            return $this->responseJson([
                'code'      => -1,
                'message'   => 'There are currently no logged in user',
            ]);
        }

        if ($user['role'] != 'admin') {
            return $this->responseJson([
                'code'      => -2,
                'message'   => 'Only admin is allow to do this',
            ]);
        }

        $this->load->model('product_model');

        // Check if the product exists
        $product = $this->product_model->getByID($this->input->post('id'));
        if (empty($product)) {
            return $this->responseJson([
                'code'      => -3,
                'message'   => 'product does not exists',
            ]);
        }

        $title  = $this->input->post('title');
        $desc   = $this->input->post('description');
        $status = $this->input->post('status');

        if (empty($title)) {
            return $this->responseJson([
                'code'      => -3,
                'message'   => 'Title is required',
            ]);
        }

        if (empty($desc)) {
            return $this->responseJson([
                'code'      => -4,
                'message'   => 'Description is required',
            ]);
        }

        if (empty($status)) {
            return $this->responseJson([
                'code'      => -5,
                'message'   => 'Status is required',
            ]);
        }

        if (!empty($_FILES['image'])) {
            $config['upload_path']      = './uploads/';
            $config['allowed_types']    = 'gif|jpg|png|jpeg';
            $config['max_size']         = 2048; // kb

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('image')) {
                $error = $this->upload->display_errors();
                return $this->responseJson([
                    'code'      => -6,
                    'message'   => $error,
                ]);
            }

            $storageData = $this->upload->data();
            $imagePath = '/uploads/'.$storageData['file_name'];

            // delete old image file
            unlink(FCPATH . 'uploads/' . basename($product['image']));
        } else {
            $imagePath = $product['image'];
        }

        $this->product_model->updateByID($product['id'], $title, $desc, $imagePath, $status);

        return $this->responseJson(['code' => 1]);
    }

    public function deleteProduct()
    {
        $user = $this->currentUser();

        if (!$user) {
            return $this->responseJson([
                'code'      => -1,
                'message'   => 'There are currently no logged in user',
            ]);
        }

        if ($user['role'] != 'admin') {
            return $this->responseJson([
                'code'      => -2,
                'message'   => 'Only admin is allow to do this',
            ]);
        }

        $productID = $this->input->post('id');

        $this->load->model('product_model');
        $this->product_model->deleteByID($productID);

        // delete relation either
        $this->load->model('user_product_model');
        $this->user_product_model->deleteByProductID($productID);

        return $this->responseJson(['code' => 1]);
    }
}
