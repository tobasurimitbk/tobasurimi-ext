<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "header" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-23/id/header/' . encrypt($lpb->id)) ?>" style="<?= session()->getFlashdata('isCompleteFormHeader') == false ? 'color: red' :  'color:green' ?>">Header</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "entitas" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-23/id/entitas/' . encrypt($lpb->id)) ?>" style="<?= session()->getFlashdata('isCompleteFormEntitas') == false ? 'color: red' :  'color:green' ?>">Entitas</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "dokumen" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-23/id/dokumen/' . encrypt($lpb->id)) ?>" style="<?= session()->getFlashdata('isCompleteFormDokumen') == false ? 'color: red' :  'color:green' ?>">Dokumen</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "pengangkut" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-23/id/pengangkut/' . encrypt($lpb->id)) ?>" style="<?= session()->getFlashdata('isCompleteFormPengangkut') == false ? 'color: red' :  'color:green' ?>">Pengangkut</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "kemasan-peti-kemas" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-23/id/kemasan-peti-kemas/' . encrypt($lpb->id)) ?>" style="<?= session()->getFlashdata('isCompleteFormPetiKemas') == false ? 'color: red' :  'color:green' ?>">Kemasan & Peti Kemas</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "transaksi" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-23/id/transaksi/' . encrypt($lpb->id)) ?>" style="<?= session()->getFlashdata('isCompleteFormTransaksi') == false ? 'color: red' :  'color:green' ?>">Transaksi</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "barang" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-23/id/barang/' . encrypt($lpb->id)) ?>" style="<?= session()->getFlashdata('isCompleteFormBarang') == false ? 'color: red' :  'color:green' ?>">Barang</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "pungutan" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-23/id/pungutan/' . encrypt($lpb->id)) ?>" style="<?= session()->getFlashdata('isCompleteFormPungutan') == false ? 'color: red' :  'color:green' ?>">Pungutan</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "pernyataan" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-23/id/pernyataan/' . encrypt($lpb->id)) ?>" style="<?= session()->getFlashdata('isCompleteFormPernyataan') == false ? 'color: red' :  'color:green' ?>">Pernyataan</a>
    </li>
</ul>