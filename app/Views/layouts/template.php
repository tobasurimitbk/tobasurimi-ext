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
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap1.min.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/all.css?v=<?= time(); ?>">

    <link href="<?= base_url() ?>assets/css/dataTables.bootstrap4.min.css?v=<?= time(); ?>" rel="stylesheet">
    <link href="<?= base_url() ?>assets/css/rowReorder.dataTables.min.css?v=<?= time(); ?>" rel="stylesheet">
    <link href="<?= base_url() ?>assets/css/responsive.dataTables.min.css?v=<?= time(); ?>" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url() ?>assets/css/select2-bootstrap-5-theme.min.css?v=<?= time(); ?>" />

    <!-- Template CSS -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/style.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/components.css?v=<?= time(); ?>">
    <link href="<?= base_url() ?>assets/css/select2.min.css?v=<?= time(); ?>" rel="stylesheet" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap-datepicker.standalone.min.css?v=<?= time(); ?>">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css?v=<?= time(); ?>" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <!-- <link href="<?= base_url(); ?>assets/_vendor/fontawesome-free/css/all.min.css?v=<?= time(); ?>" rel="stylesheet" type="text/css"> -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/all.min.css?v=<?= time(); ?>">

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

    <script src="<?= base_url(); ?>assets/js/sweetalert2@11.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url(); ?>assets/js/jquery.validate.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url(); ?>assets/js/bs-custom-file-input.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url(); ?>assets/js/jquery.inputmask.min.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url(); ?>assets/js/jquery.mask.min.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url(); ?>assets/js/imask.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url(); ?>assets/js/bootstrap-datepicker.min.js?v=<?= time(); ?>"></script>



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
            // Remove non-numeric characters
            let value = el.value.replace(/\D/g, '');

            // Format the value as a currency with commas
            if (value.length > 0) {
            const formatter = new Intl.NumberFormat('en-US', {
                currency: 'IDR',
                minimumFractionDigits: 2,
            });
            value = formatter.format(value / 100);
            }

            el.value = value;
        }

        var invalidChars = ["-", "e", "+", "E"];

        $("input[type='number']").on("keydown", function(e){ 
            if(invalidChars.includes(e.key)){
                e.preventDefault();
            }
        });

        const lettersOnly = function(event)
        {
            if (String.fromCharCode(event.keyCode).match(/[^0-9A-Za-z ]/g)) return false;
            // if (String.fromCharCode(event.keyCode).match(/[^0-9A-Za-z,-_/.() ]/g)) return false;
        }
    </script>
    <!-- General JS Scripts -->
    <script src="<?= base_url(); ?>assets/js/popper.min.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url(); ?>assets/js/bootstrap.min.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url(); ?>assets/js/jquery.nicescroll.min.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url() ?>/assets/js/stisla.js?v=<?= time(); ?>"></script>
    <!-- Template JS File -->
    <script src="<?= base_url() ?>assets/js/scripts.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url() ?>assets/js/custom.js?v=<?= time(); ?>"></script>

</body>

</html>