<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Departemen & Jam Kerja</h1>
        <?php if (can('Personalia', 'Jam Kerja', 'c')) : ?>
            <a class="btn btn-show-form btn-add float-right" href="<?= base_url("jam-kerja/setting"); ?>">
                <i class="fa-solid fa-gears fa-sm mr-2"></i> Set Jam Kerja Karyawan
            </a>
        <?php endif; ?>
    </div>
    <?= csrf_field() ?>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end">
                <div class="col-md-3 mb-3">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search" id="search" name="search" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Data</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 10px;" class="sort">No</th>
                                <th onclick="changeSort('divisis.id')" class="sort">Departemen</th>
                                <th onclick="changeSort('jam_kerja.jenis')" class="sort">Jam Kerja Default</th>
                                <th onclick="changeSort('divisis.jam_kerja_id')" class="sort" style="width: 80px;">Status</th>
                                <th style="width: 100px;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal aturJamKerjaModal" id="aturJamKerjaModal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Atur Jam Kerja Default</h5>
            </div>
            <div class="modal-body">
                <form class="form-update-default-jamkerja" role="form" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="divisi_id" id="divisi_id">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control divisi" id="divisi" name="divisi" placeholder="Departemen" readonly>
                                <label for="floatingInput">Departemen</label>
                            </div>
                        </div>
                        <div class="col-sm-6">

                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select jam_kerja_id" id="jam_kerja_id" name="jam_kerja_id">
                                    <option value=""></option>

                                </select>
                                <label>Pilih Jam Kerja Default</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard btn-discard-atur-jamkerja mr-2">Kembali</button>
                <button type="button" class="btn btn-submit-form" id="btnSubmitAturJamKerja">Simpan</button>
            </div>
        </div>
    </div>
</div>


<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "divisis.divisi";
    let sortType = "asc";
    let trigger = true;

    $(document).ready(function() {
        $(".search").keyup(function() {
            table.ajax.reload();
        });
    });

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
            url: "<?= base_url("jam-kerja/all-divisi"); ?>",
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
            sortable: false,
            orderable: false,
        }, {
            data: "divisi",
            className: "text-left"
        }, {
            data: "jenis_shift",
            className: "text-left"
        }, {
            data: "total_jam_kerja",
            className: "text-left",
            render: function(data, type, row) {
                let total_jam_kerja = row.total_jam_kerja;
                let htmlRes = '';

                if (row.total_jam_kerja != 0) {
                    htmlRes += `
                            <div class="text-success">
                               <i class="fa-solid fa-check"></i>
                            </div>`
                } else {
                    htmlRes += `
                            <div class="text-danger">
                               <i class="fa-solid fa-x"></i>
                            </div>`
                }

                return htmlRes;

            }
        }, {
            data: "id",
            className: "text-center actions",
            searchable: false,
            sortable: false,
            render: function(data, type, row) {
                let id = row.id;
                let res = '';

                res += `
                    <?php if (can('Personalia', 'Jam Kerja', 'u')): ?>
                        <a href="javascript:void(0)" onclick="detail('${id}')" data-toggle="tooltip" title="Detail Jam Kerja" class="btn btn-warning">
                            <i class="fa-solid fa-stopwatch"></i>
                        </a>
                    <?php endif ?>
                    <?php if (can('Personalia', 'Jam Kerja', 'd')): ?>
                        <button data-toggle="tooltip" title="Atur Default Jam Kerja" onclick="atur('${id}')" class="btn btn-info">
                           <i class="fa-solid fa-user-clock"></i>
                        </button>
                    <?php endif ?>
                    `;

                return res;
            }
        }],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        "drawCallback": function(settings) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        },
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });


    $('#jam_kerja_id').select2({
        placeholder: "Pilih Jam Kerja Default Departemen",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#aturJamKerjaModal')
    }).change(function() {});

    //CSS SELECT2 FLOATING LABEL
    $('#jam_kerja_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('#jam_kerja_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('#jam_kerja_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('#jam_kerja_id')
        .parent('div')
        .find('label')
        .css('z-index', '1');


    var validator = $(".form-update-default-jamkerja").validate({
        rules: {
            jam_kerja_id: {
                required: true
            },
        },
        messages: {
            jam_kerja_id: {
                required: "Pilih jam kerja default"
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

    $('.btn-discard-atur-jamkerja').click(function(e) {
        e.preventDefault();
        $('#aturJamKerjaModal').modal('hide');
    });

    $('#btnSubmitAturJamKerja').click(function(e) {
        e.preventDefault();
        if ($('.form-update-default-jamkerja').valid()) {
            var jamKerjaId = $('#jam_kerja_id option:selected').val();
            var divisiId = $('#divisi_id').val();
            const formData = new FormData();
            const csrf = $(`[name="${csrfToken}"]`);

            formData.set('jam_kerja_id', jamKerjaId);
            formData.set('divisi_id', divisiId);

            $.ajax({
                url: "<?= base_url("jam-kerja/update-jamkerja-default"); ?>",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                data: formData,
                method: "POST",
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(response) {
                    csrf.val(response.token);
                    if (response.status) {
                        Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                            .then(() => {
                                $('#aturJamKerjaModal').modal('hide');
                                table.ajax.reload();
                            })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        })
                    }
                }
            });
        }
    })

    function detail(id) {
        location.replace(`<?= base_url("jam-kerja/divisi"); ?>/${id}`);
    }

    function atur(divisiId) {
        $.ajax({
            url: "<?= base_url("jam-kerja/dropdown-jamkerja"); ?>",
            data: {
                divisi_id: divisiId
            },
            beforeSend: function(xhr) {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            method: "GET",
            success: function(response) {
                if (response.status) {
                    resetForm();
                    var jamKerja = response.data.jamKerjaList;
                    var idSelected = response.data.id_selected;
                    var divisi = response.data.divisi;

                    var jamKerjaSelect = $("#jam_kerja_id");
                    $.each(jamKerja, function(index, data) {
                        var option = $("<option></option>")
                            .attr("value", data.id)
                            .text(data.jenis_shift);
                        jamKerjaSelect.append(option);
                    });
                    $('#jam_kerja_id').val(idSelected).change();
                    $('#divisi').val(divisi);
                    $('#divisi_id').val(divisiId);
                    $('#aturJamKerjaModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: response.message,
                        confirmButtonColor: '#4e73df',
                    });
                }
            }
        });
    }

    function resetForm() {
        $("#jam_kerja_id").empty();
        $('#divisi_id').val(null);
        $('#divisi').val(null);
        $('#jam_kerja_id').val(null);
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