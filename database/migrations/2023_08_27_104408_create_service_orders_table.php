<?php

use App\Models\ServicePackage;
use App\Models\User;
use App\Models\VendorService;
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
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->comment('service purchase user');
            $table->foreignIdFor(VendorService::class);
            $table->foreignIdFor(ServicePackage::class);
            $table->enum('status',['progress','pending','success','hold','expire','delivered'])->default('pending');
            $table->string('timer')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('service_orders');
    }
};
