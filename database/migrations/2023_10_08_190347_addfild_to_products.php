<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('bulk_qty')->nullable();
            $table->float('bulk_commission')->nullable();
            $table->enum('bulk_commission_type',['flat','percentage'])->nullable();

            $table->string('payment_type')->default('cod')->comment('cod,onlinepayment,both');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['bulk_qty','bulk_commission','bulk_commission_type','payment_type']);
        });
    }
};
