<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($biayaUdang) ? "Tambah Biaya Udang" : "Update Biaya Udang" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("biaya-udang"); ?>">
                Kembali
            </a>
            <?php if (!empty($biayaUdang)) : ?>
                <?php if ($biayaUdang['status_posting'] == "0") : ?>
                    <?php if (can('Jasa Vendor', 'Biaya Udang', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="remove('<?= encrypt($biayaUdang['id']); ?>')">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Biaya Udang', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="posting('<?= encrypt($biayaUdang['id']); ?>')">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Biaya Udang', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("biaya-udang/print/"); ?><?= encrypt($biayaUdang['id']); ?>')">
                            Print
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Biaya Udang', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php else : ?>
                    <?php if (can('Jasa Vendor', 'Biaya Udang', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("biaya-udang/print/"); ?><?= encrypt($biayaUdang['id']); ?>')">
                            Print
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else : ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Barang Masuk Vendor</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id" value="<?= !empty($biayaUdang) ? encrypt($biayaUdang['id']) : '' ?>" class="id">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" <?= !empty($biayaUdang) ? 'disabled=true' : ''; ?> value="<?= !empty($biayaUdang) ? $biayaUdang['no_pembayaran'] : "PAY-UDG/" . date('m') . "/1/" . date('Y'); ?>" type="text" class="form-control no_pembayaran" id="no_pembayaran" name="no_pembayaran" placeholder="No. Rebus">
                                    <label for="floatingInput">No. Pembayaran</label>
                                </div>
                                <div style="<?= !empty($biayaUdang) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($biayaUdang) ? 'disabled' : '' ?> autocomplete="one-time-code" class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="<?= date('d/m/Y', strtotime(!empty($biayaUdang) ? $biayaUdang['tanggal'] : $tanggal)); ?>">
                                    <label for="floatingInput">Tanggal Pembayaran</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($biayaUdang) ? 'disabled' : '' ?> class="form-select vendor_id" id="vendor_id" name="vendor_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($vendor as $v) : ?>
                                    <option <?= !empty($biayaUdang) ? ($biayaUdang['vendor_id'] == $v['id'] ? 'selected' : '') : '' ?> value="<?= $v['id'] ?>">
                                        <?= strtoupper($v['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Vendor</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($biayaUdang) ? 'disabled' : '' ?> class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($divisi)) : ?>
                                    <?php foreach ($divisi as $d) : ?>
                                        <option <?= $biayaUdang['divisi_id'] == $d['id'] ? 'selected' : '' ?> value="<?= $d['id'] ?>">
                                            <?= $d['divisi'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($biayaUdang) ? 'disabled' : '' ?> multiple class="form-select multiple_jasa_vendor_in_id" id="multiple_jasa_vendor_in_id" name="multiple_jasa_vendor_in_id[]" aria-label="Floating label select example">
                                <option value=""></option>

                                <?php if (!empty($biayaUdang)) : ?>
                                    <?php foreach (json_decode($biayaUdang['multiple_jasa_vendor_in_id']) as $i => $p) : ?>
                                        <option selected value="<?= $p ?>"><?= json_decode($biayaUdang['multiple_jasa_vendor_in_no'])[$i] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($biayaUdang) ? ($biayaUdang['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Keterangan" value="<?= !empty($biayaUdang) ? $biayaUdang['keterangan'] : '' ?>" class="form-control keterangan" id="keterangan" name="keterangan" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Keterangan (Opsional)</label>
                        </div>
                    </div>
                </div>

            </form>


            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Daftar Barang Yang Akan Dibayar</label>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" border="1" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center;" colspan="4"></th>
                                    <th style="text-align: center;" colspan="1">Size</th>
                                    <th style="text-align: center;" colspan="3"></th>
                                    <th style="text-align: center;" colspan="4">Upah Kopek Yang Dibayar</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">No</th>
                                    <th style="text-align: center;">Tanggal Keluar</th>
                                    <th style="text-align: center;">Barang</th>
                                    <th style="text-align: center;">Spesifikasi</th>

                                    <th style="text-align: center;">KG KELUAR</th>
                                    <th style="text-align: center;" class="kg-daging-vendor">KG DAGING VENDOR</th>
                                    <th style="text-align: center;" class="kg-daging-divisi">KG DAGING DEPARTEMEN</th>

                                    <th style="text-align: center;">Kg Daging</th>
                                    <th style="text-align: center;">Ratio</th>
                                    <th style="text-align: center;">TB Harga</th>
                                    <th style="text-align: center;">Total Harga</th>
                                </tr>
                            </thead>
                            <tbody class="body-table">
                            </tbody>
                            <!-- <tfoot class="foot-detail-table" id="foot-detail-table">
                                <tr>
                                    <td colspan="12" style="text-align: center;">
                                        Tidak Ada Barang
                                    </td>
                                </tr>
                            </tfoot> -->
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
    const disabledStatus = <?= !empty($biayaUdang) && $biayaUdang['status_posting'] == "1" ? "'disabled'" : "''" ?>;

    var request;
    var listBarang = [];
    var listTotal = [];

    <?php if (!empty($biayaUdang)) : ?>
        // detail vendor ganti kg daging
        var selectedVendor = $('#vendor_id option:selected').text();
        var selectedDivisi = $('#divisi_id option:selected').text();

        if (selectedVendor === "") {
            $(".kg-daging-vendor").text("KG DAGING VENDOR");
        } else {
            $(".kg-daging-vendor").text("KG DAGING " + selectedVendor);
        }

        if (selectedDivisi === "") {
            $(".kg-daging-divisi").text("KG DAGING DEPARTEMEN");
        } else {
            $(".kg-daging-divisi").text("KG DAGING " + selectedDivisi);
        }

        let arr = $('.multiple_jasa_vendor_in_id').val();
        $.ajax({
            url: `<?= base_url('biaya-udang/list-barang'); ?>`,
            method: "GET",
            data: {
                multiple_jasa_vendor_in_id: JSON.stringify(arr),
                id: $('.id').val()
            },
            dataType: "json",
            success: function(res) {
                csrf.val(res.token);
                listBarang = res.data;
                listTotal = res.dataTotal;
                drawTable();
            }
        });
    <?php endif; ?>

    $(".tanggal").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });


    $('#vendor_id').select2({
        placeholder: "Pilih Vendor",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // DROPDOWN DIVISI
        getListDivisi();
        //ganti kg daging fauzy sesuai dengan vendor di select
        var selectedVendor = $('#vendor_id option:selected').text();
        if (selectedVendor === "") {
            $(".kg-daging-vendor").text("KG DAGING");
        } else {
            $(".kg-daging-vendor").text("KG DAGING " + selectedVendor);
        }

    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // DROPDOWN JASA VENDOR IN
        getListJasaVendorIn();
        // AUTO GENERATE NOMOR
        changeStatus();
        // GANTI KOLOM DEPARTEMEN
        var selectedDivisi = $("#divisi_id option:selected").text();
        if (selectedDivisi === "") {
            $(".kg-daging-divisi").text("KG DAGING DEPARTEMEN");
        } else {
            $(".kg-daging-divisi").text("KG DAGING " + selectedDivisi);
        }
    });


    $('.multiple_jasa_vendor_in_id').select2({
        placeholder: "Pilih Surat Jalan",
        theme: "bootstrap-5",
    }).change(function() {
        // GET LIST BARANG
        listDataBarang();
    });

    $("#vendor_id,#divisi_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    // VALIDATOR
    var validator = $(".create-form").validate({
        rules: {
            tanggal: {
                required: true
            },
        },
        messages: {
            tanggal: {
                required: "Tanggal wajib diisi"
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

    $('.btn-submit-parent').click(function() {
        if (!listBarang || Object.keys(listBarang).length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Barang yang akan dibayar tidak boleh kosong !',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
            return;
        }
        if ($('.create-form').valid()) {
            let isValid = true;
            let errorMessage = '';
            let errorData = null;

            Object.values(listBarang).forEach(parentBlock => {
                const p = parentBlock.parent;

                // Tanggal PO
                const tanggalPO = $(`.tanggal_po[data-parent_id="${p.id}"]`);

                // KG Daging
                const kgDaging = $(`.kg_daging[data-parent_id="${p.id}"]`);

                // Harga per kilo
                const hargaPerKilo = $(`.harga_per_kilo[data-parent_id="${p.id}"]`);

                // Assign ke listBarang (pakai jQuery semuanya)
                p.tanggal_po = tanggalPO.val();
                p.kg_daging = parseFloat(kgDaging.val());
                p.harga_per_kilo = parseFloat(hargaPerKilo.val()) || 0;

                // Hitung total harga berdasarkan harga_per_kilo dan sum_bersih
                p.total_harga = p.harga_per_kilo * (p.sum_bersih || 0);
            });

            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: errorMessage,
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
                return;
            }

            // Konfirmasi Simpan
            Swal.fire({
                icon: 'question',
                title: 'Simpan Data ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    submitData();
                }
            });
        }
    });

    function submitData() {
        let id = $('#id').val();
        let data = new FormData(document.querySelector(".create-form"));
        data.append('listBarang', JSON.stringify(listBarang));

        const url = id ? "<?= base_url("biaya-udang/update"); ?>" : "<?= base_url("biaya-udang/save"); ?>";

        $.ajax({
            url: url,
            data: data,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                setLoading();
            },
            complete: function() {
                stopLoading()
            },
            method: "POST",
            dataType: "json",
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: response.message,
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "<?= base_url("biaya-udang") ?>";
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: response.message,
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi kesalahan saat menyimpan data',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            }
        });
    }

    function listDataBarang() {
        let arr = $('.multiple_jasa_vendor_in_id').val();
        $.ajax({
            url: `<?= base_url('biaya-udang/list-barang'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                multiple_jasa_vendor_in_id: JSON.stringify(arr),
            },
            dataType: "json",
            success: function(res) {
                csrf.val(res.token);
                listBarang = res.data;
                listTotal = res.dataTotal;
                drawTable();
            }
        });
    }


    function drawTable() {
        const tbody = document.querySelector('#dataTable tbody');
        tbody.innerHTML = '';

        if (!listBarang || Object.keys(listBarang).length === 0) {
            tbody.innerHTML = '<tr><td colspan="11" class="no-data">Tidak Ada Barang</td></tr>';
            return;
        }

        let grand_total = 0;
        let no = 1;

        Object.values(listBarang).forEach(parentBlock => {
            const p = parentBlock.parent;
            const detail = parentBlock.detail;
            const rowspan = detail.length > 0 ? detail.length : 1;

            // Hitung total harga berdasarkan harga_per_kilo dan sum_bersih
            const total_harga = (p.sum_bersih || 0) * (p.harga_per_kilo || 0);
            grand_total += total_harga;

            // =========================
            // PARENT ROW
            // =========================
            const mainRow = document.createElement('tr');
            mainRow.style.color = 'black';
            mainRow.style.background = "#fafafa";

            // Kolom No
            const noCell = document.createElement('td');
            noCell.rowSpan = rowspan;
            noCell.textContent = no++;
            noCell.style = "vertical-align:middle; text-align:center;";
            mainRow.appendChild(noCell);

            // PO
            const poCell = document.createElement('td');
            poCell.rowSpan = rowspan;
            poCell.innerHTML = `
                <input type="date" class="tanggal_po" data-parent_id="${p.id}"
                value="${p.tanggal_masuk}"
                style="height:38px; width:130px; border-radius:6px; padding:4px; border:1px solid #ccc;">
            `;
            poCell.style = "vertical-align:middle; text-align:center;";
            mainRow.appendChild(poCell);

            // Jenis
            const jenisCell = document.createElement('td');
            jenisCell.rowSpan = rowspan;
            jenisCell.textContent = p.barang_name;
            jenisCell.style = "vertical-align:middle;";
            mainRow.appendChild(jenisCell);

            // Mentch
            const mentchCell = document.createElement('td');
            mentchCell.rowSpan = rowspan;
            mentchCell.textContent = p.spesifikasi;
            mentchCell.style = "vertical-align:middle;";
            mainRow.appendChild(mentchCell);

            // KG Keluar dari detail[0]
            const kgKeluarCell = document.createElement('td');
            kgKeluarCell.textContent = detail[0]?.qty_keluar?.toFixed(2) ?? "0.00";
            kgKeluarCell.style = "text-align:right;";
            mainRow.appendChild(kgKeluarCell);

            // Kotor
            const kgKotorCell = document.createElement('td');
            kgKotorCell.rowSpan = rowspan;
            kgKotorCell.textContent = p.sum_kotor?.toFixed(2) ?? "0.00";
            kgKotorCell.style = "vertical-align:middle; text-align:right;";
            mainRow.appendChild(kgKotorCell);

            // Canning
            const kgCanningCell = document.createElement('td');
            kgCanningCell.rowSpan = rowspan;
            kgCanningCell.textContent = p.sum_bersih?.toFixed(2) ?? "0.00";
            kgCanningCell.style = "vertical-align:middle; text-align:right;";
            mainRow.appendChild(kgCanningCell);

            // KG Daging
            const kgDagingCell = document.createElement('td');
            kgDagingCell.rowSpan = rowspan;
            kgDagingCell.textContent = p.sum_bersih?.toFixed(2) ?? "0.00";
            kgDagingCell.style = "vertical-align:middle; text-align:right;";
            mainRow.appendChild(kgDagingCell);

            // Ratio
            const ratioCell = document.createElement('td');
            ratioCell.rowSpan = rowspan;
            ratioCell.textContent = (p.ratio?.toFixed(2) ?? "0.00") + "%";
            ratioCell.style = "vertical-align:middle; text-align:center;";
            mainRow.appendChild(ratioCell);

            // Harga per kilo
            const tbHargaCell = document.createElement('td');
            tbHargaCell.rowSpan = rowspan;
            tbHargaCell.innerHTML = `
                <input class="harga_per_kilo" 
                    data-parent_id="${p.id}" 
                    data-per_kilo="${p.sum_bersih}" 
                    value="${p.harga_per_kilo ?? ''}"
                    style="height:38px; width:120px; border-radius:6px; padding:4px; border:1px solid #ccc;">
            `;
            tbHargaCell.style = "vertical-align:middle;";
            mainRow.appendChild(tbHargaCell);

            // Total harga (READONLY)
            const totalHargaCell = document.createElement('td');
            totalHargaCell.rowSpan = rowspan;
            totalHargaCell.innerHTML = `
                <input class="total_harga_parent" data-parent_id="${p.id}"
                    value="${total_harga.toLocaleString()}"
                    readonly
                    style="height:38px; width:150px; text-align:right; border-radius:6px; padding:4px; border:1px solid #ccc; background-color:#f8f9fa;">
            `;
            totalHargaCell.style = "vertical-align:middle;";
            mainRow.appendChild(totalHargaCell);

            tbody.appendChild(mainRow);

            // =========================
            // DETAIL ROWS
            // =========================
            for (let i = 1; i < detail.length; i++) {
                const d = detail[i];

                const detRow = document.createElement('tr');
                detRow.style.background = "#fff";

                const kgKeluarDetail = document.createElement('td');
                kgKeluarDetail.textContent = d.qty_keluar?.toFixed(2) ?? "0.00";
                kgKeluarDetail.style = "text-align:right;";
                detRow.appendChild(kgKeluarDetail);

                tbody.appendChild(detRow);
            }
        });

        // Baris grand total
        const grandTotalRow = document.createElement('tr');
        grandTotalRow.className = 'grand-total-row';
        grandTotalRow.style.color = 'black';
        grandTotalRow.innerHTML = `
            <td colspan="10"><b>GRAND TOTAL</b></td>
            <td id="grand_total">${grand_total.toLocaleString()}</td>
        `;
        tbody.appendChild(grandTotalRow);

        // Tambahkan event listener untuk input harga
        document.querySelectorAll('.harga_per_kilo').forEach(input => {
            input.addEventListener('keyup', function() {
                const parentId = this.getAttribute('data-parent_id');
                const harga = parseFloat(this.value) || 0;
                const sumBersih = parseFloat(this.getAttribute('data-per_kilo')) || 0;
                const total = harga * sumBersih;

                // Update input total_harga_parent (readonly)
                document.querySelector(`.total_harga_parent[data-parent_id="${parentId}"]`).value = total.toLocaleString();
                
                updateGrandTotal();
            });
        });
    }

    // ======================================================
    // KEYUP HANDLER — AUTO HITUNG TOTAL
    // ======================================================
    $(document).on("keyup", ".harga_per_kilo", function () {
        const parentId = $(this).data("parent_id");

        // harga per kilo yang diinput user
        const harga = parseFloat($(this).val()) || 0;

        // ambil nilai bersih dari attribute data-per_kilo
        const sumBersih = parseFloat($(this).data("per_kilo")) || 0;

        // hitung total
        const total = harga * sumBersih;

        console.log("Harga:", harga, "Sum Bersih:", sumBersih, "Total:", total);

        // update input total_harga_parent (readonly)
        $(`.total_harga_parent[data-parent_id="${parentId}"]`).val(total.toLocaleString());

        updateGrandTotal();
    });

    function updateGrandTotal() {
        let grand_total = 0;
        
        document.querySelectorAll('.total_harga_parent').forEach(input => {
            const value = input.value.replace(/[^0-9.-]+/g, '');
            grand_total += parseFloat(value) || 0;
        });
        
        document.getElementById('grand_total').textContent = grand_total.toLocaleString();
    }


    function getListDivisi() {
        // GET LIST DIVISI
        $.ajax({
            url: `<?= base_url('biaya-udang/list-divisi'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                vendor_id: $(".vendor_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".divisi_id").empty()
                $(".divisi_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".divisi_id").append(`<option value="${item.id}">${item.divisi}</option>`)
                })
                $(".divisi_id").val();
            }
        });
    }

    function getListJasaVendorIn() {
        $.ajax({
            url: `<?= base_url('biaya-udang/list-jasa-vendor-in'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                divisi_id: $('.divisi_id option:selected').val(),
                vendor_id: $('.vendor_id option:selected').val(),
            },
            dataType: "json",
            success: function(res) {
                $(".multiple_jasa_vendor_in_id").empty()
                $(".multiple_jasa_vendor_in_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".multiple_jasa_vendor_in_id").append(`<option value="${item.id}">${item.no_penerimaan_surat_jalan}</option>`)
                })
                $(".multiple_jasa_vendor_in_id").val();

            }
        });
    }

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $(".no_pembayaran").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("biaya-udang/get-no"); ?>`,
                method: "GET",
                data: {
                    divisi_id: $('#divisi_id option:selected').val()
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".no_pembayaran").val(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $(".no_pembayaran").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $(".no_pembayaran").val("");
                    }
                }
            })
        } else {
            $(".no_pembayaran").attr("readonly", false);
            $(".no_pembayaran").val("");
        }
    }

    function preventNegativeInput(inputElement) {
        var inputValue = inputElement.value;
        var numericValue = inputValue.replace(/[^0-9.]/g, '');
        numericValue = numericValue.replace(/^0+/g, '');
        numericValue = numericValue.replace(/^\./g, '0.');
        if (parseFloat(numericValue) < 0 || isNaN(parseFloat(numericValue))) {
            inputElement.value = '0';
        } else {
            inputElement.value = numericValue;
        }
    }


    const print = function(url) {
        window.open(url, "_blank");
    }

    const posting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Biaya Udang ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("biaya-udang/posting"); ?>",
                    data: {
                        id: id
                    },
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
                        csrf.val(response.token);
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            }).then((result) => {
                                window.location.href = "<?= base_url("biaya-udang") ?>";
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                        }
                    },
                });
            }
        })
    }

    const remove = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Biaya Udang ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("biaya-udang/delete"); ?>",
                    data: {
                        id: id
                    },
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            }).then((result) => {
                                location.reload();
                            });
                        }
                    },
                });
            }
        })
    }

    function autoComplete() {
        $.each(listBarang, function(i, v) {
            var tbHargaElement = $('input[data-barang_master_id="' + v.barang_master_id + '"].tb_harga');
            var kgFauzyElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].kg_fauzy');
            var kgCnElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].kg_cn');
            var kgDagingElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].kg_daging');
            var tanggalPOElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].tanggal_po');

            // assign
            listBarang[i].tb_harga = tbHargaElement.val();
            listBarang[i].kg_fauzy = kgFauzyElement.val();
            listBarang[i].kg_cn = kgCnElement.val();
            listBarang[i].kg_daging = kgDagingElement.val();
            listBarang[i].tanggal_po = tanggalPOElement.val();
        });

        let data = new FormData();
        data.append('listBarang', JSON.stringify(listBarang));

        if (request) {
            request.abort();
        }
        var focusedElementId = document.activeElement.id;
        request = $.ajax({
            url: "<?= base_url('biaya-udang/autocomplete'); ?>",
            data: data,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            method: "POST",
            dataType: "json",
            processData: false,
            contentType: false,
            success: function(res) {
                csrf.val(res.token);
                listBarang = res.data;
                listTotal = res.dataTotal;
                drawTable();

                if (focusedElementId) {
                    var newFocusedElement = document.getElementById(focusedElementId);
                    if (newFocusedElement) {
                        newFocusedElement.focus();
                        var val = newFocusedElement.value;
                        newFocusedElement.value = '';
                        newFocusedElement.value = val;
                    }
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (textStatus !== 'abort') {
                    console.error('Error:', textStatus, errorThrown);
                }
            }
        });

    }

    function formatRupiah(angka) {
        var reverse = angka.toString().split('').reverse().join('');
        var ribuan = reverse.match(/\d{1,3}/g);
        var formatted = ribuan.join('.').split('').reverse().join('');
        return '' + formatted;
    }
</script>

<?= $this->endSection(); ?>