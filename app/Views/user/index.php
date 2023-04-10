<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="mb-5">
        <h4 style="padding-top: 6px; color: #3B4758;" class="m-0 font-weight-bold my-2">Management User</h4>
        <button onclick="showAddForm()" class="btn btn-add btn-block" data-btn="create-modal" style="width: 176px;">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Create User
        </button>
   </div>
   <div class="mb-2">
        <h5 style="padding-top: 6px; color: #3B4758;" class="m-0 font-weight-bold my-2">= List User</h5>
        <input type="text" class="form-control" placeholder="Search" style="width: 30%" />
   </div>
   <div class="table-responsive">
        <table class="table table-bordered nowrap table-hover-pbtc" id="dataTable" width="100%" cellspacing="0">
            <thead class="thead-dark">
                <tr>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>johndoe</td>
                    <td>John Doe</td>
                    <td>Manager</td>
                    <td>Aktif</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create User</h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-discard" onclick="hideAddForm()">Discard</button>
                <button type="submit" class="btn btn-submit-form">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
    const showAddForm = function(e) {
        $(".add-modal").modal("show")
    }

    const hideAddForm = function(e) {
        $(".add-modal").modal("hide")
    }
</script>

<?= $this->endSection(); ?>