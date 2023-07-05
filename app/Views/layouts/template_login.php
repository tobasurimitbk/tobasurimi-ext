<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?= getenv('SITE_TITLE') ?></title>
    <link href="<?= base_url(); ?>assets/_vendor/fontawesome-free/css/all.min.css?v=<?= time(); ?>" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/img/favicon.png" type="image/png" />
    <!-- Custom fonts for this template-->
    
    <link href="<?= base_url(); ?>assets/_vendor/fontawesome-free/css/all.min.css?v=<?= time(); ?>" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap?v=<?= time(); ?>" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="<?= base_url(); ?>assets/css/style_login.min.css?v=<?= time(); ?>" rel="stylesheet">
    <link href="<?= base_url(); ?>assets/css/style_login.css?v=<?= time(); ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css?v=<?= time(); ?>" rel="stylesheet">
    <script src="<?= base_url(); ?>assets/_vendor/jquery/jquery.min.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url(); ?>assets/_vendor/jquery-easing/jquery.easing.min.js?v=<?= time(); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js?v=<?= time(); ?>" integrity="sha512-jTgBq4+dMYh73dquskmUFEgMY5mptcbqSw2rmhOZZSJjZbD2wMt0H5nhqWtleVkyBEjmzid5nyERPSNBafG4GQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js?v=<?= time(); ?>" integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/imask?v=<?= time(); ?>"></script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js?v=<?= time(); ?>"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js?v=<?= time(); ?>"></script>
</head>

<body class="bg-login-register">
    <?= $this->renderSection('content'); ?>
</body>

<script>
//Function Set Spinner Button
const setLoading = function() {
    $(".btn-login-register").attr("disabled", true)
}

const stopLoading = function() {
    $(".btn-login-register").attr("disabled", false)
}
</script>

</html>