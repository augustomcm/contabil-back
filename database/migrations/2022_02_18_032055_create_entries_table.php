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
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->bigInteger('value_value');
            $table->string('value_currency');
            $table->string('type');
            $table->string('payment_type');
            $table->string('status');
            $table->dateTime('date');
            $table->dateTime('paid_at')->nullable();
            $table->foreignIdFor(\App\Models\Category::class)
                ->nullable()->constrained('categories');
            $table->nullableMorphs('account');
            $table->foreignIdFor(\App\Models\User::class, 'owner_id')->constrained('users');
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
        Schema::dropIfExists('entries');
    }
};
