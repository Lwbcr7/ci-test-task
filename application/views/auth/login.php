<div id="wrapper">
    <div id="page-wrapper" class="gray-bg" style="position: relative; min-height: 100vh;">
        <div class="panel panel-success" style="width: 40vw; position: absolute; left: 50%; top: 40%; transform: translate(-50%, -50%);">
            <div class="panel-heading">
                CI-Test-Task
            </div>
            <div class="panel-body" id="vue-main">
                <div class="form-group">
                    <label>Email</label>
                    <input v-model="email" class="form-control" type="text" name="">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input v-model="password" class="form-control" type="password" name="" maxlength="12">
                </div>
                <div class="text-center m-b">
                    <button v-on:click="submitLogin" class="btn btn-w-m btn-success ladda-button" id="vue-submit-login" data-style="zoom-in">Log Now</button>
                </div>
                <p v-if="error.length > 0" class="text-danger text-center">{{ error }}</p>
                <div class="text-center">
                    <p>Don't have an account?</p>
                    <a href="/auth/register">Register Now</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var main = new Vue({
    el: '#vue-main'
    ,data: {
        email: ''
        ,password: ''
        ,error: ''
    }
    ,created: function() {}
    ,methods: {
        submitLogin: function() {
            this.error = '';
            
            if (this.email.length == 0) {
                return this.error = 'Email is required';
            }

            if (this.password.length == 0) {
                return this.error = 'Password is required';
            }

            self = this;

            var button = $('#'+event.currentTarget.id);
            button.ladda().ladda('start');

            $.ajax({
                type: 'POST'
                ,url: ''
                ,dataType: 'json'
                ,data: {
                    email: self.email
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