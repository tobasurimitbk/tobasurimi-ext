<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" id="add_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> User</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id" name="id" id="id" />
                    <!-- <input type="hidden" class="company_role" name="company_role" id="company_role" /> -->
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control username" id="username" name="username" placeholder="Username" maxlength="30">
                                <label for="floatingInput">Username</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input type="password" class="form-control password" id="password" name="password" placeholder="Password" maxlength="30">
                                    <label for="floatingInput">Password</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <span style="border: 0px" class="input-group-text bg-white" id="basic-addon2" onclick="password_show_hide()">
                                        <i class="fa fa-eye d-none" id="show_eye"></i>
                                        <i class="fa fa-eye-slash" id="hide_eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control name" id="name" name="name" placeholder="Name" maxlength="30">
                                <label for="floatingInput">Nama</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select employee_id" name="employee_id" id="employee_id">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Employee</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select status" name="status" id="status" aria-label="Floating label select example">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Non Aktif">Non Aktif</option>
                                </select>
                                <label for="floatingInput">Status</label>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="col-subtitle-modal">
                    <div class="row mt-3">
                        <div class="col">
                            <button class="btn btn-show-detail btn-add btn-block float-right" data-btn="detail-modal" style="width: 106px;">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive mt-2 mb-3">
                    <table class="table-inside nowrap table-hover-tobasurimi" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Company</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-2">Batal</button>
                    <button type="submit" class="btn btn-submit-form btn-submit-parent">Simpan</button>
                    <button type="button" class="btn btn-discard delete-form delete-btn">Hapus</button>
            </div>
        </div>
    </div>
</div>


<div class="modal detail-modal" tabindex="1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label class="title-detail-name"></label>  Company And Role</h5>
            </div>
            <div class="modal-body">
                <form class="detail-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id_detail" name="id_detail" id="id_detail" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select company_id" name="company_id" id="company_id">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Company</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select role_id" name="role_id" id="role_id">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Role</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-hide-detail btn-discard mr-2">Batal</button>
                    <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
                    <button type="button" class="btn btn-discard delete-detail delete-btn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1>User</h1>
    <button class="btn btn-show-form btn-add float-right" data-btn="create-modal">
        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
    </button>
</div>
<div class="card">
    <div class="card-body">
        <div class="row justify-content-end mb-3">
            <div class="col-md-4">
                <input class="form-control search form-out-search" placeholder="Ketik Username / Name" value="" />
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No.</th>
                            <th onclick="changeSort('username')" class="sort">Username</th>
                            <th onclick="changeSort('name')" class="sort">Nama</th>
                            <th onclick="changeSort('employeeName')" class="sort">Employee</th>
                            <th onclick="changeSort('status')" class="sort">Status</th>
                        </tr>
                    </thead>
                    <tbody class="body-table" id="body-table" style="cursor: pointer;">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "username";
    let sortType = "asc";

    let company_role = [];
    var row = 0;

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [[1, 'asc']],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("user/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        // scrollX: true,
        "initComplete": function (settings, json) {    
            $('.dataTables_length').empty();    
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>"); 
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
        },
        //responsive: true,
        display: "stripe",
        searching: false,
        columns: [{
            data: "no",
            className: "text-center",
            sortable: false
        }, 
        {
            data: "username",
            className: "text-center"
        },
        {
            data: "name",
            className: "text-center"
        },
        {
            data: "employeeName",
            className: "text-center"
        },
        {
            data: "status",
            className: "text-center"
        }],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    var validator_detail = $(".detail-form").validate({
        rules: {
            company_id: {
                required: true
            },
            role_id: {
                required: true
            }
        },
        messages: {
            company_id: {
                required: "Company wajib diisi"
            },
            role_id: {
                required: "Role wajib diisi"
            },
        },
        errorElement: 'span',
        errorClass: 'text-danger',
        errorPlacement: function(error, element) {
            var elem = $(element);
            if (elem.hasClass("select2-hidden-accessible")) {
                element = $("#select2-" + elem.attr("id") + "-container").parent();
                error.insertAfter(element);
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
            $(element).addClass('select-class');

        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
            $(element).removeClass('select-class');
        },
    });
    
    $(document).ready(function() {

        $('.employee_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        })

        $('.company_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content")
        })

        $('.role_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL

        $(".employee_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

        $(".employee_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

        $(".employee_id")
        .parent('div')
        .find('label')
        .css('z-index', '1');

        $(".company_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

        $(".company_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

        $(".company_id")
        .parent('div')
        .find('label')
        .css('z-index', '1');

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

        var validator = $(".create-form").validate({
            rules: {
                name: {
                    required: true
                },
                username: {
                    required: true
                },
                password: {
                    minlength: 6,
                    required: true
                }
            },
            messages: {
                name: {
                    required: "Nama wajib diisi"
                },
                username: {
                    required: "Username wajib diisi"
                },
                password: {
                    minlength: "Password minimal 6 karakter",
                    required: "Password wajib diisi"
                },
                employee_id: {
                    required: "Employee wajib diisi"
                }
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errorPlacement: function(error, element) {
                var elem = $(element);
                if (elem.hasClass("select2-hidden-accessible")) {
                    element = $("#select2-" + elem.attr("id") + "-container").parent();
                    error.insertAfter(element);
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
                $(element).addClass('select-class');                      

            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
                $(element).removeClass('select-class');   
            },
        });

        $(".btn-show-detail").click(function() {
            $(".title-detail-name").text("Tambah")
            $(".delete-detail").css('display', 'none');
            $(".id_detail").val('')

            validator_detail.resetForm();
            validator_detail.reset();

            $.ajax({
                url: `<?= base_url("company/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".company_id").empty()
                    $(".company_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".company_id").append(`<option value="${item.id}">${item.company}</option>`)
                    })

                    $(".company_id").val('').change();
                }
            })

            $.ajax({
                url: `<?= base_url("role/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".role_id").empty()
                    $(".role_id").append(`<option value=""></option>`)
                    res.data.data.forEach(function(item) {
                        $(".role_id").append(`<option value="${item.id}">${item.name}</option>`)
                    })

                    $(".role_id").val('').change();
                    $(".detail-modal").modal("show");
                }
            })
        })

        $(".btn-show-form").click(function() {
            $('.password').rules('add', {required: true});
            $('.employee_id').rules('add', {required: true});
            $(".id").val("");
            $(".title-name").text("Tambah");

            $(".company_role").val('');

            $(".company_id").val("").change();
            $(".role_id").val("").change();
            $(".body-detail-table").empty()

            validator.resetForm();
            validator.reset();
            $(".create-form")[0].reset()
            $(".delete-form").css('display', 'none');

            company_role = [];
            row = 0;

            $.ajax({
                url: `<?= base_url("employee/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".employee_id").empty()
                    $(".employee_id").val("").change()
                    $(".employee_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".employee_id").append(`<option value="${item.id}">${item.nip} - ${item.name}</option>`)
                    })
                    $(".add-modal").modal("show")
                }
            })
        })

        $(".btn-hide-detail").click(function() {
            $(".detail-modal").modal("hide")
        })

        $(".btn-hide-form").click(function() {
            $(".add-modal").modal("hide")
        })

        $(".dataTable_info").addClass("pt-0");

        $(".search").keyup(function () {
            table.ajax.reload();
        })

        $(".btn-submit-detail").click(function() {
            let id = $(".id_detail").val();
            let company_id = $(".company_id option:selected").val();
            let role_id = $(".role_id option:selected").val();
            let company_name = $(".company_id option:selected").text();
            let role_name = $(".role_id option:selected").text();

            // update detail
            if(id)
            {
                let validate_exist = true;

                company_role.map(item => {
                    if(item.row != id)
                    {
                        if(item.company_id == company_id)
                        {
                            validate_exist = false;
                        }
                    }
                })

                if(validate_exist)
                {
                    if ($(".detail-form").valid()) {
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
                                console.log(id)
                                let new_company_role = []
                                let tag_html = "";

                                row = 0;

                                $(".body-detail-table").empty()

                                company_role.map(item => {
                                    if(item.row == id)
                                    {
                                        tag_html += `<tr class="edit-table-detail" data-id ="${row + 1}" data-companyid ="${company_id}" data-roleid ="${role_id}">`;
                                        tag_html += "<td>";
                                        tag_html += company_name;
                                        tag_html += "</td>";
                                        tag_html += "<td>";
                                        tag_html += role_name;
                                        tag_html += "</td>";
                                        tag_html += "</tr>";

                                        new_company_role.push({
                                            row: item.row,
                                            company_id: company_id,
                                            role_id: role_id,
                                            company_name: company_name,
                                            role_name: role_name,
                                        });
                                    }
                                    else
                                    {
                                        tag_html += `<tr class="edit-table-detail" data-id ="${row + 1}" data-companyid ="${item.company_id}" data-roleid ="${item.role_id}">`;
                                        tag_html += "<td>";
                                        tag_html += item.company_name;
                                        tag_html += "</td>";
                                        tag_html += "<td>";
                                        tag_html += item.role_name;
                                        tag_html += "</td>";
                                        tag_html += "</tr>";

                                        new_company_role.push(item);
                                    }

                                    row = row + 1;
                                })

                                company_role = new_company_role;

                                $(".body-detail-table").append(tag_html)

                                $(".detail-modal").modal("hide")
                            }
                        })
                    }
                }
                else
                {
                    Swal.fire({
                        icon: 'error',
                        title: 'Company Already Exist',
                        confirmButtonColor: '#4e73df',
                    })
                }
            }
            // create detail
            else
            {
                let validate_exist = true;

                company_role.map(item => {
                    if(item.company_id == company_id)
                    {
                        validate_exist = false;
                    }
                })

                if(validate_exist)
                {
                    if ($(".detail-form").valid()) {
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
                                company_role.push({
                                    row: row + 1,
                                    company_id: company_id,
                                    role_id: role_id,
                                    company_name: company_name,
                                    role_name: role_name,
                                })
                                
                            let tag_html = "";
                                tag_html += `<tr class="edit-table-detail" data-id ="${row + 1}" data-companyid ="${company_id}" data-roleid ="${role_id}">`;
                                tag_html += "<td>";
                                tag_html += company_name;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += role_name;
                                tag_html += "</td>";
                                tag_html += "</tr>";
                                $(".body-detail-table").append(tag_html)
                                $(".detail-modal").modal("hide")
                                row = row + 1;
                            }
                        })
                    }
                }
                else
                {
                    Swal.fire({
                        icon: 'error',
                        title: 'Company Already Exist',
                        confirmButtonColor: '#4e73df',
                    })
                }
            }
        })

        $(".btn-submit-parent").click(function() {
            $(".detail-modal").modal("hide")

            // CHECK IF NO COMPANY ROLE
            if(company_role.length === 0)
            {
                Swal.fire({
                    icon: 'error',
                    title: "Company Role Tidak Boleh Kosong",
                    confirmButtonColor: '#4e73df',
                })
            }
            else
            {
                if ($(".create-form").valid()) {
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
                            setLoading()
                            let data = new FormData(document.querySelector(".create-form"));

                            data.append("current_company_id", company_role[0]["company_id"])
                            let new_company_role = [];
                            company_role.forEach((item) => {
                                new_company_role.push(
                                    {
                                        "company_id": item.company_id,
                                        "role_id": item.role_id
                                    }
                                )
                            })
                            data.append("company_role", JSON.stringify(new_company_role))

                            let id = $(".id").val();
                            // UPDATE
                            if(id)
                            {
                                $.ajax({
                                    url: "<?= base_url("user/update"); ?>",
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
                                            stopLoading()
                                            Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                table.ajax.reload()
                                                $(".add-modal").modal("hide")
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
                            // CREATE
                            else
                            {
                                $.ajax({
                                    url: "<?= base_url("user/save"); ?>",
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
                                            stopLoading()
                                            Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                table.ajax.reload()
                                                $(".add-modal").modal("hide")
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
                        }
                    })
                }
            }
        })

        $(".delete-form").click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Hapus Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    let id = $(".id").val();
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("user/delete"); ?>",
                        data: {
                            id: id
                        },
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        method: "POST",
                        dataType: "json",
                        success: function(response) {
                            csrf.val(response.token);
                            if (response.status) {
                                stopLoading()
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    table.ajax.reload()
                                    $(".add-modal").modal("hide")
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
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            $('.password').rules('remove', 'required');
            $('.employee_id').rules('remove', 'required');
            const data = table.row(this).data();
            
            $(".create-form")[0].reset()
            $(".delete-form").css('display', '');
            let id = data.id;
            $(".title-name").text("Update");

            validator.resetForm();
            validator.reset();

            $.ajax({
                url: "<?= base_url("user/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".id").val(id);
                        $(".name").val(res?.data?.name);
                        $(".username").val(res?.data?.username);
                        $(".status").val(res?.data?.status);

                        $(".body-detail-table").empty()
                        row = 0;
                        company_role = []
                        let tag_html = "" 

                        res?.company_role.map((item) => {
                            tag_html += `<tr class="edit-table-detail" data-id ="${row + 1}" data-companyid ="${item.company_id}" data-roleid ="${item.role_id}">`;
                            tag_html += "<td>";
                            tag_html += item.company_name;
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += item.role_name;
                            tag_html += "</td>";
                            tag_html += "</tr>";

                            company_role.push({
                                row: row + 1,
                                company_id: item.company_id,
                                role_id: item.role_id,
                                company_name: item.company_name,
                                role_name: item.role_name,
                            });

                            row = row + 1;
                        })

                        $(".body-detail-table").append(tag_html)

                        $.ajax({
                            url: `<?= base_url("employee/dropdown"); ?>`,
                            method: "GET",
                            dataType: "json",
                            success: function(result) {
                                $(".employee_id").empty()
                                $(".employee_id").append(`<option value=""></option>`)
                                result.data.forEach(function(item) {
                                    $(".employee_id").append(`<option value="${item.id}">${item.nip} - ${item.name}</option>`)
                                })
                                console.log("tes", res?.data?.employee_id)
                                $(".employee_id").val(res?.data?.employee_id).change();
                                $(".add-modal").modal("show")
                            }
                        })
                    }
                    else
                    {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                    }
                }
            })
        })
    })

    $(document).on('show.bs.modal','.detail-modal', function () {
       document.getElementById("add_modal").style = "display: block; z-index: 999 !important";
    })

    $(document).on('hide.bs.modal','.detail-modal', function () {
        document.getElementById("add_modal").style = "display: block;";
        $(".add-modal").css("overflow-y", "auto");
    })

    $(document).on('click', '.delete-detail', function() {
        let id = $(".id_detail").val()
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                console.log(id)
                let new_company_role = []
                let tag_html = "";

                row = 0;

                $(".body-detail-table").empty()

                company_role.map(item => {
                    if(item.row != id)
                    {
                        tag_html += `<tr class="edit-table-detail" data-id ="${row + 1}" data-companyid ="${item.company_id}" data-roleid ="${item.role_id}">`;
                        tag_html += "<td>";
                        tag_html += item.company_name;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += item.role_name;
                        tag_html += "</td>";
                        tag_html += "</tr>";

                        new_company_role.push({...item, row: row + 1});

                        row = row + 1;
                    }
                })

                company_role = new_company_role;

                $(".body-detail-table").append(tag_html)
                $(".detail-modal").modal("hide")

                $(".detail-modal").modal("hide")
            }
        })
    })

    $(document).on('click', '.edit-table-detail', function() {
        $(".title-detail-name").text("Update")
        $(".delete-detail").css('display', '');
        let company_id = $(this).data('companyid')
        let role_id = $(this).data('roleid')
        let id = $(this).data('id')

        $(".id_detail").val(id)

        validator_detail.resetForm();
        validator_detail.reset();

        $.ajax({
            url: `<?= base_url("company/dropdown"); ?>`,
            method: "GET",
            dataType: "json",
            success: function(res) {
                $(".company_id").empty()
                $(".company_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".company_id").append(`<option value="${item.id}">${item.company}</option>`)
                })

                $(".company_id").val(company_id).change();
            }
        })

        $.ajax({
            url: `<?= base_url("role/dropdown"); ?>`,
            method: "GET",
            dataType: "json",
            success: function(res) {
                $(".role_id").empty()
                $(".role_id").append(`<option value=""></option>`)
                res.data.data.forEach(function(item) {
                    $(".role_id").append(`<option value="${item.id}">${item.name}</option>`)
                })

                $(".role_id").val(role_id).change();
                $(".detail-modal").modal("show");
            }
        })
    })

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

    const changeSort = function(val) {
        if(sort !== val)
        {
            sortType = "asc";
            sort = val;
        }
        else
        {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }
</script>

<?= $this->endSection(); ?>