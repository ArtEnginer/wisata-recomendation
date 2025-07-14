<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApi;
use App\Models\BarangModel;
use App\Models\PengadaanKaryawanModel;

class PengadaanKaryawanController extends BaseApi
{
    protected $modelName = PengadaanKaryawanModel::class;
    protected $load = ['barang', 'user'];

    public function validateCreate(&$request)
    {
        $rules = [
            'barang_kode' => 'required',
            'jumlah' => 'required|integer',
            'alasan' => 'required',
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

    public function acc($id)
    {

        $pengadaan_karyawan = PengadaanKaryawanModel::find($id);
        if (!$pengadaan_karyawan) {
            return $this->failNotFound('pengadaan_karyawan not found');
        }

        $pengadaan_karyawan->status = 'acc';
        $pengadaan_karyawan->save();


        // Update the stock of the item
        $barang = $pengadaan_karyawan->barang;
        if ($barang) {
            $tanggal_persetujuan = date('Y-m-d H:i:s');
            $barang->stok += $pengadaan_karyawan->jumlah;
            $barang->save();
            $pengadaan_karyawan->tanggal_persetujuan = $tanggal_persetujuan;
            $pengadaan_karyawan->save();
        } else {
            return $this->failNotFound('Barang not found');
        }

        return $this->respond([
            'status' => 'success',
            'message' => 'pengadaan_karyawan approved successfully',
            'data' => $pengadaan_karyawan,
        ]);
    }
    public function tolak($id)
    {
        $pengadaan_karyawan = PengadaanKaryawanModel::find($id);
        if (!$pengadaan_karyawan) {
            return $this->failNotFound('pengadaan_karyawan not found');
        }

        $pengadaan_karyawan->status = 'tolak';
        $pengadaan_karyawan->save();

        return $this->respond([
            'status' => 'success',
            'message' => 'pengadaan_karyawan rejected successfully',
            'data' => $pengadaan_karyawan,
        ]);
    }

    public function beforeDelete(&$data)
    {
        $pengadaan_karyawan = PengadaanKaryawanModel::find($data['id']);
        if ($pengadaan_karyawan) {
            $barang = BarangModel::where('kode', $pengadaan_karyawan->barang_kode)->first();
            if ($barang) {
                $barang->stok -= $pengadaan_karyawan->jumlah;
                $barang->save();
            } else {
                return $this->failNotFound('Barang not found');
            }
        } else {
            return $this->failNotFound('pengadaan_karyawan not found');
        }
    }
}
