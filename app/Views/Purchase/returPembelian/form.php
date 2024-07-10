<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Tambah Retur</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("retur-barang"); ?>">
                Kembali
            </a>
            <button class="btn btn-show-form btn-save float-right btn-submit-form">
                Simpan
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                <?= csrf_field() ?>
                <label class="form-label font-weight-bold lable-title mt-2 mb-3">
                    Informasi Retur
                </label>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control retur_no" id="retur_no" name="retur_no" placeholder="No. Retur">
                                    <label for="floatingInput">No. Retur Barang</label>
                                </div>
                                <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control" id="" readonly name="" value="BAHAN PENOLONG LOKAL">
                                <label for="floatingInput">Tipe Bahan</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select supplier_id" name="supplier_id" id="supplier_id">
                                <option value=""></option>
                                <?php foreach ($supplier as $s) : ?>
                                    <option value="<?= $s['id'] ?>">
                                        <?= strtoupper($s['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Pilih Supplier</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control tgl_retur" id="tgl_retur" name="tgl_retur">
                                <label for="floatingInput">Tanggal Retur</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select multiple class="form-select penerimaan_barang_id" name="penerimaan_barang_id" id="penerimaan_barang_id">
                                <option value=""></option>
                            </select>
                            <label for="floatingInput">Pilih Nomor LPB</label>
                        </div>
                    </div>
                </div>
                <label class="form-label font-weight-bold lable-title mt-1 mb-3">
                    Daftar Barang
                </label>
                <div class="row">
                    <div class="col-md-12 col-table-button-tts">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-form-tts" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No.</th>
                                        <th class="sort">No. PO</th>
                                        <th class="sort">Tgl. LPB</th>
                                        <th class="sort">No. LPB</th>
                                        <th class="sort">Nama Barang</th>
                                        <th class="sort">Qty</th>
                                        <th class="sort">Qty Diretur</th>
                                        <th class="sort">Qty Sisa</th>
                                        <th class="sort">Jumlah Retur</th>
                                        <th class="sort">Satuan</th>
                                    </tr>
                                </thead>
                                <tbody class="body-table" id="body-table">
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-primary" id="select-item-btn">Pilih</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</section>

<script>
    $("input[name='tgl_retur']").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $("select[name='penerimaan_barang_id']").select2({
        placeholder: "Pilih Nomor LPB",
        theme: "bootstrap-5",
        allowClear: true
    });

    $("select[name='supplier_id']").select2({
        placeholder: "Pilih Supplier",
        theme: "bootstrap-5",
        allowClear: true
    }).on('chage', (function() {

    }));

    $('#supplier_id').change(function(e) {
        e.preventDefault();
        $.ajax({
            url: `<?= base_url("retur-barang/generate-penerimaan-barang"); ?>`,
            method: "GET",
            data: {
                supplierID: $(this).val()
            },
            dataType: "json",
            success: function(res) {
                console.log(res);
                $("#penerimaan_barang_id").empty();
                $("#penerimaan_barang_id").append(`<option value=""></option>`);
                res.data.forEach(function(item) {
                    $("#penerimaan_barang_id").append(`<option  value="${item.id}">${item.no_penerimaan_barang}</option>`);
                });
            }
        })
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


    $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: false,
        serverSide: false,
        ordering: true,
        order: [
            [1, 'asc']
        ],
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        fixedHeader: true,
        searching: false,
        display: "stripe",

        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    })

    const changeStatus = function() {
        let value = document.getElementById('auto_generate').checked ? true : false;

        if (value) {
            $(".retur_no").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("retur-barang/generate-new-no"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".retur_no").val(res.data);
                }
            })
        } else {
            $(".retur_no").attr("readonly", false);
            $(".retur_no").val("");
        }
    }
</script>
<?= $this->endSection(); ?>