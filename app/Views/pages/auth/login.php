<?php

/** @var \CodeIgniter\View\View $this */
?>

<?= $this->extend('layouts/auth/main') ?>
<?= $this->section('main') ?>

<div class="auth-card">
    <h1 class="title">
        Silahkan Masuk
    </h1>
    <br>
    <div class="row card-body">
        <form class="col s12" action="#!" id="login" method="post">
            <?= csrf_field() ?>
            <div class="row mb-0">
                <div class="input-field col s12">
                    <input id="cred" name="cred" type="email" class="validate" value="<?= old('cred') ?>"
                        required>
                    <label for="cred">Email</label>
                </div>
            </div>
            <div class="row mb-0">
                <div class="input-field col s12">
                    <input id="password" name="password" type="password" class="validate" required>
                    <label for="password">Kata Sandi</label>
                </div>
            </div>
    </div>
    <button type="submit" class="btn waves-effect waves-light btn-auth">
        Masuk
    </button>
    </form>
</div>
</div>

<?= $this->endSection() ?>