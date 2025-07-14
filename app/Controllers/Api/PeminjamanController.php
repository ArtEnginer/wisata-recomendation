<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApi;
use App\Models\BarangModel;
use App\Models\PeminjamanModel;

class PeminjamanController extends BaseApi
{
    protected $modelName = PeminjamanModel::class;
    protected $load = ['barang', 'user'];

    public function validateCreate(&$request)
    {
        $rules = [
            'barang_kode' => 'required',
            'jumlah' => 'required|integer',

        ];

        if (!$this->validate($rules)) {
            return false;
        }

        return true;
    }

    public function validateUpdate(&$request)
    {
        $rules = [
            'barang_kode' => 'required',
            'jumlah' => 'required|integer',

        ];

        if (!$this->validate($rules)) {
            return false;
        }

        return true;
    }

    public function beforeCreate(&$data)
    {
        $data['user_id'] = auth()->user()->id;
    }

    // BEFORE UPDATE
    public function beforeUpdate(&$data)
    {
        // edit stok barang
        $peminjaman = PeminjamanModel::find($data['id']);
        if ($peminjaman) {
            $barang = BarangModel::where('kode', $peminjaman->barang_kode)->first();
            if ($barang) {
                $barang->fill([
                    'stok' => $barang->stok + $peminjaman->jumlah,
                ]);
                $barang->save();
            } else {
                return $this->failNotFound('Barang not found');
            }
        } else {
            return $this->failNotFound('peminjaman not found');
        }
    }

    public function acc($id)
    {

        $peminjaman = PeminjamanModel::find($id);
        if (!$peminjaman) {
            return $this->failNotFound('peminjaman not found');
        }

        $barang = $peminjaman->barang;
        if ($barang) {
            $barang->stok -= $peminjaman->jumlah;
            $barang->save();
            $peminjaman->tanggal_pinjam = date('Y-m-d H:i:s');
            $peminjaman->status_approval = 'acc';
            $peminjaman->status = 'pinjam';
            $peminjaman->save();
        } else {
            return $this->failNotFound('Barang not found');
        }

        return $this->respond([
            'status' => 'success',
            'message' => 'peminjaman approved successfully',
            'data' => $peminjaman,
        ]);
    }
    public function tolak($id)
    {
        $peminjaman = PeminjamanModel::find($id);
        if (!$peminjaman) {
            return $this->failNotFound('peminjaman not found');
        }

        $peminjaman->status = 'tolak';
        $peminjaman->save();

        return $this->respond([
            'status' => 'success',
            'message' => 'peminjaman rejected successfully',
            'data' => $peminjaman,
        ]);
    }

    public function beforeDelete(&$data)
    {
        $peminjaman = PeminjamanModel::find($data['id']);
        if ($peminjaman) {
            $barang = BarangModel::where('kode', $peminjaman->barang_kode)->first();
            if ($barang) {
                $barang->fill([
                    'stok' => $barang->stok + $peminjaman->jumlah,
                ]);
                $barang->save();
            } else {
                return $this->failNotFound('Barang not found');
            }
        } else {
            return $this->failNotFound('peminjaman not found');
        }
    }
}
