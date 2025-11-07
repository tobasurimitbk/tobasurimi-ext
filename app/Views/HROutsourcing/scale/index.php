<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<style>
    .table-actions {
        white-space: nowrap;
        width: 1%;
    }
    
    #qrModal .modal-body {
        background: #fff !important;
        z-index: 1055 !important;
        position: relative;
    }

    #qrResult img {
        width: 100% !important;
        max-width: 350px;
        height: auto;
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    #qrResult img:hover {
        transform: scale(1.05);
    }

    .btn-action {
        width: 35px;
        height: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 2px;
    }
</style>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Manajemen Barang</h1>
    </div>
    
    <!-- Card untuk Generate QR Code -->
    <!-- <div class="card mb-4">
        <div class="card-header">
            <h5>Generate QR Code Spesifikasi</h5>
        </div>
        <div class="card-body">
            <div class="form-add-spp row justify-content-start mb-3">
                <?= csrf_field() ?>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select barang_id" id="barang_id" name="barang_id">
                            <option value=""></option>
                        </select>
                        <label for="floatingInput" style="z-index: 1;">Pilih Barang</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-spp form-floating mb-3">
                        <select class="form-select spesifikasi_id" id="spesifikasi_id" name="spesifikasi_id">
                        </select>
                        <label for="spesifikasi_id">Pilih Spesifikasi Barang</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <button type="button" id="btn-generate" class="btn btn-primary mt-3">
                        <i class="fas fa-qrcode"></i> Generate QR Code
                    </button>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Card untuk Table Data Barang -->
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h5>Data Barang</h5>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-primary" onclick="showCreateModal()">
                        <i class="fas fa-plus"></i> Tambah Barang
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="tableBarang">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Tanggal Dibuat</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan diisi via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Modal Create -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createForm">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" onclick="saveBarang()">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <?= csrf_field() ?>
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label for="edit_spesifikasi" class="form-label">Spesifikasi</label>
                        <input type="text" class="form-control" id="edit_spesifikasi" name="spesifikasi" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" onclick="updateBarang()">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal QR Code -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" id="qrResult">
                <!-- QR Code muncul disini -->
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-print" class="btn btn-success">
                    <i class="fas fa-print"></i> Print
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Delete Confirmation -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus barang ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentDeleteId = null;
    let currentEditId = null;
    const csrfToken = '<?= csrf_token() ?>';
    let abortController = null;

    // Initialize Select2 untuk form generate QR
    $("#barang_id").select2({
        placeholder: "Pilih Barang",
        theme: "bootstrap-5",
        minimumInputLength: 3,
        width: '100%',
        ajax: {
            delay: 300,
            transport: function(params, success, failure) {
                if (abortController) {
                    abortController.abort();
                }
                abortController = new AbortController();

                fetch("<?= base_url('hr-outsourcing-company/search-master-barang'); ?>?" + new URLSearchParams({
                    q: params.data.term
                }), {
                    signal: abortController.signal
                })
                .then(res => res.json())
                .then(success)
                .catch(err => {
                    if (err.name !== "AbortError") failure(err);
                });
            },
            processResults: function(data) {
                return {
                    results: data.data.map(item => ({
                        id: item.id,
                        text: `${item.master_barang}`
                    }))
                };
            }
        }
    });

    $("#spesifikasi_id").select2({
        placeholder: "Pilih Spesifikasi Barang",
        theme: "bootstrap-5",
        ajax: {
            delay: 300,
            transport: function(params, success, failure) {
                if (abortController) {
                    abortController.abort();
                }
                abortController = new AbortController();

                fetch("<?= base_url('hr-outsourcing-company/search-barang'); ?>?" + new URLSearchParams({
                    barang_id: $('#barang_id option:selected').val(),
                    q: params.data.term
                }), {
                    signal: abortController.signal
                })
                .then(res => res.json())
                .then(success)
                .catch(err => {
                    if (err.name !== "AbortError") failure(err);
                });
            },
            processResults: function(data) {
                return {
                    results: data.data.map(item => ({
                        id: item.id,
                        name: item.name,
                        satuan: item.kode_satuan,
                    }))
                };
            }
        }
    });

    // Load data table
    function loadTable() {
        $.ajax({
            url: "<?= base_url('hr-outsourcing-scale/get-data') ?>",
            method: "GET",
            success: function(response) {
                if (response.status === 'success') {
                    let html = '';
                    response.data.forEach((item, index) => {
                        html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.name}</td>
                                <td>${item.createdAt}</td>
                                <td class="table-actions text-center">
                                    <button class="btn btn-sm btn-warning btn-action" onclick="showEditModal(${item.id})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info btn-action" onclick="generateQR(${item.id})" title="QR Code">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-action" onclick="showDeleteModal(${item.id})" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#tableBarang tbody').html(html);
                }
            }
        });
    }

    // Show create modal
    function showCreateModal() {
        // Load master barang untuk form create
        $('#createModal').modal('show');
    }

    // Show edit modal
    function showEditModal(id) {
        currentEditId = id;
        
        $.ajax({
            url: "<?= base_url('hr-outsourcing-scale/edit/') ?>" + id,
            method: "GET",
            success: function(response) {
                if (response.status === 'success') {
                    // Load master barang untuk form edit
                    // Isi form dengan data yang ada
                    $('#edit_id').val(response.data.id);
                    $('#edit_name').val(response.data.name);
                    $('#editModal').modal('show');
                } else {
                    alert(response.message);
                }
            }
        });
    }

    // Show delete confirmation modal
    function showDeleteModal(id) {
        currentDeleteId = id;
        $('#deleteModal').modal('show');
    }

    // Generate QR Code dari form generate
    $("#btn-generate").on("click", function() {
        const csrf = $(`[name="${csrfToken}"]`);
        let spesifikasi_id = $("#spesifikasi_id option:selected").val();

        if (!spesifikasi_id) {
            alert("Pilih spesifikasi barang terlebih dahulu");
            return;
        }

        $.ajax({
            url: "<?= base_url('hr-outsourcing-scale/generate-qr') ?>",
            method: "POST",
            data: { 
                spesifikasi_id: spesifikasi_id,
                [csrfToken]: csrf.val()
            },
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            success: function(response) {
                if (response.status === "ok") {
                    $("#qrResult").html(response.html);
                    $("#qrModal").modal("show");
                } else {
                    alert("Gagal generate QR Code");
                }
            }
        });
    });

    // Generate QR Code dari table
    function generateQR(spesifikasi_id) {
        const csrf = $(`[name="${csrfToken}"]`);
        
        $.ajax({
            url: "<?= base_url('hr-outsourcing-scale') ?>",
            method: "POST",
            data: { 
                spesifikasi_id: spesifikasi_id,
                [csrfToken]: csrf.val()
            },
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            success: function(response) {
                if (response.status === "ok") {
                    $("#qrResult").html(response.html);
                    $("#qrModal").modal("show");
                } else {
                    alert("Gagal generate QR Code");
                }
            }
        });
    }

    // Save new barang
    function saveBarang() {
        const formData = new FormData(document.getElementById('createForm'));
        const csrf = $(`[name="${csrfToken}"]`);
        formData.append(csrfToken, csrf.val());

        $.ajax({
            url: "<?= base_url('hr-outsourcing-scale/store') ?>",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#createModal').modal('hide');
                    $('#createForm')[0].reset();
                    loadTable();
                    showToast('success', response.message);
                } else {
                    showValidationErrors(response.errors, 'createForm');
                }
            }
        });
    }

    // Update barang
    function updateBarang() {
        if (!currentEditId) return;
        
        const formData = new FormData(document.getElementById('editForm'));
        const csrf = $(`[name="${csrfToken}"]`);
        formData.append(csrfToken, csrf.val());

        $.ajax({
            url: "<?= base_url('hr-outsourcing-scale/update/') ?>" + currentEditId,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#editModal').modal('hide');
                    loadTable();
                    showToast('success', response.message);
                    currentEditId = null;
                } else {
                    showValidationErrors(response.errors, 'editForm');
                }
            }
        });
    }

    // Confirm delete
    $('#confirmDelete').on('click', function() {
        if (currentDeleteId) {
            const csrf = $(`[name="${csrfToken}"]`);
            
            $.ajax({
                url: "<?= base_url('hr-outsourcing-scale/delete/') ?>" + currentDeleteId,
                method: "POST",
                data: { 
                    [csrfToken]: csrf.val()
                },
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                success: function(response) {
                    if (response.status === 'success') {
                        $('#deleteModal').modal('hide');
                        loadTable();
                        showToast('success', response.message);
                    } else {
                        showToast('error', response.message);
                    }
                    currentDeleteId = null;
                }
            });
        }
    });

    // Print QR Code
    $("#btn-print").on("click", function() {
        let printContents = document.getElementById("qrResult").innerHTML;
        let w = window.open();
        w.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Print QR Code</title>
                <style>
                    body { text-align: center; padding: 20px; }
                    img { max-width: 300px; height: auto; }
                </style>
            </head>
            <body>
                ${printContents}
            </body>
            </html>
        `);
        w.document.close();
        w.print();
    });

    // Utility functions
    function showValidationErrors(errors, formId) {
        // Reset previous errors
        $(`#${formId} .is-invalid`).removeClass('is-invalid');
        $(`#${formId} .invalid-feedback`).remove();
        
        // Show new errors
        for (const field in errors) {
            $(`#${formId} [name="${field}"]`).addClass('is-invalid');
            $(`#${formId} [name="${field}"]`).after(`<div class="invalid-feedback">${errors[field]}</div>`);
        }
    }

    function showToast(type, message) {
        // Simple alert untuk sekarang, bisa diganti dengan toast library
        if (type === 'success') {
            alert('Sukses: ' + message);
        } else {
            alert('Error: ' + message);
        }
    }

    // Load table on page load
    $(document).ready(function() {
        loadTable();
        
        // Initialize select2 untuk form create dan edit
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });
</script>

<?= $this->endSection(); ?>