<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

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

<?php
if (session()->getFlashData("success")) {
?>
    Swal.fire({
        icon: 'success',
        title: '<?= session()->getFlashData("success"); ?>',
        confirmButtonColor: '#4e73df',
    })
<?php
}
?>
</script>

<?= $this->endSection(); ?>