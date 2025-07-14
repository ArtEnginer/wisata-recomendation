<?php

/** @var \CodeIgniter\View\View $this */
?>

<?= $this->extend('layouts/panel/main') ?>
<?= $this->section('main') ?>

<h1 class="page-title">Data wisata</h1>
<div class="page-wrapper">
    <div class="page">
        <div class="container">
            <div class="row">
                <div class="col-12 text-end">
                    <button class="btn waves-effect waves-light green btn-popup" data-target="add" type="button" data-target="form"><i class="material-icons left">add</i>Tambah</button>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <div class="table-wrapper">
                        <table class="striped highlight responsive-table" id="table-wisata" width="100%">
                            <thead>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>



<?= $this->section('popup') ?>
<div class="popup side" data-page="add">
    <h1>Tambah wisata</h1>
    <br>
    <form id="form-add" class="row" enctype="multipart/form-data">
        <input type="hidden" name="id" id="add-id">
        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
        <div class="input-field col s12">
            <input name="nama" id="add-nama" type="text" class="validate" required>
            <label for="add-nama">Nama wisata</label>
        </div>

        <div class="input-field col s12">
            <input name="kode" id="add-kode" type="text" class="validate" required>
            <label for="add-kode">Kode wisata</label>
        </div>

        <div class="input-field col s12">
            <input name="alamat" id="add-alamat" type="text" class="validate" required>
            <label for="add-alamat">Alamat wisata</label>
        </div>

        <div class="input-field col s12">
            <textarea name="deskripsi" id="add-deskripsi" class="materialize-textarea" required></textarea>
            <label for="add-deskripsi">Deskripsi wisata</label>
        </div>

        <div class="input-field col s12">
            <input type="file" name="gambar" id="add-gambar" class="validate" accept="image/*">
            <label for="add-gambar">Gambar wisata</label>
        </div>

        <div class="input-field col s6">
            <input type="text" name="latitude" id="add-latitude" class="validate" required>
            <label for="add-latitude">Latitude</label>
        </div>

        <div class="input-field col s6">
            <input type="text" name="longitude" id="add-longitude" class="validate" required>
            <label for="add-longitude">Longitude</label>
        </div>
        <div class="input-field col s12">
            <button type="button" class="btn blue modal-trigger" data-target="modal-map">
                <i class="material-icons left">map</i>Pilih Lokasi di Peta
            </button>
        </div>

        <div class="row">
            <div class="input-field col s12 center">
                <button class="btn waves-effect waves-light green" type="submit"><i class="material-icons left">save</i>Simpan</button>
            </div>
        </div>
    </form>
</div>

<div class="popup side" data-page="edit">
    <h1>Edit Data wisata</h1>
    <br>
    <form id="form-edit" class="row" enctype="multipart/form-data">
        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
        <input type="hidden" name="id" id="edit-id">

        <div class="input-field col s12">
            <input name="nama" id="edit-nama" type="text" class="validate" required>
            <label for="edit-nama">Nama wisata</label>
        </div>
        <div class="input-field col s12">
            <input name="kode" id="edit-kode" type="text" class="validate" required>
            <label for="edit-kode">Kode wisata</label>
        </div>
        <div class="input-field col s12">
            <input name="alamat" id="edit-alamat" type="text" class="validate" required>
            <label for="edit-alamat">Alamat wisata</label>
        </div>
        <div class="input-field col s12">
            <textarea name="deskripsi" id="edit-deskripsi" class="materialize-textarea" required></textarea>
            <label for="edit-deskripsi">Deskripsi wisata</label>
        </div>
        <div class="input-field col s6">
            <input type="text" name="latitude" id="edit-latitude" class="validate" required>
            <label for="edit-latitude">Latitude</label>
        </div>
        <div class="input-field col s6">
            <input type="text" name="longitude" id="edit-longitude" class="validate" required>
            <label for="edit-longitude">Longitude</label>
        </div>
        <div class="input-field col s12">
            <button type="button" class="btn blue modal-trigger" data-target="modal-map">
                <i class="material-icons left">map</i>Pilih Lokasi di Peta
            </button>
        </div>
        <div class="row">
            <div class="input-field col s12 center">
                <button class="btn waves-effect waves-light green" type="submit"><i class="material-icons left">save</i>Simpan</button>
            </div>
        </div>
    </form>
</div>


<div id="modal-map" class="modal modal-fixed-footer">
    <div class="modal-content">
        <h5>Pilih Lokasi Wisata</h5>

        <div id="modalMapContainer" style="height: 100%;">
            <div class="map-loading center-align">
                <div class="preloader-wrapper big active">
                    <div class="spinner-layer spinner-blue-only">
                        <div class="circle-clipper left">
                            <div class="circle"></div>
                        </div>
                        <div class="gap-patch">
                            <div class="circle"></div>
                        </div>
                        <div class="circle-clipper right">
                            <div class="circle"></div>
                        </div>
                    </div>
                </div>
                <p>Memuat peta...</p>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-close btn waves-effect waves-light">Simpan</a>
        <a href="#!" class="modal-close btn-flat">Batal</a>
    </div>
</div>

<?= $this->endSection() ?>