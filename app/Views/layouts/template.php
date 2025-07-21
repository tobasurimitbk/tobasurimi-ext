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
    <!-- <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.8/dist/sweetalert2.min.css" rel="stylesheet"> -->
    <!-- General CSS Files -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap1.min.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/all.css?v=<?= time(); ?>">

    <link href="<?= base_url() ?>assets/css/dataTables.bootstrap4.min.css?v=<?= time(); ?>" rel="stylesheet">
    <link href="<?= base_url() ?>assets/css/rowReorder.dataTables.min.css?v=<?= time(); ?>" rel="stylesheet">
    <link href="<?= base_url() ?>assets/css/responsive.dataTables.min.css?v=<?= time(); ?>" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url() ?>assets/css/select2-bootstrap-5-theme.min.css?v=<?= time(); ?>" />

    <!-- Template CSS -->
    <?php if (session()->get('theme') == 'dark'): ?>
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/style_dark.css?v=<?= time(); ?>">
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/components_dark.css?v=<?= time(); ?>">
    <?php else: ?>
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/style.css?v=<?= time(); ?>">
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/components.css?v=<?= time(); ?>">
    <?php endif; ?>

    <link href="<?= base_url() ?>assets/css/select2.min.css?v=<?= time(); ?>" rel="stylesheet" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap-datepicker.standalone.min.css?v=<?= time(); ?>">


    <link rel="stylesheet" href="<?= base_url() ?>assets/css/select2.min.css?v=<?= time(); ?>" crossorigin="anonymous" referrerpolicy="no-referrer">
    <!-- <link href="<?= base_url(); ?>assets/_vendor/fontawesome-free/css/all.min.css?v=<?= time(); ?>" rel="stylesheet" type="text/css"> -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/all.min.css?v=<?= time(); ?>">

    <link href="<?= base_url() ?>assets/css/css2.css?v=<?= time(); ?>" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/bootstrap-datetimepicker.min.css?v=<?= time(); ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/bootstrap-datetimepicker-standalone.css?v=<?= time(); ?>">


</head>
<?php $session = session(); ?>


<script>
    function greatFormatRupiah(x) {
        var min = false;
        // Pastikan x memiliki nilai yang valid sebelum memanggil toString
        if (x === null || x === undefined) {
            x = ""; // Berikan nilai default jika x null atau undefined
        }

        x = x.toString();
        if (x.includes("-")) {
            min = true;
        } else {
            min = false;
        }
        x = x.replace(/-/g, "");
        var parts = x.toString().split(".");
        parts[0] = parts[0].replace(/,/g, "");
        var bilangan = parts[0];

        if (parts[1] && parts[1] === "00") {
            parts.pop();
        }

        var number_string = bilangan.toString(),
            sisa = number_string.length % 3,
            rupiah = number_string.substr(0, sisa),
            ribuan = number_string.substr(sisa).match(/\d{3}/g);

        if (ribuan) {
            var separator = sisa ? "," : "";
            rupiah += separator + ribuan.join(",");
        }
        parts[0] = rupiah;
        if (min) {
            return "-" + parts.join(".");
        } else {
            return parts.join(".");
        }

    }

    function destroyFormatRupiah(x) {
        if (typeof x === "number") return x; // Jika sudah angka, langsung kembalikan
        if (!x) return 0; // Jika null, undefined, atau kosong, kembalikan 0

        let strValue = String(x); // Pastikan `x` jadi string agar aman saat `.includes()`
        let isNegative = strValue.includes("-"); // Cek apakah ada tanda negatif

        // Hapus semua karakter selain angka dan titik
        let cleaned = strValue.replace(/[^\d.]/g, "");

        // Hapus ".00" di akhir string jika ada
        let withoutDecimal = cleaned.replace(/\.00$/, "");

        let result = parseFloat(withoutDecimal) || 0; // Konversi string ke angka
        return isNegative ? -result : result; // Kembalikan negatif jika awalnya ada "-"
    }

    function greatFormatRupiahPayment(x) {
        if (x === null || x === undefined || x === "") {
            return ""; // Jika input tidak valid, kembalikan string kosong
        }

        let min = false;
        x = parseFloat(x); // Pastikan x dalam bentuk angka

        if (isNaN(x)) {
            return ""; // Jika bukan angka valid, kembalikan string kosong
        }

        if (x < 0) {
            min = true;
            x = Math.abs(x); // Ubah ke positif untuk pemrosesan
        }

        x = x.toFixed(2); // Bulatkan ke 2 angka desimal
        let parts = x.split("."); // Pisahkan angka desimal

        let bilangan = parts[0].replace(/,/g, "");
        let number_string = bilangan.toString();
        let sisa = number_string.length % 3;
        let rupiah = number_string.substr(0, sisa);
        let ribuan = number_string.substr(sisa).match(/\d{3}/g);

        if (ribuan) {
            let separator = sisa ? "," : "";
            rupiah += separator + ribuan.join(",");
        }

        parts[0] = rupiah; // Update bagian sebelum desimal

        return (min ? "-" : "") + parts.join(".");
    }

    function destroyFormatRupiahPayment(x) {
        if (typeof x !== "string") {
            return 0;
        }

        // Hilangkan semua koma (pemformatan ribuan)
        let number = x.replace(/,/g, ""); // Hapus koma

        // Jika berakhiran ".00", hapus bagian tersebut
        number = number.replace(/\.00$/, ""); // Hapus ".00" hanya di akhir angka

        return parseFloat(number);
    }
</script>


<body class="<?= $session->get('toggle');  ?>">
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/js-polyfills/0.1.43/polyfill.min.js" integrity="sha512-lvWiOP+aMKHllm4THsjzNleVuGOh0WGniJ3lgu/nvCbex1LlaQSxySUjAu/LTJw9FhnSL/PVYoQcckg1Q03+fQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> Bootstrap core JavaScript -->
    <!-- Scripts -->
    <script src="<?= base_url() ?>assets/js/jquery.min.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url() ?>assets/js/select2.full.min.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url() ?>assets/js/moment.min.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url() ?>assets/js/bootstrap-datetimepicker.min.js?v=<?= time(); ?>"></script>

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
    <script src="<?= base_url() ?>assets/js/loading.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url() ?>assets/js/JsLocalSearch.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url() ?>assets/_vendor/popper/popper.min.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url() ?>assets/_vendor/bootstrap/js/bootstrap.min.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url() ?>assets/js/pdfobject.min.js?v=<?= time(); ?>"></script>
    <!-- <script src="https://kit.fontawesome.com/6297a3e18a.js" crossorigin="anonymous"></script> -->
    <script src="<?= base_url() ?>assets/js/tinymce-jquery.min.js?v=<?= time(); ?>"></script>

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
        const isNumberKey = function(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }
        //Function Set Spinner Button
        const setLoading = function() {
            // $(".delete-btn").attr("disabled", true)
            // $(".btn-submit-form").attr("disabled", true)
            $.LoadingOverlay("show", {
                image: "",
                fontawesomeColor: "#222FCC",
                fontawesome: "fa fa-cog fa-spin"
            });
        }

        const stopLoading = function() {
            // $(".delete-btn").attr("disabled", false)
            // $(".btn-submit-form").attr("disabled", false)
            $.LoadingOverlay("hide", {
                image: "",
                fontawesomeColor: "#222FCC",
                fontawesome: "fa fa-cog fa-spin"
            });
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


        function formatRupiah(angka, prefix = "Rp ") {
            var number_string = angka.toString().replace(/[^,\d]/g, ""),
                split = number_string.split(","),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            // tambahkan titik jika yang di input sudah menjadi angka ribuan
            if (ribuan) {
                separator = sisa ? "." : "";
                rupiah += separator + ribuan.join(".");
            }

            rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
            return rupiah;
        }


        var invalidChars = ["-", "e", "+", "E"];

        $("input[type='number']").on("keydown", function(e) {
            if (invalidChars.includes(e.key)) {
                e.preventDefault();
            }
        });

        const lettersOnly = function(event) {
            if (String.fromCharCode(event.keyCode).match(/[^0-9A-Za-z ]/g)) return false;
            // if (String.fromCharCode(event.keyCode).match(/[^0-9A-Za-z,-_/.() ]/g)) return false;
        }

        $(document).on('select2:open', () => {
            setTimeout(() => {
                // Ambil semua search field, lalu fokus ke yang terakhir (paling baru di-inject)
                const searchFields = document.querySelectorAll('.select2-search__field');
                if (searchFields.length) {
                    searchFields[searchFields.length - 1].focus();
                }
            }, 5); // kasih delay sedikit agar DOM siap

        });

        $(document).ready(function() {
            $('.tiny').tinymce({
                height: 300,
                api_key: 'y9k86q7hldeb66uas38crgtorii72bzfadk84nmx0sazm2g1',
            });
        });
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