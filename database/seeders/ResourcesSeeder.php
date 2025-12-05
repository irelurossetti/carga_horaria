<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Resource;
use Carbon\Carbon;

class ResourcesSeeder extends Seeder
{
    public function run(): void
    {
        $resources = [
            // Proyectores
            [
                'name' => 'Proyector Epson EB-X41',
                'serial_number' => 'EPSON-001',
                'type' => 'Proyector',
                'status' => 'Disponible',
                'description' => 'Proyector de 3600 lúmenes, ideal para aulas grandes',
                'brand' => 'Epson',
                'model' => 'EB-X41',
                'purchase_date' => Carbon::now()->subMonths(6),
            ],
            [
                'name' => 'Proyector BenQ MH535',
                'serial_number' => 'BENQ-001',
                'type' => 'Proyector',
                'status' => 'Disponible',
                'description' => 'Proyector Full HD 1080p',
                'brand' => 'BenQ',
                'model' => 'MH535',
                'purchase_date' => Carbon::now()->subMonths(12),
            ],
            [
                'name' => 'Proyector Sony VPL-DX221',
                'serial_number' => 'SONY-001',
                'type' => 'Proyector',
                'status' => 'Mantenimiento',
                'description' => 'Proyector en mantenimiento preventivo',
                'brand' => 'Sony',
                'model' => 'VPL-DX221',
                'purchase_date' => Carbon::now()->subYears(2),
            ],

            // Laptops
            [
                'name' => 'Laptop Dell Latitude 5420',
                'serial_number' => 'DELL-LAP-001',
                'type' => 'Laptop',
                'status' => 'Disponible',
                'description' => 'Laptop Intel Core i5, 8GB RAM, 256GB SSD',
                'brand' => 'Dell',
                'model' => 'Latitude 5420',
                'purchase_date' => Carbon::now()->subMonths(8),
            ],
            [
                'name' => 'Laptop HP ProBook 450',
                'serial_number' => 'HP-LAP-001',
                'type' => 'Laptop',
                'status' => 'Disponible',
                'description' => 'Laptop Intel Core i7, 16GB RAM, 512GB SSD',
                'brand' => 'HP',
                'model' => 'ProBook 450 G8',
                'purchase_date' => Carbon::now()->subMonths(4),
            ],
            [
                'name' => 'Laptop Lenovo ThinkPad',
                'serial_number' => 'LENOVO-LAP-001',
                'type' => 'Laptop',
                'status' => 'Dañado',
                'description' => 'Laptop con pantalla dañada, en espera de reparación',
                'brand' => 'Lenovo',
                'model' => 'ThinkPad E14',
                'purchase_date' => Carbon::now()->subYears(1),
            ],

            // Pizarras Digitales
            [
                'name' => 'Pizarra Digital Interactiva Smart Board',
                'serial_number' => 'SMART-001',
                'type' => 'Pizarra Digital',
                'status' => 'Disponible',
                'description' => 'Pizarra interactiva de 77 pulgadas',
                'brand' => 'Smart Technologies',
                'model' => 'SMART Board 7000',
                'purchase_date' => Carbon::now()->subMonths(10),
            ],
            [
                'name' => 'Pizarra Digital Promethean',
                'serial_number' => 'PROM-001',
                'type' => 'Pizarra Digital',
                'status' => 'Disponible',
                'description' => 'Pizarra interactiva con software ActivInspire',
                'brand' => 'Promethean',
                'model' => 'ActivPanel Elements',
                'purchase_date' => Carbon::now()->subMonths(5),
            ],

            // Micrófonos
            [
                'name' => 'Micrófono Inalámbrico Shure',
                'serial_number' => 'SHURE-MIC-001',
                'type' => 'Micrófono',
                'status' => 'Disponible',
                'description' => 'Micrófono inalámbrico de mano',
                'brand' => 'Shure',
                'model' => 'BLX24/SM58',
                'purchase_date' => Carbon::now()->subMonths(7),
            ],
            [
                'name' => 'Micrófono de Solapa Sennheiser',
                'serial_number' => 'SENN-MIC-001',
                'type' => 'Micrófono',
                'status' => 'Disponible',
                'description' => 'Micrófono de solapa inalámbrico',
                'brand' => 'Sennheiser',
                'model' => 'EW 112P G4',
                'purchase_date' => Carbon::now()->subMonths(3),
            ],

            // Parlantes
            [
                'name' => 'Sistema de Parlantes JBL',
                'serial_number' => 'JBL-SPEAK-001',
                'type' => 'Parlantes',
                'status' => 'Disponible',
                'description' => 'Sistema de audio 2.1 con subwoofer',
                'brand' => 'JBL',
                'model' => 'Professional EON615',
                'purchase_date' => Carbon::now()->subMonths(9),
            ],
            [
                'name' => 'Parlantes Bose Portátiles',
                'serial_number' => 'BOSE-SPEAK-001',
                'type' => 'Parlantes',
                'status' => 'Disponible',
                'description' => 'Parlantes portátiles Bluetooth',
                'brand' => 'Bose',
                'model' => 'S1 Pro',
                'purchase_date' => Carbon::now()->subMonths(2),
            ],
        ];

        foreach ($resources as $resource) {
            Resource::create($resource);
        }

        $this->command->info('✅ Se crearon ' . count($resources) . ' recursos de prueba');
    }
}
