<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            //$table->unsignedInteger('wallet_id')->nullable();
            $table->string('reference')->unique();
            $table->enum('type', ['credit', 'debit'])->comment('Type of transaction credit/debit');
            $table->string('payment_gateway')->default('mpesa');
            $table->double('amount', 10, 2);
            $table->double('charge')->default(0.00);

            //$table->double('balance', 10, 2)->default(0);
            $table->longText('comments')->nullable()->comment('Details of this transaction');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
