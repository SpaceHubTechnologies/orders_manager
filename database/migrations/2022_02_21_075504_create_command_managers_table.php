<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommandManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('command_managers', function (Blueprint $table) {
            $table->id();
            $table->integer("customer_id")->default(0);
            $table->boolean("is_blocked")->default(0);
            $table->string("block_reason")->nullable();
            $table->boolean("change_Rct_VCode")->default(0);
            $table->boolean("is_vat_enabled")->default(1);
            $table->boolean("change_qr_code")->default(0);
            $table->string("new_qr_code")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('command_managers');
    }
}
