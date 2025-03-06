<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>



<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Ambil Gaji</h1>
        <div class="col-button-tambah-spp">
            <?= csrf_field() ?>
            <button class="btn btn-show-form btn-save float-right btn-submit-form " style="margin-right: 10px;">
                Ambil Pinjaman
            </button>

        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="row justify-content-end row-col-page-list-attendance">
                <div class="col-6 mb-0">
                    <form action="<?= base_url('ambil-pinjaman-karyawan') ?>" class="kt-form kt-form--fit kt-margin-b-20" method="GET">
                        <select name="month" required id="month">
                            <?php for ($i = 1; $i <= 12; $i++) : ?>
                                <?php
                                $temp = (strlen($i) == 1) ? ("0" . $i) : $i;
                                $checked = ($month == $temp) ? "selected" : "";
                                ?>
                                <option value="<?= $temp; ?>" <?= $checked; ?>>
                                    <?= $temp; ?>
                                </option>
                            <?php endfor ?>
                        </select>
                        <select name="year" required id="year">
                            <?php
                            for ($i = date("Y") - 2; $i <= date("Y") + 2; $i++) :
                                $checked = ($year == $i) ? "selected" : "";
                            ?>
                                <option value="<?= $i; ?>" <?= $checked; ?>><?= $i; ?></option>
                            <?php endfor ?>
                        </select>
                        <button type="submit" class="btn btn-primary btn-brand--icon" id="kt_search">
                            <span>
                                <i class=" la la-print"></i>
                                <span>Cari</span>
                            </span>
                        </button>
                    </form>
                </div>
                <div class="col-6 mb-0">
                    <div class="clearfix" id="loadingSpinner">
                        <div class="spinner-border text-primary float-right" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row justify-content-start mb-3">
                <div class="col-md-4">
                    <div class="form-floating mt-1">
                        <select class="form-select" name="filterDivisiID" id="filterDivisiID" aria-label="Floating label select example">
                            <option value="">
                                Cari Departemen
                            </option>
                            <?php foreach ($divisi as $d) : ?>
                                <option value="<?= $d['id'] ?>">
                                    <?= $d['divisi']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Cari Departemen</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" name="filterGolongan" aria-label="Floating label select example">
                            <option value="">
                                Cari Tipe / Golongan
                            </option>
                            <?php foreach ($golongan as $g) : ?>
                                <option <?= @$_GET['golongan'] == $g['golongan_name'] ? "selected" : "" ?> value="<?= $g['golongan_name'] ?>">
                                    <?= $g['golongan_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Cari Tipe / Golongan</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mt-1">
                        <select class="form-select" name="filterEmployeeID" id="filterEmployeeID" aria-label="Floating label select example">
                            <option value="">
                                Cari Berdasarkan Nama Karyawan
                            </option>
                        </select>
                        <label for="floatingInput">Cari Berdasarkan Nama Karyawan</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;"><input type="checkbox" id="parent"></th>
                                <th>No</th>
                                <th onclick="changeSort('employees.name')" class="sort">Nama Karyawan</th>
                                <th onclick="changeSort('employees.tipe')" class="sort">Tipe/Gol</th>
                                <th onclick="changeSort('employees.division_id')" class="sort">Departemen</th>
                                <th onclick="changeSort('pinjaman_karyawan.start_date')">Detail Absen</th>
                                <th onclick="changeSort('pinjaman_karyawan.hadir')">Hadir</th>
                                <th onclick="changeSort('pinjaman_karyawan.tidak_hadir')">Tidak Hadir</th>
                                <th>Nominal</th>
                                <th>Status Pinjaman</th>
                                <th>Status Pengambilan</th>
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

    let sort = "nomor";
    let sortType = "desc";
    $('#loadingSpinner').hide();

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
            url: "<?= base_url("/ambil-pinjaman-karyawan/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.divisi_id = $("#filterDivisiID").val();
                data.bagian_id = $("#filterBagianID").val()
                data.employee_id = $("#filterEmployeeID").val();
                data.golongan = $("select[name='filterGolongan']").val();
                data.year = "<?= $year ?>";
                data.month = "<?= $month; ?>";
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
                data: "null",
                className: "text-center",
                sortable: false,
                width: "5%",
                render: function(data, type, row) {

                    var checkboxHTML = '<div class="form-check">' +
                        '<input data-id="' + row.id + '" autocomplete="one-time-code" class="form-check-input child is-ambil" type="checkbox"' + (row.is_ambil === "1" ? 'style="display:none" ' : '') + '>' +
                        '</div>';
                    return checkboxHTML;
                }
            },
            {
                data: "no",
                className: "text-center",
                sortable: false,
                width: "5%"
            },
            {
                data: "name",
                className: "text-center"
            },
            {
                data: "tipeGol",
                className: "text-center",
                width: "10%"
            },
            {
                data: "divisi",
                className: "text-center"
            },
            {
                data: "mulaiAbsen",
                className: "text-center",
                className: "text-center",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let startDate = row.mulaiAbsen;
                    let endDate = row.selesaiAbsen;
                    return startDate + ' - ' + endDate;
                }
            },
            {
                data: "hadir",
                className: "text-center"
            },
            {
                data: "tidakHadir",
                className: "text-center"
            },
            {
                data: "nominalPinjaman",
                className: "text-center",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let nominalPinjaman = row.nominalPinjaman;
                    let is_boleh_minjam = row.isBolehMinjam;
                    let status_pinjaman = row.statusPinjaman;
                    let id = row.id;
                    return greatFormatRupiah(nominalPinjaman);
                }
            },

            {
                data: "statusPinjaman",
                className: "text-center",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let is_boleh_minjam = row.isBolehMinjam;
                    let status_pinjaman = row.statusPinjaman;
                    let htmlRes = '';

                    if (is_boleh_minjam == 0) {
                        htmlRes += `
                            <div class="text-danger">
                                Tidak Diizinkan
                            </div>`
                    } else {
                        if (status_pinjaman == 0) {
                            htmlRes += `
                            <div class="text-warning">
                                Tidak Diambil
                            </div>`
                        } else {
                            htmlRes += `
                            <div class="text-success">
                                Diambil
                            </div>`
                        }
                    }

                    return htmlRes;
                }
            },
            {
                data: "status_pengambilan",
                className: "text-center",
                render: function(data, type, row) {

                    let htmlRes = '';

                    if (data == 'Sudah') {
                        htmlRes += `
                            <div class="text-success">
                                Sudah Diambil
                            </div>`
                    } else {

                        htmlRes += `
                            <div class="text-danger">
                                Belum Diambil
                            </div>`

                    }

                    return htmlRes;
                }
            },
        ],

        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Data pinjaman bulan <?= $month ?> tahun <?= $year ?> belum digenerate", // Change this line
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });
    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    $("#filterDivisiID").change(function() {
        table.ajax.reload();
    });
    $("#filterEmployeeID").change(function() {
        table.ajax.reload();
    });
    $("#filterBagianID").change(function() {
        table.ajax.reload();
    });

    // Generate Modal Show
    $('#generate').click(function(e) {
        e.preventDefault();
        $('#generateModal').modal('show');
    });

    $("select[name='filterGolongan']").select2({
        placeholder: "Cari Tipe/Golongan Pegawai",
        theme: "bootstrap-5",
        allowClear: true,
    });
    $("select[name='filterGolongan']").change(function() {
        table.ajax.reload();
    });


    // if on change divisi
    $('#employeeID').attr('disabled', true);
    $("#divisionID").on('change', function() {
        $("#employeeID").empty();
        if ($(this).val() == "") {
            $('#employeeID').attr('disabled', true);
        } else {
            $('#employeeID').attr('disabled', false);
        }
    });



    $(".btn-submit-form").click(function() {
        listChecked = [];
        var checkedCheckboxes = $(".child:checked");
        var dataIds = checkedCheckboxes.map(function() {
            return $(this).data("id");
        }).get();;
        if (dataIds.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Cheklist minimal satu data ',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        } else {

            dataIds.forEach(function(d) {
                var targetInputElement = $('input[data-id="' + d + '"].is-ambil');
                var targetValue = targetInputElement.prop('checked');
                var isChecked = targetValue ? '1' : '0';
                listChecked.push({
                    id: d,
                    isChecked: isChecked,
                });

            });



            var id = $('.id').val();
            const csrfToken = '<?= csrf_token() ?>';
            const csrf = $(`[name="${csrfToken}"]`);
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
                    let formData = new FormData();
                    formData.append("checkList", JSON.stringify(listChecked));
                    $.ajax({
                        url: "<?= base_url("/ambil-pinjaman-karyawan/check-ambil-pinjaman"); ?>",
                        data: formData,
                        beforeSend: function(xhr) {
                            setLoading();
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        complete: function() {
                            stopLoading();
                        },
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
                                        window.location.reload();
                                    })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                });
                            }
                        },
                    });

                }
            })

        }

    })



    // Get and Show
    // $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
    //     const data = table.row(this).data();
    //     location.replace(`<?= base_url("payroll/id"); ?>/${data.id}`);
    // });
    // function helper
    function formatRupiah(angka) {
        var reverse = angka.toString().split('').reverse().join('');
        var ribuan = reverse.match(/\d{1,3}/g);
        var formatted = ribuan.join('.').split('').reverse().join('');
        return 'Rp. ' + formatted;
    }

    // select2 divisi
    $("#filterBagianID").select2({
        placeholder: "Cari Bagian",
        theme: "bootstrap-5",
        allowClear: true,
    });
    $("#filterDivisiID").select2({
        placeholder: "Cari Departemen",
        theme: "bootstrap-5",
        allowClear: true,
    })

    $("#divisionGlobalID").select2({
        placeholder: "Cari Departemen",
        theme: "bootstrap-5",
        allowClear: true,

    });

    $("select[name='filterEmployeeID']").select2({
        placeholder: "Cari Berdasarkan Karyawan",
        theme: "bootstrap-5",
        allowClear: true,

    });

    $("select[name='filterDivisiID']").on('change', function(e) {
        e.preventDefault();
        const csrf = $(`[name="${csrfToken}"]`);
        var divisionID = $(this).val();

        var formData = new FormData();
        formData.append('divisionID', divisionID);

        $.ajax({
            url: "<?= base_url("payroll/employees"); ?>",
            data: formData,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            method: "POST",
            dataType: "json",
            processData: false,
            contentType: false,
            success: function(response) {
                csrf.val(response.token);
                var employeeSelect = $("select[name='filterEmployeeID']");
                employeeSelect.empty();
                employeeSelect.append($("<option></option>")
                    .attr("value", "")
                    .text("Silahkan pilih karyawan dahulu"));
                $.each(response.data, function(index, data) {
                    var option = $("<option></option>")
                        .attr("value", data.id)
                        .text(data.name);
                    employeeSelect.append(option);
                });

            },
            onError: function(response) {
                csrf.val(response.token);

            }
        });

    });
    // $('#filterDivisiID').change(function() {
    //     var divisi = $('#filterDivisiID option:selected').val();
    //     $.ajax({
    //         url: `<?= base_url('/payroll/getBagian'); ?>`,
    //         method: "GET",
    //         beforeSend: function() {
    //             setLoading();
    //         },
    //         complete: function() {
    //             stopLoading();
    //         },
    //         data: {
    //             divisi: divisi,
    //         },
    //         dataType: "json",
    //         success: function(res) {
    //             // APPEND TO DROPDOWN
    //             console.log(res);
    //             appendDropdownBagian(res.data);
    //         }
    //     });
    // });

    // function appendDropdownBagian(data) {
    //     $("#filterBagianID").empty()
    //     $("#filterBagianID").append(`<option value=""></option>`)
    //     data.forEach(function(item) {
    //         $("#filterBagianID").append(`<option value="${item.id}">${item.nama_bagian}</option>`)
    //     })
    // }

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.form-select')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    $("input[name='startDatePersonal'], input[name='startDateGlobal']").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });


    $("input[name='finishDatePersonal'], input[name='finishDateGlobal']").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#parent').click(function() {
        $('.child:not(:disabled)').prop('checked', this.checked);
    });
</script>

<?= $this->endSection(); ?>