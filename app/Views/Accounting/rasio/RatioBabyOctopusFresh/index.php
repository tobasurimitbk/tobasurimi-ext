<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Ratio Baby Octopus Fresh</h1>
        <?php if (can("Accounting", "Ratio Baby Octopus Fresh", "c")) : ?>
            <a href="<?= base_url('ratio-baby-octopus-fresh/create') ?>" type="button" class="btn btn-show-form btn-add float-right">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" class="form-control input-picker tanggal_awal" id="tanggal_awal" name="tanggal_awal" placeholder="Tanggal Awal Dibuat" value="<?= !empty($rasio) ? date('m/Y', strtotime($rasio->bulan)) : "" ?>">
                                <label for="floatingInput">Tanggal Dibuat</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" class="form-control input-picker tanggal_akhir" id="tanggal_akhir" name="tanggal_akhir" placeholder="Tanggal Akhir Dibuat" value="<?= !empty($rasio) ? date('m/Y', strtotime($rasio->bulan)) : "" ?>">
                                <label for="floatingInput">Tanggal Dibuat</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 ">
                    <div class="form-floating">
                        <select class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php foreach ($dataDivisi as $divisi) : ?>
                                <option value="<?= $divisi["id"]; ?>"><?= strtoupper($divisi["divisi"]); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label style="z-index: 1;">Department</label>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search" id="search" name="search" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari</label>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th>Department</th>
                                <th>Tanggal Awal</th>
                                <th>Tanggal Akhir</th>
                                <th>Total Harga</th>
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
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label></h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data" onSubmit="return false">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating " style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control" placeholder="Nama Department" id="parentName" name="parentName">
                                <label for="floatingInput">Nama Department</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating " style="height: 50px;">
                                        <select class="form-select akun_ap_id" name="akun_ap_id" id="akun_ap_id">
                                            <option value=""></option>
                                            <?php
                                            if (!empty($subAkuns)) {
                                                foreach ($subAkuns as $sub) {
                                            ?>
                                                    <option value="<?= $sub->id; ?>"><?= $sub->no_sub; ?> <?= $sub->nama_sub; ?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <label for="floatingInput">AP</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating " style="height: 50px;">
                                        <select class="form-select akun_ar_id" name="akun_ar_id" id="akun_ar_id">
                                            <option value="" data-code=""></option>
                                            <?php
                                            if (!empty($subAkuns)) {
                                                foreach ($subAkuns as $sub_ar) {
                                            ?>
                                                    <option value="<?= $sub_ar->id; ?>"><?= $sub_ar->no_sub; ?> <?= $sub_ar->nama_sub; ?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <label for="floatingInput">AR</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Kembali</button>
                <button type="button" class="btn btn-submit-form">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    let sort = "nomor";
    let sortType = "desc";
    $(document).ready(function() {
        const csrfToken = '<?= csrf_token() ?>';
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
                url: "<?= base_url("ratio-baby-octopus-fresh/all"); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.search = $(".search").val();
                    data.tanggal_awal = $(".tanggal_awal").val();
                    data.tanggal_akhir = $(".tanggal_akhir").val();
                    data.divisi_id = $(".divisi_id").val();
                    data.sort = sort;
                    data.sortType = sortType;
                }
            },
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 '>Show <b class='entries-label'>25</b> Entries</label></div>");
                $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            display: "stripe",
            searching: false,
            columns: [{
                data: "no",
                className: "text-center",
                sortable: false,
                width: "5%"
            }, {
                data: "divisi",
                className: "text-center",
            }, {
                data: "tanggal_awal",
                className: "text-center",
            }, {
                data: "tanggal_akhir",
                className: "text-center",
            }, {
                data: "harga",
                className: "text-center",
            }, ],
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

        $(".search").keyup(function() {
            table.ajax.reload();
        });

        $("#tanggal_awal, #tanggal_akhir").change(function() {
            var tanggal_awal = $("#tanggal_awal").val();
            var tanggal_akhir = $("#tanggal_akhir").val();
            if (tanggal_awal != "" && tanggal_akhir != "") {
                table.ajax.reload();
            }
        });

        $(".divisi_id").change(function() {
            table.ajax.reload();
        });
        // hide modal
        $('.btn-discard').click(function() {
            $('.add-modal').modal('hide');
        });
        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            console.log(data);
            location.replace(`<?= base_url("ratio-baby-octopus-fresh/id"); ?>/${data.id}`);
        });
    });
    // sort
    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    $("#tanggal_awal, #tanggal_akhir").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    })

    $("#divisi_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');
</script>

<?= $this->endSection(); ?>