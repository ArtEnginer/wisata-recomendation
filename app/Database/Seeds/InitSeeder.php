<?php

namespace App\Database\Seeds;

use App\Models\PenggunaModel;
use CodeIgniter\Database\Seeder;

class InitSeeder extends Seeder
{
    public function run()
    {

        PenggunaModel::create([
            'username' => 'admin',
            'name' => 'Admin',
        ])->setEmailIdentity([
            'email' => 'admin@gmail.com',
            'password' => "password",
        ])->addGroup('admin')->activate();
        PenggunaModel::create([
            'username' => 'karyawan',
            'name' => 'karyawan',
        ])->setEmailIdentity([
            'email' => 'karyawan@gmail.com',
            'password' => "password",
        ])->addGroup('karyawan')->activate();
        PenggunaModel::create([
            'username' => 'hrd',
            'name' => 'hrd',
        ])->setEmailIdentity([
            'email' => 'hrd@gmail.com',
            'password' => "password",
        ])->addGroup('hrd')->activate();
        PenggunaModel::create([
            'username' => 'teknisi',
            'name' => 'teknisi',
        ])->setEmailIdentity([
            'email' => 'teknisi@gmail.com',
            'password' => "password",
        ])->addGroup('teknisi')->activate();


        // kategori
        $this->db->table('kategori')->insertBatch([
            [
                'kode' => '001',
                'nama' => 'PERLENGKAPAN',
            ],
            [
                'kode' => '002',
                'nama' => 'PERALATAN',
            ],
        ]);
        // barang
        $this->db->table('barang')->insertBatch([
            [
                'kode' => '001',
                'nama' => 'KURSI',
                'kategori_kode' => '001',
                'stok' => 0,
            ],
            [
                'kode' => '002',
                'nama' => 'MEJA',
                'kategori_kode' => '001',
                'stok' => 0,
            ],
            [
                'kode' => '003',
                'nama' => 'LAPTOP',
                'kategori_kode' => '002',
                'stok' => 0,
            ],
        ]);
    }
}
