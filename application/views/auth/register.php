<div id="wrapper">
    <div id="page-wrapper" class="gray-bg" style="position: relative; min-height: 100vh;">
        <div class="panel panel-success" style="width: 40vw; position: absolute; left: 50%; top: 40%; transform: translate(-50%, -50%);">
            <div class="panel-heading">
                CI-Test-Task
            </div>
            <div class="panel-body" id="vue-main">
                <div class="form-group">
                    <label>Your Name</label>
                    <input v-model.trim="name" class="form-control" type="text" name="" maxlength="30">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input v-model.trim="email" class="form-control" type="text" name="" maxlength="40">
                </div>
                <div class="form-group">
                    <label>Password (at least 6 characters)</label>
                    <input v-model="password" class="form-control" type="password" name="" maxlength="12">
                </div>
                <div class="text-center m-b">
                    <button v-on:click="submitRegister" class="btn btn-w-m btn-success ladda-button" id="vue-submit-register" data-style="zoom-in">Register Now</button>
                </div>
                <p v-if="error.length > 0" class="text-danger text-center">{{ error }}</p>
                <div class="text-center">
                    <p>Already have an account?</p>
                    <a href="/auth/login">Login Now</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function strIsEmail(str) {
    var reg = /^[a-zA-Z0-9_\-\.]+@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,8})$/i;
    return reg.test(str) ? true : false;
}

var main = new Vue({
    el: '#vue-main'
    ,data: {
        name: ''
        ,email: ''
        ,password: ''
        ,error: ''
    }
    ,created: function() {}
    ,methods: {
        submitRegister: function(events) {
            this.error = '';
            
            if (this.email.length == 0) {
                return this.error = 'Email is required';
            }

            if (!strIsEmail(this.email)) {
                // return this.error = 'Invalid email';
            }

            if (this.password.length == 0) {
                return this.error = 'Password is required';
            }

            if (this.password.length < 6) {
                return this.error = 'Password is at least 6 characters';
            }

            self = this;

            var button = $('#'+event.currentTarget.id);
            button.ladda().ladda('start');

            $.ajax({
                type: 'POST'
                ,url: '/auth/register'
                ,dataType: 'json'
                ,data: {
                    name: self.name
                    ,email: self.email
                    ,password: self.password
                }
                ,success: function(response) {
                    button.ladda().ladda('stop');
                    if (response.code != 1) {
                        return self.error = response.message;
                    }

                    window.location.href = response.data.redirect;
                }
                ,error: function() {
                    button.ladda().ladda('stop');
                    return self.error = 'Server error';
                }
            });
        }
    }
});
</script>
