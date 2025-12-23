<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');               // ✅ Nombre del rol (admin, usuario, etc.)
            $table->string('guard_name');         // 🔐 Spatie requiere este campo ("web" por defecto)
            $table->string('descripcion')->nullable(); // ✍️ Opcional para describir el rol
            $table->timestamps();

            // 🧠 Asegura que cada nombre de rol sea único para cada guardia
            $table->unique(['name', 'guard_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};