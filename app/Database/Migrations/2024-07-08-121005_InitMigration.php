<?php

namespace App\Database\Migrations;

use App\Libraries\Eloquent;
use CodeIgniter\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class InitMigration extends Migration
{
    public function up()
    {
        Eloquent::schema()->create("auth_jwt", function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid('user_id')
                ->constrained("users")
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->text("access_token");
            $table->string("refresh_token");
            $table->timestamps();
        });


        Eloquent::schema()->create("wisata", function (Blueprint $table) {
            $table->id();
            $table->string("kode")->unique();
            $table->string("nama");
            $table->string("alamat");
            $table->string("telepon")->nullable();
            $table->string("email")->nullable();
            $table->text("deskripsi")->nullable();
            $table->string("gambar")->nullable();
            $table->string("latitude")->nullable();
            $table->string("longitude")->nullable();
            $table->string("klaster")->nullable();
            $table->timestamps();
        });


        // kriteria_klasterisasi
        Eloquent::schema()->create("kriteria_klasterisasi", function (Blueprint $table) {
            $table->id();
            $table->string("kode")->unique();
            $table->string("nama");
            $table->text("deskripsi")->nullable();
            $table->timestamps();
        });


        // nilai_kriteria_klasterisasi
        Eloquent::schema()->create("nilai_kriteria_klasterisasi", function (Blueprint
        $table) {
            $table->id();
            $table->string("kriteria_klasterisasi_kode");
            $table->foreign("kriteria_klasterisasi_kode")
                ->references("kode")
                ->on("kriteria_klasterisasi")
                ->onUpdate("cascade")
                ->onDelete("cascade");
            $table->string("nilai");
            $table->timestamps();
        });


        // kriteria_perengkingan
        Eloquent::schema()->create("kriteria_perengkingan", function (Blueprint $table) {
            $table->id();
            $table->string("kode")->unique();
            $table->string("nama");
            $table->text("deskripsi")->nullable();
            $table->timestamps();
        });

        // barang
        Eloquent::schema()->create("barang", function (Blueprint $table) {
            $table->id();
            $table->string("kode")->unique();
            $table->string("nama");
            $table->integer("stok");
            $table->string("kategori_kode");
            $table->foreign("kategori_kode")
                ->references("kode")
                ->on("kategori")
                ->onUpdate("cascade")
                ->onDelete("cascade");
            $table->timestamps();
        });

        // Barang Masuk
        Eloquent::schema()->create('barang_masuk', function (Blueprint $table) {
            $table->id();
            $table->string("barang_kode");
            $table->foreign("barang_kode")
                ->references("kode")
                ->on("barang")
                ->onUpdate("cascade")
                ->onDelete("cascade");
            $table->integer('jumlah');
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Barang Keluar
        Eloquent::schema()->create('barang_keluar', function (Blueprint $table) {
            $table->id();
            $table->string("barang_kode");
            $table->foreign("barang_kode")
                ->references("kode")
                ->on("barang")
                ->onUpdate("cascade")
                ->onDelete("cascade");
            $table->integer('jumlah');
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });


        Eloquent::schema()->create('maintenance', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string("barang_kode");
            $table->foreign("barang_kode")
                ->references("kode")
                ->on("barang")
                ->onUpdate("cascade")
                ->onDelete("cascade");
            $table->integer('jumlah');
            $table->text('deskripsi');
            $table->enum('status', ['pending', 'acc', 'tolak', 'selesai'])->default('pending');
            $table->date('tanggal_pengajuan');
            $table->timestamps();
        });

        // Permintaan Perlengkapan
        Eloquent::schema()->create('permintaan_perlengkapan', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string("barang_kode");
            $table->foreign("barang_kode")
                ->references("kode")
                ->on("barang")
                ->onUpdate("cascade")
                ->onDelete("cascade");
            $table->integer('jumlah');
            $table->enum('status', ['pending', 'acc', 'tolak'])->default('pending');
            $table->text('alasan')->nullable();
            $table->date('tanggal_pengajuan')->nullable();
            $table->date('tanggal_persetujuan')->nullable();
            $table->timestamps();
        });


        // Permintaan Perlengkapan oleh Teknisi
        Eloquent::schema()->create('permintaan_teknisi', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string("barang_kode");
            $table->foreign("barang_kode")
                ->references("kode")
                ->on("barang")
                ->onUpdate("cascade")
                ->onDelete("cascade");
            $table->integer('jumlah');
            $table->text('alasan');
            $table->enum('status', ['pending', 'acc', 'tolak'])->default('pending');
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_persetujuan')->nullable();
            $table->timestamps();
        });

        // Peminjaman
        Eloquent::schema()->create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string("barang_kode");
            $table->foreign("barang_kode")
                ->references("kode")
                ->on("barang")
                ->onUpdate("cascade")
                ->onDelete("cascade");
            $table->integer('jumlah');
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali')->nullable();
            $table->enum('status_approval', ['pending', 'acc', 'tolak'])->default('pending');
            $table->enum('status', ['pinjam', 'kembali', 'hilang', 'rusak'])->default('pinjam');
            $table->timestamps();
        });

        // Penyusutan
        Eloquent::schema()->create('penyusutan', function (Blueprint $table) {
            $table->id();
            $table->string("barang_kode");
            $table->foreign("barang_kode")
                ->references("kode")
                ->on("barang")
                ->onUpdate("cascade")
                ->onDelete("cascade");
            $table->date('tanggal');
            $table->decimal('nilai_penyusutan', 10, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Eloquent::schema()->dropIfExists('auth_jwt');
        Eloquent::schema()->dropIfExists('kategori');
        Eloquent::schema()->dropIfExists('barang');
        Eloquent::schema()->dropIfExists('barang_masuk');
        Eloquent::schema()->dropIfExists('barang_keluar');
        Eloquent::schema()->dropIfExists('permintaan_perlengkapan');
        Eloquent::schema()->dropIfExists('maintenance');
        Eloquent::schema()->dropIfExists('permintaan_teknisi');
        Eloquent::schema()->dropIfExists('penyusutan');
        Eloquent::schema()->dropIfExists('peminjaman');
        Eloquent::schema()->dropIfExists('kriteria_klasterisasi');
        Eloquent::schema()->dropIfExists('nilai_kriteria_klasterisasi');
        Eloquent::schema()->dropIfExists('kriteria_perengkingan');
        Eloquent::schema()->dropIfExists('wisata');
    }
}
