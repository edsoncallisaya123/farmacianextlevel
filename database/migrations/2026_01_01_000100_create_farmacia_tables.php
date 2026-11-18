<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $t) {
            $t->id();
            $t->string('nombre')->unique();
            $t->string('descripcion')->nullable();
            $t->timestamps();
        });

        Schema::create('permisos', function (Blueprint $t) {
            $t->id();
            $t->string('nombre');
            $t->string('clave')->unique();
            $t->timestamps();
        });

        Schema::create('permiso_rol', function (Blueprint $t) {
            $t->foreignId('rol_id')->constrained('roles')->cascadeOnDelete();
            $t->foreignId('permiso_id')->constrained('permisos')->cascadeOnDelete();
            $t->primary(['rol_id', 'permiso_id']);
        });

        Schema::table('users', function (Blueprint $t) {
            $t->string('usuario')->nullable()->unique()->after('name');
            $t->string('apellido_paterno')->nullable();
            $t->string('apellido_materno')->nullable();
            $t->string('telefono')->nullable();
            $t->string('direccion')->nullable();
            $t->string('foto')->nullable();
            $t->boolean('activo')->default(true);
            $t->foreignId('rol_id')->nullable()->constrained('roles')->nullOnDelete();
        });

        Schema::create('categorias', function (Blueprint $t) {
            $t->id();
            $t->string('nombre')->unique();
            $t->string('descripcion')->nullable();
            $t->timestamps();
        });

        Schema::create('laboratorios', function (Blueprint $t) {
            $t->id();
            $t->string('nombre');
            $t->string('telefono')->nullable();
            $t->string('contacto')->nullable();
            $t->string('celular')->nullable();
            $t->string('direccion')->nullable();
            $t->timestamps();
        });

        Schema::create('productos', function (Blueprint $t) {
            $t->id();
            $t->string('codigo')->unique();
            $t->string('nombre');
            $t->text('descripcion')->nullable();
            $t->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $t->foreignId('laboratorio_id')->nullable()->constrained('laboratorios')->nullOnDelete();
            $t->decimal('precio_venta', 10, 2)->default(0);
            $t->unsignedInteger('stock_minimo')->default(0);
            $t->unsignedInteger('stock_maximo')->default(0);
            $t->string('imagen')->nullable();
            $t->boolean('activo')->default(true);
            $t->timestamps();
        });

        Schema::create('clientes', function (Blueprint $t) {
            $t->id();
            $t->string('nombre');
            $t->string('nit_ci')->nullable();
            $t->string('celular')->nullable();
            $t->string('correo')->nullable();
            $t->timestamps();
        });

        Schema::create('cajas', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained('users');
            $t->dateTime('fecha_apertura');
            $t->decimal('monto_inicial', 12, 2)->default(0);
            $t->string('descripcion')->nullable();
            $t->dateTime('fecha_cierre')->nullable();
            $t->decimal('monto_final', 12, 2)->nullable();
            $t->string('estado')->default('abierta');
            $t->timestamps();
        });

        Schema::create('caja_movimientos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('caja_id')->constrained('cajas')->cascadeOnDelete();
            $t->string('tipo'); // ingreso | egreso
            $t->string('concepto');
            $t->decimal('monto', 12, 2);
            $t->string('origen')->default('manual'); // manual | venta | compra
            $t->unsignedBigInteger('referencia_id')->nullable();
            $t->dateTime('fecha');
            $t->timestamps();
        });

        Schema::create('compras', function (Blueprint $t) {
            $t->id();
            $t->foreignId('laboratorio_id')->constrained('laboratorios');
            $t->foreignId('user_id')->constrained('users');
            $t->foreignId('caja_id')->nullable()->constrained('cajas')->nullOnDelete();
            $t->date('fecha');
            $t->string('comprobante')->default('FACTURA');
            $t->decimal('total', 12, 2)->default(0);
            $t->string('observacion')->nullable();
            $t->timestamps();
        });

        Schema::create('lotes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('producto_id')->constrained('productos');
            $t->string('numero_lote');
            $t->date('fecha_vencimiento')->nullable();
            $t->unsignedInteger('cantidad_inicial');
            $t->unsignedInteger('cantidad_actual');
            $t->decimal('costo_unitario', 10, 2)->default(0);
            $t->foreignId('compra_id')->nullable()->constrained('compras')->nullOnDelete();
            $t->timestamps();
        });

        Schema::create('detalle_compras', function (Blueprint $t) {
            $t->id();
            $t->foreignId('compra_id')->constrained('compras')->cascadeOnDelete();
            $t->foreignId('producto_id')->constrained('productos');
            $t->foreignId('lote_id')->constrained('lotes');
            $t->unsignedInteger('cantidad');
            $t->decimal('costo_unitario', 10, 2);
            $t->decimal('subtotal', 12, 2);
            $t->timestamps();
        });

        Schema::create('ventas', function (Blueprint $t) {
            $t->id();
            $t->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $t->foreignId('user_id')->constrained('users');
            $t->foreignId('caja_id')->nullable()->constrained('cajas')->nullOnDelete();
            $t->dateTime('fecha');
            $t->decimal('total', 12, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('detalle_ventas', function (Blueprint $t) {
            $t->id();
            $t->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $t->foreignId('producto_id')->constrained('productos');
            $t->foreignId('lote_id')->constrained('lotes');
            $t->unsignedInteger('cantidad');
            $t->decimal('precio_unitario', 10, 2);
            $t->decimal('subtotal', 12, 2);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
        Schema::dropIfExists('ventas');
        Schema::dropIfExists('detalle_compras');
        Schema::dropIfExists('lotes');
        Schema::dropIfExists('compras');
        Schema::dropIfExists('caja_movimientos');
        Schema::dropIfExists('cajas');
        Schema::dropIfExists('clientes');
        Schema::dropIfExists('productos');
        Schema::dropIfExists('laboratorios');
        Schema::dropIfExists('categorias');
        Schema::table('users', function (Blueprint $t) {
            $t->dropConstrainedForeignId('rol_id');
            $t->dropColumn(['usuario', 'apellido_paterno', 'apellido_materno', 'telefono', 'direccion', 'foto', 'activo']);
        });
        Schema::dropIfExists('permiso_rol');
        Schema::dropIfExists('permisos');
        Schema::dropIfExists('roles');
    }
};
