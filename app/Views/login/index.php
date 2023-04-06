<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Toba Surimi Industries</title>
    <link href="<?= base_url(); ?>/assets/_vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/img/favicon.png" type="image/png" />
    <!-- Custom fonts for this template-->
    
    <link href="<?= base_url(); ?>assets/_vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="<?= base_url(); ?>assets/css/style.min.css" rel="stylesheet">
    <link href="<?= base_url(); ?>assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-login-register">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-6 col-lg-6 col-md-6">

                <div class="card o-hidden card-login-register">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-5">
                                    <div class="d-flex justify-content-center align-items-center mb-5">
                                        <img src="<?= base_url("assets/img/logo.png"); ?>" width="500" height="120">
                                    </div>
                                    <form class="user" method="POST" autocomplete="off">
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
                                                <input type="text" class="form-control password" id="password" name="password" placeholder="Password" maxlength="30">
                                                <label for="floatingInput">Password</label>
                                            </div>
                                            <div class="input-group-prepend group-prepend-password-login-register align-items-center">
                                                <span style="border: 0px" class="input-group-text bg-white" id="basic-addon2" onclick="password_show_hide()">
                                                    <i class="fas fa-eye" id="show_eye"></i>
                                                    <i class="fas fa-eye-slash d-none" id="hide_eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <button class="btn btn-login-register btn-block">
                                            Log In
                                        </button>
                                    </form>
                                </div>
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

        const password_show_hide = function() {
            var x = document.getElementById("password");
            var show_eye = document.getElementById("show_eye");
            var hide_eye = document.getElementById("hide_eye");
            hide_eye.classList.remove("d-none");
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

</body>

</html>