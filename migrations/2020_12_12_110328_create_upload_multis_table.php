<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploadMultisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upload_multis', function (Blueprint $table) {
            $table->id();
            $table->integer('upload_id');
            $table->string('model', 30)->nullable(true);
            $table->string('alpha_id', 30)->nullable(true);
            $table->string('title')->nullable(true);
            $table->integer('rid');
            $table->integer('orders')->nullable(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['upload_id', 'rid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('upload_multis');
    }
}
