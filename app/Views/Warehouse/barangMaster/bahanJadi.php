<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Bahan Jadi</h1>
        <button class="btn btn-show-form btn-add float-right">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </button>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp row-form-select-master-barang-index">
                <div class="col-md-3 col mb-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Ketik Nama Barang / Kode Barang" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th onclick="changeSort('parent_barang.parent_name')" class="sort">Kelompok</th>
                                <th onclick="changeSort('barang_master.kode_barang')" class="sort">Kode Barang</th>
                                <th onclick="changeSort('barang_master.barang_name')" class="sort">Nama Barang</th>
                                <th class="sort">Satuan 1</th>
                                <th class="sort">Satuan 2</th>
                                <th class="sort">Satuan 3</th>
                                <th class="sort">Akun COA</th>
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

<div class="modal add-modal" id="add_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label></h5>
            </div>
            <div class="modal-body">
                <?= csrf_field() ?>
                <form class="create-form" role="form" method="POST">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="type" value="<?= $type ?>">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select" name="parent_type_id" id="parent_type_id">
                                    <option value=""></option>
                                    <?php foreach ($kelompokBarang as $kb) : ?>
                                        <option value="<?= encrypt($kb['id']) ?>"><?= $kb['parent_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Kelompok Barang</label>
                            </div>
                        </div>
                        <!-- <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select" name="divisi_id" id="divisi_id">
                                    <option value=""></option>
                                    <?php foreach ($divisi as $d) : ?>
                                        <option value="<?= encrypt($d->id); ?>"><?= $d->divisi; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Department</label>
                            </div>
                        </div> -->
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <div class="input-group input-group-password">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control" name="kode_barang" placeholder="Kode Barang">
                                        <label for="floatingInput">Kode Barang</label>
                                    </div>
                                    <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                        <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" id="generate_new_code" name="generate_new_code" type="checkbox" onchange="generateNewCode()">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control" name="barang_name" id="barang_name">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" style="font-size: 12px;">
                            Note : <br>
                            <ul>
                                <li style="height: 15px;">PCS, Lusin 12 PCS, Dus 24 PCS</li>
                                <li style="height: 15px;">Satuan 1 adalah satuan terkecil</li>
                                <li style="height: 15px;">Satuan 2 harus lebih besar daripada satuan 1</li>
                                <li style="height: 15px;">Saturan 3 harus lebih besar dari satuan 2</li>
                            </ul>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi" id="" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col" style="width: 1%;">No</th>
                                        <th scope="col" colspan="3" style="width: 89%;">Spesifikasi</th>
                                        <th scope="col" style="width: 10%;"></th>
                                    </tr>
                                </thead>
                                <tbody class="body-table" id="tbody2" style="cursor: pointer;">
                                    <tr>
                                        <td rowspan="2" style="padding:0px!important;text-align:center;">
                                            <span id="nomber">1</span>
                                        </td>
                                        <td colspan="3" style="padding:0px!important;">
                                            <input type="text" name="spek[]" id="spek" class="form-control">
                                        </td>
                                        <td rowspan="2" style="padding:0px!important;text-align:center;">
                                            <button type="button" class="btn btn-primary" onclick="addRow('tbody2')"><i class="fas fa-plus"></i></button>
                                            <button type="button" class="btn btn-danger" onclick="deleteRow('tbody2')"><i class="far fa-trash-alt"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <div class="row">
                                                <div class="col-sm-6" style="padding:0px!important;">
                                                    <div class="form-floating">
                                                        <input autocomplete="one-time-code" type="text" class="form-control" name="harga_pokok[]" id="harga_pokok" onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');" onchange="this.value = formatRupiah(this.value);">
                                                        <label for="floatingInput">Harga Pokok</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6" style="padding:0px!important;">
                                                    <div class="form-floating">
                                                        <input autocomplete="one-time-code" type="text" class="form-control" name="harga_jual[]" id="harga_jual" onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');" onchange="this.value = formatRupiah(this.value);">
                                                        <label for="floatingInput">Harga Jual</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;">
                                            <div class="row">
                                                <div class="col-sm-12" style="padding:0px!important;">
                                                    <div class="form-floating">
                                                        <select class="form-select" name="satuan1_id[]" id="satuan1_id" title="Satuan terkecil dari produk. Cth: PCS" onchange="changeSpanText()">
                                                            <option value=""></option>
                                                            <?php foreach ($satuanBarang as $sb) : ?>
                                                                <option value="<?= ($sb['id']); ?>"><?= $sb['kode_satuan'] ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <label for="floatingInput">Satuan 1</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="width: 30%;">
                                            <div class="row">
                                                <div class="col-sm-6" style="padding:0px!important;">
                                                    <div class="form-floating">
                                                        <select class="form-select" name="satuan2_id[]" id="satuan2_id" title="Satuan yang lebih besar dari Satuan 1. Cth: LUSIN (12 Pcs)" onchange="changeSpanText()">
                                                            <option value=""></option>
                                                            <?php foreach ($satuanBarang as $sb) : ?>
                                                                <option value="<?= ($sb['id']); ?>"><?= $sb['kode_satuan'] ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <label for="floatingInput">Satuan 2</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6" style="padding:0px!important;">
                                                    <div class="input-group ">
                                                        <input type="text" name="konversi_satuan_2[]" onchange="checkValueKonversi()" id="konversi_satuan_2" class="form-control" onkeypress="return isNumberKey(event)">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text satuan1">-</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="width: 30%;">
                                            <div class="row">
                                                <div class="col-sm-6" style="padding:0px!important;">
                                                    <div class="form-floating">
                                                        <select class="form-select" name="satuan3_id[]" id="satuan3_id" title="Satuan terbesar dari produk. Cth: DUS (konversi 48 PCS)" onchange="changeSpanText()">
                                                            <option value=""></option>
                                                            <?php foreach ($satuanBarang as $sb) : ?>
                                                                <option value="<?= ($sb['id']); ?>"><?= $sb['kode_satuan'] ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <label for="floatingInput">Satuan 3</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6" style="padding:0px!important;">
                                                    <div class="input-group ">
                                                        <input type="text" name="konversi_satuan_3[]" onchange="checkValueKonversi()" id="konversi_satuan_3" class="form-control" onkeypress="return isNumberKey(event)">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text satuan1">-</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Batal</button>
                <button type="submit" class="btn btn-submit-form">Simpan</button>
                <button type="button" class="btn btn-discard delete-btn">Hapus</button>
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
                url: "<?= base_url("barang-master/all"); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.search = $(".search").val();
                    data.sort = sort;
                    data.sortType = sortType;
                    data.parent_type = "<?= $type ?>";
                }
            },
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
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
                    data: "kelompok_barang",
                    className: "text-center",
                },
                {
                    data: "kode_barang",
                    className: "text-center",
                },
                {
                    data: "barang_name",
                    className: "text-center",
                },
                {
                    data: "satuan",
                    className: "text-center",
                    sortable: false,
                },
                {
                    data: "satuan2",
                    className: "text-center",
                    sortable: false,
                },
                {
                    data: "satuan3",
                    className: "text-center",
                    sortable: false,
                },
                {
                    data: "akun_coa", // Assuming "akun_coa" is the field name in your data source
                    className: "text-center",
                    render: function(data, type, row) {
                        // If "akun_coa" exists and is not empty, display a checkbox
                        if (data && data !== "") {
                            return "<i class='fa fa-check-circle-o' aria-hidden='true'></i>";
                        } else { // Otherwise, display a dash "-"
                            return "<i class='fa fa-minus-circle' aria-hidden='true'></i>";
                        }
                    }
                }
            ],
            columnDefs: [{
                defaultContent: "-",
                targets: "_all"
            }],
            language: {
                emptyTable: "Master Data Bahan Jadi Masih Kosong",
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

        $('.btn-add').click(function() {
            $('.title-name').text("Tambah Bahan Jadi");
            $(".create-form :input:not([name='type'])").val('');
            $('select[name="parent_type_id"]').val(null).change();

            $('.delete-btn').hide();
            $('input[name="kode_barang"]').attr('readonly', false);
            $('#generate_new_code').prop('checked', true).change().show();
            $('.add-modal').modal('show');
            $('#tbody2').empty();
            var table = document.getElementById('tbody2');
            var row = table.insertRow();
            var row2 = table.insertRow();
            var row3 = table.insertRow();

            // Create cells with appropriate colspan
            row.innerHTML = `
            <td rowspan="3" style="padding:0px!important;text-align:center;">
                <span id="nomber">1</span>
            </td>
            <td colspan="3">
                <div class="row">
                    <div class="col-sm-12" style="padding:0px!important;">
                        <div class="form-floating">
                            <input type="text" name="spek[]" id="spek" class="form-control">
                            <label for="floatingInput">Spesifikasi</label>
                        </div>
                    </div>
                </div>
            </td>
            <td rowspan="3" style="padding:0px!important;text-align:center;">
                <button type="button" class="btn btn-primary" onclick="addRow('tbody2')"><i class="fas fa-plus"></i></button>
                <button type="button" class="btn btn-danger" onclick="deleteRow('tbody2')"><i class="far fa-trash-alt"></i></button>
            </td>`;
            row2.innerHTML = `
            <td colspan="3">
                <div class="row">
                    <div class="col-sm-6" style="padding:0px!important;">
                        <div class="form-floating">
                            <input autocomplete="one-time-code" type="text" class="form-control" name="harga_pokok[]" id="harga_pokok" onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');" onchange="this.value = formatRupiah(this.value);">
                            <label for="floatingInput">Harga Pokok</label>
                        </div>
                    </div>
                    <div class="col-sm-6" style="padding:0px!important;">
                        <div class="form-floating">
                            <input autocomplete="one-time-code" type="text" class="form-control" name="harga_jual[]" id="harga_jual" onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');" onchange="this.value = formatRupiah(this.value);">
                            <label for="floatingInput">Harga Jual</label>
                        </div>
                    </div>
                </div>
            </td>`;
            row3.innerHTML = `
            <td style="width: 15%;">
                <div class="row">
                    <div class="col-sm-12" style="padding:0px!important;">
                        <div class="form-floating">
                            <select class="form-select" name="satuan1_id[]" id="satuan1_id" title="Satuan terkecil dari produk. Cth: PCS" onchange="changeSpanText()">
                                <option value=""></option>
                                <?php foreach ($satuanBarang as $sb) : ?>
                                    <option value="<?= ($sb['id']); ?>"><?= $sb['kode_satuan'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Satuan 1</label>
                        </div>
                    </div>
                </div>
            </td>
            <td style="width: 30%;">
                <div class="row">
                    <div class="col-sm-6" style="padding:0px!important;">
                        <div class="form-floating">
                            <select class="form-select" name="satuan2_id[]" id="satuan2_id" title="Satuan yang lebih besar dari Satuan 1. Cth: LUSIN (12 Pcs)" onchange="changeSpanText()">
                                <option value=""></option>
                                <?php foreach ($satuanBarang as $sb) : ?>
                                    <option value="<?= ($sb['id']); ?>"><?= $sb['kode_satuan'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Satuan 2</label>
                        </div>
                    </div>
                    <div class="col-sm-6" style="padding:0px!important;">
                        <div class="input-group ">
                            <input type="text" name="konversi_satuan_2[]" onchange="checkValueKonversi()" id="konversi_satuan_2" onkeypress="return isNumberKey(event)" class="form-control">
                            <div class="input-group-append">
                                <span class="input-group-text satuan1">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
            <td style="width: 30%;">
                <div class="row">
                    <div class="col-sm-6" style="padding:0px!important;">
                        <div class="form-floating">
                            <select class="form-select" name="satuan3_id[]" id="satuan3_id" title="Satuan terbesar dari produk. Cth: DUS (konversi 48 PCS)" onchange="changeSpanText()">
                                <option value=""></option>
                                <?php foreach ($satuanBarang as $sb) : ?>
                                    <option value="<?= ($sb['id']); ?>"><?= $sb['kode_satuan'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Satuan 3</label>
                        </div>
                    </div>
                    <div class="col-sm-6" style="padding:0px!important;">
                        <div class="input-group ">
                            <input type="text" name="konversi_satuan_3[]" onchange="checkValueKonversi()" id="konversi_satuan_3" onkeypress="return isNumberKey(event)" class="form-control">
                            <div class="input-group-append">
                                <span class="input-group-text satuan1">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </td>`;
            $("#satuan1_id, #satuan2_id, #satuan3_id")
                .parent('div')
                .find('label')
                .css('z-index', '1');
            $("#satuan1_id, #satuan2_id, #satuan3_id").select2({
                theme: "bootstrap-5",
                allowClear: true,
                placeholder: 'Pilih Satuan',
                dropdownParent: $(".add-modal .modal-content")
            });
        });

        $('.btn-discard').click(function() {
            $('.add-modal').modal('hide');
        });

        var validator = $(".create-form").validate({
            rules: {
                parent_type_id: {
                    required: true
                },
                barang_name: {
                    required: true
                },
                kode_barang: {
                    required: true
                },
                satuan_id: {
                    required: true
                },
                minimum_stock: {
                    required: true,
                    number: true
                },
            },
            messages: {
                parent_type_id: {
                    required: "Kelompok Barang Wajib Diisi"
                },
                barang_name: {
                    required: "Nama Barang Wajib Diisi"
                },
                kode_barang: {
                    required: "Kode Barang Wajib Diisi"
                },
                satuan_id: {
                    required: "Satuan Barang Wajib Diisi"
                },
                minimum_stock: {
                    required: "Minimal stock harus diisi",
                    number: "Masukkan angka valid"
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
            let data = table.row(this).data();
            let id = data.id;
            let csrf = $(`[name="${csrfToken}"]`);
            $(".create-form :input:not([name='type'])").val('');
            $.ajax({
                url: "<?= base_url('barang-master/get') ?>",
                data: {
                    id: id
                },
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                method: "POST",
                dataType: "json",
                success: function(res) {
                    $('.delete-btn').show();
                    $('.title-name').text("Update Bahan Jadi");
                    $('input[name="kode_barang"]').attr('readonly', true);
                    $('#generate_new_code').hide();
                    $('input[name="kode_barang"]').val(res.data.kode_barang);
                    $('input[name="barang_name"]').val(res.data.barang_name);
                    $('select[name="parent_type_id"]').val(res.data.parent_type_id).change();
                    $('select[name="divisi_id"]').val(res.data.divisi_id).change();

                    $('input[name="id"]').val(res.data.id);

                    // Iterate through dataSpekDetail and append rows to the table
                    if (res.dataSpekDetail && res.dataSpekDetail.length > 0) {
                        // Clear existing rows from the table
                        $('#tbody2').empty();
                        var counter = 1;
                        res.dataSpekDetail.forEach(function(item) {
                            // Get the ID of satuan1 for the current item
                            var satuan1Id = item.satuan_1;
                            var satuan2Id = item.satuan_2;
                            var satuan3Id = item.satuan_3;
                            var newRow = '<tr>' +
                                '<td rowspan="3" style="padding:0px!important;text-align:center;"><span id="nomber">' + counter + '</span></td>' +
                                '<td colspan="3"><div class="row"><div class="col-sm-12" style="padding:0px!important;"><div class="form-floating"><input type="text" name="spek_old[]" id="spek" class="form-control"  value="' + item.spesifikasi + '"><input type="hidden" name="id_spek[]" id="id_spek" value="' + item.id + '"><label for="floatingInput">Spesifikasi</label></div></div></div></td>' +
                                '<td rowspan="3" style="padding:0px!important;text-align:center;"><button type="button" class="btn btn-primary" onclick="addRow(\'tbody2\')"><i class="fas fa-plus"></i></button>' +
                                '<button type="button" class="btn btn-danger" onclick="deleteRow(\'tbody2\')"><i class="far fa-trash-alt"></i></button></td>' +
                                '</tr>' +
                                '<tr>' +
                                '<td colspan="3">' +
                                '<div class="row"><div class="col-sm-6" style="padding:0px!important;"><div class="form-floating">' +
                                '<input autocomplete="one-time-code" type="text" class="form-control" name="harga_pokok_old[]" id="harga_pokok" onkeyup="this.value = this.value.replace(/[^0-9,]/g, "");" onchange="this.value = formatRupiah(this.value);" value="' + item.harga_pokok + '"><label for="floatingInput">Harga Pokok</label>' +
                                '</div></div>' +
                                '<div class = "col-sm-6" style = "padding:0px!important;" ><div class = "form-floating" >' +
                                '<input autocomplete = "one-time-code" type = "text" class = "form-control" name = "harga_jual_old[]" id = "harga_jual" onkeyup = "this.value = this.value.replace(/[^0-9,]/g, "");" onchange = "this.value = formatRupiah(this.value);"  value="' + item.harga_jual + '"><label for = "floatingInput" > Harga Jual </label> ' +
                                '</div></div></div>' +
                                '</td>' +
                                '</tr>' +
                                '<tr>' +
                                '<td style="width: 15%;"><div class="row"><div class="col-sm-12" style="padding:0px!important;"><div class="form-floating"><select class="form-select" name="satuan1_id_old[]" id="satuan1_id_' + counter + '" title="Satuan terkecil dari produk. Cth: PCS" onchange="changeSpanText(' + counter + ')"><option value=""></option><?php foreach ($satuanBarang as $sb) : ?><option value="<?= ($sb['id']); ?>"><?= $sb['kode_satuan'] ?></option><?php endforeach; ?></select><label for="floatingInput">Satuan 1</label></div></div></div></td>' +
                                '<td style="width: 30%;"><div class="row"><div class="col-sm-6" style="padding:0px!important;"><div class="form-floating"><select class="form-select" name="satuan2_id_old[]" id="satuan2_id_' + counter + '" title="Satuan yang lebih besar dari Satuan 1. Cth: LUSIN (12 Pcs)" onchange="changeSpanText(' + counter + ')"><option value=""></option><?php foreach ($satuanBarang as $sb) : ?><option value="<?= ($sb['id']); ?>"><?= $sb['kode_satuan'] ?></option><?php endforeach; ?></select><label for="floatingInput">Satuan 2</label></div></div><div class="col-sm-6" style="padding:0px!important;"><div class="input-group "><input type="text" name="konversi_satuan_2_old[]" id="konversi_satuan_2_' + counter + '" onchange="checkValueKonversi(' + counter + ')" onkeypress="return isNumberKey(event)" class="form-control" value="' + item.konversi_satuan_2 + '"><div class="input-group-append"><span class="input-group-text satuan_' + counter + '">-</span></div></div></div></div></td>' +
                                '<td style="width: 30%;"><div class="row"><div class="col-sm-6" style="padding:0px!important;"><div class="form-floating"><select class="form-select" name="satuan3_id_old[]" id="satuan3_id_' + counter + '" title="Satuan terbesar dari produk. Cth: DUS (konversi 48 PCS)" onchange="changeSpanText(' + counter + ')"><option value=""></option><?php foreach ($satuanBarang as $sb) : ?><option value="<?= ($sb['id']); ?>"><?= $sb['kode_satuan'] ?></option><?php endforeach; ?></select><label for="floatingInput">Satuan 3</label></div></div><div class="col-sm-6" style="padding:0px!important;"><div class="input-group "><input type="text" name="konversi_satuan_3_old[]" id="konversi_satuan_3_' + counter + '" onchange="checkValueKonversi(' + counter + ')" onkeypress="return isNumberKey(event)" class="form-control" value="' + item.konversi_satuan_3 + '"><div class="input-group-append"><span class="input-group-text satuan_' + counter + '">-</span></div></div></div></div></td>' +
                                '</tr>';
                            $('#tbody2').append(newRow);
                            $(`#satuan1_id_${counter} option`).each(function() {
                                if ($(this).val() == satuan1Id) {
                                    $(this).prop('selected', true);
                                    changeSpanText(counter);
                                }
                            });
                            $(`#satuan2_id_${counter} option`).each(function() {
                                if ($(this).val() == satuan2Id) {
                                    $(this).prop('selected', true);
                                }
                            });
                            $(`#satuan3_id_${counter} option`).each(function() {
                                if ($(this).val() == satuan3Id) {
                                    $(this).prop('selected', true);
                                }
                            });
                            $(`#satuan_id_${counter}, #satuan1_id_${counter}, #satuan2_id_${counter}, #satuan3_id_${counter}`)
                                .parent('div')
                                .children('span')
                                .children('span')
                                .children('span')
                                .css('height', ' calc(3.5rem + 2px)');

                            $(`#satuan_id_${counter}, #satuan1_id_${counter}, #satuan2_id_${counter}, #satuan3_id_${counter}`)
                                .parent('div')
                                .children('span')
                                .children('span')
                                .children('span')
                                .children('span')
                                .css('margin-top', '22px').css('margin-left', '-7px');

                            $(`#satuan_id_${counter}, #satuan1_id_${counter}, #satuan2_id_${counter}, #satuan3_id_${counter}`)
                                .parent('div')
                                .find('label')
                                .css('z-index', '1');

                            $(`#satuan_id_${counter}, #satuan1_id_${counter}, #satuan2_id_${counter}, #satuan3_id_${counter}`).select2({
                                theme: "bootstrap-5",
                                allowClear: true,
                                placeholder: 'Pilih Satuan',
                                dropdownParent: $(".add-modal .modal-content")
                            });
                            counter++;
                        });
                    }

                    $('.add-modal').modal('show');
                }
            })
        })
        $('.btn-submit-form').click(function(e) {
            e.preventDefault();
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
                        let id = $('input[name="id"]').val();
                        let csrf = $(`[name="${csrfToken}"]`);
                        let data = new FormData(document.querySelector(".create-form"));

                        if (id) {
                            $.ajax({
                                url: "<?= base_url("barang-master/update"); ?>",
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
                                        Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                table.ajax.reload();
                                                $(".add-modal").modal("hide");
                                            })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        }).then(() => {

                                        });
                                    }
                                },
                                onError: function(response) {
                                    csrf.val(response.token);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    })
                                }
                            });
                        } else {
                            $.ajax({
                                url: "<?= base_url("barang-master/save"); ?>",
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
                                        Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                table.ajax.reload();
                                                $(".add-modal").modal("hide");
                                            })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        }).then(() => {

                                        });
                                    }
                                },
                                onError: function(response) {
                                    csrf.val(response.token);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    })
                                }
                            });
                        }
                    }
                })

            }
        });
        $(".delete-btn").click(function() {
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
                    let id = $("#id").val();
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("barang-master/delete"); ?>",
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
                                    });
                            }
                        },
                        onError: function(response) {
                            csrf.val(response.token);
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Gagal Disimpan, coba Lagi',
                                confirmButtonColor: '#4e73df',
                            })
                        }
                    });
                }
            })
        });
    });

    function changeSpanText(counter = null) {
        var selectedText, selectedSatuan1Val, selectedSatuan2Val, selectedSatuan3Val, spanText;

        if (counter) {
            selectedText = $(`#satuan1_id_${counter}`).find('option:selected').text();
            selectedSatuan1Val = $(`#satuan1_id_${counter}`).val();
            selectedSatuan2Val = $(`#satuan2_id_${counter}`).val();
            selectedSatuan3Val = $(`#satuan3_id_${counter}`).val();
            spanText = $(`.satuan_${counter}`);
        } else {
            selectedText = $('#satuan1_id').find('option:selected').text();
            selectedSatuan1Val = $('#satuan1_id').val();
            selectedSatuan2Val = $('#satuan2_id').val();
            selectedSatuan3Val = $('#satuan3_id').val();
            spanText = $('.satuan1');
        }

        if (selectedSatuan1Val === selectedSatuan2Val && selectedSatuan1Val !== "") {
            Swal.fire({
                icon: 'error',
                title: 'Satuan 2 tidak boleh sama dengan satuan 1',
                confirmButtonColor: '#4e73df',
            }).then(() => {
                if (counter) {
                    $(`#satuan2_id_${counter}`).val('').change();
                } else {
                    $('#satuan2_id').val('').change();
                }
            });
        }
        if (selectedSatuan1Val === selectedSatuan3Val && selectedSatuan1Val !== "") {
            Swal.fire({
                icon: 'error',
                title: 'Satuan 3 tidak boleh sama dengan satuan 1',
                confirmButtonColor: '#4e73df',
            }).then(() => {
                if (counter) {
                    $(`#satuan3_id_${counter}`).val('').change();
                } else {
                    $('#satuan3_id').val('').change();
                }
            });
        }
        if (selectedSatuan2Val === selectedSatuan3Val && selectedSatuan2Val !== "") {
            Swal.fire({
                icon: 'error',
                title: 'Satuan 3 tidak boleh sama dengan satuan 2',
                confirmButtonColor: '#4e73df',
            }).then(() => {
                if (counter) {
                    $(`#satuan3_id_${counter}`).val('').change();
                } else {
                    $('#satuan3_id').val('').change();
                }
            });
        }
        // Ubah konten span sesuai dengan nilai yang dipilih
        spanText.text(selectedText ? selectedText : '-');
    }

    function checkValueKonversi(counter = null) {
        var selectedSatuan2Val, selectedSatuan3Val;

        if (counter) {
            selectedSatuan2Val = $(`#konversi_satuan_2_${counter}`).val();
            selectedSatuan3Val = $(`#konversi_satuan_3_${counter}`).val();
        } else {
            selectedSatuan2Val = $('#konversi_satuan_2').val();
            selectedSatuan3Val = $('#konversi_satuan_3').val();
        }

        if (Number(selectedSatuan3Val) <= Number(selectedSatuan2Val) && selectedSatuan3Val !== "") {
            Swal.fire({
                icon: 'error',
                title: 'Satuan 3 harus lebih besar dari satuan 2',
                confirmButtonColor: '#4e73df',
            }).then(() => {
                if (counter) {
                    $(`#konversi_satuan_3_${counter}`).val('').change();
                } else {
                    $('#konversi_satuan_3').val('').change();
                }
            });
        }
    }

    var counter = 2; // Counter variable for rowspan

    function addRow(tableID) {
        var table = document.getElementById(tableID);
        var row = table.insertRow();
        var row2 = table.insertRow();
        var row3 = table.insertRow();

        // Create cells with appropriate colspan
        row.innerHTML = `
        <td rowspan="3" style="padding:0px!important;text-align:center;">
            <span id="nomber">${counter}</span>
        </td>
        <td colspan="3">
            <div class="row">
                <div class="col-sm-12" style="padding:0px!important;">
                    <div class="form-floating">
                        <input type="text" name="spek[]" id="spek" class="form-control">
                        <label for="floatingInput">Spesifikasi</label>
                    </div>
                </div>
            </div>
        </td>
        <td rowspan="3" style="padding:0px!important;text-align:center;">
            <button type="button" class="btn btn-primary" onclick="addRow('tbody2')"><i class="fas fa-plus"></i></button>
            <button type="button" class="btn btn-danger" onclick="deleteRow('tbody2')"><i class="far fa-trash-alt"></i></button>
        </td>`;
        row2.innerHTML = `
            <td colspan="3">
                <div class="row">
                    <div class="col-sm-6" style="padding:0px!important;">
                        <div class="form-floating">
                            <input autocomplete="one-time-code" type="text" class="form-control" name="harga_pokok[]" id="harga_pokok" onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');" onchange="this.value = formatRupiah(this.value);">
                            <label for="floatingInput">Harga Pokok</label>
                        </div>
                    </div>
                    <div class="col-sm-6" style="padding:0px!important;">
                        <div class="form-floating">
                            <input autocomplete="one-time-code" type="text" class="form-control" name="harga_jual[]" id="harga_jual" onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');" onchange="this.value = formatRupiah(this.value);">
                            <label for="floatingInput">Harga Jual</label>
                        </div>
                    </div>
                </div>
            </td>`;
        row3.innerHTML = `
        <td style="width: 15%;">
            <div class="row">
                <div class="col-sm-12" style="padding:0px!important;">
                    <div class="form-floating">
                        <select class="form-select" name="satuan1_id[]" id="satuan1_id_${counter}" title="Satuan terkecil dari produk. Cth: PCS" onchange="changeSpanText(${counter})">
                            <option value=""></option>
                            <?php foreach ($satuanBarang as $sb) : ?>
                                <option value="<?= ($sb['id']); ?>"><?= $sb['kode_satuan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Satuan 1</label>
                    </div>
                </div>
            </div>
        </td>
        <td style="width: 30%;">
            <div class="row">
                <div class="col-sm-6" style="padding:0px!important;">
                    <div class="form-floating">
                        <select class="form-select" name="satuan2_id[]" id="satuan2_id_${counter}" title="Satuan yang lebih besar dari Satuan 1. Cth: LUSIN (12 Pcs)" onchange="changeSpanText(${counter})">
                            <option value=""></option>
                            <?php foreach ($satuanBarang as $sb) : ?>
                                <option value="<?= ($sb['id']); ?>"><?= $sb['kode_satuan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Satuan 2</label>
                    </div>
                </div>
                <div class="col-sm-6" style="padding:0px!important;">
                    <div class="input-group ">
                        <input type="text" name="konversi_satuan_2[]" id="konversi_satuan_2_${counter}" onchange="checkValueKonversi(${counter})" class="form-control" onkeypress="return isNumberKey(event)">
                        <div class="input-group-append">
                            <span class="input-group-text satuan_${counter}">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </td>
        <td style="width: 30%;">
            <div class="row">
                <div class="col-sm-6" style="padding:0px!important;">
                    <div class="form-floating">
                        <select class="form-select" name="satuan3_id[]" id="satuan3_id_${counter}" title="Satuan terbesar dari produk. Cth: DUS (konversi 48 PCS)" onchange="changeSpanText(${counter})">
                            <option value=""></option>
                            <?php foreach ($satuanBarang as $sb) : ?>
                                <option value="<?= ($sb['id']); ?>"><?= $sb['kode_satuan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Satuan 3</label>
                    </div>
                </div>
                <div class="col-sm-6" style="padding:0px!important;">
                    <div class="input-group ">
                        <input type="text" name="konversi_satuan_3[]" id="konversi_satuan_3_${counter}" onchange="checkValueKonversi(${counter})" class="form-control" onkeypress="return isNumberKey(event)">
                        <div class="input-group-append">
                            <span class="input-group-text satuan_${counter}">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </td>`;
        $(`#satuan_id_${counter}, #satuan1_id_${counter}, #satuan2_id_${counter}, #satuan3_id_${counter}`)
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(`#satuan_id_${counter}, #satuan1_id_${counter}, #satuan2_id_${counter}, #satuan3_id_${counter}`)
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $(`#satuan_id_${counter}, #satuan1_id_${counter}, #satuan2_id_${counter}, #satuan3_id_${counter}`)
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $(`#satuan_id_${counter}, #satuan1_id_${counter}, #satuan2_id_${counter}, #satuan3_id_${counter}`).select2({
            theme: "bootstrap-5",
            allowClear: true,
            placeholder: 'Pilih Satuan',
            dropdownParent: $(".add-modal .modal-content")
        });

        counter++;
        // rows++;
    }

    function deleteRow(tableID) {
        try {
            var table = document.getElementById(tableID);
            var rowCount = table.rows.length;

            // Variable to track whether any checkbox is checked
            var isChecked = false;

            for (var i = 0; i < rowCount; i++) {
                var row = table.rows[i];
                var chkbox = row.cells[0].childNodes[0];

                if (null != chkbox && true == chkbox.checked) {
                    isChecked = true;
                    table.deleteRow(i);
                    table.deleteRow(i - 1); // Remove the previous row as well
                    rowCount -= 2; // Reduce rowCount by 2
                    i--;
                }
            }

            // If no checkbox is checked, remove the last two rows
            if (!isChecked && rowCount > 3) {
                table.deleteRow(rowCount - 1);
                table.deleteRow(rowCount - 2);
                table.deleteRow(rowCount - 3);
                rowCount -= 3;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Baris terakhir tidak boleh dihapus!!!',
                    confirmButtonColor: '#4e73df',
                })
            }
            // Reset counter based on the remaining rows
            // counter = rowCount > 1 ? currentCount : 2;
            // Get the last row in the table
            var lastRow = table.rows[rowCount - 3];

            // Update the value of the span with the updated counter value
            var currentCount = parseInt(lastRow.querySelector('#nomber').innerText);
            counter = currentCount + 1;

        } catch (e) {
            alert(e);
        }
    }

    function generateNewCode() {
        let csrfToken = '<?= csrf_token() ?>';
        let value = document.getElementById('generate_new_code').checked ? true : false;
        let csrf = $(`[name="${csrfToken}"]`);
        if (value) {
            $("input[name='kode_barang']").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("barang-master/generate-new-code"); ?>`,
                data: {
                    type: "<?= $type ?>"
                },
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                method: "POST",
                success: function(res) {
                    csrf.val(res.token);
                    $("input[name='kode_barang']").attr("readonly", true);
                    $("input[name='kode_barang']").val(res.codeNew);
                }
            })
        } else {
            $("input[name='kode_barang']").attr("readonly", false);
            $("input[name='kode_barang']").val("");
        }
    }
</script>
<script>
    $("select[name='parent_type_id']")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $("select[name='parent_type_id']")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $("select[name='parent_type_id']")
        .parent('div')
        .find('label')
        .css('z-index', '1');
    $("select[name='parent_type_id']").select2({
        placeholder: "Pilih Kelompok Barang",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $(".add-modal .modal-content")
    });

    //styling Pilih Department
    $("select[name='divisi_id']")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');
    $("select[name='divisi_id']")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');
    $("select[name='divisi_id']")
        .parent('div')
        .find('label')
        .css('z-index', '1');
    $("select[name='divisi_id']").select2({
        placeholder: "Pilih Department",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $(".add-modal .modal-content")
    });
    //end styling Pilih Department

    // $("select[name='satuan_id']")
    //     .parent('div')
    //     .children('span')
    //     .children('span')
    //     .children('span')
    //     .css('height', ' calc(3.5rem + 2px)');

    // $("select[name='satuan_id']")
    //     .parent('div')
    //     .children('span')
    //     .children('span')
    //     .children('span')
    //     .children('span')
    //     .css('margin-top', '22px').css('margin-left', '-7px');

    // $("select[name='satuan_id']")
    //     .parent('div')
    //     .find('label')
    //     .css('z-index', '1');
    // $("select[name='satuan_id']").select2({
    //     placeholder: "Pilih Satuan Barang",
    //     theme: "bootstrap-5",
    //     allowClear: true,
    //     dropdownParent: $(".add-modal .modal-content")
    // });

    $("#satuan1_id, #satuan2_id, #satuan3_id")
        .parent('div')
        .find('label')
        .css('z-index', '1');
    $("#satuan1_id, #satuan2_id, #satuan3_id").select2({
        theme: "bootstrap-5",
        allowClear: true,
        placeholder: 'Pilih Satuan',
        dropdownParent: $(".add-modal .modal-content")
    });

    function formatRupiah(angka) {
        angka = angka.replace(/\./g, ',');
        angka = angka.replace(/[^\d,]/g, '');
        var parts = angka.split(',');
        var ribuan = parts[0];
        var desimal = parts[1] || '00';
        var reverse = ribuan.toString().split('').reverse().join('');
        var ribuanFormatted = reverse.match(/\d{1,3}/g).join('.').split('').reverse().join('');
        return '' + ribuanFormatted + ',' + desimal;
    }
</script>

<?= $this->endSection(); ?>