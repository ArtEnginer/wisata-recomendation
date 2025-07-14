<?php

/** @var \CodeIgniter\View\View $this */
?>

<?= $this->extend('layouts/panel/main') ?>
<?= $this->section('main') ?>
<h1 class="page-title">Data Pengadaan Karyawan</h1>
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
                        <table class="striped highlight responsive-table" id="table-pengadaan_karyawan" width="100%">
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
    <h1>Pengajuan Pengadaan Karyawan</h1>
    <br>
    <form action="" id="form-add" class="row">
        <input type="hidden" name="id" id="add-id">
        <div class="input-field col s12">
            <select name="barang_kode" id="add-barang_kode" required>
                <option value="" disabled selected>Pilih barang</option>

            </select>
        </div>
        <div class="input-field col s12">
            <input name="jumlah" id="add-jumlah" type="number" class="validate" required>
            <label for="add-jumlah">Jumlah Pengadaan Karyawan</label>
        </div>
        <div class="input-field col s12">
            <textarea name="alasan" id="add-alasan" class="materialize-textarea" required></textarea>
            <label for="add-alasan">Alasan Pengadaan Karyawan</label>
        </div>

        <div class="row">
            <div class="input-field col s12 center">
                <button class="btn waves-effect waves-light green" type="submit"><i class="material-icons left">save</i>Simpan</button>
            </div>
        </div>
    </form>
</div>
<div class="popup side" data-page="edit">
    <h1>Edit Pengadaan Karyawan</h1>
    <br>
    <form action="" id="form-edit" class="row">
        <input type="hidden" name="id" id="edit-id">
        <div class="input-field col s12">
            <select name="barang_kode" id="edit-barang_kode" required>
                <option value="" disabled selected>Pilih barang</option>

            </select>
        </div>
        <div class="input-field col s12">
            <input name="jumlah" id="edit-jumlah" type="number" class="validate" required>
            <label for="edit-jumlah">Jumlah pengadaan_karyawan</label>
        </div>
        <div class="input-field col s12">
            <textarea name="alasan" id="edit-alasan" class="materialize-textarea" required></textarea>
            <label for="edit-alasan">Alasan Pengadaan Karyawan</label>
        </div>
        <div class="row">
            <div class="input-field col s12 center">
                <button class="btn waves-effect waves-light green" type="submit"><i class="material-icons left">save</i>Simpan</button>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>