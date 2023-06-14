<?= $this->extend('layouts/template_login'); ?>
<?= $this->Section('content'); ?>

<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-5 col-lg-5 col-md-5">

            <div class="card o-hidden card-login-register">
                <div class="card-body p-5">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-12">
                                <div class="d-flex justify-content-center align-items-center mb-5">
                                    <img src="<?= base_url("assets/img/logo.png"); ?>">
                                </div>
                                <form class="user" method="POST" action="<?= base_url("login"); ?>" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <div class="input-group input-group-login-register">
                                        <div class="input-group-prepend group-prepend-login-register align-items-center">
                                            <span class="input-group-text input-group-text-login-register" id="basic-addon1"><i class="icon-login-register fas fa-user"></i></span>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control username" id="username" name="username" placeholder="Username" maxlength="30">
                                            <label for="floatingInput">Username</label>
                                        </div>
                                    </div>
                                    <div class="input-group input-group-login-register">
                                        <div class="input-group-prepend group-prepend-login-register align-items-center">
                                            <span class="input-group-text input-group-text-login-register" id="basic-addon1"><i class="icon-login-register fas fa-lock"></i></span>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control password" id="password" name="password" placeholder="Password" maxlength="30">
                                            <label for="floatingInput">Password</label>
                                        </div>
                                        <div class="input-group-prepend group-prepend-password-login-register align-items-center">
                                            <span style="border: 0px" class="input-group-text bg-white" id="basic-addon2" onclick="password_show_hide()">
                                                <i class="fas fa-eye d-none" id="show_eye"></i>
                                                <i class="fas fa-eye-slash" id="hide_eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </form>
                                <button class="btn btn-login-register btn-block" onclick="submitForm()">
                                    Log In
                                </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Bootstrap core JavaScript-->
<script src="<?= base_url(); ?>assets/_vendor/jquery/jquery.min.js"></script>
<script src="<?= base_url(); ?>assets/_vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="<?= base_url(); ?>assets/_vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="<?= base_url(); ?>assets/js/style.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    <?php
    if (session()->getFlashData("errors")) {
    ?>
        Swal.fire({
            icon: 'error',
            title: '<?= session()->getFlashData("errors"); ?>',
            confirmButtonColor: '#4e73df',
        })
    <?php
    }
    ?>

    $(document).ready(function () {
        var validator = $(".user").validate({
            rules: {
                username: {
                    required: true
                },
                password: {
                    required: true
                }
            },
            messages: {
                username: {
                    required: "Username wajib diisi"
                },
                password: {
                    required: "Password wajib diisi"
                }
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errorPlacement: function(error, element) {
                var elem = $(element);
                if (elem.hasClass("select2-hidden-accessible")) {
                    element = $("#select2-" + elem.attr("id") + "-container").parent();
                    error.insertAfter(element);
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
                $(element).addClass('select-class');                      

            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
                $(element).removeClass('select-class');   
            },
        });
    })

    $('.username, .password').keypress(function (e) {
        var key = e.which;
        if(key == 13)  // the enter key code
        {
            if ($(".user").valid()) {
                $(".user").submit()
            }
        }
    })

    const submitForm = function() {
        if ($(".user").valid()) {
            $(".user").submit()
        }
    }

    const password_show_hide = function() {
        var x = document.getElementById("password");
        var show_eye = document.getElementById("show_eye");
        var hide_eye = document.getElementById("hide_eye");
        show_eye.classList.remove("d-none");
        if (x.type === "text") {
            x.type = "password";
            show_eye.style.display = "none";
            hide_eye.style.display = "block";
        } else {
            x.type = "text";
            show_eye.style.display = "block";
            hide_eye.style.display = "none";
        }
    }
</script>

<?= $this->endSection(''); ?>