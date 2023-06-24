<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?= getenv("SITE_TITLE"); ?></title>
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/img/favicon.png" type="image/png" />
    <link href="<?= base_url() ?>assets/css/bootstrap.min.css?v=<?= time(); ?>" rel="stylesheet">
    <script src="<?= base_url() ?>assets/js/bootstrap.bundle.min.js?v=<?= time(); ?>"></script>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap1.min.css?v=<?= time(); ?>" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/all.css?v=<?= time(); ?>" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <link href="<?= base_url() ?>assets/css/dataTables.bootstrap4.min.css?v=<?= time(); ?>" rel="stylesheet">
    <link href="<?= base_url() ?>assets/css/rowReorder.dataTables.min.css?v=<?= time(); ?>" rel="stylesheet">
    <link href="<?= base_url() ?>assets/css/responsive.dataTables.min.css?v=<?= time(); ?>" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url() ?>assets/css/select2-bootstrap-5-theme.min.css?v=<?= time(); ?>" />

    <!-- Template CSS -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/style.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/components.css?v=<?= time(); ?>">
    <link href="<?= base_url() ?>assets/css/select2.min.css?v=<?= time(); ?>" rel="stylesheet" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap-datepicker.standalone.min.css?v=<?= time(); ?>" integrity="sha512-TQQ3J4WkE/rwojNFo6OJdyu6G8Xe9z8rMrlF9y7xpFbQfW5g8aSWcygCQ4vqRiJqFsDsE1T6MoAOMJkFXlrI9A==" crossorigin="anonymous" referrerpolicy="no-referrer">


    <link href="<?= base_url(); ?>assets/_vendor/fontawesome-free/css/all.min.css?v=<?= time(); ?>" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/all.min.css?v=<?= time(); ?>" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">

    <link href="<?= base_url() ?>assets/css/css2.css?v=<?= time(); ?>" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/bootstrap-datetimepicker.min.css?v=<?= time(); ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/bootstrap-datetimepicker-standalone.css?v=<?= time(); ?>">
</head>

<body>
    <!-- Bootstrap core JavaScript-->
    <script src="<?= base_url(); ?>assets/_vendor/jquery/jquery.min.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url() ?>assets/js/moment.min.js?v=<?= time(); ?>"></script>
    <script type="text/javascript" src="<?= base_url() ?>assets/js/bootstrap-datetimepicker.min.js?v=<?= time(); ?>"></script>

    <script src="<?= base_url() ?>assets/js/select2.min.js?v=<?= time(); ?>"></script>

    <script src="<?= base_url(); ?>assets/js/datatables.min.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url(); ?>assets/js/dataTables.bootstrap4.min.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url(); ?>assets/js/dataTables.rowReorder.min.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url(); ?>assets/js/dataTables.responsive.min.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url(); ?>assets/js/responsive.bootstrap4.min.js?v=<?= time(); ?>"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11?v=<?= time(); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js?v=<?= time(); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js?v=<?= time(); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js?v=<?= time(); ?>" integrity="sha512-jTgBq4+dMYh73dquskmUFEgMY5mptcbqSw2rmhOZZSJjZbD2wMt0H5nhqWtleVkyBEjmzid5nyERPSNBafG4GQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js?v=<?= time(); ?>" integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="<?= base_url(); ?>assets/js/imask.js?v=<?= time(); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js?v=<?= time(); ?>" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>



    <div id="app">
        <!-- {% block content_2 %}{% endblock %} -->
        <div class="main-wrapper">
            <div class="navbar-bg"></div>

            <!-- Header -->
            <?= $this->include('layouts/header'); ?>

            <!-- Sidebar -->
            <?= $this->include('layouts/sidebar');  ?>

            <!-- Main Content -->
            <div class="main-content">
                <?= $this->renderSection('content'); ?>
            </div>

            <!-- Footer -->
            <?= $this->include('layouts/footer'); ?>
        </div>
        <!-- {% endif %} -->
    </div>
    <script>
        //Function Set Spinner Button
        const setLoading = function() {
            $(".delete-btn").attr("disabled", true)
            $(".btn-submit-form").attr("disabled", true)
        }

        const stopLoading = function() {
            $(".delete-btn").attr("disabled", false)
            $(".btn-submit-form").attr("disabled", false)
        }

        const formatNumber = function(el) {
            if (isNaN(el.value.replaceAll(",", ""))) {
                el.value = ""
            }
            el.value = Number(el.value.replaceAll(",", "")).toLocaleString()
            console.log(el.value)
            return true
        }
    </script>
    <!-- General JS Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js?v=<?= time(); ?>" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js?v=<?= time(); ?>" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url() ?>/assets/js/stisla.js?v=<?= time(); ?>"></script>
    <!-- Template JS File -->
    <script src="<?= base_url() ?>assets/js/scripts.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url() ?>assets/js/custom.js?v=<?= time(); ?>"></script>

</body>

</html>