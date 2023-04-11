<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="width: 1200px !important; max-width: 1200px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Employee</h5>
            </div>
            <div class="modal-body">
                <form method="post">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="file" class="form-control employeeImg" id="employeeImg" name="employeeImg">
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 5opx;">
                                <input type="text" class="form-control nip" id="nip" name="nip" placeholder="Nip" maxlength="30">
                                <label for="floatingInput">NIP</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 5opx;">
                                <input type="text" class="form-control divisi" id="divisi" name="divisi" placeholder="Divisi" maxlength="30">
                                <label for="floatingInput">Divisi</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 5opx;">
                                <input type="text" class="form-control name" id="name" name="name" placeholder="Full Name" maxlength="30">
                                <label for="floatingInput">Full Name</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 5opx;">
                                <input type="text" class="form-control phone_no" id="phone_no" name="phone_no" placeholder="Phone" maxlength="30">
                                <label for="floatingInput">Phone</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 5opx;">
                                <input type="email" class="form-control email" id="email" name="email" placeholder="Email" maxlength="30">
                                <label for="floatingInput">Email</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 5opx;">
                                <input type="text" class="form-control address" id="address" name="address" placeholder="Address" maxlength="30">
                                <label for="floatingInput">Address</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 5opx;">
                                <input class="form-control dob" id="dob" name="dob" placeholder="Date of Birth" maxlength="30">
                                <label for="floatingInput">Date of Birth</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 5opx;">
                                <select class="form-select gender" name="gender" id="floatingSelect" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <option value="Pria">Pria</option>
                                    <option value="Wanita">Wanita</option>
                                </select>
                                <label for="floatingInput">Gender</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 5opx;">
                                <input type="text" class="form-control acc_no" id="acc_no" name="acc_no" placeholder="No. Rekening" maxlength="30">
                                <label for="floatingInput">No. Rekening</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 5opx;">
                                <select class="form-select status" name="status" id="floatingSelect" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                </select>
                                <label for="floatingInput">Status</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-discard" onclick="hideAddForm()">Discard</button>
                <button type="submit" class="btn btn-submit-form">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="mb-5">
        <h4 style="padding-top: 6px; color: #3B4758;" class="m-0 font-weight-bold my-2">Employee</h4>
        <button onclick="showAddForm()" class="btn btn-add btn-block" data-btn="create-modal" style="width: 176px;">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Create Employee
        </button>
   </div>
   <div class="mb-2">
        <h5 style="padding-top: 6px; color: #3B4758;" class="m-0 font-weight-bold my-2">= List Employee</h5>
        <input class="form-control" placeholder="Search" style="width: 30%" value="" />
   </div>
   <div class="table-responsive">
        <table class="table table-bordered nowrap table-hover-pbtc" id="dataTable" width="100%" cellspacing="0">
            <thead class="thead-dark">
                <tr>
                    <th>NIP</th>
                    <th>Full Name</th>
                    <th>Divisi</th>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php
                if (!empty($dataEmployee)) {
                    foreach ($dataEmployee as $employee) {
                ?>
                    <tr>
                        <td><?= $employee->nip; ?></td>
                        <td><?= $employee->name; ?></td>
                        <td></td>
                        <td><?= $employee->email; ?></td>
                        <td><?= $employee->status; ?></td>
                    </tr>
                <?php
                    }
                }
            ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(".dob").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })
    })

    const showAddForm = function(e) {
        $(".add-modal").modal("show")
    }

    const hideAddForm = function(e) {
        $(".add-modal").modal("hide")
    }
</script>

<?= $this->endSection(); ?>