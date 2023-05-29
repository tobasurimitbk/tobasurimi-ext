<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="width: 1200px !important; max-width: 1200px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Company Access</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id" name="id" id="id" />
                    <input type="hidden" class="username" name="username" id="username" />
                    <input type="hidden" class="employee_id" name="employee_id" id="employee_id" />
                    <input type="hidden" class="status" name="status" id="status" />
                    <input type="hidden" class="password" name="password" id="password" />
                    <?= csrf_field() ?>
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <div class="form-floating mb-3 create-user" style="height: 50px;">
                                <select class="form-select user_id" id="edit_user" name="user_id" id="user_id" onchange="changeUser()">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($dataUser)) {
                                        foreach ($dataUser as $user) {
                                    ?>
                                            <option value="<?= $user->id; ?>"><?= $user->name; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">User</label>
                            </div>
                            <div class="form-floating mb-3 edit-user" style="height: 50px;">
                                <input type="text" readonly="true" class="form-control name" name="name" id="name">
                                <label for="floatingInput">User</label>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row mt-5">
                    <div class="col">
                        <button class="btn btn-show-detail btn-add btn-block float-right" data-btn="detail-modal" style="width: 106px;">
                            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Add New
                        </button>
                    </div>
                </div>
                <div class="table-responsive mt-2">
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
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-discard delete-form">Delete</button>
                <label>&nbsp;</label>
                <div class="d-flex">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3">Discard</button>
                    <button type="submit" class="btn btn-submit-form">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal detail-modal" tabindex="1">
    <div class="modal-dialog" style="width: 1200px !important; max-width: 1200px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label class="title-detail-name"></label>  Company And Role</h5>
            </div>
            <div class="modal-body">
                <form class="detail-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id_detail" name="id_detail" id="id_detail" />
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select company_id" name="company_id" id="company_id">
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
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select role_id" name="role_id" id="role_id">
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
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-discard delete-detail">Delete</button>
                <label>&nbsp;</label>
                <div class="d-flex">
                    <button type="button" class="btn btn-hide-detail btn-discard mr-3">Discard</button>
                    <button type="submit" class="btn btn-submit-detail">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="mb-5">
        <h4 style="padding-top: 6px; color: #3B4758;" class="m-0 font-weight-bold my-2">Company Access</h4>
        <button class="btn btn-show-form btn-add btn-block" data-btn="create-modal" style="width: 176px;">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Add New
        </button>
   </div>
    <div class="mb-2">
        <h5 style="padding-top: 6px; color: #3B4758;" class="m-0 font-weight-bold my-2">= List Company Access</h5>
        <input class="form-control search" placeholder="Search" style="width: 30%" value="" />
    </div>
    <div class="table-responsive">
        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
            <thead class="thead-dark">
                <tr>
                    <th>User</th>
                </tr>
            </thead>
            <tbody class="body-table" id="body-table" style="cursor: pointer;">

            </tbody>
        </table>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';

    let company_role = [];
    var row = 0;

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
                    required: "Company is Required"
                },
                role_id: {
                    required: "Role is Required"
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
        $('.user_id').select2({
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
        $(".user_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

        $(".user_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

        $(".user_id")
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
                user_id: {
                    required: true
                },
                company_id: {
                    required: true
                },
                role_id: {
                    required: true
                }
            },
            messages: {
                user_id: {
                    required: "User is Required"
                },
                company_id: {
                    required: "Company is Required"
                },
                role_id: {
                    required: "Role is Required"
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

        const table = $('.dataTable').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
            processing: true,
            serverSide: true,
            ordering: false,
            fixedHeader: true,
            lengthMenu: [
                [25],
                [25],
            ],
            pageLength: 25,
            ajax: {
                url: "<?= base_url("user/all-user-company"); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.search = $(".search").val();
                }
            },
            // scrollX: true,
            "initComplete": function (settings, json) {    
                $('.dataTables_length').empty();    
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show 25 Entries</label></div>"); 
                $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
            },
            //responsive: true,
            display: "stripe",
            searching: false,
            columns: [{
                data: "name",
                className: "text-left"
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

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', '');
            let id = data.id;
            $(".title-name").text("Update");

            $(".create-user").css("display", "none");
            $(".edit-user").css("display", "");

            $(".id").val(id);

            $(".user_id").val(id).change();

            validator.resetForm();
            validator.reset();
            $(".add-modal").modal("show")
        })

        $(".btn-show-detail").click(function() {
            let user_id = $(".id").val();
            if(user_id)
            {
                $(".title-detail-name").text("Add New")
                $(".delete-detail").css('display', 'none');
                $(".id_detail").val('')
                $(".company_id").val('').change()
                $(".role_id").val('').change()

                validator_detail.resetForm();
                validator_detail.reset();

                $(".detail-modal").modal("show")
            }
            else
            {
                Swal.fire({
                    icon: 'error',
                    title: 'User Wajib Dipilih',
                    confirmButtonColor: '#4e73df',
                })
            }
        })

        $(".btn-show-form").click(function() {
            $(".id").val("");
            $(".create-user").css("display", "");
            $(".edit-user").css("display", "none");
            $(".user_id").val("").change();
            $(".company_id").val("").change();
            $(".role_id").val("").change();
            $(".title-name").text("Add New");

            $(".body-detail-table").empty()

            row = 0;

            list_address = [];

            $(".body-detail-table").empty()

            validator.resetForm();
            validator.reset();
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', 'none');
            $(".add-modal").modal("show")
        })

        $(".btn-hide-detail").click(function() {
            $(".detail-modal").modal("hide")
        })

        $(".btn-hide-form").click(function() {
            $(".add-modal").modal("hide")
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

        $(".btn-submit-form").click(function() {
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
                            let data = new FormData(document.querySelector(".create-form"));

                            data.append("company_role", JSON.stringify(company_role))

                            let id = $(".id").val();

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
                                                $(".add-modal").modal("hide")
                                                table.ajax.reload()
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
            }
        })
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

        $(".company_id").val(company_id).change()
        $(".role_id").val(role_id).change()

        validator_detail.resetForm();
        validator_detail.reset();

        $(".detail-modal").modal("show")
    })

    const changeUser = function() {
        let user_id = $(".user_id option:selected").val();
        $(".id").val(user_id);
        if(user_id)
        {
            $.ajax({
                url: "<?= base_url("user/id"); ?>" + "/" + user_id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".name").val(res?.data?.name);
                        $(".username").val(res?.data?.username);
                        $(".employee_id").val(res?.data?.employee_id); 
                        $(".status").val(res?.data?.status);

                        $(".body-detail-table").empty()
                        row = 0;
                        company_role = []
                        let tag_html = "" 

                        res.data.company_role.map((item) => {
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
        }
    }
</script>

<?= $this->endSection(); ?>