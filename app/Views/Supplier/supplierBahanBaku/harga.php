<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Set Harga Barang</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("supplier-bahan-baku"); ?>">
                Batal
            </a>
            <?php if(!empty($dataSupplier)){ ?>
            <button class="btn btn-show-form btn-save float-right btn-submit-harga">
                Simpan
            </button>
            <?php } ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <?= csrf_field() ?>
            <table width="100%" class="mb-3">
                <tbody>
                    <tr style="color: black;">
                        <td width="150px">Nama Supplier</td>
                        <td width="5px">:</td>
                        <td><?= empty($dataSupplier) ? "" : $dataSupplier->name; ?></td>
                    </tr>
                    <tr style="color: black; height: 20px;">
                        <td colspan="3"></td>
                    </tr>
                    <tr style="color: black;">
                        <td width="150px">Alamat</td>
                        <td width="25px">:</td>
                        <td><?= empty($dataSupplier) ? "" : $dataSupplier->address; ?></td>
                    </tr>
                </tbody>
            </table>
            <input autocomplete="one-time-code" value="<?= empty($dataSupplier) ? "" : $dataSupplier->id; ?>" type="hidden" class="id_supplier" name="id_supplier" id="id_supplier" />
            <input autocomplete="one-time-code" type="hidden" class="id_supplier_harga" name="id_supplier_harga" id="id_supplier_harga" />
            <input autocomplete="one-time-code" type="hidden" class="row" name="row" id="row" />
            <form class="harga-form" role="form" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select bahan_baku" name="bahan_baku" id="bahan_baku" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataBarang as $b) : ?>
                                    <option value="<?= $b["id"]; ?>">
                                        <?= $b["barang_name"]; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Nama Barang</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select bagian" name="bagian" id="bagian" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataBagian as $b) : ?>
                                    <option value="<?= $b->id; ?>">
                                        <?= $b->nama_bagian; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Bagian</label>
                        </div>
                    </div>
                </div>
                <div class="row"> 
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control spesifikasi" id="spesfikasi" name="spesifikasi" placeholder="Spesifikasi">
                            <label for="floatingInput">Spesifikasi</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="0" autocomplete="one-time-code" type="number" class="form-control harga_umum" id="harga_umum" name="harga_umum" placeholder="Harga Umum">
                            <label for="floatingInput">Harga Umum</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="0" autocomplete="one-time-code" type="number" class="form-control harga_harian" id="harga_harian" name="harga_harian" placeholder="Harga Harian">
                            <label for="floatingInput">Harga Harian</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="0" autocomplete="one-time-code" type="number" class="form-control harga_bulanan" id="harga_bulanan" name="harga_bulanan" placeholder="Harga Bulanan">
                            <label for="floatingInput">Harga Bulanan</label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">

                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-add btn-block float-right" onclick="createHarga()">
                            <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                        </button>
                        <button style="border-color: #e7323a !important; background-color: #e7323a !important; margin-right: 10px !important;" class="btn btn-add btn-block float-right" onclick="setHarga()">
                            Reset
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th width="10">No</th>
                                <th>Nama Barang</th>
                                <th>Nama Bagian</th>
                                <th>Spesifikasi</th>
                                <th>Harga Umum</th>
                                <th>Harga Harian</th>
                                <th>Harga Bulanan</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">
                        <?php 
                            $no = 0;    
                            foreach($dataSupplierHarga as $item){ 
                                $no = $no + 1;
                        ?>
                            <tr>
                                <td>
                                    <?= $no; ?>
                                </td>
                                <td>
                                    <?= $item["barang_name"]; ?>
                                </td>
                                <td>
                                    <?= $item["nama_bagian"]; ?>
                                </td>
                                <td>
                                    <?= $item["spesifikasi"]; ?>
                                </td>
                                <td>
                                    <?= "Rp " . number_format(formatter($item["harga_umum"], "STR_TO_FLOAT"), 2, '.', ','); ?>
                                </td>
                                <td>
                                    <?= "Rp " . number_format(formatter($item["harga_harian"], "STR_TO_FLOAT"), 2, '.', ','); ?>
                                </td>
                                <td>
                                    <?= "Rp " . number_format(formatter($item["harga_bulanan"], "STR_TO_FLOAT"), 2, '.', ','); ?>
                                </td>
                                <td>
                                    <button onclick="editHarga('<?= $no; ?>')" class="btn btn-warning posting-spp">
                                        <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                                    </button>
                                    <button onclick="deleteHarga('<?= $no; ?>')" class="btn btn-danger">
                                        <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let list_item = [];
    let list_delete = [];
    var row = 0;

    <?php if($dataSupplierHarga){ 
        $no = 1;    
        foreach($dataSupplierHarga as $item){  
    ?>
        row = row + 1;

        list_item.push({
            row: Number(row),
            id: Number('<?= $item["id"]?>'),
            bahan_baku_id: Number('<?= $item["bahan_baku_id"]?>'),
            bagian_id: Number('<?= $item["bagian_ids"]?>'),
            barang_name: '<?= $item["barang_name"]?>',
            bagian_name: '<?= $item["nama_bagian"]?>',
            spesifikasi: '<?= $item["spesifikasi"]?>',
            harga_umum: Number('<?= $item["harga_umum"] ? $item["harga_umum"] : 0; ?>').toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
            harga_harian: Number('<?= $item["harga_harian"] ? $item["harga_harian"] : 0; ?>').toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
            harga_bulanan: Number('<?= $item["harga_bulanan"] ? $item["harga_bulanan"] : 0; ?>').toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
        })
    <?php } 
        }
    ?>

    $('.dataTable').DataTable({
        ordering: false,
        //responsive: true,
        display: 'stripe',
        searching: false,
        lengthChange: false,
        pageLength: 25,
        columnDefs: [{
            defaultContent: '-',
            targets: '_all'
        }],
        "initComplete": function (settings, json) {  
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
        },
        language: {
            emptyTable: "Belum Ada Data Harga"
        }
    })

    $('.bahan_baku').select2({
        placeholder: "Pilih Nama Barang",
        theme: "bootstrap-5"
    })

    $('.bagian').select2({
        placeholder: "Pilih Nama Bagian",
        theme: "bootstrap-5"
    })

    //CSS SELECT2 FLOATING LABEL
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

    var validator = $(".harga-form").validate({
        rules: {
            bahan_baku: {
                required: true
            },
            spesifikasi: {
                required: true
            },
            harga_umum: {
                required: true
            },
            harga_harian: {
                required: true
            },
            harga_bulanan: {
                required: true
            }
        },
        messages: {
            bahan_baku: {
                required: "Bahan Baku wajib diisi"
            },
            spesifikasi: {
                required: "Spesifikasi wajib diisi"
            },
            harga_umum: {
                required: "Harga Umum wajib diisi"
            },
            harga_harian: {
                required: "Harga Harian wajib diisi"
            },
            harga_bulanan: {
                required: "Harga Bulanan wajib diisi"
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

    $(".btn-submit-harga").click(function() {
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
                setLoading()
                const csrf = $(`[name="${csrfToken}"]`);

                let data = new FormData(document.querySelector(".harga-form"));
                data.append("supplier_id", $(".id_supplier").val())
                data.append("list_item", JSON.stringify(list_item))
                data.append("list_delete", JSON.stringify(list_delete))

                $.ajax({
                    url: "<?= base_url("supplier-harga/save"); ?>",
                    data: data,
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                            }).then(() => {
                                window.location.href = "<?= base_url("supplier-bahan-baku"); ?>";
                            })
                        } else {
                            stopLoading()
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                        }
                    },
                    onError: function(response) {
                        csrf.val(response.token);
                        stopLoading()
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Gagal Disimpan, coba Lagi',
                            confirmButtonColor: '#4e73df',
                        })
                    }
                });
            }
        })
    })

    let reset = function() {
        validator.resetForm();
        validator.reset();
        $(".harga-form")[0].reset()

        $(".row").val('');
        $(".id_supplier_harga").val('')
        $(".bahan_baku").val('').change()
        $(".bagian").val('').change()
        $(".harga_umum").val(0)
        $(".harga_harian").val(0)
        $(".harga_bulanan").val(0)

        $(".bahan_baku").removeAttr("disabled")
        $(".spesifikasi").removeAttr("disabled")
    }

    const setHarga = function() {
        reset()
    }

    const createHarga = function() {
        let row_detail = $(".row").val();
        let id_supplier_harga = $(".id_supplier_harga").val()
        let bahan_baku_id = $(".bahan_baku option:selected").val()
        let barang_name = $(".bahan_baku option:selected").text()
        let bagian_id = $(".bagian option:selected").val()
        let bagian_name = $(".bagian option:selected").text()
        let spesifikasi = $(".spesifikasi").val()
        let harga_umum = $(".harga_umum").val()
        let harga_harian = $(".harga_harian").val()
        let harga_bulanan = $(".harga_bulanan").val()

        if ($(".harga-form").valid()) {
            // update
            if(row_detail)
            {
                Swal.fire({
                    icon: 'question',
                    title: 'Yakin akan mengubah data?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        let tag_html = ""
                        row = 0;
                        let new_item = []

                        $(".body-detail-table").empty()

                        list_item.map(item => {
                            row = row + 1;
                            if(item.row === Number(row_detail))
                            {
                                tag_html += `<tr>`;
                                tag_html += "<td>";
                                tag_html += row;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += barang_name;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += bagian_name;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += spesifikasi;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += "Rp " + Number(harga_umum).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += "Rp " + Number(harga_harian).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += "Rp " + Number(harga_bulanan).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += `
                                <button class="btn btn-warning posting-spp mr-1" onclick="editHarga(${row})">
                                    <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                                </button><button class="btn btn-danger" onclick="deleteHarga(${row})">
                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                </button>`;
                                tag_html += "</td>";
                                tag_html += "</tr>";

                                new_item.push({
                                    ...item,
                                    row: row,
                                    id: id_supplier_harga,
                                    harga_umum: Number(harga_umum).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                                    harga_harian: Number(harga_harian).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                                    harga_bulanan: Number(harga_bulanan).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                                })
                            }
                            else
                            {
                                tag_html += `<tr>`;
                                tag_html += "<td>";
                                tag_html += row;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += item.barang_name;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += item.bagian_name;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += item.spesifikasi;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += "Rp " + item.harga_umum;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += "Rp " + item.harga_harian;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += "Rp " + item.harga_bulanan;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += `
                                <button class="btn btn-warning posting-spp mr-1" onclick="editHarga(${row})">
                                    <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                                </button><button class="btn btn-danger" onclick="deleteHarga(${row})">
                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                </button>`;
                                tag_html += "</td>";
                                tag_html += "</tr>";

                                new_item.push({
                                    ...item,
                                    row: row
                                })
                            }
                        })

                        list_item = new_item;
                        $(".body-detail-table").append(tag_html)
                        reset()
                    }
                })
            }
            // create
            else
            {
                // check if bahan baku, spesifikasi already exist
                let view_exist = list_item.find(item => (
                    item.spesifikasi === spesifikasi && item.bahan_baku_id === bahan_baku_id
                ))

                if(view_exist)
                {
                    Swal.fire({
                        icon: 'error',
                        title: "Bahan Baku, spesifikasi tidak boleh sama",
                        confirmButtonColor: '#4e73df',
                    })
                }
                else
                {
                    Swal.fire({
                        icon: 'question',
                        title: 'Yakin akan menambahkan data?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let tag_html = "";
                            row = 0;

                            $(".body-detail-table").empty()

                            list_item.map(item => {
                                row = row + 1;
                                tag_html += `<tr>`;
                                tag_html += "<td>";
                                tag_html += row;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += item.barang_name;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += item.bagian_name;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += item.spesifikasi;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += "Rp " + item.harga_umum;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += "Rp " + item.harga_harian;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += "Rp " + item.harga_bulanan;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += `
                                <button class="btn btn-warning posting-spp mr-1" onclick="editHarga(${row})">
                                    <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                                </button><button class="btn btn-danger" onclick="deleteHarga(${row})">
                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                </button>`;
                                tag_html += "</td>";
                                tag_html += "</tr>";
                            })
                            row = row + 1;
                            tag_html += `<tr>`;
                            tag_html += "<td>";
                            tag_html += row;
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += barang_name;
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += bagian_name;
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += spesifikasi;
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += "Rp " + Number(harga_umum).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += "Rp " + Number(harga_harian).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += "Rp " + Number(harga_bulanan).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += `
                            <button class="btn btn-warning posting-spp mr-1" onclick="editHarga(${row})">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button class="btn btn-danger" onclick="deleteHarga(${row})">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>`;
                            tag_html += "</td>";
                            tag_html += "</tr>";

                            $(".body-detail-table").append(tag_html)

                            list_item.push({
                                row: row,
                                id: id_supplier_harga,
                                bahan_baku_id: bahan_baku_id,
                                bagian_id: bagian_id,
                                barang_name: barang_name,
                                spesifikasi: spesifikasi,
                                harga_umum: Number(harga_umum).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                                harga_harian: Number(harga_harian).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                                harga_bulanan: Number(harga_bulanan).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                            })

                            reset()
                        }
                    })
                }
            }
        }
    }

    const editHarga = function(nilai_row) {
        validator.resetForm();
        validator.reset();
        $(".bahan_baku").attr("disabled", true)
        $(".spesifikasi").attr("disabled", true)

        let current_row = list_item.find(item => (
            item.row === Number(nilai_row)
        ));

        $(".row").val(current_row?.row);
        $(".id_supplier_harga").val(current_row?.id);
        $(".harga_harian").val(current_row?.harga_harian.replaceAll(",", ""));
        $(".harga_umum").val(current_row?.harga_umum.replaceAll(",", ""));
        $(".harga_bulanan").val(current_row?.harga_bulanan.replaceAll(",", ""));
        $(".bahan_baku").val(current_row?.bahan_baku_id).change();
        $(".bagian").val(current_row?.bagian_id).change();
        $(".spesifikasi").val(current_row?.spesifikasi);
    }

    const deleteHarga = function(nilai_row) {
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
                let tag_html = ""
                row = 0;
                let new_item = []

                $(".body-detail-table").empty()

                list_item.map(item => {
                    if(item.row === Number(nilai_row))
                    {
                        if(item.id)
                        {
                            list_delete.push(item)
                        }
                    }
                    else
                    {
                        row = row + 1;

                        tag_html += `<tr>`;
                        tag_html += "<td>";
                        tag_html += row;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += item.barang_name;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += item.spesifikasi;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += "Rp " + item.harga_umum;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += "Rp " + item.harga_harian;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += "Rp " + item.harga_bulanan;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += `
                        <button class="btn btn-warning posting-spp mr-1" onclick="editHarga(${row})">
                            <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                        </button><button class="btn btn-danger" onclick="deleteHarga(${row})">
                            <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                        </button>`;
                        tag_html += "</td>";
                        tag_html += "</tr>";

                        new_item.push({
                            ...item,
                            row: row
                        })
                    }
                })

                list_item = new_item;
                $(".body-detail-table").append(tag_html)
                reset()
            }
        })
    }
</script>

<?= $this->endSection(); ?>