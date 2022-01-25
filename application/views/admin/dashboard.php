<div id="wrapper">
    <div id="page-wrapper" class="gray-bg" style="position: relative; min-height: 100vh; padding: 20px;">
        <div class="row">
            <div class="col-sm-12">
                <template v-if="currentUser">
                    <div class="m-b">
                        <span>Hi, {{ currentUser.name }}</span>
                        <a class="pull-right" href="/auth/logout">Logout</a>
                    </div>
                </template>
            </div>
            <div class="col-sm-4">
                <div class="panel panel-success">
                    <div class="panel-heading" style="position: relative;">
                        Data
                        <select v-model="currency" class="form-control" style="color: black; width: 100px; position: absolute; right: 5px; top: 50%; transform: translateY(-50%);">
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                            <option value="RON">RON</option>
                        </select>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="ibox" style="border: 1px solid #eee;">
                                    <div class="ibox-title">Active and verified users</div>
                                    <div class="ibox-content text-center font-bold" style="font-size: 24px;"><?php echo $data['users']; ?></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="ibox" style="border: 1px solid #eee;">
                                    <div class="ibox-title">Users who have attached products</div>
                                    <div class="ibox-content text-center font-bold" style="font-size: 24px;"><?php echo $data['attached_product_users']; ?></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="ibox" style="border: 1px solid #eee;">
                                    <div class="ibox-title">Active Products</div>
                                    <div class="ibox-content text-center font-bold" style="font-size: 24px;"><?php echo $data['products']; ?></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="ibox" style="border: 1px solid #eee;">
                                    <div class="ibox-title">Unattached Products</div>
                                    <div class="ibox-content text-center font-bold" style="font-size: 24px;"><?php echo $data['unattached_products']; ?></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="ibox" style="border: 1px solid #eee;">
                                    <div class="ibox-title">Total Attached Products</div>
                                    <div class="ibox-content text-center font-bold" style="font-size: 24px;"><?php echo $data['attached_products']; ?></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="ibox" style="border: 1px solid #eee;">
                                    <div class="ibox-title" style="position: relative;">
                                        Total Price
                                    </div>
                                    <div class="ibox-content text-center font-bold" style="font-size: 24px;">
                                        <font>{{ currencySymbol }}</font> {{ displayTotalPrice }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="ibox" style="border: 1px solid #eee;">
                                    <div class="ibox-title" style="position: relative;">
                                        Detail Price
                                    </div>
                                    <div class="ibox-content font-bold">
                                        <template v-if="priceDetail.length == 0">
                                            <p>No details yet...</p>
                                        </template>
                                        <template v-else>
                                            <p v-for="(data, userID) in priceDetail" class="m-b-none">UserID:{{ data.id }} - Name:{{ data.name }} => <font>{{ currencySymbol }}</font> {{ parseFloat(data.total).toFixed(2) }}</p>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="panel panel-primary">
                    <div class="panel-heading">Products</div>
                    <div class="panel-body">
                        <button v-on:click="toggleCurdModal(null)" class="btn btn-w-m btn-primary">Create Product</button>
                        <hr>
                        <template v-if="products.length > 0">
                        <div class="row">
                            <div v-for="(product, index) in products" class="col-sm-4">
                                <div class="ibox" style="margin-bottom: 10px; border: 1px solid #eee;">
                                    <div class="ibox-title">
                                        <div class="pull-left">
                                            <span v-if="product.status == 'publish'" class="badge badge-primary">Publish</span>
                                            <span v-else class="badge">Draft</span>
                                        </div>
                                        <div class="pull-right">
                                            <button v-on:click="toggleCurdModal(product)" class="btn btn-info btn-xs">Edit</button>
                                            <button v-on:click="deleteProduct(product.id)" class="btn btn-danger btn-xs">Delete</button>
                                        </div>        
                                    </div>
                                    <div class="ibox-content">
                                        <div v-bind:style="{'background-image': 'url('+product.image+')'}" style="width: 100%; height: 200px; background-size: contain !important; background-position: 50% !important; background-repeat: no-repeat; cursor: pointer;"></div>
                                        <p class="m-t m-b-none text-2line" style="min-height: 38px;">Title: {{ product.title }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </template>
                        <template v-else>
                            <div style="width: 100%; min-height: 20vh; position: relative;">
                                <p class="m-b-none" style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);">No products yet...</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- modal -->
    <div class="modal inmodal fade in" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;" v-bind:style="{'display': displayCurdModal}">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button v-on:click="toggleCurdModal(null)" type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Create & Update Product</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Title</label>
                        <input v-model.trim="target.title" class="form-control" type="text" name="" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea v-model.trim="target.description" class="form-control" rows="5" style="resize: none;"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Image</label>

                        <input v-on:change="pushImage" type="file" accept="image/*" ref="images" style="display: none" />
                        <p>
                            <button v-on:click="$refs.images.click()" class="btn btn-success btn-w-m m-b">
                                <i class="fa fa-upload"></i> Select Images
                            </button>
                        </p>
                        <div v-on:click="$refs.images.click()" v-if="preview.length > 0" v-bind:style="{'background-image': 'url('+preview+')'}" style="width: 50%; height: 200px; border: 1px solid #eee; background-size: contain !important; background-position: 50% !important; background-repeat: no-repeat; cursor: pointer;"></div>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select v-model="target.status" class="form-control">
                            <option value=""></option>
                            <option value="publish">Publish</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                    <!-- <div class="form-group">
                        <label>Started At</label>
                    </div>
                    <div class="form-group">
                        <label>Ended At</label>
                    </div> -->
                </div>

                <div class="modal-footer" style="text-align: center;">
                    <button v-on:click="toggleCurdModal(null)" type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <button v-on:click="submitCreate" v-if="target.id == 0" type="button" class="btn btn-primary ladda-button" data-style="zoom-in">Submit</button>
                    <button v-on:click="submitUpdate" v-else type="button" class="btn btn-primary ladda-button" data-style="zoom-in">Save changes</button>
                    <p v-if="error.length > 0" class="text-danger m-t">{{ error }}</p>
                </div>
            </div>
        </div>
        <div class="modal-backdrop in"></div>
    </div>
    <!-- modal -->
</div>
<script>
var main = new Vue({
    el: '#wrapper'
    ,data: {
        currentUser: <?php echo $user ? json_encode($user) : null; ?>
        ,currency: 'USD'
        ,exchangeRates: {}
        ,currentRate: 1
        ,totalPrice: <?php echo $data['total_price']; ?>
        ,priceDetail: <?php echo json_encode($data['detail_price']); ?>
        ,products: <?php echo json_encode($products); ?>
        ,showCurdModal: false

        ,target: {
            id: 0
            ,title: ''
            ,description: ''
            ,image: null
            ,status: ''
            // ,startedAt: ''
            // ,endedAt: ''
        }
        ,error: ''
        ,file: null
        ,preview: ''
    }
    ,created: function() {
        self = this;
        $.ajax({
            type: 'GET'
            ,url: 'http://api.exchangeratesapi.io/v1/latest?access_key=19b21389c0494af28c7238531fd35802'
            ,dataType: 'json'
            ,data: {}
            ,success: function(response) {
                if (response.success == true) {
                    self.exchangeRates = response.rates;
                }
            }
        });
    }
    ,computed: {
        displayCurdModal: function() {
            return this.showCurdModal ? 'block' : 'none';
        }
        ,currencySymbol: function() {
            if (this.currency == 'USD') {
                // this.currentRate = 1;
                return '$';
            }

            if (this.currency == 'EUR') {
                // this.currentRate = this.exchangeRates['USD'];
                return '€';
            }

            if (this.currency == 'RON') {
                // this.currentRate = this.exchangeRates['USD'] / this.exchangeRates['RON'];
                return 'lei';
            }
        }
        ,displayTotalPrice: function() {
            var total = 0;

            for (var i in this.priceDetail) {
                total += parseFloat(this.priceDetail[i]['total']);
            }

            return total.toFixed(2);
        }
    }
    ,watch: {
        currency: function() {
            // console.log('currency change => '+this.currency);

            if (this.currency == 'USD') {
                this.currentRate = 1;
            }

            if (this.currency == 'EUR') {
                this.currentRate = 1 / this.exchangeRates['USD'];
            }

            if (this.currency == 'RON') {
                this.currentRate = (1 / this.exchangeRates['USD']) * this.exchangeRates['RON'];
            }

            // console.log('currency rate => '+this.currentRate);

            for (var i in this.priceDetail) {
                this.priceDetail[i]['total'] = parseFloat(this.priceDetail[i]['origin'] * this.currentRate).toFixed(2);
            }
        }
    }
    ,methods: {
        toggleCurdModal: function(target) {
            if (this.showCurdModal) {
                this.showCurdModal = false;
                this.target = {
                    id: 0
                    ,title: ''
                    ,description: ''
                    ,image: null
                    ,status: ''
                    // ,startedAt: ''
                    // ,endedAt: ''
                };
                this.preview = '';
            } else {
                this.showCurdModal = true;
                if (target != null) {
                    this.target = target;
                    this.preview = target.image;
                }
            }
        }
        ,pushImage: function(event) {
            self = this;
            self.error = '';
            self.file = null;
            self.preview = '';

            var file = event.target.files[0];

            if(file.size / 1024 > 2048){
                self.preview = '';
                self.error = 'Your image file is too big.(Max 2MiB)';
                return false;
            }

            self.target.image = file;

            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function(e) {
                self.preview = this.result;
            }
        }
        ,submitCreate: function(event) {
            if (this.target.title.length == 0) {
                return this.error = 'Title is required';
            }

            if (this.target.description.length == 0) {
                return this.error = 'Description is required';
            }

            if (this.target.image == null) {
                return this.error = 'Please upload an image for the product';
            }

            if (this.target.status.length == 0) {
                return this.error = 'Please select a status';
            }

            self = this;

            var button = $('#'+event.currentTarget.id);
            button.ladda().ladda('start');

            var formData = new FormData();
            formData.append("title", this.target.title);
            formData.append("description", this.target.description);
            formData.append("image", this.target.image);
            formData.append("status", this.target.status);
            // TODO started_at ended_at

            $.ajax({
                type: 'POST'
                ,url: '/admin/product'
                ,processData: false  // tell jQuery not to process the data
                ,contentType: false  // tell jQuery not to set contentType
                ,dataType: 'json'
                ,data: formData
                ,success: function(response) {
                    button.ladda().ladda('stop');
                    if (response.code != 1) {
                        return self.error = response.message;
                    }

                    window.location.reload();
                }
                ,error: function() {
                    button.ladda().ladda('stop');
                    return self.error = 'Server error';
                }
            });
        }
        ,submitUpdate: function(event) {
            if (this.target.title.length == 0) {
                return this.error = 'Title is required';
            }

            if (this.target.description.length == 0) {
                return this.error = 'Description is required';
            }

            if (this.target.status.length == 0) {
                return this.error = 'Please select a status';
            }

            self = this;

            var button = $('#'+event.currentTarget.id);
            button.ladda().ladda('start');

            var formData = new FormData();
            formData.append("id", this.target.id);
            formData.append("title", this.target.title);
            formData.append("description", this.target.description);
            if (this.target.image instanceof File) {
                formData.append("image", this.target.image);
            }
            formData.append("status", this.target.status);

            $.ajax({
                type: 'POST'
                ,url: '/admin/product/update'
                ,processData: false  // tell jQuery not to process the data
                ,contentType: false  // tell jQuery not to set contentType
                ,dataType: 'json'
                ,data: formData
                ,success: function(response) {
                    button.ladda().ladda('stop');
                    if (response.code != 1) {
                        return self.error = response.message;
                    }

                    window.location.reload();
                }
                ,error: function() {
                    button.ladda().ladda('stop');
                    return self.error = 'Server error';
                }
            });
        }
        ,deleteProduct: function(productID) {
            swal({
                type: 'warning'
                ,title: ''
                ,text: 'Are you sure to delete this product?'
                ,showCancelButton: true
                // ,closeOnConfirm: false
            }, function() {
                $.ajax({
                    type: 'POST'
                    ,url: '/admin/product/delete'
                    ,dataType: 'json'
                    ,data: {
                        id: productID
                    }
                    ,success: function(response) {
                        if (response.code != 1) {
                            return swal('', response.message, 'error');
                        }

                        window.location.reload();
                    }
                    ,error: function() {
                        return swal('', 'Server error', 'error');
                    }
                });
            });
        }
    }
});
</script>
