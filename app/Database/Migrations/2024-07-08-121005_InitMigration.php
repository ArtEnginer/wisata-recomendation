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
    }

    public function down()
    {
        Eloquent::schema()->dropIfExists('auth_jwt');
        Eloquent::schema()->dropIfExists('kriteria_klasterisasi');
        Eloquent::schema()->dropIfExists('nilai_kriteria_klasterisasi');
        Eloquent::schema()->dropIfExists('kriteria_perengkingan');
        Eloquent::schema()->dropIfExists('wisata');
    }
}
