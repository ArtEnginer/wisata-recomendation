<?php

/** @var \CodeIgniter\View\View $this */
?>

<?= $this->extend('layouts/panel/main') ?>
<?= $this->section('main') ?>

<h1 class="page-title">Data kriteria-perengkingan</h1>
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
                        <table class="striped highlight responsive-table" id="table-kriteria-perengkingan" width="100%">
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
    <h1>Tambah kriteria-perengkingan</h1>
    <br>
    <form id="form-add" class="row" enctype="multipart/form-data">
        <input type="hidden" name="id" id="add-id">
        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
        <div class="input-field col s12">
            <input name="nama" id="add-nama" type="text" class="validate" required>
            <label for="add-nama">Nama kriteria-perengkingan</label>
        </div>

        <div class="input-field col s12">
            <input name="kode" id="add-kode" type="text" class="validate" required>
            <label for="add-kode">Kode kriteria-perengkingan</label>
        </div>

        <div class="input-field col s12">
            <input name="weight" id="add-weight" type="number" step="any" class="validate" value="1" required>
            <label for="add-weight">Weight (Bobot)</label>
        </div>

        <div class="input-field col s12">
            <select name="benefit" id="add-benefit">
                <option value="true">Benefit</option>
                <option value="false">Cost</option>
            </select>
            <label for="add-benefit">Jenis Kriteria</label>
        </div>

        <div class="row">
            <div class="input-field col s12 center">
                <button class="btn waves-effect waves-light green" type="submit"><i class="material-icons left">save</i>Simpan</button>
            </div>
        </div>
    </form>
</div>

<div class="popup side" data-page="edit">
    <h1>Edit Data kriteria-perengkingan</h1>
    <br>
    <form id="form-edit" class="row" enctype="multipart/form-data">
        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
        <input type="hidden" name="id" id="edit-id">

        <div class="input-field col s12">
            <input name="nama" id="edit-nama" type="text" class="validate" required>
            <label for="edit-nama">Nama kriteria-perengkingan</label>
        </div>
        <div class="input-field col s12">
            <input name="kode" id="edit-kode" type="text" class="validate" required>
            <label for="edit-kode">Kode kriteria-perengkingan</label>
        </div>
        <div class="input-field col s12">
            <input name="weight" id="edit-weight" type="number" step="any" class="validate" value="1" required>
            <label for="edit-weight">Weight (Bobot)</label>
        </div>

        <div class="input-field col s12">
            <select name="benefit" id="edit-benefit">
                <option value="true">Benefit</option>
                <option value="false">Cost</option>
            </select>
            <label for="edit-benefit">Jenis Kriteria</label>
        </div>

        <div class="row">
            <div class="input-field col s12 center">
                <button class="btn waves-effect waves-light green" type="submit"><i class="material-icons left">save</i>Simpan</button>
            </div>
        </div>
    </form>
</div>


<?= $this->endSection() ?>