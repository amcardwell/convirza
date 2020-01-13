<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConvirzaConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('convirza_config', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key');
            $table->string('value')->nullable();
        });

        $table = \DB::table('convirza_config');
        $table->insert([
            'key' => 'report_last_run',
            'value' => now()->toDateTimeString()
        ]);
        $table->insert([
            'key' => 'api_key',
            'value' => null
        ]);
        $table->insert([
            'key' => 'api_key_expires',
            'value' => null
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('convirza_config');
    }
}
