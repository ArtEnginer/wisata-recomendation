<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApi;
use App\Models\MaintenanceModel;

class MaintenanceController extends BaseApi
{
    protected $modelName = MaintenanceModel::class;
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

    public function acc($id)
    {

        $maintenance = MaintenanceModel::find($id);
        if (!$maintenance) {
            return $this->failNotFound('Maintenance not found');
        }

        $maintenance->status = 'acc';
        $maintenance->save();


        // Update the stock of the item
        $barang = $maintenance->barang;
        if ($barang) {
            $barang->stok -= $maintenance->jumlah;
            $barang->save();
        } else {
            return $this->failNotFound('Barang not found');
        }

        return $this->respond([
            'status' => 'success',
            'message' => 'Maintenance approved successfully',
            'data' => $maintenance,
        ]);
    }
    public function tolak($id)
    {
        $maintenance = MaintenanceModel::find($id);
        if (!$maintenance) {
            return $this->failNotFound('Maintenance not found');
        }

        $maintenance->status = 'tolak';
        $maintenance->save();

        return $this->respond([
            'status' => 'success',
            'message' => 'Maintenance rejected successfully',
            'data' => $maintenance,
        ]);
    }

    // selesai
    public function selesai($id)
    {
        $maintenance = MaintenanceModel::find($id);
        if (!$maintenance) {
            return $this->failNotFound('Maintenance not found');
        }

        $maintenance->status = 'selesai';
        $maintenance->save();

        // Update the stock of the item
        $barang = $maintenance->barang;
        if ($barang) {
            $barang->stok += $maintenance->jumlah;
            $barang->save();
        } else {
            return $this->failNotFound('Barang not found');
        }


        return $this->respond([
            'status' => 'success',
            'message' => 'Maintenance completed successfully',
            'data' => $maintenance,
        ]);
    }
}
