<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog modal-lg" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Departemen</h5>
            </div>
            <div class="modal-body">

                <form class="create-form" role="form" method="POST" enctype="multipart/form-data" onSubmit="return false">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control divisi" placeholder="Masukkan Divisi" id="divisi" name="divisi">
                                <label for="floatingInput">Departemen</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select" name="jam_kerja_id" required>
                                    <option value="">PILIH JAM KERJA</option>
                                    <?php foreach ($jamKerja as $j) : ?>
                                        <option value="<?= $j['id'] ?>">
                                            <?= $j['jenis'] ?>
                                        </option>
                                    <?php endforeach; ?>

                                </select>
                                <label for="floatingInput">Pilih Jam Kerja</label>
                            </div>
                        </div>
                    </div>
                    <?php if ($isGajiPokok == null || $isCadangan == null) : ?>
                        <div class="alert alert-danger mt-2 mb-2" role="alert">
                            KOMPONEN GAJI POKOK DAN KOMPONEN CADANGAN BELUM ADA
                        </div>
                    <?php endif; ?>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr style="text-align: center;">
                                    <th scope="col" style="width: 10px;"><input type="checkbox" id="parent"></th>
                                    <th style="width: 10px;">No</th>
                                    <th>Nama Komponen Gaji</th>
                                    <th>Nominal Awal</th>
                                </tr>
                            </thead>
                            <tbody class="body-table" id="body-table">
                                <?php $no = 1; ?>
                                <?php foreach ($tunjangan as $t) : ?>
                                    <?php if ($t['is_gaji_harian']) : ?>
                                        <input type="hidden" name="komponenGaji[]" value="<?= $t['id'] ?>">
                                    <?php endif; ?>
                                    <?php if ($t['is_cadangan']) : ?>
                                        <input type="hidden" name="komponenGaji[]" value="<?= $t['id'] ?>">
                                    <?php endif; ?>
                                    <tr style="text-align: center;">
                                        <td data-id="<?= $t['id'] ?>"><input name="komponenGaji[]" <?= $t['is_gaji_harian'] || $t['is_cadangan']  ? 'checked disabled' : '' ?> class="child" type="checkbox" value="<?= $t['id'] ?>"></td>
                                        <td><?= $no++; ?></td>
                                        <td style="font-weight: bold;" class="<?= ($t['tipe'] == "PLUS") ? "text-success" : "text-danger" ?>"><?= ($t['tipe'] == "PLUS") ? "(+) " . $t['name'] : "(-) " . $t['name']; ?></td>
                                        <td>
                                            <div class="form-floating" style="height: 50px;">
                                                <input required onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');" onchange="this.value = formatRupiah(this.value);" autocomplete="one-time-code" type="text" data-id="<?= $t['id'] ?>" name="<?= $t['id'] ?>" class="form-control target input-picker" value="Rp. 0,00">
                                                <label for="floatingInput"><?= ($t['tipe'] == "PLUS") ? "(+) " . $t['name'] : "(-) " . $t['name']; ?></label>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Kembali</button>
                <button type="button" class="btn btn-submit-form">Simpan</button>
                <button type="button" class="btn btn-discard delete-btn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Departemen</h1>
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
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable dataTableDivisi" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 10%;">No</th>
                                <th onclick="changeSort('divisi')" class="sort">Departemen</th>
                                <th onclick="changeSort('jam_kerja.jenis')" class="sort">Jam Kerja</th>
                                <th class="sort">Komponen Gaji</th>
                                <th class="sort">Total Bagian</th>
                                <th class="sort" style="width: 100px;">Action</th>
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
    let sort = "divisi";
    let sortType = "desc";

    const table = $('.dataTableDivisi').DataTable({

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
            url: "<?= base_url("divisi/all"); ?>",
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
                width: "2%"
            }, {
                data: "divisi",
                className: "text-center"
            }, {
                data: "jamKerja",
                className: "text-center"
            },
            {
                data: "komponenGaji",
                className: "text-center",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let htmlRes = '';

                    if (row.komponenGaji == "SUDAH DIATUR") {
                        htmlRes += `
                            <div class="text-success">
                               <b>SUDAH DIATUR</b>
                            </div>`
                    } else {
                        htmlRes += `
                            <div class="text-danger">
                               <b>BELUM DIATUR</b>
                            </div>`
                    }

                    return htmlRes;
                }
            },
            {
                data: "totalBagian",
                className: "text-center",
                searchable: false,
                sortable: false,
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    return `
                        <a class="btn btn-warning" href="<?= base_url(); ?>divisi/bagian/${id}" style="box-shadow: none !important;">
                            Set Bagian
                        </a>
                    `
                }
            }
        ],
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

    $(document).ready(function() {

        var validator = $(".create-form").validate({
            rules: {
                divisi: {
                    required: true
                },
                jam_kerja_id: {
                    required: true
                }
            },
            messages: {
                divisi: {
                    required: "Departemen wajib diisi"
                },
                jam_kerja_id: {
                    required: "Jam Kerja wajib diisi"
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
            validator.resetForm();
            validator.reset();
            $(".title-name").text("Tambah");
            $(".divisi").val('').change();
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', 'none');
            $(".add-modal").modal("show")
        })

        $(".btn-hide-form").click(function() {
            $(".add-modal").modal("hide")
        })

        $(".dataTable_info").addClass("pt-0");

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            $('.logo').rules('remove', 'required');
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', '');
            let id = data.id;
            $(".title-name").text("Update");

            $.ajax({
                url: "<?= base_url("divisi/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".id").val(id);
                        $(".divisi").val(res.data.divisi);
                        $("select[name='jam_kerja_id']").val(res.data.jam_kerja_id);
                        validator.resetForm();
                        validator.reset();

                        $("input[name='komponenGaji[]']").each(function() {
                            let komponenId = $(this).val();
                            let isSelected = res.komponenGaji.some(function(komponen) {
                                return komponen.id === komponenId;
                            });
                            if (isSelected) {
                                $(this).prop('checked', true);
                            }
                        });

                        $.each(res.komponenGaji, function(i, v) {
                            $("input[name=" + v.id + "]").val(formatRupiah(v.nominal || '0'));
                        });

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

        $(".btn-submit-form").click(function() {
            proses_form();
        })

        $(".divisi").keyup(function(event) {
            if (event.keyCode === 13) {

                proses_form();
                return false;
            }
        })

        function proses_form() {
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
                                url: "<?= base_url("divisi/update"); ?>",
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
                        else {
                            $.ajax({
                                url: "<?= base_url("divisi/save"); ?>",
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
                        url: "<?= base_url("divisi/delete"); ?>",
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
    })

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    $('#parent').click(function() {
        $('.child:not(:disabled)').prop('checked', this.checked);
    });

    $('.child').click(function() {
        if ($('.child:checked').length == $('.child').length) {
            $('#parent').prop('checked', true);
        } else {
            $('#parent').prop('checked', false);
        }
    });

    function formatRupiah(angka) {
        angka = angka.replace(/\./g, ',');
        angka = angka.replace(/[^\d,]/g, '');
        var parts = angka.split(',');
        var ribuan = parts[0];
        var desimal = parts[1] || '00';
        var reverse = ribuan.toString().split('').reverse().join('');
        var ribuanFormatted = reverse.match(/\d{1,3}/g).join('.').split('').reverse().join('');
        return 'Rp. ' + ribuanFormatted + ',' + desimal;
    }
</script>

<?= $this->endSection(); ?>