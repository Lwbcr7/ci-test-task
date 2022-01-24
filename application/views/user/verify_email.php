<div id="wrapper">
    <div id="page-wrapper" class="gray-bg" style="position: relative; min-height: 100vh;">
        <div class="panel panel-warning" style="width: 40vw; position: absolute; left: 50%; top: 40%; transform: translate(-50%, -50%);">
            <div class="panel-body" id="vue-main">
                <p class="text-center">We have send a verification email to you, please check and click the verify link.</p>
                <p class="text-center">
                    <button v-on:click="doVerify" class="btn btn-w-m btn-success">Assuming you have completed verification, continue</button>
                </p>
                <p v-if="error.length > 0" class="text-center text-danger">{{ error }}</p>
            </div>
        </div>
    </div>
</div>
<script>
var main = new Vue({
    el: '#vue-main'
    ,data: {
        error: ''
    }
    ,created: function() {}
    ,methods: {
        doVerify: function() {
            self = this;
            $.ajax({
                type: 'POST'
                ,url: '/auth/email/verify'
                ,dataType: 'json'
                ,data: {}
                ,success: function(response) {
                    if (response.code != 1) {
                        return self.error = response.message;
                    }

                    // success and redirect to product page
                    window.location.href = '/user/products';
                }
                ,error: function() {
                    return self.error = 'Server error';
                }
            });
        }
    }
});
</script>