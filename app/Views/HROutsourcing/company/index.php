<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>


<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>List Company Outsourcing</h1>
        <button class="btn btn-primary btn-add float-right" style="margin-right: 150px;" data-toggle="modal" data-target="#modalTipeKaryawan">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah Tipe Karyawan
        </button>
        <button class="btn btn-show-form btn-add float-right" data-btn="create-modal">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </button>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-start mb-3">
                <div class="col-sm-6 mt-2">
                    <div class="form-floating">
                        <select class="form-select filter_divisi_id" id="filter_divisi_id" name="filter_divisi_id">
                            <option value=""></option>
                            <?php foreach ($divisi as $d): ?>
                                <option value="<?= $d['id'] ?>"><?= $d['divisi'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label style="z-index: 1;">Departemen</label>
                    </div>
                </div>
                <div class="col-sm-6 mt-2">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search" id="search" name="search" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Data</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 10px;">No</th>
                                <th class="sort" style="width: 100px;" onclick="changeSort('hr_outsourcing_company.divisi_id')" class="sort">Departemen</th>
                                <th class="sort" onclick="changeSort('hr_outsourcing_company.name')">Company</th>
                                <th class="sort" style="width: 80px;">Total Karyawan</th>
                                <th class="sort" style="width: 90px;">Action</th>
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

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Company Outsourcing</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control name" id="name" name="name" placeholder="Nama Company" maxlength="30">
                                <label for="floatingInput">Company</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating">
                                <select class="form-select divisi_id" id="divisi_id" name="divisi_id">
                                    <option value=""></option>
                                    <?php foreach ($divisi as $d): ?>
                                        <option value="<?= $d['id'] ?>"><?= $d['divisi'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Departemen</label>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3 mb-3">
                             <label>Mesin Finger</label>
                            <select class="form-select ip_finger" id="ip_finger" name="ip_finger">
                                <option value=""></option>
                                <?php foreach ($dataAttendanceUnit as $d): ?>
                                    <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12 mt-3 mb-3">
                            <label>Alamat Company / Vendor</label>
                            <div class="form-floating" style="height: 50px;">
                                <textarea name="address" class="form-control address" id="address" placeholder="Alamat Perusahaan / Vendor" style="height: 100px;"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer mt-5">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Kembali</button>
                <button type="submit" class="btn btn-submit-form">Simpan</button>
                <button type="button" class="btn btn-discard delete-btn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalTipeKaryawan" tabindex="-1" role="dialog" aria-labelledby="modalTipeKaryawanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTipeKaryawanLabel">Manajemen Tipe Karyawan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
                <!-- Form Tambah Tipe Karyawan -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white">
                        <h6 class="mb-0"><i class="fa fa-plus mr-2"></i>Tambah Tipe Karyawan Baru</h6>
                    </div>
                    <div class="card-body">
                        <form id="formTambahTipeKaryawan">
                            <div class="form-group">
                                <label for="namaTipe">Nama Tipe Karyawan</label>
                                <input type="text" class="form-control" id="namaTipe" name="nama_tipe" required placeholder="Contoh: Kontrak, Tetap, Magang">
                                <div class="invalid-feedback" id="namaTipeError"></div>
                            </div>
                            <div class="form-group">
                                <label for="keterangan">Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="2" placeholder="Deskripsi singkat tipe karyawan"></textarea>
                            </div>
                            <button type="submit" class="btn btn-warning" id="btnSimpan">
                                <i class="fa fa-save mr-2"></i>Simpan
                            </button>
                            <button type="button" class="btn btn-secondary" id="btnReset">
                                <i class="fa fa-refresh mr-2"></i>Reset
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Tabel Daftar Tipe Karyawan -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fa fa-list mr-2"></i>Daftar Tipe Karyawan</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm" id="tableTipeKaryawan">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Tipe</th>
                                        <th>Keterangan</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyTipeKaryawan">
                                    <!-- Data akan dimuat via AJAX -->
                                    <tr>
                                        <td colspan="4" class="text-center">Memuat data...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times mr-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "hr_outsourcing_company.id";
    let sortType = "desc";

    const table = $('.dataTable').DataTable({
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
            url: "<?= base_url("hr-outsourcing-company/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.divisi_id = $(".filter_divisi_id").val();
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
            sortable: false,
            width: "5%"
        }, {
            data: "divisi",
        }, {
            data: "name",
        }, {
            data: "total",
            className: "text-center",
            searchable: false,
            sortable: false,
        }, {
            data: "id",
            className: "text-center actions",
            searchable: false,
            sortable: false,
            render: function(data, type, row) {
                let id = row.id;
                return `
                        <a class="btn btn-warning" href="<?= base_url(); ?>hr-outsourcing-company/employee/id/${id}" style="box-shadow: none !important;">
                            Data Karyawan
                        </a>
                       
                    `
            }
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

    $("#divisi_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('#filter_divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {

    });

    $('#ip_finger').select2({
        placeholder: "Pilih Mesin Finger",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {

    });

   

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('.add-modal')
    }).change(function() {

    });
    $("#filter_divisi_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $(document).ready(function() {


        // Variabel global untuk edit mode
        let editMode = false;
        let currentEditId = null;

        // Load data saat modal dibuka
        $('#modalTipeKaryawan').on('shown.bs.modal', function() {
            loadDataTipeKaryawan();
        });

        // Fungsi load data
        function loadDataTipeKaryawan() {
            $.ajax({
                url: 'hr-outsourcing-company/all-tipe-karyawan', // Sesuaikan endpoint
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $('#tbodyTipeKaryawan').html('<tr><td colspan="4" class="text-center">Memuat data...</td></tr>');
                },
                success: function(response) {
                    if(response.status === 'success' && response.data.length > 0) {
                        let html = '';
                        $.each(response.data, function(index, item) {
                            html += `
                                <tr id="row-${item.id}">
                                    <td>${index + 1}</td>
                                    <td>${item.nama_tipe}</td>
                                    <td>${item.keterangan || '-'}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary btn-edit" data-id="${item.id}" data-nama="${item.nama_tipe}" data-keterangan="${item.keterangan || ''}">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-hapus" data-id="${item.id}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        $('#tbodyTipeKaryawan').html(html);
                    } else {
                        $('#tbodyTipeKaryawan').html('<tr><td colspan="4" class="text-center">Belum ada data</td></tr>');
                    }
                },
                error: function(xhr) {
                    $('#tbodyTipeKaryawan').html('<tr><td colspan="4" class="text-center text-danger">Gagal memuat data</td></tr>');
                    console.error(xhr.responseText);
                }
            });
        }

        // Submit form (Create/Update)
        $('#formTambahTipeKaryawan').submit(function(e) {
            const csrf = $(`[name="${csrfToken}"]`);
            e.preventDefault();
            
            const formData = {
                nama_tipe: $('#namaTipe').val(),
                keterangan: $('#keterangan').val()
            };

            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            const url = editMode ? 
                'hr-outsourcing-company/update-tipe-karyawan' : 
                'hr-outsourcing-company/save-tipe-karyawan';
            const method = editMode ? 'POST' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: formData,
                dataType: 'json',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    $('#btnSimpan').prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-2"></i>Menyimpan...');
                },
                success: function(response) {
                    if(response.status === 'success') {
                        // Reset form
                        resetForm();
                        
                        // Reload data
                        loadDataTipeKaryawan();
                        
                        // Show success message
                        alert(response.message || 'Data berhasil disimpan!');
                        
                        // If not edit mode, you might want to stay in modal
                        if(!editMode) {
                            $('#namaTipe').focus();
                        }
                    }
                },
                error: function(xhr) {
                    if(xhr.status === 422) {
                        // Validation errors
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $(`#${key}`).addClass('is-invalid');
                            $(`#${key}Error`).text(value[0]);
                        });
                    } else {
                        alert('Terjadi kesalahan: ' + (xhr.responseJSON?.message || 'Server error'));
                    }
                },
                complete: function() {
                    $('#btnSimpan').prop('disabled', false).html('<i class="fa fa-save mr-2"></i>Simpan');
                }
            });
        });

        // Edit button click
        $(document).on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            const keterangan = $(this).data('keterangan');
            
            // Set form values
            $('#namaTipe').val(nama);
            $('#keterangan').val(keterangan);
            
            // Change mode
            editMode = true;
            currentEditId = id;
            
            // Change button text and focus
            $('#btnSimpan').html('<i class="fa fa-edit mr-2"></i>Update');
            $('#namaTipe').focus();
            
            // Scroll to form
            $('.modal-body').animate({
                scrollTop: 0
            }, 500);
        });

        // Delete button click
        $(document).on('click', '.btn-hapus', function() {
            if(!confirm('Apakah Anda yakin ingin menghapus data ini?')) return;
            
            const id = $(this).data('id');
            
            $.ajax({
                url: `hr-outsourcing-company/tipe-karyawan/${id}`,
                type: 'DELETE',
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success') {
                        // Remove row from table
                        $(`#row-${id}`).fadeOut(300, function() {
                            $(this).remove();
                            // Reload data to reorder numbers
                            loadDataTipeKaryawan();
                        });
                        alert(response.message || 'Data berhasil dihapus!');
                    }
                },
                error: function(xhr) {
                    alert('Gagal menghapus data: ' + (xhr.responseJSON?.message || 'Server error'));
                }
            });
        });

        // Reset form button
        $('#btnReset').click(function() {
            resetForm();
        });

        // Reset form function
        function resetForm() {
            $('#formTambahTipeKaryawan')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            editMode = false;
            currentEditId = null;
            $('#btnSimpan').html('<i class="fa fa-save mr-2"></i>Simpan');
        }

        // Reset form when modal is closed
        $('#modalTipeKaryawan').on('hidden.bs.modal', function() {
            resetForm();
        });

        var validator = $(".create-form").validate({
            rules: {
                name: {
                    required: true
                },
                divisi_id: {
                    required: true
                },
                address: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: "Company Wajib Diisi"
                },
                divisi_id: {
                    required: "Departemen Wajib Diisi"
                },
                address: {
                    required: "Alamat Wajib Diisi"
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

        $(".btn-show-form").click(function() {
            $(".id").val("");
            $(".title-name").text("Tambah");
            validator.resetForm();
            validator.reset();
            $(".create-form")[0].reset()
            $('.divisi_id').val(null).change();
            $(".delete-btn").css('display', 'none');
            $(".add-modal").modal("show")
        })

        $(".btn-hide-form").click(function() {
            $(".add-modal").modal("hide")
        })

        $(".dataTable_info").addClass("pt-0");

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', '');
            let id = data.id;
            $(".title-name").text("Update");

            validator.resetForm();
            validator.reset();

            $.ajax({
                url: "<?= base_url("hr-outsourcing-company/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".id").val(id);
                        $(".name").val(res.data.name);
                        $(".divisi_id").val(res.data.divisi_id).change();
                        $(".address").val(res.data.address);
                        $(".ip_finger").val(res.data.ip_finger).change();

                        $(".add-modal").modal("show")
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                    }
                }
            })
        })

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $(".filter_divisi_id").change(function() {
            table.ajax.reload();
        })

        $(".btn-submit-form").click(function() {
            if ($(".create-form").valid()) {
                Swal.fire({
                    icon: 'question',
                    title: 'Simpan Data?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Kembali',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const csrf = $(`[name="${csrfToken}"]`);
                        setLoading()
                        let data = new FormData(document.querySelector(".create-form"));

                        let id = $(".id").val();
                        // UPDATE
                        if (id) {
                            $.ajax({
                                url: "<?= base_url("hr-outsourcing-company/update"); ?>",
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

                            });
                        }
                        // CREATE
                        else {
                            $.ajax({
                                url: "<?= base_url("hr-outsourcing-company/save"); ?>",
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

                            });
                        }
                    }
                })
            }
        })

        $(".delete-btn").click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Hapus Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    let id = $(".id").val();
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("hr-outsourcing-company/delete"); ?>",
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
                    });
                }
            })
        })
    })

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