<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="mb-5 d-flex">
        <h4 style="padding-top: 6px; color: #3B4758;" class="m-0 font-weight-bold my-2">Management Hak Akses</h4>
        <div class="col px-0 py-2 text-right">
            <button type="button" id="submit-btn" class="btn btn-submit-form" onclick="submitForm()" disabled>Save</button>
        </div>
   </div>
   <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="mb-5">
            <div class="form-floating mb-2" style="height: 50px; width: 40%;">
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
        <div class="table-responsive" id="view_access" name="view_access" style="display: none">
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

<script>
    const csrfToken = '<?= csrf_token() ?>';
    
    $(document).ready(function() {
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
        document.getElementById("role_id").value ? document.getElementById("view_access").style = "" : document.getElementById("view_access").style = "display: none"
        // document.getElementById("create_1").checked = true;
        var tag_html = "";
        //"[\"c\", \"r\", \"u\", \"d\"]"

        if (document.getElementById("role_id").value) {
            $.ajax({
                url: "<?= base_url("akses/id"); ?>" + "/" + document.getElementById("role_id").value,
                method: "GET",
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
                                tag_html += "Employee";
                                tag_html += "</td>";
                                if (child.access.includes('c')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' checked name='create_" + child.menu_url_id + "'  id='create_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                if (!child.access.includes('c')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' name='create_" + child.menu_url_id + "'  id='create_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                if (child.access.includes('r')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' checked name='read_" + child.menu_url_id + "'  id='read_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                if (!child.access.includes('r')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' name='read_" + child.menu_url_id + "'  id='read_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                if (child.access.includes('u')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' checked name='update_" + child.menu_url_id + "'  id='update_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                if (!child.access.includes('u')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' name='update_" + child.menu_url_id + "'  id='update_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                if (child.access.includes('d')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' checked name='delete_" + child.menu_url_id + "'  id='delete_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                if (!child.access.includes('d')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' name='delete_" + child.menu_url_id + "'  id='delete_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                if (child.access.includes('p')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' checked name='print_" + child.menu_url_id + "'  id='print_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                if (!child.access.includes('p')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' name='print_" + child.menu_url_id + "'  id='print_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                if (child.access.includes('a')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' checked name='approve_" + child.menu_url_id + "'  id='approve_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                if (!child.access.includes('a')) {
                                    tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' name='approve_" + child.menu_url_id + "'  id='approve_" + child.menu_url_id + "'  /><span></span></label></td>";
                                }
                                tag_html += "<td style='text-align:center;'><label class='checkbox'><input type='checkbox' id='" + child.menu_url_id + "' onchange='checkAll(" + child.menu_url_id + ")' /><span></span></label></td>";
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
                            document.getElementById("view_access").style = "display: none";
                            $(".role_id").val('').trigger('change');

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