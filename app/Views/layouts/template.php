<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title><?= getenv("SITE_TITLE"); ?></title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css?v=<?= time(); ?>" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css?v=<?= time(); ?>" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <!-- Template CSS -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/css/style.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/css/components.css?v=<?= time(); ?>">
</head>

<body>
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
        {% endif %}
    </div>

    <!-- General JS Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js?v=<?= time(); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js?v=<?= time(); ?>" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js?v=<?= time(); ?>" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js?v=<?= time(); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url() ?>/template/assets/js/stisla.js?v=<?= time(); ?>"></script>

    <!-- Template JS File -->
    <script src="<?= base_url() ?>/assets/js/scripts.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url() ?>/assets/js/custom.js?v=<?= time(); ?>"></script>

    <!-- Page Specific JS File -->
    <script src="<?= base_url() ?>/assets/js/page/index-0.js?v=<?= time(); ?>"></script>

</body>

</html>