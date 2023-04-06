<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>User Data</h1>
    </div>
    <div>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Username</th>
                    <th scope="col">Role</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                <?php foreach ($user as $item) : ?>
                    <tr>
                        <th scope="row"><?= $i++; ?></th>
                        <td><?= $item['nama']; ?></td>
                        <td><?= $item['username']; ?></td>
                        <td><?= $item['id_role']; ?></td>
                        <td><a href="<?= base_url() ?>user/<?= $item['id'] ?>" class="btn btn-success">Detail</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?= $this->endSection(); ?>