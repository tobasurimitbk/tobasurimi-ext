<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>


<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($data) ? "Ubah" : "Tambah"; ?> Penjualan Lokal</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("order-form-lokal"); ?>">
                Batal
            </a>

            <?php if (!empty($data)) : ?>
                <!-- <a class="btn btn-save float-right" href="#"> -->
                <a class="btn btn-warning btn-print float-right" href="<?= base_url("order-form-lokal/print/{$data->id}"); ?>" target="_blank">
                    Print
                </a>
            <?php endif; ?>

            <button class="btn btn-show-form btn-save float-right btn-submit">
                Simpan
            </button>

        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-order-form-lokal" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($data) ? $data->id : ""; ?>" />
                <input autocomplete="one-time-code" type="hidden" class="tipe_sales_order" name="tipe_sales_order" id="tipe_sales_order" value="LOKAL" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" <?= !empty($data) ? 'readonly' : '' ?> class="form-control no_sales_order" id="no_sales_order" name="no_sales_order" placeholder="No. Sales Order" required <?= !empty($data) ? 'disabled value="' . $data->no_sales_order . '"' : '' ?>>
                                    <label for="floatingInput">No. Order</label>
                                </div>
                                <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 5px; margin-left: -30px; <?= !empty($data) ? 'display:none;' : '' ?>" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= !empty($data) ? 'readonly' : '' ?> autocomplete="one-time-code" name="order_date" type="text" value="<?= !empty($data) ? $data->order_date : date('d/m/Y', strtotime(date('Y-m-d'))) ?>" class="form-control order_date" id="order_date">
                                <label>Tanggal Pemesanan</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 25px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select id_customer" name="id_customer" id="id_customer" <?= !empty($data) ? 'disabled' : ''; ?>>
                                    <option value=""></option>
                                    <?php
                                    if (!empty($dataCustomers)) {
                                        foreach ($dataCustomers as $customer) {
                                    ?>
                                            <option value="<?= $customer['id']; ?>" data-tipepelanggan="<?= rawurlencode($customer['tipe_pelanggan']); ?>" data-customerphone="<?= rawurlencode($customer['phone']); ?>" data-address="<?= rawurlencode($customer['address']) ?>" data-termin="<?= rawurlencode($customer['termin']) ?>" data-salesname="<?= rawurlencode($customer['salesName']); ?>" data-jenis_penjualan="<?= rawurlencode($customer['jenis_penjualan']); ?>" <?= !empty($data) ? ($data->id_customer == $customer['id'] ? "selected" : "") : ""; ?>><?= $customer['kode']; ?> - <?= $customer['name']; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Nama Konsumen</label>
                            </div>
                            <div class="input-group-append" style="height:50px;">
                                <button class="btn btn-success btn-customer-add <?= !empty($data) ? (($data->used == "USED") ? 'disabled' : '') : '' ?>" id="btn-customer-add" data-toggle="modal" type="button">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control sales_name" id="sales_name" name="sales_name" disabled=true value="<?= $data->salesName ?? '' ?>">
                            <input autocomplete="one-time-code" type="hidden" class="form-control sales_name" id="id_user" name="id_user" value="<?= $id_user ?>">
                            <label for="floatingInput">Nama Sales</label>
                        </div>
                    </div> -->
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select jenis_penjualan" name="jenis_penjualan" id="jenis_penjualan" <?= !empty($data) ? 'disabled' : ''; ?>>
                                <option value=""></option>
                                <option value="1" <?= !empty($data) ? ($data->jenis_penjualan == 1 ? "selected" : "") : ""; ?>>By Sales</option>
                                <option value="2" <?= !empty($data) ? ($data->jenis_penjualan == 2 ? "selected" : "") : ""; ?>>By Office</option>
                                <option value="3" <?= !empty($data) ? ($data->jenis_penjualan == 3 ? "selected" : "") : ""; ?>>By Ecommerce</option>
                            </select>
                            <label for="floatingInput">Jenis Penjualan</label>
                        </div>
                    </div>
                    <div class="col-md-4 nama_sales_div">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select id_sales" name="id_sales" id="id_sales" <?= !empty($data) ? 'disabled' : ''; ?>>
                                <option value=""></option>
                                <?php foreach ($dataSales ?? [] as $sales) : ?>
                                    <option value="<?= $sales['id']; ?>" <?= !empty($data) ? ($data->sales_id == $sales['id'] ? "selected" : "") : ""; ?>><?= $sales['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Nama Sales</label>
                        </div>
                    </div>
                    <div class="col-md-4 nama_ecommerce_div">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" type="text" class="form-control nama_ecommerce" id="nama_ecommerce" name="nama_ecommerce" value="<?= !empty($data) ? $data->nama_ecommerce : ""; ?>">
                            <label for="floatingInput">Nama Ecommerce</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control customerphone" id="customerphone" name="customerphone" value="<?= $data->customerPhone ?? ''; ?>" disabled>
                            <label for="floatingInput">No. Telp</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control" id="tagihan_ke" name="tagihan_ke" disabled value="<?= $data->address ?? '' ?>">
                            <label for="floatingInput">Alamat Konsumen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select termin" name="termin" id="termin" <?= !empty($data) ? 'disabled' : ''; ?>>
                                <option value=""></option>
                                <?php foreach ($dataTermin ?? [] as $termin) : ?>
                                    <option value="<?= $termin['id']; ?>" <?= !empty($data) ? ($data->payment_terms == $termin['id'] ? "selected" : "") : ""; ?>><?= $termin['value']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <!-- <input autocomplete="one-time-code" type="text" class="form-control" id="termin" name="termin" readonly value="<?= $data->termin ?? '' ?>"> -->
                            <label for="floatingInput">Termin</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="hidden" class="hidden_tipe_pelanggan" id="hidden_tipe_pelanggan" name="hidden_tipe_pelanggan" value="<?= $data->tipe_pelanggan ?? '' ?>">
                            <input autocomplete="one-time-code" disabled class="form-control input-picker tipe_pelanggan" id="tipe_pelanggan" name="tipe_pelanggan" placeholder="Tipe Pelanggan" value="<?= $data->tipe_pelanggan_value ?? '' ?>">
                            <label for="floatingInput">Tipe Pelanggan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= !empty($data) ? 'readonly' : '' ?> autocomplete="one-time-code" name="shipping_date" type="text" value="<?= !empty($data) ? $data->shipping_date : '' ?>" class="form-control shipping_date" id="shipping_date">
                                <label>Tanggal Pengiriman</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 25px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($data) ? 'readonly' : '' ?> onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');formatNumber(this)" autocomplete="one-time-code" class="form-control input-picker" id="estimated_freight" name="estimated_freight" value="<?= number_format($data->estimated_freight ?? 0); ?>" placeholder="Biaya Kirim">
                            <label for="floatingInput">Biaya Kirim</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating" style="height: 50px;">
                            <select <?= !empty($data) ? 'disabled' : ''; ?> class="form-select aju_document_type" id="aju_document_type" name="aju_document_type" aria-label="Floating label select example">

                                <option value="1445" selected>BC 3.0</option>
                            </select>
                            <label for="floatingInput">Dokumen Pabean (Opsional)</label>
                        </div>
                        <small class="mb-3"><i>Kosongkan jika non pabean</i></small>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating ff-ket mb-3" style="height: 80px;">
                            <textarea autocomplete="one-time-code" <?= !empty($data) ? 'disabled=true' : ''; ?> class="form-control parent_keterangan text-area-all" style="height: 100%" id="parent_keterangan" name="parent_keterangan" placeholder="keterangan"><?= !empty($data) ? $data->keterangan : ""; ?></textarea>
                            <label for="floatingInput">Keterangan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($data) ? 'readonly' : '' ?> autocomplete="one-time-code" type="text" class="form-control 
                            no_po" id="no_po" name="no_po" value="<?= !empty($data) ? $data->no_po : ""; ?>">
                            <label for="floatingInput">No PO</label>
                        </div>
                    </div>
                </div>

            </form>

            <!-- list barang -->
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Barang</label>
                    </div>

                    <div class="col-md-6">
                        <button class="btn btn-show-detail btn-add btn-block float-right <?= !empty($data) ? (($data->used == "USED") ? 'disabled' : '') : '' ?>" data-btn="detail-modal">
                            <i class="fa fa-plus fa-sm mr-2 " aria-hidden="true"></i>Tambah
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-tambah-spp" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Qty</th>
                                <th>Satuan</th>
                                <th>Harga Satuan</th>
                                <th>Discount (%)</th>
                                <th>PPN</th>
                                <th>Amount</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-borderless" width="100%" cellspacing="0">
                        <tr>
                            <td class="font-weight-bold" style="height: 40px;">Sub Total</td>
                            <td class="font-weight-bold text-right" style="height: 40px;">Rp. <span id="itemSubTotal">0</span></td>
                        </tr>
                        <tr>
                            <td style="height: 40px;">Discount</td>
                            <td class="text-right" style="height: 40px;">Rp. <span id="discTotal">0</span></td>
                        </tr>
                        <!-- <tr>
                            <td style="height: 40px;">PPn (11%)</td>
                            <td class="text-right" style="height: 40px;">Rp. <span id="taxTotal">0</span></td>
                        </tr> -->
                        <tr>
                            <td style="height: 40px;">Biaya Kirim</td>
                            <td class="text-right" style="height: 40px;">Rp. <span id="freightCost"><?= number_format($data->estimated_freight ?? 0); ?></span></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold" style="border-top: 1px solid #929292; height:40px;">Total Order </td>
                            <td style="border-top: 1px solid #929292; height: 40px;" class="text-right font-weight-bold">Rp. <span id="grandTotal">0</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- modal barang -->
<div class="modal detail-modal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label class="title-detail-name"></label> Barang</h5>
            </div>
            <div class="modal-body">
                <form class="detail-form" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id_detail" name="id_detail" id="id_detail" />
                    <!-- <input autocomplete="one-time-code" type="hidden" class="id_barang" name="id_barang" id="id_barang" /> -->

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select id_barang" name="id_barang" id="id_barang" aria-label="Floating label select example">
                                        <option value=""></option>
                                    </select>
                                    <label for="floatingInput">Nama Barang</label>
                                </div>
                                <?php if (can('Penjualan Lokal', 'Master Barang', 'c')) : ?>
                                    <div class="input-group-append" style="height:50px;">
                                        <button class="btn btn-success btn-barang-add" id="btn-barang-add" data-toggle="modal" type="button">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control satuan" name="satuan" id="satuan" placeholder="Satuan">
                                <label for="floatingInput">Satuan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" onkeyup="formatNumber(this)" type="text" class="form-control harga" name="harga" id="harga" placeholder="Harga Barang">
                                <label for="floatingInput">Harga Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" oninput="this.value=this.value.replace(/[^0-9.]/g,'');" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                                <label for="floatingInput">Qty</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control amount" name="amount" id="amount" placeholder="Total Harga" oninput="this.value=this.value.replace(/[^0-9.]/g,'');">
                                <label for="floatingInput">Total Harga</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control discount_percentage" name="discount_percentage" id="discount_percentage" placeholder="discount">
                                <label for="floatingInput">disc%</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control keterangan" name="keterangan" id="keterangan" placeholder="Keterangan">
                                <label for="floatingInput">Keterangan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control keteranganppn" name="keteranganppn" id="keteranganppn" placeholder="keteranganppn">
                                <label for="floatingInput">Status PPN</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="hidden" readonly="true" class="form-control statusppn" name="statusppn" id="statusppn" placeholder="statusppn">
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-detail btn-discard mr-3">Batal</button>
                <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
                <!-- <button type="button" class="btn btn-discard delete-btn delete-detail delete-form">Hapus</button> -->
            </div>
        </div>
    </div>
</div>

<div class="modal addCustomerModal" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Customer</h5>
            </div>
            <div class="modal-body">
                <form class="create-form-customer" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="tipe_customer" value="LOKAL">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control name" id="name" name="name" placeholder="Nama">
                                <label for="floatingInput">Nama</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" minlength="16" maxlength="16" class="form-control nik" id="nik" name="nik" placeholder="NIK (Opsional)">
                                <label for="floatingInput">NIK (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control no_npwp" id="no_npwp" name="no_npwp" placeholder="NPWP (Opsional)">
                                <label for="floatingInput"> NPWP (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <textarea autocomplete="one-time-code" class="form-control address" id="address" name="address"></textarea>
                                <label for="floatingInput">Alamat</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select province_parent_id" name="province_parent_id" id="province_parent_id" onchange="getCityParent()">
                                            <option value=""></option>
                                            <?php
                                            if (!empty($dataProvinces)) {
                                                foreach ($dataProvinces as $province) {
                                            ?>
                                                    <option value="<?= $province["id"]; ?>"><?= strtoupper($province["province_name"]); ?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <label for="floatingInput">Provinsi (Opsional)</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select city_parent_id" name="city_parent_id" id="city_parent_id">
                                            <option value="" data-code=""></option>
                                        </select>
                                        <label for="floatingInput">Kota (Opsional)</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" minlength="5" maxlength="5" class="form-control parent_postal_code" id="parent_postal_code" name="parent_postal_code" placeholder="Kode Pos (Opsional)">
                                        <label for="floatingInput">Kode Pos (Opsional)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control phone" id="phone" name="phone" placeholder="No. Telepon (Opsional)">
                                <label for="floatingInput">No. Telepon (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control contact_person" id="contact_person" name="contact_person" placeholder="Contact Person (Opsional)">
                                <label for="floatingInput">Nama PIC (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="email" class="form-control email" id="email" name="email" placeholder="Email (Opsional)">
                                <label for="floatingInput">Email (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select currency" id="currency" name="currency">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Mata Uang (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select termin" id="termin" name="termin">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Termin (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" value="0" type="text" onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');" onchange="this.value = formatRupiah(this.value);" class="form-control piutang" id="piutang" name="piutang" placeholder="Limit Piutang">
                                <label for="floatingInput">Limit Piutang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select tipe_pelanggan" name="tipe_pelanggan" id="tipe_pelanggan">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Tipe Pelanggan (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" disabled value="<?= session()->get('login')->name; ?>" class="form-control sales_id" id="sales_id" name="sales_id" placeholder="Nama Sales">
                                <label for="floatingInput">Nama Sales</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2 btn-discard-customer" id="btn-discard-customer">Batal</button>
                <button type="submit" class="btn btn-submit-form btn-submit-customer">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal add-modal" id="addMasterBarangModal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Master Barang</h5>
            </div>
            <div class="modal-body">
                <?= csrf_field() ?>
                <form class="create-form-master-barang" role="form" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <div class="input-group input-group-password">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" id="kode_barang" class="form-control kode_barang" name="kode_barang" placeholder="Kode Barang">
                                        <label for="floatingInput">Kode Barang</label>
                                    </div>
                                    <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                        <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 5px; margin-left: -30px;" id="generate_new_code" name="generate_new_code" type="checkbox" onchange="generateCodeMasterBarang()">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control barang_name" name="barang_name" id="barang_name" placeholder="Nama Kemasan">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select satuan_id" name="satuan_id" id="satuan_id">
                                    <option value=""></option>
                                    <?php if (!empty($dataSatuan)) : ?>
                                        <?php foreach ($dataSatuan as $d) : ?>
                                            <option value="<?= $d['id'] ?>"><?= $d['kode_satuan'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <label for="floatingInput">Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');" onchange="this.value = formatRupiah(this.value);" autocomplete="one-time-code" type="text" class="form-control harga_pokok" name="harga_pokok" id="harga_pokok" placeholder="Harga Pokok">
                                <label for="floatingInput">Harga Pokok</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');" onchange="this.value = formatRupiah(this.value);" autocomplete="one-time-code" type="text" class="form-control harga_jual" name="harga_jual" id="harga_jual" placeholder="Harga Jual">
                                <label for="floatingInput">Harga Jual</label>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard btn-discard-master-barang mr-2">Batal</button>
                <button type="submit" class="btn btn-submit-form btn-submit-form-master-barang">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';

    let list_items = [];
    let list_delete = [];
    var row = 0;
    var total_harga_barang = 0;
    var total_qty = 0;
    var total_harga = 0;
    var priceEdit = 0;
    var totalPriceEdit = 0;
    let no = 0;

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        info: false,
        paging: false,
        fixedHeader: true,
        display: "stripe",
        searching: false,
        ordering: false,
        columns: [{
                data: "no",
                className: "text-center",
            },
            {
                data: "kode_barang",
                className: "text-center"
            },
            {
                data: "nama_barang",
                className: "text-center"
            },
            {
                data: "qty",
                className: "text-center"
            },
            {
                data: "satuan",
                className: "text-center"
            },
            {
                data: "harga_barang",
                className: "text-center"
            },
            {
                data: "disc",
                className: "text-center"
            },
            {
                data: "statusppn",
                className: "text-center",
                render: function(data, type, row) {
                    if (data && data != 0) {
                        return "<i class='fa fa-check' aria-hidden='true' style='color:green;'></i>";
                    } else { // Otherwise, display a dash "-"
                        return "<i class='fa fa-minus' aria-hidden='true' style='color:red;'></i>";
                    }
                }
            },
            {
                data: "amount",
                className: "text-center"
            },
            {
                data: "status",
                className: "text-center actions",
                render: function(data, type, row) {
                    let id = row.id;
                    let disableButton = "<?= !empty($data) && $data->used == 'USED' ? 'disabled' : '' ?>";
                    return `
                    <div class="">
                        <button data-no="${row.no}" data-id="${row.id}" class="" ${disableButton}><i class="fa fa-trash" aria-hidden="true"></i></button>
                    </div>
                `;

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
        <?php if (!empty($data)) {
            foreach ($data->detail as $payload) {
        ?>
                no = no + 1;
                list_items.push({
                    no: no,
                    id: <?= $payload['id'] ?>,
                    id_barang: <?= $payload['id_barang'] ?>,
                    nama_barang: "<?= $payload['nama_barang'] ?>",
                    harga: "<?= $payload['harga_barang'] ?>",
                    qty: "<?= $payload['qty'] ?>",
                    amount: "<?= $payload['amount'] ?>",
                    keterangan: "<?= $payload['keterangan'] ?>",
                    statusppn: "<?= $payload['tax'] ?>",
                    tax: null,
                    discount_percentage: <?= $payload['discount_percentage'] ?? 0 ?>,
                    isDeleted: false,
                    kode_barang: '<?= $payload['kode_barang'] ?>',
                    satuan: '<?= $payload['satuan'] ?>',
                    harga_barang: Number('<?= $payload['harga_barang'] ?>').toLocaleString(),
                    barangTotal: "<?= $payload['barangTotal'] ?>",
                    disc: "<?= $payload['discount_percentage'] ?? 0 ?>",
                    taxAmt: null,
                    discAmt: <?= $payload['barangTotal'] ?> * (<?= $payload['discount_percentage'] ?? 0 ?> / 100),
                });

                table.row.add({
                    no: no,
                    id: <?= $payload['id'] ?>,
                    id_barang: <?= $payload['id_barang'] ?>,
                    nama_barang: "<?= $payload['nama_barang'] ?>",
                    harga: "<?= $payload['harga_barang'] ?>",
                    qty: "<?= $payload['qty'] ?>",
                    amount: "<?= $payload['amount'] ?>",
                    keterangan: "<?= $payload['keterangan'] ?>",
                    statusppn: "<?= $payload['tax'] ?>",
                    tax: null,
                    discount_percentage: <?= $payload['discount_percentage'] ?? 0 ?>,
                    isDeleted: false,
                    kode_barang: '<?= $payload['kode_barang'] ?>',
                    satuan: '<?= $payload['satuan'] ?>',
                    harga_barang: Number('<?= $payload['harga_barang'] ?>').toLocaleString(),
                    barangTotal: "<?= $payload['barangTotal'] ?>",
                    disc: "<?= $payload['discount_percentage'] ?? 0 ?>",
                    taxAmt: null,
                    discAmt: <?= $payload['barangTotal'] ?> * (<?= $payload['discount_percentage'] ?? 0 ?> / 100),
                }).draw(false);
            <?php
            }
            ?>
        <?php
        } ?>

        $('.dataTable tbody').on('click', 'button', function() {
            let rowData = table.row($(this).parents('tr')).data();
            let dataNo = $(this).data('no');
            let dataId = $(this).data('id');
            const csrf = $(`[name="${csrfToken}"]`);

            if (dataId) {
                Swal.fire({
                    icon: 'question',
                    title: 'Yakin akan di hapus?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "<?= base_url("order-form-lokal/delete-detail"); ?>",
                            data: {
                                id: dataId,
                            },
                            beforeSend: function(xhr) {
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                setLoading();
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
                                        })
                                        .then(() => {
                                            // Update the list_items array
                                            list_items = list_items.filter(item => item.id !== dataId);
                                            // Redraw the table with the updated list_items
                                            table.clear().rows.add(list_items).draw();
                                            reCountTotal();
                                        })
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#e74a3b',
                                    });
                                }
                            },
                        });
                    }
                })
            } else {
                // Hapus item dari array JavaScript dan gambar ulang tabel
                console.log(list_items);
                // console.log(table);
                let indexToRemove = list_items.findIndex(item => item.no === dataNo);
                if (indexToRemove !== -1) {
                    list_items.splice(indexToRemove, 1);

                }
                table.clear().rows.add(list_items).draw();
                console.log(list_items);
            }
            reCountTotal();
        });
        generateCodeMasterBarang();
        getBarang();
        <?php if (empty($data)) : ?>
            $('#auto_generate').prop('checked', true).change();
        <?php endif; ?>

        $(".order_date").datepicker({
            todayHighlight: true,
            enableOnReadonly: false,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        $(".shipping_date").datepicker({
            todayHighlight: true,
            enableOnReadonly: false,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true,
        })

        // SATUAN
        $('.satuan_id').select2({
            placeholder: "Pilih Satuan",
            theme: "bootstrap-5",
        });

        //CSS SELECT2 FLOATING LABEL
        $('.satuan_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.satuan_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.satuan_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.satuan_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // Customer
        $('.id_customer').select2({
            placeholder: "Pilih Nama Customer",
            allowClear: true,
            theme: "bootstrap-5"
        }).change(function() {
            const customerPhone = $(this).find(':selected').data('customerphone') ? $(this).find(':selected').data('customerphone') : "";
            const customerAddress = $(this).find(':selected').data('address') ? $(this).find(':selected').data('address') : "";
            const termin = $(this).find(':selected').data('termin') ? $(this).find(':selected').data('termin') : "";

            const salesName = $(this).find(':selected').data('salesname') ? $(this).find(':selected').data('salesname') : "";
            const tipePelanggan = $(this).find(':selected').data('tipepelanggan') ? $(this).find(':selected').data('tipepelanggan') : "";
            const jenis_penjualan = $(this).find(':selected').data('jenis_penjualan') ? $(this).find(':selected').data('jenis_penjualan') : "";

            $('#customerphone').val(decodeURIComponent(customerPhone));
            $('#tagihan_ke').val(decodeURIComponent(customerAddress));
            $('#termin').val(decodeURIComponent(termin)).change();

            $('#sales_name').val(decodeURIComponent(salesName));
            $('#hidden_tipe_pelanggan').val(decodeURIComponent(tipePelanggan)).change();
            $('#jenis_penjualan').val(decodeURIComponent(jenis_penjualan)).change();
        });

        $("#hidden_tipe_pelanggan").on('input change keyup paste', function() {
            // if ($(".hidden_tipe_pelanggan").val()) {


            let tipePelanggan = $("#hidden_tipe_pelanggan").val();
            $.ajax({
                url: "<?= base_url('/order-form-lokal/getmetaData'); ?>" + "/" + tipePelanggan,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    res.dataMetaData.forEach(function(item) {
                        $(".tipe_pelanggan").val(item.value);
                    })
                }
            })
            // }
        });

        //CSS SELECT2 FLOATING LABEL
        $('.id_customer')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.id_customer')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.id_customer')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // Sales
        $('.termin').select2({
            placeholder: "Pilih Termin",
            allowClear: true,
            theme: "bootstrap-5"
        }).change(function() {});

        //CSS SELECT2 FLOATING LABEL
        $('.termin')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.termin')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.termin')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // Sales
        $('.id_sales').select2({
            placeholder: "Pilih Nama Sales",
            allowClear: true,
            theme: "bootstrap-5"
        }).change(function() {});

        //CSS SELECT2 FLOATING LABEL
        $('.id_sales')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.id_sales')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.id_sales')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // warehouse
        $('.warehouse').select2({
            placeholder: "Pilih warehouse",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content"),
            // tags: true,
            allowClear: true
        })

        //CSS SELECT2 FLOATING LABEL
        $('.warehouse')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.warehouse')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.warehouse')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // BARANG
        $('.jenis_penjualan').select2({
            placeholder: "Pilih Jenis Penjualan",
            theme: "bootstrap-5",
            allowClear: true
        }).change(function() {
            let isi = $(this).val();
            if (isi == 1) {
                $('.id_sales').prop('disabled', false);
                $('.nama_ecommerce').prop('readonly', true);
            } else if (isi == 2) {
                $('.id_sales').prop('disabled', true);
                $('.nama_ecommerce').prop('readonly', true);
            } else if (isi == 3) {
                $('.id_sales').prop('disabled', true);
                $('.nama_ecommerce').prop('readonly', false);
            } else {
                $('.id_sales').prop('disabled', true);
                $('.nama_ecommerce').prop('readonly', true);
            }
            $('.id_sales').val('').change();
            $('.nama_ecommerce').val('');
        });

        //CSS SELECT2 FLOATING LABEL
        $('.jenis_penjualan')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.jenis_penjualan')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.jenis_penjualan')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // BARANG
        $('.id_barang').select2({
            placeholder: "Pilih Barang",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content"),
            allowClear: true
        })

        //CSS SELECT2 FLOATING LABEL
        $('.id_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.id_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.id_barang')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $(".nik").mask("AAAAAAAAAAAAAAAA", {
            translation: {
                "A": {
                    pattern: /[0-9]/,
                }
            }
        })

        $(".parent_postal_code").mask("AAAAA", {
            translation: {
                "A": {
                    pattern: /[0-9]/,
                }
            }
        })

        // TERMIN
        //CSS SELECT2 FLOATING LABEL
        $('.termin').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: true,
            dropdownParent: $(".add-modal .modal-content")
        });
        $(".termin")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".termin")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $(".termin")
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // MATA UANG
        //CSS SELECT2 FLOATING LABEL
        $('.currency').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: false,
            dropdownParent: $(".add-modal .modal-content")
        });

        $(".currency")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".currency")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $(".currency")
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // COUNTRY
        //CSS SELECT2 FLOATING LABEL
        $(".country_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".country_id")
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $('.country_id').select2({
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal-internasional .modal-content")
        })

        $(".country_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-8px');

        // TIPE PELANGGAN
        //CSS SELECT2 FLOATING LABEL
        $('.tipe_pelanggan').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: true,
            dropdownParent: $(".add-modal .modal-content")
        });

        $(".tipe_pelanggan")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".tipe_pelanggan")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $(".tipe_pelanggan")
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // PROVINCE PARENT
        //CSS SELECT2 FLOATING LABEL
        $('.province_parent_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: true,
            dropdownParent: $(".add-modal .modal-content")
        });

        $(".province_parent_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".province_parent_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $(".province_parent_id")
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // CITY PARENT
        //CSS SELECT2 FLOATING LABEL
        $('.city_parent_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: true,
            dropdownParent: $(".add-modal .modal-content")
        });

        $('.city_parent_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.city_parent_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.city_parent_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');


        $(".phone").mask("0000000000000")

        $(".postal_code").mask("00000")

        $(".no_npwp").mask("000000000000000")

        // CUSTOMER 
        $('.btn-customer-add').click(function() {
            $('.name').val(null);
            $('.country_id').val(null).change();
            $('.address').val(null);
            $('#addCustomerModal').modal('show');
        });

        $('.btn-discard-customer').click(function() {
            $('#addCustomerModal').modal('hide');
        });

        var validatorCustomer = $(".create-form-customer").validate({
            rules: {
                name: {
                    required: true
                },
                address: {
                    required: true
                },
                nik: {
                    minlength: 16,
                    maxlength: 16
                },
                parent_postal_code: {
                    minlength: 5,
                    maxlength: 5
                },
                email: {
                    email: true
                },
                piutang: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: "Nama wajib diisi"
                },
                address: {
                    required: "Alamat wajib diisi"
                },
                nik: {
                    minlength: "NIK Minimal 16 Digit",
                    maxlength: "NIK Maksimal 16 Digit"
                },
                parent_postal_code: {
                    minlength: "Kode Pos Minimal 5 Digit",
                    maxlength: "Kode Pos Maksimal 5 Digit"
                },
                email: {
                    email: "Email Harus Valid"
                },
                piutang: {
                    required: "Limit piutang wajib diisi"
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

        $('.btn-submit-customer').click(function() {
            if ($('.create-form-customer').valid()) {
                const csrf = $(`[name="${csrfToken}"]`);
                const data = new FormData(document.querySelector(".create-form-customer"));
                Swal.fire({
                    icon: 'question',
                    title: 'Simpan Customer?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "<?= base_url("customer-lokal/save"); ?>",
                            data: data,
                            beforeSend: function(xhr) {
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                setLoading();
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
                                $("#addCustomerModal").modal("hide");
                                if (response.status) {
                                    Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        .then(() => {
                                            $('#addCustomerModal').modal('hide');

                                        })
                                    getListCustomer();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
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

            }
        });

        // MASTER BARANG
        $('.btn-barang-add').click(function() {
            $('#addMasterBarangModal').modal('show');
            $('.detail-modal').modal('hide');
            // reset form master barang
            validatorMasterBarang.resetForm();
            validatorMasterBarang.reset();
            $('#generate_new_code').attr('checked', true);
            generateCodeMasterBarang();
            $('.barang_name').val(null);
            $('.statusppn').val(null);
            $('.satuan_id').val(null).change();
            $('.harga_pokok').val(null);
            $('.harga_jual').val(null);
        })

        $('.btn-discard-master-barang').click(function() {
            $(".detail-modal").modal("show")
            $('#addMasterBarangModal').modal('hide');
        });

        // VALIDATOR MASTER BARANG
        var validatorMasterBarang = $(".create-form-master-barang").validate({
            rules: {
                kode_barang: {
                    required: true
                },
                barang_name: {
                    required: true
                },
                type_barang: {
                    required: true
                },
                satuan_id: {
                    required: true
                },
                harga_pokok: {
                    required: true
                },
                harga_jual: {
                    required: true
                }
            },
            messages: {
                kode_barang: {
                    required: "Kode barang wajib diisi"
                },
                barang_name: {
                    required: "Nama barang wajib diisi"
                },
                type_barang: {
                    required: "Tipe barang wajib diisi"
                },
                satuan_id: {
                    required: "Satuan wajib diisi"
                },
                harga_pokok: {
                    required: "Harga pokok wajib diisi"
                },
                harga_jual: {
                    required: "Harga jual wajib diisi"
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

        $('.btn-submit-form-master-barang').click(function() {
            if ($('.create-form-master-barang').valid()) {
                const csrf = $(`[name="${csrfToken}"]`);
                const data = new FormData(document.querySelector(".create-form-master-barang"));
                Swal.fire({
                    icon: 'question',
                    title: 'Simpan Master Barang ?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "<?= base_url("master-barang-lokal/save"); ?>",
                            data: data,
                            method: "POST",
                            dataType: "json",
                            beforeSend: function(xhr) {
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                setLoading();
                            },
                            complete: function() {
                                stopLoading();
                            },
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                        reverseButtons: true,
                                        confirmButtonText: 'Oke',
                                    }).then((result) => {
                                        // jika sukses
                                        $(".detail-modal").modal("show")
                                        $('#addMasterBarangModal').modal('hide');
                                        // update list data barang
                                        getBarang();
                                    })
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                        cancelButtonColor: '#d33',
                                        reverseButtons: true,
                                        confirmButtonText: 'Oke',
                                    })
                                }
                            }
                        });
                    }
                })

            }
        });

        var validator = $(".create-form").validate({
            rules: {
                no_sales_order: {
                    required: true
                },
                id_customer: {
                    required: true
                },
                order_date: {
                    required: true
                },
                shipping_date: {
                    required: true
                },
                termin: {
                    required: true
                },
                jenis_penjualan: {
                    required: true
                }
            },
            messages: {
                no_sales_order: {
                    required: "No sales order wajib diisi"
                },
                id_customer: {
                    required: "Nama Customer wajib diisi"
                },
                order_date: {
                    required: "Tanggal pemesanan wajib diisi"
                },
                shipping_date: {
                    required: "Tanggal pengiriman wajib diisi"
                },
                termin: {
                    required: "Termin wajib diisi"
                },
                jenis_penjualan: {
                    required: "Jenis penjualan wajib diisi"
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

        // MODAL
        var validator_detail = $(".detail-form").validate({
            rules: {
                id_barang: {
                    required: true
                },
                harga: {
                    required: true
                },
                qty: {
                    required: true
                }
            },
            messages: {
                id_barang: {
                    required: "Nama barang wajib diisi"
                },
                harga: {
                    required: "Harga wajib diisi"
                },
                qty: {
                    required: "Qty wajib diisi"
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

        $(".id_barang").change(function() {
            if ($(".id_barang").val()) {
                let nama = $(".id_barang option:selected").data("nama") ? $(".id_barang option:selected").data("nama") : "";
                let idBarang = $(".id_barang option:selected").data("id_item") ? $(".id_barang option:selected").data("id_item") : "";
                let satuan = $(".id_barang option:selected").data("satuan") ? $(".id_barang option:selected").data("satuan") : "";
                let statusppn = $(".id_barang option:selected").data("statusppn");
                let warehouseId = $(".id_barang option:selected").data("warehouse_id") ? $(".id_barang option:selected").data("warehouse_id") : "";
                let warehouseName = $(".id_barang option:selected").data("warehouse_name") ? $(".id_barang option:selected").data("warehouse_name") : "";
                let harga = $(".id_barang option:selected").data("harga") ? $(".id_barang option:selected").data("harga") : 0;

                // let stok = $(".id_barang option:selected").data("stok") ? $(".id_barang option:selected").data("stok") : "";

                $(".nama_barang").attr("readonly", nama ? true : false);
                $.ajax({
                    url: "<?= base_url('/order-form-lokal/warehouseAll'); ?>" + "/" + idBarang,
                    method: "GET",
                    dataType: "json",
                    success: function(res) {
                        $(".warehouse").empty();
                        //bug di penjualan lokal
                        // $(".warehouse").append(`<option value=""></option>`);
                        res.dataWarehouse.forEach(function(item) {
                            $(".warehouse").append(`<option  value="${item.warehouse_id}" ${warehouseId==item.id?"selected":""}>${item.warehouse_name}</option>`).change();
                            // $(".stok").val(item.qty);
                        })
                    }
                })
                if (statusppn == 1) {
                    $(".keteranganppn").val("Barang PPN");

                } else {
                    $(".keteranganppn").val("Barang Tidak PPN");
                }

                $(".statusppn").val(statusppn);
                $(".nama_barang").val(nama);
                $(".warehouse").on('change', function() {
                    let idBarang = $(".id_barang option:selected").data("id_item") ? $(".id_barang option:selected").data("id_item") : "";
                    let warehouseId = $(this).val();
                    $(".stok").val("");
                    $.ajax({
                        url: "<?= base_url('/order-form-lokal/stok'); ?>" + "/" + idBarang + "/" + warehouseId,
                        method: "GET",
                        dataType: "json",
                        success: function(res) {
                            //bug di penjualan lokal
                            // $(".warehouse").append(`<option value=""></option>`);
                            if (res.dataDetailStock.length > 0) {
                                // You can set the value of .stok based on the selected warehouse here
                                let selectedWarehouse = res.dataDetailStock; // Assuming you want the first item in the response
                                $(".stok").val(res.dataDetailStock);
                            }
                        }
                    })
                });
                $(".satuan").val(satuan);
                $(".harga").val(harga);

            } else {
                $(".nama_barang").attr("readonly", false)
                // $(".harga").val("0");
                $(".qty").val("");
                $(".amount").val("0");
                $(".keterangan").val("").change();
                $(".statusppn").val("");
                $(".tax").val("");
                $(".discount_percentage").val("0");
                $(".dept").val("");
                $(".warehouse").val("");
                $(".id_warehouse").val("");
            }
        });


        $(document).on('click', '.edit-table-detail', function() {
            $(".title-detail-name").text("Update")
            $(".delete-detail").css('display', '');

            let id_barang = $(this).data('id_barang')
            let nama_barang = $(this).data('nama_barang')
            let tipe_pelanggan = $(this).data('tipe_pelanggan')
            let harga = $(this).data('harga')
            let qty = $(this).data('qty')
            let amount = $(this).data('amount')
            let warehouseName = $(this).data('warehouse_name')
            let keterangan = $(this).data('keterangan')
            let dept = $(this).data('dept')
            let idWarehouse = $(this).data('id_warehouse')
            let statusppn = $(this).data('statusppn')
            let tax = $(this).data('tax')
            let discount = $(this).data('discount_percentage')
            let row = $(this).data('row')
            let id = $(this).data('id')
            let valData = 0

            validator_detail.resetForm();
            validator_detail.reset();

            $(".id_detail").val(id)
            // $(".harga").val(parseInt(harga))
            $(".qty").val(qty)
            $(".tipe_pelanggan").val(tipe_pelanggan)
            $(".amount").val(amount)
            $(".keterangan").val(keterangan)
            $(".statusppn").val(statusppn)
            $(".tax").val(tax)
            $(".discount_percentage").val(discount)
            $(".dept").val(dept)
            $("#warehouse").val(idWarehouse)
            $("#warehouse").text(warehouseName)

            $.ajax({
                url: `<?= base_url("order-form-lokal/barangAll"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {

                    // $(".id_barang").empty();

                    $(".id_barang").append(`<option data-satuan="" data-warehouse_id="" data-harga="" data-statusppn="" data-warehouse_name="" data-id_item="" value=""></option>`);

                    res.dataBarang.forEach(function(item) {
                        if (item.id == id_barang) {
                            valData = item.id_barang
                        }
                        $(".id_barang").append(`<option data-code="${item.kode_barang}" data-harga="${item.harga_jual}" data-statusppn="${item.harga_jual}" data-satuan="${item.nama_satuan}" data-warehouse_id="${idWarehouse}" data-warehouse_name="${item.warehouse_name}" data-id_item="${item.id_barang}  value="${item.id_barang}" ${item.id_barang==id_barang?'selected':''}>${item.nama_barang}</option>`);
                    })

                    $(".id_barang").val(valData).change();
                    $(".detail-modal").modal("show");
                }
            })

        });

        $(".btn-submit").click(function() {
            $(".detail-modal").modal("hide")

            // CHECK IF NO BARANG

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
                        const csrf = $(`[name="${csrfToken}"]`);
                        setLoading()
                        let data = new FormData(document.querySelector(".create-form"));
                        var total = 0;
                        let taxAmt = 0;
                        const tableData = table.rows().data().toArray();

                        let update_list_items = [];

                        update_list_items = list_items;

                        if (list_delete.length !== 0) {
                            list_delete.map(obj => {
                                update_list_items.push({
                                    id: obj.id ? Number(obj.id) : 0,
                                    id_barang: obj.id_barang ? Number(obj.id_barang) : 0,
                                    nama_barang: obj.nama_barang,
                                    harga_barang: obj.harga_barang ? Number(obj.harga_barang.replaceAll(",", "")) : 0,
                                    qty: obj.qty ? Number(obj.qty) : 0,
                                    amount: obj.amount ? Number(obj.amount.replaceAll(",", "")) : 0,
                                    keterangan: obj.keterangan,
                                    statusppn: obj.statusppn,
                                    tax: obj.tax ? Number(obj.tax) : 0,
                                    discount_percentage: obj.discount_percentage ? Number(obj.discount_percentage) : 0,
                                    dept: obj.dept ? Number(obj.dept) : 0,
                                    warehouse_id: obj.warehouse_id ? Number(obj.warehouse_id) : 0,
                                    warhouse_name: obj.warhouse_name,
                                    isDeleted: true
                                })
                            })
                        }
                        // console.log(update_list_items)

                        const taxStatus = $('#tax_status').is(':checked');
                        const includeTaxStatus = $('#include_tax').is(':checked');
                        tableData.map(obj => {
                            // total = total + (obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0) * (obj.qty ? Number(obj.qty) : 0);
                            total += Number(obj.amount.replaceAll(",", "")) || 0;

                            if (taxStatus && !includeTaxStatus) {
                                taxAmt += obj.taxAmt ?? 0;
                                total += obj.taxAmt ?? 0;
                            }
                        })

                        data.append("total", total)
                        data.append('taxAmt', taxAmt)
                        data.append("tax_status", taxStatus)
                        data.append("include_tax", includeTaxStatus)
                        data.append("items", JSON.stringify(update_list_items))



                        let id = $(".id").val();
                        // UPDATE
                        if (id) {
                            $.ajax({
                                url: "<?= base_url("order-form-lokal/update"); ?>",
                                data: data,
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                    setLoading();
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
                                                window.location.href = "<?= base_url('order-form-lokal') ?>"
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
                            if (!tableData.length) {
                                Swal.fire({
                                    icon: 'error',
                                    title: "Barang Tidak Boleh Kosong",
                                    confirmButtonColor: '#4e73df',
                                });
                                stopLoading();
                                return;
                            }

                            $.ajax({
                                url: "<?= base_url("order-form-lokal/save"); ?>",
                                data: data,
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                    setLoading();
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
                                                window.location.href = "<?= base_url('order-form-lokal') ?>"
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
        });

        $(".btn-hide-detail").click(function() {
            $(".detail-modal").modal("hide")
        });

        $(".btn-show-detail").click(function() {
            $(".title-detail-name").text("Tambah");

            $(".id_detail").val('')
            $(".id_barang").empty('')
            $(".id_barang").val('').change()
            $("#warehouse").val('').change()
            $("#warehouse").empty()
            $(".harga").val('')
            $(".qty").val('')
            $(".statusppn").val('')
            $(".amount").val('')
            $(".keterangan").val('')

            $(".id_barang").val('')

            getBarang();
            $(".detail-modal").modal("show");
        });

        $(".harga, .qty").keyup(function() {
            let harga = $(".harga").val() ? $(".harga").val().replaceAll(",", "") : 0;
            let qty = $(".qty").val() ? parseInt($(".qty").val()) : 0;

            let amount = (harga * qty).toLocaleString();
            $(".amount").val(amount);
        });

        $(".discount_percentage").keyup(function() {
            if ($(".discount_percentage").val()) {
                if ($(".discount_percentage").val() > 100) {
                    $(".discount_percentage").val(100)
                }
                if ($(".discount_percentage").val() < 0) {
                    $(".discount_percentage").val();
                }
            } else {
                $(".discount_percentage").val();
            }
        })

        $(".btn-submit-detail").click(function() {
            let row_detail = $(".id_detail").val() ? Number($(".id_detail").val()) : 0;
            let id_barang = $(".id_barang option:selected").val()
            let nama_barang = $(".id_barang option:selected").text()
            const selectedData = $(".id_barang option:selected").data();
            let harga = $(".harga").val()
            let qty = $(".qty").val()
            let amount = $(".amount").val().replace(/\,/g, '');
            let keterangan = $(".keterangan").val()
            let statusppn = $(".statusppn").val()
            const tax = selectedData.tax;
            let discountPercentage = $(".discount_percentage").val() || 0;
            let dept = $(".dept").val()
            let warehouseId = $(".warehouse").val()
            let warhouseName = $(".warehouse").text()

            const discAmt = amount * (discountPercentage / 100);
            const discountedAmt = amount - discAmt;

            const currentItemList = table.rows().data().toArray();
            let validate_same = currentItemList.findIndex((obj) => obj.id_barang == id_barang && obj.warehouse_id == warehouseId);

            if (validate_same >= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Barang tidak boleh sama',
                    confirmButtonColor: '#4e73df',
                })

            } else {
                // update detail
                if (row_detail) {} else {
                    if ($(".detail-form").valid()) {
                        Swal.fire({
                            icon: 'question',
                            title: 'Simpan Data?',
                            confirmButtonColor: '#4e73df',
                            cancelButtonColor: '#d33',
                            showCancelButton: true,
                            reverseButtons: true,
                            confirmButtonText: 'Simpan',
                            cancelButtonText: 'Batal',
                        }).then(result => {
                            if (result.isConfirmed) {
                                no = no + 1;
                                list_items.push({
                                    id: "",
                                    no: no,
                                    row: row + 1,
                                    id_barang: id_barang,
                                    nama_barang: nama_barang,
                                    harga_barang: harga,
                                    qty: qty,
                                    amount: amount,
                                    discountedAmt: discountedAmt,
                                    keterangan: keterangan,
                                    statusppn: statusppn,
                                    tax: tax,
                                    taxAmt: amount * (tax / 100),
                                    discount_percentage: discountPercentage,
                                    dept: dept,
                                    warehouse_id: warehouseId,
                                    warhouse_name: warhouseName,

                                    kode_barang: selectedData.code,
                                    satuan: selectedData.satuan,
                                    disc: discountPercentage,
                                    discAmt: discAmt,
                                    isDeleted: false,

                                    barangTotal: amount,
                                });

                                table.row.add({
                                    id: "",
                                    no: no,
                                    id_barang: id_barang,
                                    kode_barang: selectedData.code,
                                    nama_barang: nama_barang,
                                    qty: qty,
                                    satuan: selectedData.satuan,
                                    harga_barang: harga,
                                    barangTotal: amount,
                                    disc: discountPercentage,
                                    statusppn: statusppn,
                                    tax: tax,
                                    taxAmt: amount * (tax / 100),
                                    keterangan: keterangan,
                                    discAmt: discAmt,
                                    amount: Number(discountedAmt).toLocaleString(),
                                    dept: dept,
                                    warehouse_id: warehouseId,
                                    warehouse_name: warhouseName,
                                    isDeleted: false
                                }).draw(false);

                                reCountTotal();

                                total_harga_barang = total_harga_barang + Number(harga.replaceAll(",", ""));
                                total_qty = total_qty + Number(qty);
                                total_harga = total_harga + Number(amount.replaceAll(",", ""));

                                let tag_html = "";
                                let tag_total = "";

                                $(".foot-detail-table").empty()

                                tag_total += `<tr>`;
                                tag_total += "<td colspan='1'>";
                                tag_total += "</td>";
                                tag_total += "<td>";
                                tag_total += "<b>TOTAL</b>";
                                tag_total += "</td>";
                                tag_total += "<td>";
                                tag_total += `<b>${total_harga_barang.toLocaleString()}</b>`;
                                tag_total += "</td>";
                                tag_total += "<td>";
                                tag_total += `<b>${total_qty}</b>`;
                                tag_total += "</td>";
                                tag_total += "<td>";
                                tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                                tag_total += "</td>";
                                tag_total += "<td colspan='3'>";
                                tag_total += "</td>";
                                tag_total += "</tr>";

                                $(".foot-detail-table").append(tag_total);

                                $(".detail-modal").modal("hide")
                                row = row + 1;
                            }
                        })
                    }
                }
            }
        });

        $('#estimated_freight').keyup(function() {
            const estimatedFreight = $(this).val();
            $('#freightCost').html(estimatedFreight);
            reCountTotal();
        });

        const reCountTotal = () => {
            const taxStatus = $('#tax_status').is(':checked');
            const includeTax = $('#include_tax').is(':checked');
            const itemList = table.rows().data();

            let itemSubTotal = 0;
            let discTotal = 0;
            let taxTotal = 0;
            let taxTotalHtml = 0;
            const estimatedFreightVal = $('#estimated_freight').val() || '0';
            const estimatedFreight = +estimatedFreightVal.replace(/\,/g, '');


            itemList.map((obj) => {
                itemSubTotal += +obj.barangTotal;
                discTotal += +obj.discAmt;

                if (taxStatus && !includeTax) {
                    taxTotal += +obj.taxAmt;
                    taxTotalHtml += +obj.taxAmt;
                } else if (taxStatus && includeTax) {
                    taxTotalHtml += +obj.taxAmt;
                }
            });

            if (taxStatus || includeTax) {
                $('#includeTaxText').html('(Termasuk Pajak)');
            } else {
                $('#includeTaxText').html('');
            }

            $('#itemSubTotal').html(itemSubTotal.toLocaleString());
            $('#discTotal').html(discTotal.toLocaleString());

            const grandTotal = itemSubTotal + estimatedFreight + taxTotal - discTotal;
            $('#grandTotal').html(grandTotal.toLocaleString());
        };

        $('#tax_status').change(function() {

            if (!this.checked) {
                $('#include_tax').prop('checked', false);
            }

            reCountTotal();
        });
        $('#include_tax').change(function() {

            const taxStatus = $('#tax_status').is(':checked');

            if (this.checked && !taxStatus) {
                $(this).prop('checked', false);
            }

            reCountTotal()
        });

        <?php if (!empty($data)) : ?>
            // const dataHaciu = <?= json_encode($data->detail) ?>;
            // table.rows.add(dataHaciu).draw(false);
            reCountTotal();
        <?php endif; ?>

    })

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        if (value) {
            $.ajax({
                url: "<?= base_url("order-form-lokal/generate-no-order-form"); ?>",
                method: "POST",
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                processData: false,
                contentType: false,
                success: function(response) {
                    csrf.val(response.token);
                    $(".no_sales_order").val(response.data);
                },

            });
            $(".no_sales_order").attr("readonly", true);
        } else {
            $(".no_sales_order").attr("readonly", false);
            $(".no_sales_order").val("");
        }
    }

    function getListCustomer() {
        $.ajax({
            url: `<?= base_url('order-form-lokal/customer'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {},
            dataType: "json",
            success: function(res) {
                $(".id_customer").empty()
                $(".id_customer").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".id_customer").append(`<option value="${item.id}" data-jenis_penjualan="${item.jenis_penjualan}" data-tipepelanggan="${item.tipe_pelanggan}" data-customerphone="${item.customerPhone}" data-address="${item.address}" data-termin="${item.termin}" data-salesname="${item.salesName}">${item.kode} - ${item.name}</option>`)
                })
                $(".id_customer").val();
            }
        });
    }

    function getBarang() {
        $.ajax({
            url: `<?= base_url("order-form-lokal/barangAll"); ?>`,
            method: "GET",
            dataType: "json",
            success: function(res) {
                $(".id_barang").empty();

                $(".id_barang").append(`<option data-satuan="" data-warehouse_id="" data-harga="" data-statusppn="" data-warehouse_name="" data-id_item="" value=""></option>`);



                res.dataBarang.forEach(function(item) {
                    $(".id_barang").append(`<option data-code="${item.kode_barang}" data-harga="${item.harga_jual}" data-statusppn="${item.statusppn}" data-satuan="${item.nama_satuan}" data-warehouse_id="${item.warehouse_id}" data-harga="${item.harga_barang}" data-warehouse_name="${item.warehouse_name}" data-id_item="${item.id}" value="${item.id}">${item.nama_barang}</option>`);
                })
                // console.log(res.dataBarang);

                $(".id_barang").val("").change();
            }
        })
    }

    function generateCodeMasterBarang() {
        let csrfToken = '<?= csrf_token() ?>';
        let value = document.getElementById('generate_new_code').checked ? true : false;
        let csrf = $(`[name="${csrfToken}"]`);
        let type_barang = "bahan_jadi"
        if (value) {
            $("input[name='kode_barang']").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("master-barang-lokal/generate-new-code"); ?>`,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                complete: function() {},
                data: {
                    type_barang: type_barang
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

    // Company
    $('.company').select2({
        placeholder: "",
        theme: "bootstrap-5",
    })

    //CSS SELECT2 FLOATING LABEL
    $('.company')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.company')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.company')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    // change data model jika sudah ada datanya di pilih
</script>

<?= $this->endSection(); ?>