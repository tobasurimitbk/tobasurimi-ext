<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Neraca</h1>
        <button style="right: 10px;" class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
            Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <a class="dropdown-item" onclick="printExcel()">Excel</a>
        </ul>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <div class="input-group">
                                        <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal Awal" value="<?= $dateStart; ?>">
                                        <div class="input-group-prepend group-prepend-password align-items-center">
                                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="input-group">
                                        <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal Akhir" value="<?= $dateEnd; ?>">
                                        <div class="input-group-prepend group-prepend-password align-items-center">
                                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="input-group">
                                        <button type="submit" name="cariTanggal" class="btn btn-primary" value="cari">Cari</button>
                                    </div>
                                </div>
                            </div>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table nowrap table-hover" id="myTable" width="100%" cellspacing="0">
                        <thead>
                            <th>Name</th>
                            <th>Nominal</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Set default tanggal: awal bulan - hari ini
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);

    function formatDate(date) {
        // Format dd/mm/yyyy
        let dd = String(date.getDate()).padStart(2, '0');
        let mm = String(date.getMonth() + 1).padStart(2, '0'); // Januari = 0
        let yyyy = date.getFullYear();
        return dd + '/' + mm + '/' + yyyy;
    }

    $(".dateStart").val(formatDate(firstDay));
    $(".dateEnd").val(formatDate(today));

    $(document).ready(function() {
        function loadTable(){
            setLoading(); // tampilkan loading sebelum ajax
            $('#myTable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                paging: false,
                ordering: false,
                searching: false,
                ajax: {
                    url: "<?= base_url('/laporan-accounting/neraca/getData'); ?>",
                    data: {
                        dateStart: $('.dateStart').val(),
                        dateEnd: $('.dateEnd').val()
                    },
                    type: "GET",
                    dataSrc: function (json) {
                        return json.data.map(item => {
                            return {
                                name: formatRow(item),
                                nominal: formatCurrency(item)
                            };
                        });
                    },
                    complete: function() {
                        stopLoading(); // hentikan loading ketika ajax selesai
                    }
                },
                columns: [
                    { data: "name", sortable: false },
                    { data: "nominal", className: "text-right", sortable: false }
                ]
            });
        }

        loadTable(); // load pertama kali

        // tombol cari
        $('button[type=submit]').click(function(e){
            e.preventDefault();
            loadTable(); // load ulang tabel + setLoading/stopLoading otomatis
        });

        // datepicker
        $(".dateStart, .dateEnd").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        $('.icon-dateStart').click(function() { $(".dateStart").focus(); });
        $('.icon-dateEnd').click(function() { $(".dateEnd").focus(); });
    });

    /** styling baris */
    function formatRow(d) {
        let indent = "";

        if (d.type === "kategori") indent = "&nbsp;&nbsp;&nbsp;&nbsp;";
        if (d.type === "sub") indent = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        if (d.type === "total_kategori") indent = "&nbsp;&nbsp;&nbsp;&nbsp;";
        if (d.type === "total_kelompok") indent = "";

        // warna & tebal di total kelompok
        if (d.type === "total_kelompok" || d.type === "total_kategori") {
            return `<b style="font-weight: 700 !important;">${indent}${d.name}</b>`;
        }

        if (d.type === "kategori" || d.type === "kelompok") {
            return `<b style="font-weight: 700 !important;">${indent}${d.name}</b>`;
        }

        // format lain
        return `<span>${indent}${d.name}</span>`;
    }

    function formatCurrency(item) {
        // if (!num) return "";
        if (item.type === "sub") {
            return "Rp " + new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2 }).format(item.nominal);
        } else if(item.type === "total_kategori" || item.type === "total_kelompok") {
            return "<b style='font-weight: 700 !important;'>Rp " + new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2 }).format(item.nominal) + "</b>";
        } else{
            return "";
        }
    }

    /** ===== Export Excel ===== */
    function printExcel() {
        const dateStart = $('.dateStart').val();
        const dateEnd = $('.dateEnd').val();

        const url = "<?= base_url('/laporan-accounting/neraca/printExcel'); ?>?dateStart=" 
                    + encodeURIComponent(dateStart) 
                    + "&dateEnd=" + encodeURIComponent(dateEnd);

        window.open(url, "_blank");
    }
</script>
<?= $this->endSection(); ?>