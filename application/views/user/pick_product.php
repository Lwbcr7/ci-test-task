<div id="wrapper">
    <div id="page-wrapper" style="position: relative; min-height: 100vh; padding: 20px;">
        <div class="row">
            <div class="col-sm-12">
                <template v-if="currentUser">
                    <div class="m-b" style="color: white;">
                        <span>Hi, {{ currentUser.name }}</span>
                        <a class="pull-right" href="/auth/logout" style="color: white;">Logout</a>
                    </div>
                </template>
            </div>
            <div class="col-sm-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">Product List</div>
                    <div class="panel-body" style="min-height: 80vh; position: relative; padding: 10px;">
                        <div class="row">
                            <template v-if="products.length == 0">
                                <p style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);">No available products yet...</p>
                            </template>
                            <template v-else>
                            <div v-for="(product, index) in products" class="col-sm-4">
                                <div class="ibox" style="margin-bottom: 10px; border: 1px solid #eee;">
                                    <div class="ibox-title">
                                        <p class="text-2line m-b-none" style="min-height: 38px;">{{ product.title }}</p>
                                    </div>
                                    <div class="ibox-content" style="padding: 10px;">
                                        <div v-bind:style="{'background-image': 'url('+product.image+')'}" style="width: 100%; height: 200px; background-size: contain !important; background-position: 50% !important; background-repeat: no-repeat; cursor: pointer;"></div>
                                        <p class="text-center m-b-none m-t">
                                            <template v-if="!isPicked(product.id)">
                                                <button v-on:click="togglePickModal(product)" class="btn btn-w-m btn-primary btn-sm">Pick Me</button>
                                            </template>
                                            <template v-else>
                                                <button class="btn btn-w-m btn-block btn-sm text-danger" disabled="true">Already Picked</button>
                                            </template>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="panel panel-success">
                    <div class="panel-heading">Picked Products</div>
                    <div class="panel-body" style="min-height: 80vh; position: relative; padding: 10px;">
                        <template v-if="pickedProducts.length == 0">
                            <p style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);">No products yet...</p>
                        </template>
                        <template v-else>
                            <div v-for="(product, index) in pickedProducts" style="border: 1px solid #eee; padding: 10px;">
                                <p class="m-b-none">Title: {{ product.title }}</p>
                                <div style="justify-content: space-between; display: flex;">
                                    <div v-bind:style="{'background-image': 'url('+product.image+')'}" style="width: 100px; height: 100px; background-size: contain !important; background-position: 50% !important; background-repeat: no-repeat; cursor: pointer;"></div>
                                    <div>
                                        <p>Price: $ {{ product.price }}</p>
                                        <p>Quantity: {{ product.count }}</p>
                                        <button v-on:click="unattach(product.id)" class="btn btn-danger btn-w-m btn-xs">Unattach</button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- pick modal -->
    <div class="modal inmodal fade in" id="myModal6" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;" v-bind:style="{'display': displayPickModal}">
        <div v-if="targetProduct != null" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button v-on:click="togglePickModal(null)" type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Pick {{ targetProduct.title }}</h4>
                </div>
                <div class="modal-body">
                    <div v-bind:style="{'background-image': 'url('+targetProduct.image+')'}" style="width: 100%; height: 200px; background-size: contain !important; background-position: 50% !important; background-repeat: no-repeat; cursor: pointer;"></div>
                    <div class="form-group">
                        <label>Price</label>
                        <!-- <input v-model.trim="price" class="form-control" type="text" name=""> -->

                        <div class="input-group">
                            <span class="input-group-addon">USD ($)</span>
                            <input v-model.trim="price" type="text" placeholder="" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input v-model.trim="count" class="form-control" type="text" name="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button v-on:click="togglePickModal(null)" type="button" class="btn btn-white" data-dismiss="modal">Cancel</button>
                    <button v-on:click="submitAttach" type="button" class="btn btn-primary">Submit</button>
                    <p v-if="error.length > 0" class="text-center text-danger m-t">{{ error }}</p>
                </div>
            </div>
        </div>
        <div class="modal-backdrop in"></div>
    </div>
    <!-- pick modal -->
</div>
<script>
var main = new Vue({
    el: '#wrapper'
    ,data: {
        currentUser: <?php echo $user ? json_encode($user) : null; ?>
        ,pickedProducts: <?php echo json_encode($pickedProducts); ?>
        ,products: <?php echo json_encode($products); ?>
        ,showPickModal: false

        ,targetProduct: null
        ,price: ''
        ,count: ''
        ,error: ''
    }
    ,created: function() {}
    ,computed: {
        displayPickModal: function() {
            return this.showPickModal ? 'block' : 'none';
        }
    }
    ,methods: {
        isPicked: function(productID) {
            for (var i in this.pickedProducts) {
                if (productID == this.pickedProducts[i].id) {
                    return true;
                }
            }

            return false;
        }
        ,togglePickModal: function(product) {
            if (this.showPickModal) {
                this.showPickModal = false;
                this.targetProduct = null;
                this.price = '';
                this.count = '';
                this.error = '';
            } else {
                this.showPickModal = true;
                if (product != null) {
                    this.targetProduct = product;
                }
            }
        }
        ,submitAttach: function(event) {
            this.error = '';

            if (this.targetProduct == null) {
                return this.error = 'Target product is null';
            }

            if (this.price.length == 0) {
                return this.error = 'Price is required';
            }

            if (isNaN(this.price)) {
                return this.error = 'Price value is not a number';
            }

            if (this.count.length == 0) {
                return this.error = 'Quantity is required';
            }

            if (this.count != parseInt(this.count)) {
                return this.error = 'Quantity value is not an integer';
            }

            self = this;

            $.ajax({
                type: 'POST'
                ,url: '/user/product/pick'
                ,dataType: 'json'
                ,data: {
                    product_id: self.targetProduct.id
                    ,price: self.price
                    ,count: self.count
                }
                ,success: function(response) {
                    if (response.code != 1) {
                        return self.error = response.message;
                    }

                    self.pickedProducts.push({
                        id: self.targetProduct.id
                        ,title: self.targetProduct.title
                        ,image: self.targetProduct.image
                        ,description: self.targetProduct.description
                        ,status: self.targetProduct.status
                        ,price: parseFloat(self.price).toFixed(2)
                        ,count: self.count
                    });

                    self.showPickModal = false;
                    self.targetProduct = null;
                    self.price = '';
                    self.count = '';
                }
                ,error: function() {
                    self.error = 'Server error';
                }
            });
        }
        ,unattach: function(productID) {
            self = this;
            swal({
                type: 'warning'
                ,title: ''
                ,text: 'Are you sure to unattach this product?'
                ,showCancelButton: true
                // ,closeOnConfirm: false
            }, function() {
                $.ajax({
                    type: 'POST'
                    ,url: '/user/product/unpick'
                    ,dataType: 'json'
                    ,data: {
                        product_id: productID
                    }
                    ,success: function(response) {
                        if (response.code != 1) {
                            return swal('', response.message, 'error');
                        }

                        for (var i in self.pickedProducts) {
                            if (productID == self.pickedProducts[i].id) {
                                // delete from array
                                self.pickedProducts.splice(i, 1);
                            }
                        }
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
