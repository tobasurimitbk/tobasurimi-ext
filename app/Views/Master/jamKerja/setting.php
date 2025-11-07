<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Set Jam Kerja Karyawan</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("jam-kerja"); ?>">
                Kembali
            </a>
            <button class="btn btn-show-form btn-save float-right btn-submit">
                Update
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="#" method="post" id="formPost" class="mt-4 detail-form">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-sm-2">
                        <div class="input-group mb-3">
                            <div class="form-floating" style="height: 50px;">
                                <input type="text"
                                    id="tanggal"
                                    name="tanggal"
                                    class="form-control tanggal"
                                    placeholder="Tanggal"
                                    value="">
                                <label for="tanggal">Tanggal</label>
                            </div>
                            <div class="input-group-append" style="height:50px;">
                                <button disabled class="btn btn-secondary" type="button">
                                    <i class="fas fa-calendar-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select divisi_id" id="divisi_id" name="divisi_id">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option value="<?= $d['id'] ?>">
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select bagian_id" id="bagian_id" name="bagian_id">
                                <option value=""></option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Bagian</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select jam_kerja_id" id="jam_kerja_id" name="jam_kerja_id">
                                <option value=""></option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Jam Kerja Diterapkan</label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-lg" style="height: 45px; background-color:#B8522A; color:whitesmoke;" id="cari_karyawan">
                            <span style="font-size: 15px;">
                                <i class="fas fa-search"></i> Cari
                            </span>
                        </button>
                    </div>
                </div>

                <div class="col-subtitle-modal">
                    <div class="row mt-1">
                        <div class="col-md-12">
                            <label class="form-label font-weight-bold modal-sub-title">List Karyawan & Jam Kerja</label>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-end">
                    <div class="col-sm-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input value="" type=" text" class="form-control search" id="search" name="search" placeholder="Cari Data">
                                    <label for="floatingInput">Cari Data</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 10px;">No</th>
                                    <th style="width: 10px;">#</th>
                                    <th>Nip</th>
                                    <th>Karyawan</th>
                                    <th>Jam Kerja Sekarang</th>
                                </tr>
                            </thead>
                            <tbody class="body-detail-table" id="body-detail-table">

                            </tbody>
                            <tfoot class="foot-detail-table" id="foot-detail-table">
                                <tr>
                                    <td colspan="5" style="text-align: left;">Tidak ada data</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </div>
</section>


<script>
    let csrfToken = '<?= csrf_token() ?>';
    let listData = [];
    let listDataSelected = [];

    $("#tanggal").datepicker({
        placeholder: "Pilih Tanggal Mulai Log Absensi",
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });
    $("#divisi_id").select2({
        placeholder: "Cari Departemen",
        theme: "bootstrap-5",
    }).change(function(e) {
        e.preventDefault();
        dropdownBagian();
        dropdownJamKerja();
    });
    $('#bagian_id').select2({
        placeholder: "Cari Bagian",
        theme: "bootstrap-5",
    }).change(function(e) {
        e.preventDefault();
    });

    $('#jam_kerja_id').select2({
        placeholder: "Jam Kerja Diterapkan",
        theme: "bootstrap-5",
    }).change(function(e) {
        e.preventDefault();
    });

    $(document).on('change', '.child', function() {
        const id = $(this).val(); // ambil value dari checkbox
        const checked = $(this).is(':checked');

        // cari data di listData berdasarkan id
        const data = listData.find(item => item.id == id);

        if (checked) {
            // tambahkan ke listDataSelected jika belum ada
            if (!listDataSelected.find(item => item.id == id)) {
                listDataSelected.push(data);
            }
        } else {
            // hapus jika di-uncheck
            listDataSelected = listDataSelected.filter(item => item.id != id);
        }

    });

    $(document).on('keyup', '#search', function() {
        const keyword = $(this).val().toLowerCase().trim();

        // filter berdasarkan keyword
        const filtered = listData.filter(v =>
            v.nip.toLowerCase().includes(keyword) ||
            v.name.toLowerCase().includes(keyword) ||
            v.jenis.toLowerCase().includes(keyword) ||
            v.shift.toLowerCase().includes(keyword)
        );

        console.log(filtered);

        // redraw tabel dengan hasil filter
        drawTable(filtered);
    });


    var validator = $("#formPost").validate({
        rules: {
            tanggal: {
                required: true
            },
            divisi_id: {
                required: true
            },
            bagian_id: {
                required: true
            },
            jam_kerja_id: {
                required: true
            },
        },
        messages: {
            tanggal: {
                required: "pilih tanggal"
            },
            divisi_id: {
                required: "pilih departemen"
            },
            bagian_id: {
                required: "pilih bagian"
            },
            jam_kerja_id: {
                required: "pilih jam kerja"
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

    $('.btn-submit').click(function(e) {
        e.preventDefault();
        if ($('#formPost').valid()) {
            if (listDataSelected.length == 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Checklist karyawan yang ingin diubah jam kerjanya',
                    confirmButtonColor: '#4e73df',
                });
            } else {
                let csrf = $(`[name="${csrfToken}"]`);
                let formData = new FormData(document.getElementById('formPost'));
                let jamKerjaId = $('#jam_kerja_id').val();
                formData.append('jam_kerja_id', jamKerjaId);
                formData.append('listData', JSON.stringify(listDataSelected));

                $.ajax({
                    url: `<?= base_url("jam-kerja/update-jam-kerja-karyawan"); ?>`,
                    data: formData,
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    method: "POST",
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        csrf.val(result.token);
                        if (result.status) {
                            Swal.fire({
                                icon: 'success',
                                title: result.message,
                                confirmButtonColor: '#4e73df',
                            }).then((res) => {
                                if (res.isConfirmed) {
                                    var tanggal = $('#tanggal').val();
                                    var bagianId = $('#bagian_id').val();
                                    getListData(
                                        bagianId,
                                        tanggal
                                    );
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: result.message,
                                confirmButtonColor: '#4e73df',
                            });
                        }
                    }
                });
            }

        }
    })


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

    $('#cari_karyawan').click(function(e) {
        e.preventDefault();
        var tanggal = $('#tanggal').val();
        var divisiId = $('#divisi_id').val();
        var bagianId = $('#bagian_id').val();
        $('#search').val(null);

        if (tanggal == '') {
            Swal.fire({
                icon: 'error',
                title: 'Pilih tanggal',
                confirmButtonColor: '#4e73df',
            });
        } else if (divisiId == '') {
            Swal.fire({
                icon: 'error',
                title: 'Pilih departemen',
                confirmButtonColor: '#4e73df',
            });
        } else if (bagianId == '') {
            Swal.fire({
                icon: 'error',
                title: 'Pilih bagian',
                confirmButtonColor: '#4e73df',
            });
        } else {
            getListData(bagianId, tanggal);
        }
    });

    function getListData(bagianId, tanggal) {
        $.ajax({
            url: `<?= base_url("jam-kerja/get-jam-kerja-karyawan"); ?>`,
            data: {
                bagian_id: bagianId,
                tanggal: tanggal
            },
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            success: function(result) {
                if (result.status) {
                    listData = result.data;
                    listDataSelected = [];
                    drawTable(listData);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: result.message,
                        confirmButtonColor: '#4e73df',
                    });
                }
            }
        });
    }

    function drawTable(dataList) {
        const table = $('#dataTable');
        table.find('tbody').empty();
        table.find('tfoot').empty();

        if (dataList.length === 0) {
            const newRow = $('<tr>');
            newRow.append($('<td colspan="5">').text("Tidak Ada Data"));
            table.find('tfoot').append(newRow);
            return;
        }

        let no = 1;
        $.each(dataList, function(i, v) {
            // cek apakah data ini sudah ada di listDataSelected
            const isChecked = listDataSelected.some(item => item.id == v.id);

            const newRow = $('<tr style="color:whitesmoke;">');
            newRow.append($('<td>').text(no++));
            newRow.append($('<td>').html(`
      <input type="checkbox" class="row-check child" value="${v.id}" ${isChecked ? 'checked' : ''}>
    `));
            newRow.append($('<td>').text(v.nip));
            newRow.append($('<td>').text(v.name));
            newRow.append($('<td>').text(v.jenis + " - " + v.shift));
            table.find('tbody').append(newRow);
        });
    }


    function dropdownBagian() {
        let divisiId = $('#divisi_id option:selected').val();
        let csrf = $(`[name="${csrfToken}"]`);
        var formData = new FormData();
        formData.append('divisionID', divisiId);
        $.ajax({
            url: `<?= base_url("list-attendance/get-bagian"); ?>`,
            data: formData,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            method: "POST",
            dataType: "json",
            processData: false,
            contentType: false,
            success: function(result) {
                csrf.val(result.token);
                $("select[name='bagian_id']").empty()
                $("select[name='bagian_id']").append(`<option value=""></option>`)
                result.data.forEach(function(item) {
                    $("select[name='bagian_id']").append(`<option value="${item.id}">${item.kode_bagian.toUpperCase()} - ${item.nama_bagian.toUpperCase()}</option>`)
                });
            }
        });
    }

    function dropdownJamKerja() {
        let divisiId = $('#divisi_id option:selected').val();
        $.ajax({
            url: `<?= base_url("jam-kerja/get-by-divisi"); ?>`,
            data: {
                divisi_id: divisiId
            },
            method: "GET",
            success: function(result) {
                $("select[name='jam_kerja_id']").empty()
                $("select[name='jam_kerja_id']").append(`<option value=""></option>`)
                result.data.forEach(function(item) {
                    $("select[name='jam_kerja_id']").append(`<option value="${item.id}">${item.jenis} - ${item.shift}</option>`)
                });
            }
        });
    }
</script>

<?= $this->endSection(); ?>