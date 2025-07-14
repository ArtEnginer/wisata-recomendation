<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Models\PenggunaModel;
use App\Models\KategoriModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Manage extends BaseController
{
    protected PenggunaModel $user;
    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger,
    ) {
        parent::initController($request, $response, $logger);
        $this->user = PenggunaModel::find(auth()->user()->id);
        $this->view->setData([
            "user" => $this->user,
        ]);
    }
    public function index(): string
    {
        $this->view->setData([
            "page" => "dashboard"
        ]);
        return $this->view->render("pages/panel/admin/index");
    }
    public function barang(): string
    {
        $this->view->setData([
            "page" => "barang",
            "kategori" => KategoriModel::all(),
        ]);
        return $this->view->render("pages/panel/admin/barang");
    }
    public function kategori(): string
    {
        $this->view->setData([
            "page" => "kategori",
        ]);
        return $this->view->render("pages/panel/admin/kategori");
    }
    // barang masuk
    public function barangMasuk(): string
    {
        $this->view->setData([
            "page" => "barang_masuk",
        ]);
        return $this->view->render("pages/panel/admin/barang_masuk");
    }
    // barang keluar
    public function barangKeluar(): string
    {
        $this->view->setData([
            "page" => "barang_keluar",
        ]);
        return $this->view->render("pages/panel/admin/barang_keluar");
    }
    public function maintenance(): string
    {
        $this->view->setData([
            "page" => "maintenance",
        ]);
        return $this->view->render("pages/panel/admin/maintenance");
    }

    public function pengadaanKaryawan(): string
    {
        $this->view->setData([
            "page" => "pengadaan_karyawan",
        ]);
        return $this->view->render("pages/panel/admin/pengadaan_karyawan");
    }

    public function pengadaanTeknisi(): string
    {
        $this->view->setData([
            "page" => "pengadaan_teknisi",
        ]);
        return $this->view->render("pages/panel/admin/pengadaan_teknisi");
    }
    public function peminjaman(): string
    {
        $this->view->setData([
            "page" => "peminjaman",
        ]);
        return $this->view->render("pages/panel/admin/peminjaman");
    }
    public function user(): string
    {
        $this->view->setData([
            "page" => "user",
        ]);
        return $this->view->render("pages/panel/admin/user");
    }

    public function laporan(): string
    {
        $this->view->setData([
            "page" => "laporan",
        ]);
        return $this->view->render("pages/panel/admin/laporan");
    }
}
