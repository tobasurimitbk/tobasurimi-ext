<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="mb-5">
        <h4 style="padding-top: 6px; color: #3B4758;" class="m-0 font-weight-bold my-2">Customer</h4>
        <button class="btn btn-show-form btn-add btn-block" data-btn="create-modal" style="width: 176px;">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Create Customer
        </button>
    </div>
    <div class="mb-2">
        <h5 style="padding-top: 6px; color: #3B4758;" class="m-0 font-weight-bold my-2">= List Customer</h5>
        <input class="form-control" placeholder="Search" style="width: 30%" value="" />
    </div>
    <div class="table-responsive">
        <table class="table table-bordered nowrap table-hover-pbtc" id="dataTable" width="100%" cellspacing="0">
            <thead class="thead-dark">
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody class="body-table" id="body-table" style="cursor: pointer;">
                <?php
                if (!empty($dataCustomers)) {
                    foreach ($dataCustomers as $customer) {
                ?>
                        <tr class="row-table" data-id="<?= $customer->id; ?>">
                            <td><?= $customer->name; ?></td>
                            <td><?= $customer->address; ?></td>
                            <td><?= $customer->phone; ?></td>
                            <td><?= $customer->email; ?></td>
                        </tr>
                <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection(); ?>