<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->id();

            $table->string('model' , 30)->nullable(true);
            $table->string('alpha_id' , 30)->nullable(true);
            $table->string('file_name')->nullable(true);
            $table->string('full_path')->nullable(true);
            $table->string('view_path')->nullable(true);
            $table->string('original_name')->nullable(true);
            $table->string('file_ext')->nullable(true);
            $table->string('mime')->nullable(true);
            $table->string('size')->nullable(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uploads');
    }
}
