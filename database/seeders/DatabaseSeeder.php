<?php

namespace Database\Seeders;

use App\Models\Caja;
use App\Models\CajaMovimiento;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Laboratorio;
use App\Models\Lote;
use App\Models\Permiso;
use App\Models\Producto;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- Permisos y roles ----------
        $permisos = [
            'usuarios' => 'Usuarios',
            'roles' => 'Roles',
            'permisos' => 'Permisos',
            'categorias' => 'Categorías',
            'laboratorios' => 'Laboratorios y proveedores',
            'productos' => 'Productos',
            'lotes' => 'Lotes',
            'clientes' => 'Clientes',
            'inventario' => 'Inventarios',
            'compras' => 'Compras',
            'ventas' => 'Ventas',
            'caja' => 'Caja',
            'reportes' => 'Reportes',
        ];
        $ids = [];
        foreach ($permisos as $clave => $nombre) {
            $ids[$clave] = Permiso::create(['nombre' => $nombre, 'clave' => $clave])->id;
        }

        $admin = Rol::create(['nombre' => 'admin', 'descripcion' => 'Administrador del sistema']);
        $admin->permisos()->sync(array_values($ids));

        $vendedor = Rol::create(['nombre' => 'vendedor', 'descripcion' => 'Vendedor / farmacéutico']);
        $vendedor->permisos()->sync([$ids['productos'], $ids['clientes'], $ids['inventario'], $ids['ventas'], $ids['caja']]);

        // ---------- Usuarios ----------
        $uAdmin = User::create([
            'name' => 'Edson',
            'usuario' => 'edson',
            'apellido_paterno' => 'Callisaya',
            'apellido_materno' => 'Calle',
            'email' => 'admin@nextlevel.com',
            'password' => Hash::make('Admin123*'),
            'telefono' => '70000000',
            'direccion' => 'La Paz, Bolivia',
            'rol_id' => $admin->id,
            'activo' => true,
        ]);
        User::create([
            'name' => 'Adriana',
            'usuario' => 'adriana',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'Espejo',
            'email' => 'vendedor@nextlevel.com',
            'password' => Hash::make('Vendedor123*'),
            'telefono' => '76754567',
            'direccion' => 'La Paz, Bolivia',
            'rol_id' => $vendedor->id,
            'activo' => true,
        ]);

        // ---------- Catálogos ----------
        $cat = [];
        foreach ([
            'Analgésicos y antiinflamatorios' => 'Medicamentos para el dolor y la inflamación',
            'Antibióticos' => 'Medicamentos de venta con receta',
            'Vitaminas y suplementos' => 'Vitaminas y complementos nutricionales',
            'Higiene y antisépticos' => 'Alcohol, barbijos y limpieza',
            'Gastrointestinales' => 'Medicamentos para el estómago',
            'Respiratorios' => 'Jarabes y medicamentos para la tos',
        ] as $nombre => $desc) {
            $cat[$nombre] = Categoria::create(['nombre' => $nombre, 'descripcion' => $desc])->id;
        }

        $lab = [
            'LAFAR' => Laboratorio::create(['nombre' => 'LAFAR', 'telefono' => '2308759', 'contacto' => 'JUAN PEREZ', 'celular' => '78965453', 'direccion' => 'Av. Buenos Aires 1200, La Paz'])->id,
            'BAGO' => Laboratorio::create(['nombre' => 'BAGO', 'telefono' => '2308790', 'contacto' => 'MARIA ALEJANDRA ORTIZ', 'celular' => '75121390', 'direccion' => 'Calle Murillo 540, La Paz'])->id,
            'INTI' => Laboratorio::create(['nombre' => 'INTI', 'telefono' => '23028790', 'contacto' => 'OSCAR ALEJANDRO ESPEJO', 'celular' => '75098790', 'direccion' => 'Av. 6 de Agosto 2100, La Paz'])->id,
        ];

        Cliente::create(['nombre' => 'JOSE ARMANDO TORRES', 'nit_ci' => '8797999', 'celular' => '76567678', 'correo' => 'jose@gmail.com']);
        Cliente::create(['nombre' => 'ARMANDO GUTIERREZ', 'nit_ci' => '5678', 'celular' => '76777', 'correo' => null]);

        // ---------- Productos ----------
        $productos = [
            ['P001', 'Paracetamol 500 mg (caja x 20)', 'Analgésico y antipirético', 'Analgésicos y antiinflamatorios', 'LAFAR', 12.00, 15, 120],
            ['P002', 'Ibuprofeno 400 mg (caja x 20)', 'Antiinflamatorio', 'Analgésicos y antiinflamatorios', 'BAGO', 18.00, 15, 100],
            ['P003', 'Amoxicilina 500 mg (caja x 12)', 'Antibiótico de amplio espectro', 'Antibióticos', 'INTI', 35.00, 10, 80],
            ['P004', 'Azitromicina 500 mg (caja x 3)', 'Antibiótico', 'Antibióticos', 'BAGO', 42.00, 10, 60],
            ['P005', 'Vitamina C 1 g (tubo x 10)', 'Tabletas efervescentes', 'Vitaminas y suplementos', 'LAFAR', 20.00, 10, 80],
            ['P006', 'Complejo B (caja x 30)', 'Suplemento vitamínico', 'Vitaminas y suplementos', 'INTI', 28.00, 10, 80],
            ['P007', 'Alcohol medicinal 70% 250 ml', 'Antiséptico', 'Higiene y antisépticos', 'LAFAR', 10.00, 20, 120],
            ['P008', 'Alcohol en gel 250 ml', 'Desinfectante de manos', 'Higiene y antisépticos', 'LAFAR', 15.00, 15, 100],
            ['P009', 'Barbijo descartable (caja x 50)', 'Protección respiratoria', 'Higiene y antisépticos', 'INTI', 25.00, 10, 60],
            ['P010', 'Suero oral (sobre)', 'Rehidratación oral', 'Gastrointestinales', 'BAGO', 5.00, 30, 200],
            ['P011', 'Jarabe para la tos 120 ml', 'Antitusivo', 'Respiratorios', 'BAGO', 22.00, 10, 60],
            ['P012', 'Omeprazol 20 mg (caja x 14)', 'Protector gástrico', 'Gastrointestinales', 'INTI', 24.00, 10, 80],
        ];
        $prod = [];
        foreach ($productos as [$codigo, $nombre, $desc, $categoria, $laboratorio, $precio, $min, $max]) {
            $prod[$codigo] = Producto::create([
                'codigo' => $codigo, 'nombre' => $nombre, 'descripcion' => $desc,
                'categoria_id' => $cat[$categoria], 'laboratorio_id' => $lab[$laboratorio],
                'precio_venta' => $precio, 'stock_minimo' => $min, 'stock_maximo' => $max, 'activo' => true,
            ]);
        }

        // ---------- Compra inicial (stock de ejemplo) ----------
        // Las fechas de vencimiento son relativas a hoy para mostrar las alertas
        $hoy = now();
        $lotes = [
            ['P001', 'LT-2026-001', $hoy->copy()->addMonths(18), 60, 8.00],
            ['P002', 'LT-2026-002', $hoy->copy()->addMonths(14), 40, 12.00],
            ['P003', 'LT-2026-003', $hoy->copy()->addMonths(10), 25, 25.00],
            ['P004', 'LT-2026-004', $hoy->copy()->addMonths(12), 4, 30.00],
            ['P005', 'LT-2026-005', $hoy->copy()->addMonths(8), 30, 14.00],
            ['P006', 'LT-2026-006', $hoy->copy()->addDays(25), 30, 20.00],
            ['P007', 'LT-2026-007', $hoy->copy()->addMonths(20), 50, 6.00],
            ['P008', 'LT-2026-008', $hoy->copy()->addMonths(16), 35, 10.00],
            ['P009', 'LT-2026-009', null, 20, 17.00],
            ['P010', 'LT-2026-010', $hoy->copy()->addMonths(9), 80, 3.00],
            ['P011', 'LT-2026-011', $hoy->copy()->subDays(40), 8, 15.00],
            ['P012', 'LT-2026-012', $hoy->copy()->addMonths(11), 30, 16.00],
        ];
        $compra = Compra::create([
            'laboratorio_id' => $lab['LAFAR'], 'user_id' => $uAdmin->id, 'fecha' => $hoy->toDateString(),
            'comprobante' => 'FACTURA', 'total' => 0, 'observacion' => 'Stock inicial de ejemplo',
        ]);
        $total = 0;
        foreach ($lotes as [$codigo, $numero, $venc, $cant, $costo]) {
            $lote = Lote::create([
                'producto_id' => $prod[$codigo]->id, 'numero_lote' => $numero,
                'fecha_vencimiento' => $venc ? $venc->toDateString() : null,
                'cantidad_inicial' => $cant, 'cantidad_actual' => $cant, 'costo_unitario' => $costo, 'compra_id' => $compra->id,
            ]);
            DetalleCompra::create([
                'compra_id' => $compra->id, 'producto_id' => $prod[$codigo]->id, 'lote_id' => $lote->id,
                'cantidad' => $cant, 'costo_unitario' => $costo, 'subtotal' => $cant * $costo,
            ]);
            $total += $cant * $costo;
        }
        $compra->update(['total' => $total]);
    }
}
