<?php

/** @var \CodeIgniter\View\View $this */
?>

<?= $this->extend('layouts/panel/main') ?>
<?= $this->section('main') ?>


<h1 class="page-title">Data nilai-kriteria-perengkingan</h1>
<div class="page-wrapper">
    <div class="page">
        <div class="container">
            <div class="row">
                <div class="col-6 text-end">
                    <button class="btn waves-effect waves-light green btn-popup" data-target="add" type="button" data-target="form"><i class="material-icons left">add</i>Nilai</button>
                </div>

            </div>
            <div class="row">
                <div class="col s12">
                    <div class="table-wrapper">
                        <table class="striped highlight responsive-table" id="table-nilai-kriteria-perengkingan" width="100%">
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
<div class="popup side" data-page="add" style="max-width: 80%;">
    <h1>Tambah nilai-kriteria-perengkingan</h1>
    <br>
    <form id="form-add" class="row" enctype="multipart/form-data">
        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
        <div class="row">
            <div class="col s12">
                <div class="table-wrapper">
                    <table class="striped highlight responsive-table" id="form-table">
                        <thead>
                            <tr id="form-table-head">
                                <!-- Kolom akan diisi via JS -->
                            </tr>
                        </thead>
                        <tbody id="form-table-body">
                            <!-- Baris akan diisi via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s12 center">
                <button class="btn waves-effect waves-light green" type="submit"><i class="material-icons left">save</i>Simpan</button>
            </div>
        </div>

    </form>
</div>


<div class="popup side" data-page="edit" style="max-width: 80%;">
    <h1>Edit nilai-kriteria-perengkingan</h1>
    <br>
    <form id="form-edit" class="row">
        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
        <input type="hidden" name="wisata_kode" id="edit-wisata-kode">

        <div class="row">
            <div class="col s12">
                <div class="table-wrapper">
                    <table class="striped highlight responsive-table" id="form-edit-table">
                        <thead>
                            <tr id="form-edit-table-head"></tr>
                        </thead>
                        <tbody id="form-edit-table-body"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12 center">
                <button class="btn green" type="submit"><i class="material-icons left">save</i>Simpan</button>
            </div>
        </div>
    </form>
</div>



<?= $this->endSection() ?>