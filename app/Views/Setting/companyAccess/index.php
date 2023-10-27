<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" id="add_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Company Access</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <input autocomplete="one-time-code" type="hidden" class="username" name="username" id="username" />
                    <input autocomplete="one-time-code" type="hidden" class="employee_id" name="employee_id" id="employee_id" />
                    <input autocomplete="one-time-code" type="hidden" class="status" name="status" id="status" />
                    <input autocomplete="one-time-code" type="hidden" class="password" name="password" id="password" />
                    <?= csrf_field() ?>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3 create-user" style="height: 50px;">
                                <select class="form-select user_id" name="user_id" id="user_id" onchange="changeUser()">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">User</label>
                            </div>
                            <div class="form-floating mb-3 edit-user" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control name" name="name" id="name">
                                <label for="floatingInput">User</label>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="col-subtitle-modal">
                    <div class="row mt-5">
                        <div class="col">
                            <button class="btn btn-show-detail btn-add btn-block float-right" data-btn="detail-modal" style="width: 106px;">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive mt-2">
                    <table class="table-inside table-borderd nowrap table-hover-tobasurimi" width="100%" cellspacing="0">
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
                <div class="d-flex">
                    <button type="button" class="btn btn-discard delete-btn delete-form">Hapus</button>
                </div>
                <label>&nbsp;</label>
                <div class="d-flex">
                    <button type="button" class="btn btn-hide-form btn-discard mr-2">Batal</button>
                    <button type="submit" class="btn btn-submit-form btn-submit-parent">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal detail-modal" tabindex="1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label class="title-detail-name"></label> Company dan Role</h5>
            </div>
            <div class="modal-body">
                <form class="detail-form" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id_detail" name="id_detail" id="id_detail" />
                    <div class="row mb-3">
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
            <div class="modal-footer justify-content-between">
                <div class="d-flex">
                    <button type="button" class="btn btn-discard delete-detail delete-btn">Hapus</button>
                </div>
                <label>&nbsp;</label>
                <div class="d-flex">
                    <button type="button" class="btn btn-hide-detail btn-discard mr-2">Batal</button>
                    <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Company Access</h1>
        <button class="btn btn-show-form btn-add float-right" data-btn="create-modal">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </button>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end mb-3">
                <div class="col-md-2">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Search" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th onclick="changeSort('name')" class="sort">User</th>
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
    let sort = "name";
    let sortType = "desc";

    let company_role = [];
    var row = 0;

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [1, 'asc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("company-access/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        // scrollX: true,
        "initComplete": function(settings, json) {
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
        }, {
            data: "name",
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
                    required: "User wajib diisi"
                },
                company_id: {
                    required: "Company wajib diisi"
                },
                role_id: {
                    required: "Role wajib diisi"
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
            highlight: function(element) {
                $(element).closest('.form-group').addClass('has-error');
                $(element).addClass('select-class');

            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error');
                $(element).removeClass('select-class');
            },
        });

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            $(".create-form")[0].reset()
            $(".delete-form").css('display', '');
            let id = data.id;
            $(".title-name").text("Update");

            $(".create-user").css("display", "none");
            $(".edit-user").css("display", "");

            $(".id").val(id);

            validator.resetForm();
            validator.reset();

            $.ajax({
                url: `<?= base_url("user/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".user_id").empty()
                    $(".user_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".user_id").append(`<option value="${item.id}">${item.name}</option>`)
                    })

                    $(".user_id").val(id).change();
                    $(".add-modal").modal("show")
                }
            })
        })

        $(".btn-show-detail").click(function() {
            let user_id = $(".id").val();
            if (user_id) {
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
                        res.data.forEach(function(item) {
                            $(".role_id").append(`<option value="${item.id}">${item.name}</option>`)
                        })

                        $(".role_id").val('').change();
                        $(".detail-modal").modal("show");
                    }
                })
            } else {
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
            $(".company_id").val("").change();
            $(".role_id").val("").change();
            $(".title-name").text("Tambah");

            $(".body-detail-table").empty()

            row = 0;

            list_address = [];

            validator.resetForm();
            validator.reset();
            $(".create-form")[0].reset()
            $(".delete-form").css('display', 'none');

            $.ajax({
                url: `<?= base_url("user/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".user_id").empty()
                    $(".user_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".user_id").append(`<option value="${item.id}">${item.name}</option>`)
                    })

                    $(".user_id").val("").change();
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

        $(".btn-submit-detail").click(function() {
            let id = $(".id_detail").val();
            let company_id = $(".company_id option:selected").val();
            let role_id = $(".role_id option:selected").val();
            let company_name = $(".company_id option:selected").text();
            let role_name = $(".role_id option:selected").text();

            // update detail
            if (id) {
                let validate_exist = true;

                company_role.map(item => {
                    if (item.row != id) {
                        if (item.company_id == company_id) {
                            validate_exist = false;
                        }
                    }
                })

                if (validate_exist) {
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
                                    if (item.row == id) {
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
                                    } else {
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
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Company Already Exist',
                        confirmButtonColor: '#4e73df',
                    })
                }
            }
            // create detail
            else {
                let validate_exist = true;

                company_role.map(item => {
                    if (item.company_id == company_id) {
                        validate_exist = false;
                    }
                })

                if (validate_exist) {
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
                } else {
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
            if (company_role.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: "Company Role Tidak Boleh Kosong",
                    confirmButtonColor: '#4e73df',
                })
            } else {
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

    $(document).on('show.bs.modal', '.detail-modal', function() {
        document.getElementById("add_modal").style = "display: block; z-index: 999 !important";
    })

    $(document).on('hide.bs.modal', '.detail-modal', function() {
        document.getElementById("add_modal").style = "display: block;";
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
                    if (item.row != id) {
                        tag_html += `<tr class="edit-table-detail" data-id ="${row + 1}" data-companyid ="${item.company_id}" data-roleid ="${item.role_id}">`;
                        tag_html += "<td>";
                        tag_html += item.company_name;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += item.role_name;
                        tag_html += "</td>";
                        tag_html += "</tr>";

                        new_company_role.push({
                            ...item,
                            row: row + 1
                        });

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
                res.data.forEach(function(item) {
                    $(".role_id").append(`<option value="${item.id}">${item.name}</option>`)
                })

                $(".role_id").val(role_id).change();
                $(".detail-modal").modal("show");
            }
        })
    })

    const changeUser = function() {
        let user_id = $(".user_id option:selected").val();
        $(".id").val(user_id);
        if (user_id) {
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
                    } else {
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

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }
</script>

<?= $this->endSection(); ?>