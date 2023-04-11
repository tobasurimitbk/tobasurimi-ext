<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create User</h5>
            </div>
            <div class="modal-body">
                <form method="post">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 5opx;">
                                <input type="text" class="form-control username" id="username" name="username" placeholder="Username" maxlength="30">
                                <label for="floatingInput">Username</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control password" id="password" name="password" placeholder="Password" maxlength="30">
                                    <label for="floatingInput">Password</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <span style="border: 0px" class="input-group-text bg-white" id="basic-addon2" onclick="password_show_hide()">
                                        <i class="fas fa-eye d-none" id="show_eye"></i>
                                        <i class="fas fa-eye-slash" id="hide_eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 5opx;">
                                <input type="text" class="form-control name" id="name" name="name" placeholder="Name" maxlength="30">
                                <label for="floatingInput">Name</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 5opx;">
                                <select class="form-select" id="floatingSelect" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <option value="Manager">Manager</option>
                                    <option value="Accounting">Accounting</option>
                                </select>
                                <label for="floatingInput">Role</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 5opx;">
                                <select class="form-select" id="floatingSelect" aria-label="Floating label select example">
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
        <h4 style="padding-top: 6px; color: #3B4758;" class="m-0 font-weight-bold my-2">Management User</h4>
        <button onclick="showAddForm()" class="btn btn-add btn-block" data-btn="create-modal" style="width: 176px;">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Create User
        </button>
   </div>
   <div class="mb-2">
        <h5 style="padding-top: 6px; color: #3B4758;" class="m-0 font-weight-bold my-2">= List User</h5>
        <input class="form-control" placeholder="Search" style="width: 30%" value="" />
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

<script>
    const showAddForm = function(e) {
        $(".add-modal").modal("show")
    }

    const hideAddForm = function(e) {
        $(".add-modal").modal("hide")
    }

    const password_show_hide = function() {
        var x = document.getElementById("password");
        var show_eye = document.getElementById("show_eye");
        var hide_eye = document.getElementById("hide_eye");
        show_eye.classList.remove("d-none");
        if (x.type === "text") {
            x.type = "password";
            show_eye.style.display = "none";
            hide_eye.style.display = "block";
        } else {
            x.type = "text";
            show_eye.style.display = "block";
            hide_eye.style.display = "none";
        }
    }
</script>

<?= $this->endSection(); ?>