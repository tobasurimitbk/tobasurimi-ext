<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "header" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-41/id/header/' . encrypt($bc41['id'])) ?>" style="<?= session()->getFlashdata('isCompleteFormHeader') == false ? 'color: red' :  'color:green' ?>">Header</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "entitas" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-41/id/entitas/' . encrypt($bc41['id'])) ?>" style="<?= session()->getFlashdata('isCompleteFormEntitas') == false ? 'color: red' :  'color:green' ?>">Entitas</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "dokumen" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-41/id/dokumen/' . encrypt($bc41['id'])) ?>" style="<?= session()->getFlashdata('isCompleteFormDokumen') == false ? 'color: red' :  'color:green' ?>">Dokumen</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "pengangkut" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-41/id/pengangkut/' . encrypt($bc41['id'])) ?>" style="<?= session()->getFlashdata('isCompleteFormPengangkut') == false ? 'color: red' :  'color:green' ?>">Pengangkut</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "kemasan-peti-kemas" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-41/id/kemasan-peti-kemas/' . encrypt($bc41['id'])) ?>" style="<?= session()->getFlashdata('isCompleteFormPetiKemas') == false ? 'color: red' :  'color:green' ?>">Kemasan & Peti Kemas</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "transaksi" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-41/id/transaksi/' . encrypt($bc41['id'])) ?>" style="<?= session()->getFlashdata('isCompleteFormTransaksi') == false ? 'color: red' :  'color:green' ?>">Transaksi</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "barang" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-41/id/barang/' . encrypt($bc41['id'])) ?>" style="<?= session()->getFlashdata('isCompleteFormBarang') == false ? 'color: red' :  'color:green' ?>">Barang</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "pungutan" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-41/id/pungutan/' . encrypt($bc41['id'])) ?>" style="<?= session()->getFlashdata('isCompleteFormPungutan') == false ? 'color: red' :  'color:green' ?>">Pungutan</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "pernyataan" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-41/id/pernyataan/' . encrypt($bc41['id'])) ?>" style="<?= session()->getFlashdata('isCompleteFormPernyataan') == false ? 'color: red' :  'color:green' ?>">Pernyataan</a>
    </li>
</ul>