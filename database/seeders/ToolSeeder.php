<?php

namespace Database\Seeders;

use App\Models\Tool;
use Illuminate\Database\Seeder;

class ToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tools = [
            [
                'nama_alat' => 'Dial Gauge Mitutoyo 0-10mm (0.01mm)',
                'kode_alat' => 'DG-001',
                'total_stock' => 12,
                'available_stock' => 12,
            ],
            [
                'nama_alat' => 'Digital Dial Indicator Mitutoyo ID-C 12.7mm (0.001mm)',
                'kode_alat' => 'DG-002',
                'total_stock' => 8,
                'available_stock' => 8,
            ],
            [
                'nama_alat' => 'Bore Gauge / Ball Gauge Set 50-160mm (0.01mm)',
                'kode_alat' => 'BG-001',
                'total_stock' => 6,
                'available_stock' => 6,
            ],
            [
                'nama_alat' => 'Small Hole Bore / Ball Gauge Set 18-35mm',
                'kode_alat' => 'BG-002',
                'total_stock' => 5,
                'available_stock' => 5,
            ],
            [
                'nama_alat' => 'Digital Outside Micrometer 0-25mm (0.001mm)',
                'kode_alat' => 'MIC-001',
                'total_stock' => 10,
                'available_stock' => 10,
            ],
            [
                'nama_alat' => 'Outside Micrometer Mitutoyo 25-50mm (0.01mm)',
                'kode_alat' => 'MIC-002',
                'total_stock' => 8,
                'available_stock' => 8,
            ],
            [
                'nama_alat' => 'Inside Micrometer Tubular Type 50-300mm',
                'kode_alat' => 'MIC-003',
                'total_stock' => 5,
                'available_stock' => 5,
            ],
            [
                'nama_alat' => 'Depth Micrometer Interchangeable Rod 0-150mm',
                'kode_alat' => 'MIC-004',
                'total_stock' => 6,
                'available_stock' => 6,
            ],
            [
                'nama_alat' => 'Vernier Caliper Mitutoyo 150mm (0.02mm)',
                'kode_alat' => 'CAL-001',
                'total_stock' => 15,
                'available_stock' => 15,
            ],
            [
                'nama_alat' => 'Digital Vernier Caliper Absolute 200mm (0.01mm)',
                'kode_alat' => 'CAL-002',
                'total_stock' => 10,
                'available_stock' => 10,
            ],
            [
                'nama_alat' => 'Dial Test Indicator Lever Type 0.8mm (0.01mm)',
                'kode_alat' => 'DTI-001',
                'total_stock' => 8,
                'available_stock' => 8,
            ],
            [
                'nama_alat' => 'High-Accuracy Dial Test Indicator 0.14mm (0.002mm)',
                'kode_alat' => 'DTI-002',
                'total_stock' => 4,
                'available_stock' => 4,
            ],
            [
                'nama_alat' => 'Magnetic Stand Flexible Articulated Arm Holder',
                'kode_alat' => 'MAG-001',
                'total_stock' => 14,
                'available_stock' => 14,
            ],
            [
                'nama_alat' => 'Digital Height Gauge Mitutoyo 300mm (0.01mm)',
                'kode_alat' => 'HG-001',
                'total_stock' => 4,
                'available_stock' => 4,
            ],
            [
                'nama_alat' => 'Vernier Height Gauge Heavy Duty 600mm',
                'kode_alat' => 'HG-002',
                'total_stock' => 3,
                'available_stock' => 3,
            ],
            [
                'nama_alat' => 'Precision Gauge Block Set (Grade 0 - 87 Pcs)',
                'kode_alat' => 'GB-001',
                'total_stock' => 3,
                'available_stock' => 3,
            ],
            [
                'nama_alat' => 'Pin Gauge Set Metric 1.000 - 10.000mm (Step 0.01mm)',
                'kode_alat' => 'PG-001',
                'total_stock' => 4,
                'available_stock' => 4,
            ],
            [
                'nama_alat' => 'Feeler Gauge Thickness Leaf Set (0.03 - 1.00mm)',
                'kode_alat' => 'FG-001',
                'total_stock' => 10,
                'available_stock' => 10,
            ],
        ];

        foreach ($tools as $tool) {
            Tool::updateOrCreate(
                ['kode_alat' => $tool['kode_alat']],
                $tool
            );
        }
    }
}
