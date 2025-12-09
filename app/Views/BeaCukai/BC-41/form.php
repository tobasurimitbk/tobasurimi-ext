<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-add-spp .form-floating .form-floating-custom .select2 .selection .select2-selection {
        height: 90px !important;
    }

    .form-add-spp .form-floating .form-floating-custom .select2 .selection .select2-selection__rendered {
        height: 60px !important;
    }
</style>
<section class="section">
    <div class="section-header">
        <h1>Tambah Dokumen BC 4.1</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right root-form-view" href="<?= base_url("bea-cukai-bc-41"); ?>">
                Kembali
            </a>
            <a class="btn btn-info btn-print float-right text-white" href="<?= base_url('bea-cukai-bc-41/id/header/' . encrypt($bc41['id'])) ?>">
                Form Ceisa
            </a>
            <?php if ($bc41['status_posting'] == 0) : ?>
                <button class="btn btn-hapus delete-parent float-right" onclick="deleteAction()">
                    Hapus
                </button>
                <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="posting()">
                    Posting
                </button>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-header" style="font-weight: bold; <?= session()->get('theme') == 'dark' ? 'color:white;' : 'color:black;' ?>">
            PILIH INVENTORI YANG AKAN KELUAR
        </div>
        <div class="card-body">
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id" class="id" value="<?= encrypt($bc41['id']) ?>">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-floating mb-3 mt-1" style="height: 50px;">
                            <select class="form-select jenis_pengeluaran" id="jenis_pengeluaran" name="jenis_pengeluaran" aria-label="Floating label select example">
                                <option value=""></option>
                                <option <?= $bc41['jenis_pengeluaran'] == "ORDER FORM LOKAL" ? "selected" : "" ?> value="ORDER FORM LOKAL">ORDER FORM LOKAL</option>
                                <option <?= $bc41['jenis_pengeluaran'] == "LAINNYA" ? "selected" : "" ?> value="LAINNYA">LAINNYA</option>
                            </select>
                            <label style="z-index: 1;">Pilih Pengeluaran</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control tanggal" id="tanggal" name="tanggal" placeholder="Tanggal" value="<?= date('d/m/Y', strtotime($bc41['tanggal'])) ?>">
                            <label for="floatingInput">Tanggal Dokumen</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="no_pengajuan" value="<?= $noAju ?>" name="no_pengajuan" type="text" readonly class="no_pengajuan form-control" placeholder="">
                                <label>Nomor Pengajuan</label>
                            </div>
                            <div class="input-group-append" style="height:50px;">
                                <button class="btn btn-success" type="button" id="btnUpdateNoAjuModal">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" value="<?= $bc41['no_daftar'] ?>" type="number" class="form-control no_daftar" id="no_daftar" name="no_daftar" placeholder="No Daftar (Opsional)">
                                <label for="floatingInput">Nomor Daftar (Opsional)</label>
                            </div>
                            <?php if (!empty($bc41)): ?>
                                <?php if ($bc41['status_dokumen'] == "Sudah Kirim"): ?>
                                    <div class="input-group-append" style="height:50px;">
                                        <button class="btn btn-success" data-toggle="modal" type="button" onclick="ambilNoDaftar()">
                                            <i class="fa-solid fa-download"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-floating mb-3 mt-1" style="height: 50px;">
                            <select class="form-select reference_penerima_id" id="reference_penerima_id" name="reference_penerima_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataCustomer as $d): ?>
                                    <option selected value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label style="z-index: 1;">Pilih Penerima (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 90px;">
                            <div class="form-floating-custom">
                                <select class="form-select multiple_reference_id" id="multiple_reference_id" multiple name="multiple_reference_id[]" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($dataReferencePengeluaran as $d): ?>
                                        <option selected value="<?= $d['id'] ?>"><?= $d['no_sales_order'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>
                    </div>
                </div>
            </form>

            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">Daftar Barang Yang Akan Keluar</label>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-show-detail btn-add btn-block float-right" type="button" id="btninventoriModal">
                            <i class="fa-solid fa-magnifying-glass"></i> Inventori
                        </button>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered nowrap table-hover-tobasurimi table-selected dataTable" id="dataTableSelected" width="100%" border="1" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th style="text-align: center; width:5px;">No</th>
                            <th>Tgl Masuk</th>
                            <th>Reference No</th>
                            <th>Kode Barang</th>
                            <th>Barang</th>
                            <th>Spesifikasi</th>
                            <th>Qty</th>
                            <th>Valas</th>
                            <th>Harga Satuan</th>
                            <th>Nilai Tukar</th>
                            <th>Satuan</th>
                            <th>Sub Total (IDR)</th>
                            <th style="width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody class="body-table">
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="14">Tidak Ada Data</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</section>

<div class="modal detail-modal" id="inventoriModal" tabindex="1">
    <div class="modal-dialog modal-xl" style="min-width: 100rem !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Cari Stok Barang</h5>
            </div>
            <div class="modal-body">
                <div class="detail-form-component">
                    <div class="detail-form-layout">
                        <label class="form-label font-weight-bold lable-title" id="cari_stock_title">Pilih Tipe Ambil Stok</label>
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select type_pengambilan_stock" id="type_pengambilan_stock" name="type_pengambilan_stock">
                                        <option value=""></option>
                                        <option value="PABEAN">PABEAN</option>
                                        <option value="FIFO">FIFO</option>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Tipe Pengambilan Stok</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select divisi_id" id="divisi_id" name="divisi_id">
                                        <option value=""></option>
                                        <option value="ALL" selected>ALL</option>
                                        <?php foreach ($divisi as $d): ?>
                                            <option value="<?= $d['id'] ?>"><?= $d['divisi'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Pilih Departemen</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select warehouse_id" id="warehouse_id" name="warehouse_id">
                                        <option value=""></option>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Pilih Warehouse</label>
                                </div>
                            </div>

                        </div>
                        <label class="form-label font-weight-bold lable-title form-fifo">Input nilai barang</label>
                        <div class="row mt-3">
                            <div class="col-md-2 form-fifo">
                                <div class="form-floating" style="height: 50px;">
                                    <input placeholder="Qty Keluar" oninput="this.value = greatFormatRupiah(this.value)" class="form-control qty_keluar_fifo" id="qty_keluar_fifo" name="qty_keluar_fifo" />
                                    <label for="floatingInput" style="z-index: 1;">Qty Keluar</label>
                                </div>
                            </div>
                            <div class="col-md-2 form-fifo">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select valas_id_fifo" name="valas_id_fifo" id="valas_id_fifo">
                                        <option value=""></option>
                                        <?php foreach ($dataValuta as $d): ?>
                                            <option value="<?= $d['id'] ?>"><?= $d['value']  ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Mata Uang</label>
                                </div>
                            </div>
                            <div class="col-md-3 form-fifo">
                                <div class="form-floating" style="height: 50px;">
                                    <input placeholder="Harga Satuan" oninput="this.value = greatFormatRupiah(this.value)" class="form-control harga_satuan_fifo" id="harga_satuan_fifo" name="harga_satuan_fifo" />
                                    <label for="floatingInput" style="z-index: 1;">Harga Satuan</label>
                                </div>
                            </div>
                            <div class="col-md-2 form-fifo">
                                <div class="form-floating" style="height: 50px;">
                                    <input placeholder="Nilai Tukar" oninput="this.value = greatFormatRupiah(this.value)" class="form-control nilai_tukar_fifo" id="nilai_tukar_fifo" name="nilai_tukar_fifo" />
                                    <label for="floatingInput" style="z-index: 1;">Nilai Tukar</label>
                                </div>
                            </div>
                            <div class="col-md-3 form-fifo">
                                <div class="form-floating" style="height: 50px;">
                                    <input placeholder="Nilai Tukar" oninput="this.value = greatFormatRupiah(this.value)" class="form-control sub_total_fifo" id="sub_total_fifo" name="sub_total_fifo" />
                                    <label for="floatingInput" style="z-index: 1;">Sub Total</label>
                                </div>
                            </div>
                        </div>

                        <label class="form-label font-weight-bold lable-title">Pilih Inventori Barang Yang Akan Anda Keluarkan</label>
                        <div class="row mt-3 justify-content-left">
                            <div class="col-sm-3 mb-2">
                                <div class="form-floating" style="height: 50px;">
                                    <select class="form-select type_barang" id="type_barang" name="type_barang">
                                        <option value=""></option>
                                        <?php foreach ($tipeBarang as $t) : ?>
                                            <option value="<?= $t['description'] ?>">
                                                <?= strtoupper($t['value']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Tipe Barang</label>
                                </div>
                            </div>
                            <div class="col-sm-3 mb-2">
                                <div class="input-group">
                                    <div class="form-floating" style="height: 50px;">
                                        <input value="01/09/2025" placeholder="" class="form-control dateStart" id="dateStart" name="dateStart" />
                                        <label style="z-index: 1;" style="z-index: 1;">Tgl Awal Masuk</label>
                                    </div>
                                    <div class="input-group-append" style="height:50px;">
                                        <button disabled class="btn btn-secondary" type="button">
                                            <i class="fas fa-calendar-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 mb-2">
                                <div class="input-group">
                                    <div class="form-floating" style="height: 50px;">
                                        <input value="<?= date('d/m/Y') ?>" placeholder="" class="form-control dateEnd" id="dateEnd" name="dateEnd" />
                                        <label style="z-index: 1;" style="z-index: 1;">Tgl Akhir Masuk</label>
                                    </div>
                                    <div class="input-group-append" style="height:50px;">
                                        <button disabled class="btn btn-secondary" type="button">
                                            <i class="fas fa-calendar-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 mb-2">
                                <div class="form-floating" style="height: 50px;">
                                    <select class="form-select barang_id" id="barang_id" name="barang_id">
                                        <option value=""></option>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Cari Barang</label>
                                </div>
                            </div>
                            <div class="col-sm-3 mb-2">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input placeholder="Cari Data" value="" class="form-control search" id="search" name="search" />
                                    <label for="floatingInput" style="z-index: 1;">Cari Data</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-table-button-tts" style="margin-top: 10px;">
                                <div class="table-responsive">
                                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-inventori" id="dataTable" width="100%" cellspacing="0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>#</th>
                                                <th>Dept</th>
                                                <th>Warehouse</th>
                                                <th>Sumber</th>
                                                <th>Kode Barang</th>
                                                <th>Barang</th>
                                                <th>Spesifikasi</th>
                                                <th>Doc</th>
                                                <th>Tgl Masuk</th>
                                                <th>Ref No</th>
                                                <th>Qty</th>
                                                <th>Satuan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="body-table" id="body-detail-list-inventori">

                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-primary" id="select-item-btn">
                                        Pilih Inventori
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-discard mr-2" id="btnHideDetailStock">Kembali</button>
            </div>
        </div>
    </div>
</div>

<div class="modal detail-modal" id="updateStockModal" tabindex="1">
    <div class="modal-dialog modal-xl" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Input Qty Keluar</h5>
            </div>
            <form class="update-form-keluar" role="form" method="POST">
                <input type="hidden" name="id_stock_detail" id="id_stock_detail" class="id_stock_detail">
                <div class="modal-body">
                    <label class="form-label font-weight-bold lable-title">Detail Qty Keluar</label>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" type="text" class="form-control kode_barang" id="kode_barang" name="kode_barang" placeholder="Kode Barang">
                                    <label for="floatingInput">Kode Barang</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" type="text" class="form-control barang_name" id="barang_name" name="barang_name" placeholder="Barang">
                                    <label for="floatingInput">Nama Barang</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" type="text" class="form-control spesifikasi" id="spesifikasi" name="spesifikasi" placeholder="Spesifikasi">
                                    <label for="floatingInput">Spesifikasi</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input oninput="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control qty_keluar" id="qty_keluar" name="qty_keluar" placeholder="Qty Keluar">
                                    <label for="floatingInput">Qty Keluar</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select unit_id_keluar" name="unit_id_keluar" id="unit_id_keluar">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Satuan Keluar</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly oninput="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control qty_asal" id="qty_asal" name="qty_asal" placeholder="Qty Asal">
                                    <label for="floatingInput">Stok Awal</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly oninput="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control qty_konversi" id="qty_konversi" name="qty_konversi" placeholder="Qty Konversi">
                                    <label for="floatingInput">Qty Konversi</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select unit_id_konversi" name="unit_id_konversi" id="unit_id_konversi" disabled>
                                    <option value=""></option>
                                    <?php foreach ($dataSatuan as $d): ?>
                                        <option value="<?= $d['id'] ?>"><?= $d['kode_satuan'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Satuan Konversi</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly oninput="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control qty_hasil_keluar" id="qty_hasil_keluar" name="qty_hasil_keluar" placeholder="Qty Keluar">
                                    <label for="floatingInput">Sisa Stok</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <label class="form-label font-weight-bold lable-title">Detail Nilai Barang</label>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select valas_id" name="valas_id" id="valas_id">
                                    <option value=""></option>
                                    <?php foreach ($dataValuta as $d): ?>
                                        <option value="<?= $d['id'] ?>"><?= $d['value']  ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Mata Uang</label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input oninput="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control harga_satuan" id="harga_satuan" name="harga_satuan" placeholder="Harga Satuan">
                                    <label for="floatingInput">Harga Satuan</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input oninput="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control nilai_tukar" id="nilai_tukar" name="nilai_tukar" placeholder="Nilai Tukar">
                                    <label for="floatingInput">Nilai Tukar</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input oninput="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control sub_total" id="sub_total" name="sub_total" placeholder="Sub Total">
                                    <label for="floatingInput">Sub Total</label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHidekeluarModal">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitkeluar">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUpdateNoAju" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Nomor Pengajuan</h5>
            </div>
            <form id="form-update-noaju">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6 mt-1">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input id="tanggal_pengajuan" value="" name="tanggal_pengajuan" type="text" class="tanggal_pengajuan form-control" placeholder="">
                                    <label>Tanggal</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="no_urut_dokumen" name="no_urut_dokumen" type="number" class="no_urut_dokumen form-control" placeholder="" oninput="event.target.value = /^\d{0,6}$/.test(event.target.value) ? event.target.value : ''">
                                <label>Nomor Urut</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="kode_kantor" name="kode_kantor" type="number" class="kode_kantor form-control" placeholder="" oninput="event.target.value = /^\d{0,6}$/.test(event.target.value) ? event.target.value : ''">
                                <label>Kode Kantor</label>
                            </div>
                        </div>
                        <div class="col-sm-6 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="no_pengajuan_prev" value="<?= $noAju ?>" name="no_pengajuan_prev" type="text" readonly class="no_pengajuan_prev form-control" placeholder="">
                                <label>Preview Nomor Pengajuan</label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3" id="btnHideModalNoAju">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitUpdateNoAju">Simpan</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
    var listStockInventori = [];
    var listStock = [];

    listStock = <?= json_encode($dataBarang) ?>;
    drawTable(listStock);

    var table = $('.table-inventori').DataTable({
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [0, 'desc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url('bea-cukai-bc-41/all-stock-list'); ?>",
            type: "GET",
            data: function(data) {
                data.barang_id = $("#barang_id").val();
                data.divisi_id = $("#divisi_id").val();
                data.warehouse_id = $("#warehouse_id").val();
                data.dateStart = $("#dateStart").val();
                data.dateEnd = $("#dateEnd").val();
                data.type_barang = $("#type_barang").val();
                data.search = $("#search").val();
            },
            dataSrc: function(json) {
                window.listStockInventori = json.data;
                return json.data;
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
                className: "text-left",
                orderable: false
            },
            {
                data: null,
                orderable: false,
                className: "text-center",
                render: function(data, type, row) {
                    return `<input type="checkbox" class="row-check child" value="${row.id}">`;
                }
            },
            {
                data: "divisi",
                className: "text-left"
            },
            {
                data: "warehouse_name",
                className: "text-left"
            },
            {
                data: "reference_type",
                className: "text-left"
            },
            {
                data: "kode_barang",
                className: "text-left"
            },
            {
                data: "barang_name",
                className: "text-left",
            },
            {
                data: "spesifikasi",
                className: "text-left"
            },
            {
                data: "bc_detail",
                className: "text-left"
            },
            {
                data: "lpb_date",
                className: "text-left"
            },
            {
                data: "reference_no",
                className: "text-left"
            },
            {
                data: "qty_diterima",
                className: "text-left",
                render: function(data) {
                    return greatFormatRupiah(data);
                }
            },
            {
                data: "kode_satuan",
                className: "text-left"
            },
        ],
        "drawCallback": function(settings) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
            var typePengambilanStock = $('#type_pengambilan_stock option:selected').val();
            if (typePengambilanStock == "FIFO") {
                // Hide form check
                $('.child').hide();
            } else {
                $('.child').show();
            }
        },
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

    var validator = $(".create-form").validate({
        rules: {
            jenis_pengeluaran: {
                required: true
            },
            tanggal: {
                required: true
            },
            no_pengajuan: {
                required: true
            },
        },
        messages: {
            jenis_pengeluaran: {
                required: "Jenis pengeluaran wajib diisi"
            },
            tanggal: {
                required: "Tanggal wajib diisi"
            },
            no_pengajuan: {
                required: "Nomor pengajuan wajib diisi"
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

    var validatorUpdate = $(".update-form-keluar").validate({
        rules: {
            qty_keluar: {
                required: true
            },
            unit_id_keluar: {
                required: true
            },
            valas_id: {
                required: true
            },
            harga_satuan: {
                required: true
            },
            nilai_tukar: {
                required: true
            },
            sub_total: {
                required: true
            },
        },
        messages: {
            qty_keluar: {
                required: "Qty keluar wajib diisi"
            },
            unit_id_keluar: {
                required: "Satuan keluar wajib diisi"
            },
            valas_id: {
                required: "Pilih valas / mata uang"
            },
            harga_satuan: {
                required: "harga satuan wajib diisi"
            },
            nilai_tukar: {
                required: "nilai tukar wajib diisi"
            },
            sub_total: {
                required: "sub total wajib diisi"
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

    var validatorNoAju = $(".form-update-noaju").validate({
        rules: {
            tanggal_pengajuan: {
                required: true
            },
            no_urut_dokumen: {
                required: true
            },
            kode_kantor: {
                required: true
            },
            no_pengajuan_prev: {
                required: true
            },
        },
        messages: {
            tanggal_pengajuan: {
                required: "Tanggal pengajuan wajib diisi"
            },
            no_urut_dokumen: {
                required: "No urut wajib diisi"
            },
            kode_kantor: {
                required: "Kode kantor wajib diisi"
            },
            no_pengajuan_prev: {
                required: "No pengajuan wajib diisi"
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

    $('.table-inventori').on('preXhr.dt', function(e, settings, data) {
        if (data.order[0].column === 9) {
            data.order[0].column = 13;
        }
        if (data.order[0].column === 11) {
            data.order[0].column = 15;
        }
    });

    $('#dateStart,#dateEnd,#divisi_id,#warehouse_id,#barang_id,#type_barang').change(function() {
        table.ajax.reload();
    });

    $('#search').keyup(function() {
        table.ajax.reload();
    });

    $("#tanggal,#tanggal_pengajuan").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('.form-fifo').hide();

    $('#type_pengambilan_stock').select2({
        placeholder: "Pilih Tipe Ambil Stok",
        theme: "bootstrap-5",
        allowClear: false,
        dropdownParent: $('#inventoriModal')
    }).change(function() {
        // FIFO
        if ($(this).val() == "FIFO") {
            $('.form-fifo').show();
        } else {
            $('.form-fifo').hide();
        }
        $('#barang_id').val(null).change();
    });

    $('#type_barang').select2({
        placeholder: "Pilih Tipe Barang",
        theme: "bootstrap-5",
        allowClear: false,
        dropdownParent: $('#inventoriModal')
    }).change(function() {

    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: false,
        dropdownParent: $('#inventoriModal')
    }).change(function() {

    });

    $('#unit_id_keluar').select2({
        placeholder: "Pilih Satuan Keluar",
        theme: "bootstrap-5",
        allowClear: false,
        dropdownParent: $('#updateStockModal')
    }).change(function() {

    });

    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: false,
        dropdownParent: $('#inventoriModal')
    }).change(function() {

    });

    $('#barang_id').select2({
        placeholder: "Pilih Barang",
        theme: "bootstrap-5",
        allowClear: false,
        dropdownParent: $('#inventoriModal')
    }).change(function() {

    });

    $("#dateStart,#dateEnd").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#barang_id').select2({
        placeholder: "Cari Kode / Nama Barang",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#inventoriModal'),
        ajax: {
            url: '<?= base_url("barang/dropdown/type-server-barang-master-inventori") ?>',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    type_barang: $('#type_barang option:selected').val()
                };
            },
            processResults: function(data) {
                return {
                    results: $.map(data.results, function(item) {
                        return {
                            id: item.id,
                            text: item.text,
                        };
                    })
                };
            },
            cache: false
        },
        minimumInputLength: 1
    });

    $('#valas_id').select2({
        placeholder: "Pilih Mata Uang",
        theme: "bootstrap-5",
        allowClear: false,
        dropdownParent: $('#updateStockModal')
    }).change(function() {});

    $('#valas_id_fifo').select2({
        placeholder: "Pilih Mata Uang",
        theme: "bootstrap-5",
        allowClear: false,
        dropdownParent: $('#inventoriModal')
    }).change(function() {});

    $('#jenis_pengeluaran').select2({
        placeholder: "Pilih Jenis Pengeluaran",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function() {
        var jenis_pengeluaran = $('#jenis_pengeluaran option:selected').val();
        if (jenis_pengeluaran == 'LAINNYA') {
            $('#reference_penerima_id').val(null).change();
            $('#multiple_reference_id').val(null).change();
        } else {
            dropdownReferensiPenerima();
        }

    });

    $('#reference_penerima_id').select2({
        placeholder: "Pilih Penerima (Opsional)",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function() {
        dropdownReferensiPengeluaran();
    });

    $('.multiple_reference_id').select2({
        placeholder: "Pilih Referensi Pengeluaran (Opsional)",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function() {});

    $('#divisi_id').change(function() {
        dropdownWarehouse();
    });

    $('#btnSubmitUpdateNoAju').click(function(e) {
        e.preventDefault();
        if ($('#form-update-noaju').valid()) {
            let data = new FormData(document.querySelector("#form-update-noaju"));
            let no_pengajuan = $('#no_pengajuan_prev').val();
            data.append("id", "<?= encrypt($bc41['id']) ?>");
            data.append("no_pengajuan", no_pengajuan);
            $.ajax({
                url: "<?= base_url("bea-cukai-bc-41/update-no-aju"); ?>",
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
                        location.reload();
                    }
                },
            });

        }
    });

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('#btninventoriModal').click(function(e) {
        e.preventDefault();
        $('#valas_id_fifo').val(30).change();
        $('#harga_satuan_fifo').val(null);
        $('#nilai_tukar_fifo').val(1);
        $('#sub_total_fifo').val(null);
        $('#qty_keluar_fifo').val(null);

        $('#inventoriModal').modal('show');
    });

    $('#btnHideDetailStock').click(function(e) {
        e.preventDefault();
        $('#inventoriModal').modal('hide');
    });
    $('#select-item-btn').click(function() {
        var typePengambilanStock = $('#type_pengambilan_stock option:selected').val();
        if (typePengambilanStock == "FIFO") {
            insertListFifo();
        } else {
            insertListPabean();
        }
    });

    $('#btnSubmitkeluar').click(function(e) {
        e.preventDefault();
        if ($('.update-form-keluar').valid()) {
            var qty_hasil_keluar = destroyFormatRupiah($('#qty_hasil_keluar').val());
            if (qty_hasil_keluar < 0) {
                Swal.fire({
                    icon: 'error',
                    title: "Qty hasil keluar menghasilkan nilai minus  !",
                    confirmButtonColor: '#4e73df',
                });
                return;
            } else {
                var id_stock_detail = $('#id_stock_detail').val();
                var qty_keluar = parseFloat(destroyFormatRupiah($('#qty_keluar').val()));
                var unit_id_keluar = $('#unit_id_keluar option:selected').val();
                var unit_name_keluar = $('#unit_id_keluar option:selected').text().trim();
                var qty_konversi = parseFloat(destroyFormatRupiah($('#qty_konversi').val()));
                var hasil_keluar = parseFloat(destroyFormatRupiah($('#qty_hasil_keluar').val()));
                var valas_id = $('#valas_id option:selected').val();
                var valas_name = $('#valas_id option:selected').text();
                var harga_satuan = parseFloat(destroyFormatRupiah($('#harga_satuan').val()));
                var nilai_tukar = parseFloat(destroyFormatRupiah($('#nilai_tukar').val()));
                var sub_total = parseFloat(destroyFormatRupiah($('#sub_total').val()));

                var index = null;
                for (let i = 0; i < listStock.length; i++) {
                    if (listStock[i].id == id_stock_detail) {
                        index = i;
                    }
                }

                listStock[index].keluar.qty_keluar = qty_keluar;
                listStock[index].keluar.unit_id_keluar = unit_id_keluar;
                listStock[index].keluar.unit_name_keluar = unit_name_keluar;
                listStock[index].keluar.qty_konversi = qty_konversi;
                listStock[index].keluar.valas_id = valas_id;
                listStock[index].keluar.valas_name = valas_name;
                listStock[index].keluar.nilai_tukar = nilai_tukar;
                listStock[index].keluar.harga_satuan = harga_satuan;
                listStock[index].keluar.sub_total = sub_total;
                drawTable(listStock);
                $('#updateStockModal').modal('hide');

            }
        }
    });

    $('#qty_keluar').keyup(function(e) {
        hitungHasilKeluar();
    });

    $('#unit_id_keluar').change(function(e) {
        e.preventDefault();
        hitungHasilKeluar();
    });

    // Keyup Bawah
    $('#harga_satuan').keyup(function(e) {
        e.preventDefault();
        var hargaSatuan = destroyFormatRupiah($('#harga_satuan').val());
        var qtyKeluar = destroyFormatRupiah($('#qty_keluar').val());
        var nilaiTukar = destroyFormatRupiah($('#nilai_tukar').val());
        var subTotal = ((hargaSatuan * nilaiTukar) * qtyKeluar).toFixed(2);
        $('#sub_total').val(greatFormatRupiah(subTotal));
    });

    $('#sub_total').keyup(function(e) {
        e.preventDefault();
        var subTotal = destroyFormatRupiah($('#sub_total').val());
        var qtyKeluar = destroyFormatRupiah($('#qty_keluar').val());

        var hargaSatuan = (subTotal / qtyKeluar).toFixed(2);
        $('#harga_satuan').val(greatFormatRupiah(hargaSatuan));
        $('#nilai_tukar').val(1);
    });

    $('#nilai_tukar').keyup(function(e) {
        e.preventDefault();
        var nilaiTukar = destroyFormatRupiah($('#nilai_tukar').val());
        var hargaSatuan = destroyFormatRupiah($('#harga_satuan').val());
        var qtyKeluar = destroyFormatRupiah($('#qty_keluar').val());

        var subTotal = ((hargaSatuan * nilaiTukar) * qtyKeluar).toFixed(2);
        $('#sub_total').val(greatFormatRupiah(subTotal));
    });

    // Keyup Fifo
    $('#harga_satuan_fifo').keyup(function(e) {
        e.preventDefault();
        var hargaSatuan = destroyFormatRupiah($('#harga_satuan_fifo').val());
        var qtyKeluar = destroyFormatRupiah($('#qty_keluar_fifo').val());
        var nilaiTukar = destroyFormatRupiah($('#nilai_tukar_fifo').val());
        var subTotal = ((hargaSatuan * nilaiTukar) * qtyKeluar).toFixed(2);
        $('#sub_total_fifo').val(greatFormatRupiah(subTotal));
    });

    $('#sub_total_fifo').keyup(function(e) {
        e.preventDefault();
        var subTotal = destroyFormatRupiah($('#sub_total_fifo').val());
        var qtyKeluar = destroyFormatRupiah($('#qty_keluar_fifo').val());

        var hargaSatuan = (subTotal / qtyKeluar).toFixed(2);
        $('#harga_satuan_fifo').val(greatFormatRupiah(hargaSatuan));
        $('#nilai_tukar_fifo').val(1);
    });

    $('#nilai_tukar_fifo').keyup(function(e) {
        e.preventDefault();
        var nilaiTukar = destroyFormatRupiah($('#nilai_tukar_fifo').val());
        var hargaSatuan = destroyFormatRupiah($('#harga_satuan_fifo').val());
        var qtyKeluar = destroyFormatRupiah($('#qty_keluar_fifo').val());

        var subTotal = ((hargaSatuan * nilaiTukar) * qtyKeluar).toFixed(2);
        $('#sub_total_fifo').val(greatFormatRupiah(subTotal));
    });

    // SUBMIT HEADER
    $('.btn-submit-parent').click(function() {
        var jenis_pengeluaran = $('#jenis_pengeluaran').val();
        var reference_penerima_id = $('#reference_penerima_id').val();
        var multiple_reference_id = $('#multiple_reference_id').val();

        if (jenis_pengeluaran == '') {
            Swal.fire({
                icon: 'error',
                title: 'Jenis pengeluaran wajib diisi',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        } else if (listStock.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'List barang keluar kosong',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        } else {
            if ($('.create-form').valid()) {
                var isFalid = true;

                if (jenis_pengeluaran == "ORDER FORM LOKAL") {
                    if (reference_penerima_id == '' || multiple_reference_id == '') {
                        isFalid = false;
                    }
                }

                if (!isFalid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Penerima & No Referensi wajib diisi',
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Oke',
                    });
                    return;
                } else {
                    var id = $('.id').val();
                    var url = "<?= base_url("bea-cukai-bc-41/update"); ?>";
                    var formData = new FormData(document.querySelector('.create-form'));
                    formData.append("listStock", JSON.stringify(listStock));

                    $.ajax({
                        url: url,
                        data: formData,
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
                                        location.reload();
                                    })
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

            }
        }
    });

    function getIDListDataSelected() {
        var id_selected = [];
        $.each(listStock, function(i, v) {
            id_selected.push(v.id);
        })
        return id_selected;
    }


    function hitungHasilKeluar() {
        var qty_keluar = destroyFormatRupiah($('#qty_keluar').val());
        var konversi = parseFloat($('#unit_id_keluar option:selected').data('konversi_satuan'));
        var qty_asal = destroyFormatRupiah($('#qty_asal').val());
        var qty_konversi = 0;
        var qty_hasil_keluar = 0;

        qty_konversi = (qty_keluar * konversi);
        qty_hasil_keluar = qty_asal - qty_konversi;

        qty_konversi = parseFloat(qty_konversi);
        qty_hasil_keluar = parseFloat(qty_hasil_keluar);
        $('#qty_hasil_keluar').val(greatFormatRupiah(qty_hasil_keluar));
        $('#qty_konversi').val(greatFormatRupiah(qty_konversi));

    }

    function insertListFifo() {
        var dataIds = getIDListDataSelected();
        var qtyKeluarFifo = parseFloat(destroyFormatRupiah($('#qty_keluar_fifo').val()));
        var barangId = $(".barang_id option:selected").val();
        var valasIdFifo = $('#valas_id_fifo option:selected').val();
        var valasNameFifo = $('#valas_id_fifo option:selected').text();
        var hargaSatuanFifo = parseFloat(destroyFormatRupiah($('#harga_satuan_fifo').val()));
        var nilaiTukarFifo = parseFloat(destroyFormatRupiah($('#nilai_tukar_fifo').val()));
        var subTotalFifo = parseFloat(destroyFormatRupiah($('#sub_total_fifo').val()));

        if (listStockInventori.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Stok Inventori Kosong',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        } else if (isNaN(qtyKeluarFifo)) {
            Swal.fire({
                icon: 'error',
                title: 'Qty keluar Fifo Wajib Diisi',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        } else if (barangId == '') {
            Swal.fire({
                icon: 'error',
                title: 'Kode & Nama Barang Wajib Diisi',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        } else if (isNaN(hargaSatuanFifo)) {
            Swal.fire({
                icon: 'error',
                title: 'Harga satuan wajib diisi',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        } else if (isNaN(nilaiTukarFifo)) {
            Swal.fire({
                icon: 'error',
                title: 'Nilai tukar wajib diisi',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        } else if (isNaN(subTotalFifo)) {
            Swal.fire({
                icon: 'error',
                title: 'Sub total wajib diisi',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        } else {
            var totalStokTotal = 0;
            $.each(listStockInventori, function(i, v) {
                totalStokTotal += parseFloat(v.qty_diterima);
            });

            if (qtyKeluarFifo > totalStokTotal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan : Operasi FIFO akan menghasilkan nilai minus',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Oke',
                })
            } else {
                $.each(listStockInventori, function(i, v) {
                    var currentID = Number(v.id);
                    if ($.inArray(currentID, dataIds) == -1) {
                        var isIDSelected = $.grep(listStock, function(item) {
                            return item.id == Number(currentID);
                        }).length > 0;

                        if (!isIDSelected && qtyKeluarFifo != 0 && parseFloat(listStockInventori[i].qty_diterima) != 0) {
                            var keluarFifo = Math.min(qtyKeluarFifo, parseFloat(listStockInventori[i].qty_diterima));
                            var subTotal = (nilaiTukarFifo * hargaSatuanFifo) * keluarFifo;

                            listStockInventori[i].keluar = {
                                qty_keluar: parseFloat(keluarFifo),
                                unit_id_keluar: v.unit_id,
                                unit_name_keluar: v.kode_satuan,
                                qty_konversi: parseFloat(keluarFifo),
                                unit_id_konversi: v.unit_id,
                                unit_name_konversi: v.kode_satuan,
                                valas_id: valasIdFifo,
                                valas_name: valasNameFifo,
                                nilai_tukar: nilaiTukarFifo,
                                harga_satuan: hargaSatuanFifo,
                                sub_total: parseFloat(subTotal).toFixed(2),
                            }

                            listStock.push(listStockInventori[i]);

                            qtyKeluarFifo = qtyKeluarFifo - keluarFifo;
                        }
                    }
                });

                drawTable(listStock);
                $('#inventoriModal').modal('hide');
            }


        }
    }


    function insertListPabean() {
        var checkedCheckboxes = $(".child:checked");
        var dataIds = checkedCheckboxes.map(function() {
            return parseFloat($(this).val());
        }).get();
        if (dataIds.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Checklist inventori yang ingin di keluar',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            })
        } else {
            var id_selected = getIDListDataSelected();
            $.each(listStockInventori, function(i, v) {
                var currentID = Number(v.id);
                if ($.inArray(currentID, dataIds) !== -1) {
                    var isIDSelected = $.grep(listStock, function(item) {
                        return item.id == Number(currentID);
                    }).length > 0;

                    if (!isIDSelected) {
                        listStockInventori[i].keluar = {
                            qty_keluar: 0,
                            unit_id_keluar: null,
                            unit_name_keluar: "",
                            qty_konversi: 0,
                            unit_id_konversi: v.unit_id,
                            unit_name_konversi: v.kode_satuan,
                            valas_id: null,
                            valas_name: "",
                            nilai_tukar: 1,
                            harga_satuan: 0,
                            sub_total: 0
                        }
                        listStock.push(listStockInventori[i]);
                    }
                }
            });

            drawTable(listStock);
            // Tutup Modal Stok
            $('#inventoriModal').modal('hide');
        }

    }

    function remove(id) {
        var indexToRemove = -1;
        for (var i = 0; i < listStock.length; i++) {
            if (listStock[i].id == id) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listStock.splice(indexToRemove, 1);
            drawTable(listStock);
        }
    }

    function detail(id) {
        resetFormDetail();
        var item = null;
        for (let i = 0; i < listStock.length; i++) {
            if (listStock[i].id == id) {
                item = listStock[i];
            }
        }
        if (item == '') {
            Swal.fire({
                icon: 'error',
                title: "Detail stok tidak ada (Kesalahan sistem)",
                confirmButtonColor: '#4e73df',
            });
            return;
        } else {
            $.ajax({
                url: "<?= base_url("bea-cukai-bc-41/list-satuan-konversi"); ?>",
                data: {
                    id: id
                },
                method: "GET",
                success: function(response) {
                    if (response.status) {
                        var satuankeluarArr = response.data;
                        $('#id_stock_detail').val(item.id);
                        $('#kode_barang').val(item.kode_barang);
                        $('#barang_name').val(item.barang_name);
                        $('#spesifikasi').val(item.spesifikasi);
                        $('#qty_keluar').val(greatFormatRupiah(item.keluar.qty_keluar));
                        $('#unit_id_keluar').val(item.keluar.unit_id_keluar).change();
                        $('#qty_konversi').val(greatFormatRupiah(item.keluar.qty_konversi));
                        $('#unit_id_konversi').val(item.keluar.unit_id_konversi).change();
                        $('#qty_hasil_keluar').val(greatFormatRupiah(item.keluar.hasil_keluar));
                        $('#qty_asal').val(greatFormatRupiah(item.qty_diterima));
                        if (item.keluar.valas_id == null) {
                            $('#valas_id').val(30).change();
                            $('#nilai_tukar').val(greatFormatRupiah(1));
                        } else {
                            $('#valas_id').val(item.keluar.valas_id).change();
                            $('#nilai_tukar').val(greatFormatRupiah(item.keluar.nilai_tukar));
                        }
                        $('#harga_satuan').val(greatFormatRupiah(item.keluar.harga_satuan));
                        $('#nilai_tukar').val(greatFormatRupiah(item.keluar.nilai_tukar));
                        $('#sub_total').val(greatFormatRupiah(item.keluar.sub_total));

                        // append select
                        dropdownUnitkeluar(satuankeluarArr);
                        $('#unit_id_keluar').val(item.keluar.unit_id_konversi).change();

                        $('#updateStockModal').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                            cancelButtonColor: '#d33',
                            reverseButtons: true,
                            confirmButtonText: 'Oke',
                        });
                        return;
                    }
                },
            });


        }


    }

    function resetFormDetail() {
        $('#id_stock_detail').val(null);
        $('#kode_barang').val(null);
        $('#barang_name').val(null);
        $('#spesifikasi').val(null);
        $('#qty_keluar').val(null);
        $('#unit_id_keluar').val(null).change();
        $('#qty_konversi').val(null);
        $('#unit_id_konversi').val(null).change();
        $('#qty_hasil_keluar').val(null);
        $('#qty_asal').val(null);
    }

    $('#btnHidekeluarModal').click(function(e) {
        e.preventDefault();
        $('#updateStockModal').modal('hide');
    });

    function drawTable(listStock) {
        const table = $('.table-selected');
        table.find('tbody').empty();
        table.find('tfoot').empty();
        var no = 1;
        if (listStock.length == 0) {
            var newRow = $('<tr>');
            newRow.append($('<td  colspan="14" >').text("Tidak Ada Data"));
            table.find('tfoot').append(newRow);
        } else {
            var no = 1;
            var totalKeluar = 0;
            var totalSubTotal = 0;
            $.each(listStock, function(i, v) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td>').text(no++));
                newRow.append($('<td>').text(v.lpb_date));
                newRow.append($('<td>').text(v.reference_no));
                newRow.append($('<td>').text(v.kode_barang));
                newRow.append($('<td>').text(v.barang_name));
                newRow.append($('<td>').text(v.spesifikasi));
                newRow.append($('<td>').text(greatFormatRupiah(v.keluar.qty_keluar)));
                newRow.append($('<td>').text(v.keluar.valas_name));
                newRow.append($('<td>').text(greatFormatRupiah(v.keluar.harga_satuan)));
                newRow.append($('<td>').text(greatFormatRupiah(v.keluar.nilai_tukar)));
                newRow.append($('<td>').text(v.keluar.unit_name_keluar));
                newRow.append($('<td>').text(greatFormatRupiah(v.keluar.sub_total)));
                newRow.append($('<td >').html(
                    `
                    <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detail('${v.id}')">
                        <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="btn btn-danger" onclick="remove('${v.id}')">
                        <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                    </button>
                `
                ));
                table.find('tbody').append(newRow);

                totalKeluar += parseFloat(v.keluar.qty_keluar);
                totalSubTotal += parseFloat(v.keluar.sub_total);
            });
            table.find('tfoot').empty();
            var newRow = $('<tr>');
            newRow.append($('<td colspan="6" style="text-align:right;"><b>TOTAL</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalKeluar) + '</b></td>'));
            newRow.append($('<td><b></b></td>'));
            newRow.append($('<td><b></b></td>'));
            newRow.append($('<td><b></b></td>'));
            newRow.append($('<td><b></b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalSubTotal) + '</b></td>'));
            newRow.append($('<td><b></b></td>'));
            table.find('tfoot').append(newRow);
        }

    }

    function dropdownWarehouse() {
        $.ajax({
            url: `<?= base_url('bea-cukai-bc-41/warehouse'); ?>`,
            method: "GET",
            data: {
                divisi_id: $(".divisi_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".warehouse_id").empty()
                $(".warehouse_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".warehouse_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                });
            }
        });
    }

    function dropdownReferensiPenerima() {
        var jenis_pengeluaran = $('#jenis_pengeluaran option:selected').val();
        $.ajax({
            url: "<?= base_url("bea-cukai-bc-41/get-referensi-penerima"); ?>",
            data: {
                jenis_pengeluaran: jenis_pengeluaran,
            },
            method: "GET",
            beforeSend: function(xhr) {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            success: function(res) {
                if (res.status) {
                    $("#reference_penerima_id").empty()
                    $("#reference_penerima_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $("#reference_penerima_id").append(`<option value="${item.id}">${item.name}</option>`)
                    })
                    $("#reference_penerima_id").val();
                }
            }
        });
    }

    function dropdownReferensiPengeluaran() {
        var jenis_pengeluaran = $('#jenis_pengeluaran option:selected').val();
        var reference_penerima_id = $('#reference_penerima_id option:selected').val();

        $.ajax({
            url: "<?= base_url("bea-cukai-bc-41/get-referensi"); ?>",
            data: {
                jenis_pengeluaran: jenis_pengeluaran,
                reference_penerima_id: reference_penerima_id
            },
            method: "GET",
            beforeSend: function(xhr) {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            success: function(res) {
                if (res.status) {
                    $("#multiple_reference_id").empty()
                    $("#multiple_reference_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $("#multiple_reference_id").append(`<option value="${item.id}">${item.reference_no}</option>`)
                    })
                    $("#multiple_reference_id").val();
                }
            }
        });
    }

    function dropdownUnitkeluar(satuanArr) {
        $("#unit_id_keluar").empty()
        $("#unit_id_keluar").append(`<option value=""></option>`)
        satuanArr.forEach(function(item) {
            $("#unit_id_keluar").append(`<option data-konversi_satuan="${item.konversi_satuan}" value="${item.id}">${item.kode_satuan}</option>`)
        });
    }

    $('#btnHideModalNoAju').click(function(e) {
        e.preventDefault();
        $('#modalUpdateNoAju').modal('hide');
    });

    $('#btnUpdateNoAjuModal').click(function(e) {
        e.preventDefault();
        var noAju = $('#no_pengajuan_prev').val();
        var splitValues = noAju.split("-");

        var year = splitValues[2].substring(0, 4);
        var month = splitValues[2].substring(4, 6);
        var day = splitValues[2].substring(6, 8);

        var formattedDate = day + '/' + month + '/' + year;

        $('#tanggal_pengajuan').val(formattedDate);
        $('#no_pengajuan').val(noAju);
        $('#no_urut_dokumen').val(splitValues[3]);
        $('#kode_kantor').val(splitValues[1]);

        $('#modalUpdateNoAju').modal('show');
    });

    // NO AJU ACTION
    $('#no_urut_dokumen').keyup(function() {
        var noAju = $('#no_pengajuan_prev').val();
        var splitValues = noAju.split("-");
        splitValues[3] = $(this).val();
        $('#no_pengajuan_prev').val(splitValues[0] + '-' + splitValues[1] + '-' + splitValues[2] + '-' + splitValues[3]);
    });
    $('#kode_kantor').keyup(function() {
        var noAju = $('#no_pengajuan_prev').val();
        var splitValues = noAju.split("-");
        splitValues[1] = $(this).val();
        $('#no_pengajuan_prev').val(splitValues[0] + '-' + splitValues[1] + '-' + splitValues[2] + '-' + splitValues[3]);
    });

    $("#no_pengajuan_prev").change(function() {
        var tanggalPengajuan = $(this).val();
        var noAju = $('#no_pengajuan_prev').val();
        var tanggalPengajuanSplit = tanggalPengajuan.split("/");
        var noPengajuanSplit = noAju.split("-");
        $('#no_pengajuan_prev').val(noPengajuanSplit[0] + '-' + noPengajuanSplit[1] + '-' + tanggalPengajuanSplit[2] + '' + tanggalPengajuanSplit[1] + '' + tanggalPengajuanSplit[0] + '-' + noPengajuanSplit[3]);
    });

    $("#tanggal_pengajuan").change(function() {
        var tanggalPengajuan = $(this).val();
        var noAju = $('#no_pengajuan_prev').val();
        var tanggalPengajuanSplit = tanggalPengajuan.split("/");
        var noPengajuanSplit = noAju.split("-");
        $('#no_pengajuan_prev').val(noPengajuanSplit[0] + '-' + noPengajuanSplit[1] + '-' + tanggalPengajuanSplit[2] + '' + tanggalPengajuanSplit[1] + '' + tanggalPengajuanSplit[0] + '-' + noPengajuanSplit[3]);
    });
</script>

<?= $this->endSection(); ?>