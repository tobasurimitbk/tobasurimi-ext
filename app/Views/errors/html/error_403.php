<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    body {
        background-color: #f8f9fa;
    }

    .section {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 80vh;
    }

    p {
        font-size: large;
    }

    h1 {
        font-size: 100px;
    }

    .card {
        width: 100%;
        max-width: 400px;
    }

    .back-btn {
        padding: 10px;
        font-size: large;
    }
</style>
<section class="section">
    <div class="">
        <div class="card-body text-center">
            <h1 class="text-danger">403</h1>
            <p>Anda tidak punya akses untuk mengakses modul ini</p>
            <a href="/" class="btn btn-danger back-btn"><b style="margin-left: 30px; margin-right:30px;">Kembali</b></a>
        </div>
    </div>
</section>


<?= $this->endSection(); ?>