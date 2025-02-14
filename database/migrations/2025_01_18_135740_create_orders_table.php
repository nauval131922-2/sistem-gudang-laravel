<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name', 100);
            $table->string('table_no', 5);
            $table->date('order_date');
            // order time
            $table->string('order_time', 100);
            // status
            $table->string('status', 100);
            // total
            $table->integer('total')->unsigned();
            // waitress id
            $table->unsignedBigInteger('waitress_id');
            // cashier id
            $table->unsignedBigInteger('cashier_id');
            $table->timestamps();
            // soft delete
            $table->softDeletes();

            $table->foreign('waitress_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('cashier_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
