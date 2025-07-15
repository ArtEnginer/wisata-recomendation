<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Models\PenggunaModel;
use App\Models\WisataModel;
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
    public function wisata(): string
    {
        $this->view->setData([
            "page" => "wisata",
            "wisata" => WisataModel::all(),
        ]);
        return $this->view->render("pages/panel/admin/wisata");
    }

    public function kriteriaKlasterisasi(): string
    {
        $this->view->setData([
            "page" => "kriteria-klasterisasi",
        ]);
        return $this->view->render("pages/panel/admin/kriteria-klasterisasi");
    }

    public function kriteriaPerengkingan(): string
    {
        $this->view->setData([
            "page" => "kriteria-perengkingan",
        ]);
        return $this->view->render("pages/panel/admin/kriteria-perengkingan");
    }

    // nilaiKriteriaKlasterisasi
    public function nilaiKriteriaKlasterisasi(): string
    {
        $this->view->setData([
            "page" => "nilai-kriteria-klasterisasi",
        ]);
        return $this->view->render("pages/panel/admin/nilai-kriteria-klasterisasi");
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
