<?php

namespace App\Controllers;

use App\Models\Sawkrs\MahasiswaModel;
use App\Models\PenggunaModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class Home extends BaseController
{
    use ResponseTrait;
    public function index(): string
    {

        return view('pages/landing/index');
    }

    public function register()
    {
        // validation
        if (!$this->validate([
            'nama' => 'required',
            'username' => 'required|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[auth_identities.secret]',
            'password' => 'required',
            'password_confirm' => 'required|matches[password]',
        ])) {
            return $this->fail($this->validator->getErrors());
        }

        $user = PenggunaModel::create([
            'username' => $this->request->getVar('username'),
            'name' => $this->request->getVar('nama'),
        ])->setEmailIdentity([
            'email' => $this->request->getVar('email'),
            'password' => $this->request->getVar('password'),
        ])->addGroup('user')->activate();

        MahasiswaModel::create([
            'user_id' => $user->id,
            'nama' => $this->request->getVar('nama'),
        ]);

        $userModel = new UserModel();
        $loggedUser = $userModel->find($user->id);
        auth()->login($loggedUser);

        return $this->respond($loggedUser, 201);
    }
}
