<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>User Detail</h1>
    </div>
    <div>
        <h4>Id : <?= $user['id'] ?> </h4>
        <h4>Nama : <?= $user['nama'] ?> </h4>
        <h4>Username : <?= $user['username'] ?> </h4>
        <h4>Role : <?= $user['id_role'] ?> </h4>
        <div>
            <button class="btn btn-success">Edit</button>
            <button class="btn btn-danger">Delete</button>
        </div>
    </div>
</section>

<?= $this->endSection(); ?>