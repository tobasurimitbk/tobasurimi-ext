<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1>Manajemen Hak Akses</h1>
    <button class="btn btn-show-form btn-add float-right" id="submit-btn" onclick="submitForm()" disabled>
        Simpan
    </button>
</div>
<div class="card">
    <div class="card-body">
        <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row mb-5">
                <div class="col-md-4">
                    <div class="form-floating mb-2" style="height: 50px;">
                        <select class="form-control company_id" name="company_id" id="company_id" onchange="setChanges()">
                            <option value=""></option>
                            <?php
                                if (!empty($dataCompany)) {
                                    foreach ($dataCompany as $company) {
                                ?>
                                        <option value="<?= $company->id; ?>"><?= $company->company; ?></option>
                                <?php
                                    }
                                }
                            ?>
                        </select>
                        <label for="floatingInput">Company</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-2" style="height: 50px;">
                        <select class="form-control role_id" name="role_id" id="role_id" onchange="setChanges()">
                            <option value=""></option>
                            <?php
                            if (!empty($dataRole)) {
                                foreach ($dataRole as $role) {
                            ?>
                                <option value="<?= $role->id; ?>"><?= $role->name; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <label for="floatingInput">Role</label>
                    </div>
                </div>
            </div>
            <div class="table-responsive view_access" id="view_access" name="view_access" style="display: none">
                <table style="overflow-x: scroll;" class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 200px;">Menu</th>
                            <th class="text-center" width="60">Create</th>
                            <th class="text-center" width="60">Read</th>
                            <th class="text-center" width="60">Update</th>
                            <th class="text-center" width="60">Delete</th>
                            <th class="text-center" width="60">Print</th>
                            <th class="text-center" width="60">Approval</th>
                            <th class="text-center" width="60">All</th>
                        </tr>
                    </thead>
                    <tbody class="body-akses" id="body-akses">

                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    
    $(document).ready(function() {
        $('.company_id').select2({
            placeholder: "",
            allowClear: true,
            theme: "bootstrap-5",
            //dropdownParent: $(".add-modal .modal-content")
        })

        $('.company_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

        $('.company_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

        $('.company_id')
        .parent('div')
        .find('label')
        .css('z-index', '1');

        $('.role_id').select2({
            placeholder: "",
            allowClear: true,
            theme: "bootstrap-5",
            //dropdownParent: $(".add-modal .modal-content")
        })

        $(".role_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

        $(".role_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

        $(".role_id")
        .parent('div')
        .find('label')
        .css('z-index', '1');
    })

    const setChanges = function() {
        $(".role_id").val() && $(".company_id").val() ? $(".view_access").css("display", "") : $(".view_access").css("display", "none")
        // document.getElementById("create_1").checked = true;
        var tag_html = "";
        //"[\"c\", \"r\", \"u\", \"d\"]"

        if ($(".role_id").val() && $(".company_id").val()) {
            $.ajax({
                url: "<?= base_url("akses/id"); ?>",
                method: "GET",
                data: {
                    role_id: $(".role_id").val(), 
                    company_id: $(".company_id").val()
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        var list = res.data;
                        $(".body-akses").empty();
                        list.forEach((item) => {
                            tag_html += "<tr>";
                            tag_html += "<td colspan='8'>";
                            tag_html += "<label class='form-header'>" + item.menuName + "</label>";
                            tag_html += "</td>";
                            tag_html += "</tr>";
                            item.child.forEach((child) => {
                                tag_html += "<tr>";
                                tag_html += "<td>";
                                tag_html += child.name;
                                tag_html += "<input type='hidden' value='" + item.menu_url_id + "' name='parent_" + child.menu_url_id + "'  id='parent_" + child.menu_url_id + "'  />";
                                tag_html += "</td>";
                                if (child.access.includes('c')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' class='checkbox-round' checked name='create_" + child.menu_url_id + "'  id='create_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                if (!child.access.includes('c')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' class='checkbox-round' name='create_" + child.menu_url_id + "'  id='create_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                if (child.access.includes('r')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' class='checkbox-round' checked name='read_" + child.menu_url_id + "'  id='read_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                if (!child.access.includes('r')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' class='checkbox-round' name='read_" + child.menu_url_id + "'  id='read_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                if (child.access.includes('u')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' class='checkbox-round' checked name='update_" + child.menu_url_id + "'  id='update_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                if (!child.access.includes('u')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' class='checkbox-round' name='update_" + child.menu_url_id + "'  id='update_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                if (child.access.includes('d')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' class='checkbox-round' checked name='delete_" + child.menu_url_id + "'  id='delete_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                if (!child.access.includes('d')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' class='checkbox-round' name='delete_" + child.menu_url_id + "'  id='delete_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                if (child.access.includes('p')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' class='checkbox-round' checked name='print_" + child.menu_url_id + "'  id='print_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                if (!child.access.includes('p')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' class='checkbox-round' name='print_" + child.menu_url_id + "'  id='print_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                if (child.access.includes('a')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' class='checkbox-round' checked name='approve_" + child.menu_url_id + "'  id='approve_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                if (!child.access.includes('a')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' class='checkbox-round' name='approve_" + child.menu_url_id + "'  id='approve_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' class='checkbox-round' id='" + child.menu_url_id + "' onchange='checkAll(" + child.menu_url_id + ")' /><span></span></label></td>";
                                tag_html += "</tr>";
                            })
                        })
                        $(".body-akses").append(tag_html);
                        document.getElementById('submit-btn').removeAttribute("disabled");
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                    }
                },
                onError: function(err) {
                    alert("Eror")
                }
            });
        } else {
            document.getElementById('submit-btn').setAttribute("disabled", "disabled");
        }
    }

    const checkAll = function(id) {
        console.log(id)
        let checked = document.getElementById(id).checked;

        if(checked === true)
        {
            document.getElementById("create_" + id).checked = true;
            document.getElementById("read_" + id).checked = true;
            document.getElementById("update_" + id).checked = true;
            document.getElementById("delete_" + id).checked = true;
            document.getElementById("print_" + id).checked = true;
            document.getElementById("approve_" + id).checked = true;
        }
        else
        {
            document.getElementById("create_" + id).checked = false;
            document.getElementById("read_" + id).checked = false;
            document.getElementById("update_" + id).checked = false;
            document.getElementById("delete_" + id).checked = false;
            document.getElementById("print_" + id).checked = false;
            document.getElementById("approve_" + id).checked = false;
        }
    }

    const submitForm = function() {
        Swal.fire({
            icon: 'question',
            title: 'Simpan Data?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                let data = new FormData(document.querySelector(".create-form"));

                setLoading()

                $.ajax({
                    url: "<?= base_url("akses/save"); ?>",
                    data: data,
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    method: "POST",
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        csrf.val(response.token);
                        if (response.status) {
                            document.getElementById('submit-btn').setAttribute("disabled", "disabled");
                            $(".body-akses").empty();
                            $(".view_access").css("display", "none")
                            $(".role_id").val('').trigger('change');
                            $(".company_id").val('').trigger('change');

                            stopLoading()
                            
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                            stopLoading()
                        }
                    },
                    onError: function(response) {
                        csrf.val(response.token);
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Gagal Disimpan, coba Lagi',
                            confirmButtonColor: '#4e73df',
                        })
                        stopLoading()
                    }
                });
            }
        })
    }
</script>

<?= $this->endSection(); ?>