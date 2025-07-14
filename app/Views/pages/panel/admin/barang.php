<?php

/** @var \CodeIgniter\View\View $this */
?>

<?= $this->extend('layouts/panel/main') ?>
<?= $this->section('main') ?>
<h1 class="page-title">Data barang</h1>
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
                        <table class="striped highlight responsive-table" id="table-barang" width="100%">
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
    <h1>Tambah Peserta barang</h1>
    <br>
    <form action="" id="form-add" class="row">
        <input type="hidden" name="id" id="add-id">
        <div class="input-field col s12">
            <input name="nama" id="add-nama" type="text" class="validate" required>
            <input type="hidden" name="name" id="add-name">
            <label for="add-nama">Nama barang</label>
        </div>
        <!-- kode -->
        <div class="input-field col s12">
            <input name="kode" id="add-kode" type="text" class="validate" required>
            <label for="add-kode">Kode barang</label>
        </div>
        <!-- kategori -->
        <div class="input-field col s12">
            <select name="kategori_kode" id="add-kategori_kode" required>
                <option value="" disabled selected>Pilih kategori</option>
                <?php foreach ($kategori as $k) : ?>
                    <option value="<?= $k->kode ?>"><?= $k->nama ?></option>
                <?php endforeach; ?>
            </select>
            <label for="add-kategori_kode">Kategori</label>
        </div>


        <div class="row">
            <div class="input-field col s12 center">
                <button class="btn waves-effect waves-light green" type="submit"><i class="material-icons left">save</i>Simpan</button>
            </div>
        </div>
    </form>
</div>
<div class="popup side" data-page="edit">
    <h1>Edit Data barang</h1>
    <br>
    <form action="" id="form-edit" class="row">
        <input type="hidden" name="id" id="edit-id">
        <div class="input-field col s12">
            <input name="nama" id="edit-nama" type="text" class="validate" required>
            <input type="hidden" name="name" id="edit-name">
            <label for="edit-nama">Nama barang</label>
        </div>
        <!-- kode -->
        <div class="input-field col s12">
            <input name="kode" id="edit-kode" type="text" class="validate" required>
            <label for="edit-kode">Kode barang</label>
        </div>
        <!-- kategori -->
        <div class="input-field col s12">
            <select name="kategori_kode" id="edit-kategori_kode" required>
                <option value="" disabled selected>Pilih kategori</option>
                <?php foreach ($kategori as $k) : ?>
                    <option value="<?= $k->kode ?>"><?= $k->nama ?></option>
                <?php endforeach; ?>
            </select>
            <label for="edit-kategori_kode">Kategori</label>
        </div>

        <div class="row">
            <div class="input-field col s12 center">
                <button class="btn waves-effect waves-light green" type="submit"><i class="material-icons left">save</i>Simpan</button>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>