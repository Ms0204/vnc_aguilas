<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('recursos', function (Blueprint $table) {
            $table->unique('nombre');
        });
    }

    public function down()
    {
        Schema::table('recursos', function (Blueprint $table) {
            $table->dropUnique(['nombre']);
        });
    }
};
