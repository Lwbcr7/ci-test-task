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
            <div class="col-sm-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">Products</div>
                    <div class="panel-body">
                        <button v-on:click="toggleCurdModal(null)" class="btn btn-w-m btn-success">Create Product</button>
                        <hr>
                        <template v-if="products.length > 0">
                        <div class="row">
                            <div v-for="(product, index) in products" class="col-sm-4">
                                <div class="ibox" style="margin-bottom: 10px; border: 1px solid #eee;">
                                    <div class="ibox-title">
                                        {{ product.title }}
                                        <div class="pull-right">
                                            <button v-on:click="toggleCurdModal(product)" class="btn btn-info btn-xs">Edit</button>
                                            <button v-on:click="deleteProduct(product.id)" class="btn btn-danger btn-xs">Delete</button>
                                        </div>        
                                    </div>
                                    <div class="ibox-content">
                                        <div v-bind:style="{'background-image': 'url('+product.image+')'}" style="width: 100%; height: 200px; background-size: contain !important; background-position: 50% !important; background-repeat: no-repeat; cursor: pointer;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </template>
                        <template v-else>
                            <div style="width: 100%; min-height: 20vh; position: relative;">
                                <p style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);">No products yet...</p>
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
    ,computed: {
        displayCurdModal: function() {
            return this.showCurdModal ? 'block' : 'none';
        }
    }
    ,created: function() {}
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
