<?php

namespace Database\Seeders;

use App\Models\IshiharaPlate;
use Illuminate\Database\Seeder;

class IshiharaPlateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data 30 Ishihara plates
        $plates = [
            ['plate_number' => 'PLATE-001', 'image_path' => 'images/ishihara/plate-1.jpg', 'correct_answer' => '12', 'difficulty_level' => 1],
            ['plate_number' => 'PLATE-002', 'image_path' => 'images/ishihara/plate-2.jpg', 'correct_answer' => '8', 'difficulty_level' => 1],
            ['plate_number' => 'PLATE-003', 'image_path' => 'images/ishihara/plate-3.jpg', 'correct_answer' => '6', 'difficulty_level' => 1],
            ['plate_number' => 'PLATE-004', 'image_path' => 'images/ishihara/plate-4.jpg', 'correct_answer' => '29', 'difficulty_level' => 1],
            ['plate_number' => 'PLATE-005', 'image_path' => 'images/ishihara/plate-5.jpg', 'correct_answer' => '5', 'difficulty_level' => 1],
            ['plate_number' => 'PLATE-006', 'image_path' => 'images/ishihara/plate-6.jpg', 'correct_answer' => '3', 'difficulty_level' => 1],
            ['plate_number' => 'PLATE-007', 'image_path' => 'images/ishihara/plate-7.jpg', 'correct_answer' => '15', 'difficulty_level' => 1],
            ['plate_number' => 'PLATE-008', 'image_path' => 'images/ishihara/plate-8.jpg', 'correct_answer' => '74', 'difficulty_level' => 2],
            ['plate_number' => 'PLATE-009', 'image_path' => 'images/ishihara/plate-9.jpg', 'correct_answer' => '2', 'difficulty_level' => 1],
            ['plate_number' => 'PLATE-010', 'image_path' => 'images/ishihara/plate-10.jpg', 'correct_answer' => '45', 'difficulty_level' => 2],
            ['plate_number' => 'PLATE-011', 'image_path' => 'images/ishihara/plate-11.jpg', 'correct_answer' => '7', 'difficulty_level' => 1],
            ['plate_number' => 'PLATE-012', 'image_path' => 'images/ishihara/plate-12.jpg', 'correct_answer' => '16', 'difficulty_level' => 2],
            ['plate_number' => 'PLATE-013', 'image_path' => 'images/ishihara/plate-13.jpg', 'correct_answer' => '73', 'difficulty_level' => 2],
            ['plate_number' => 'PLATE-014', 'image_path' => 'images/ishihara/plate-14.jpg', 'correct_answer' => '26', 'difficulty_level' => 2],
            ['plate_number' => 'PLATE-015', 'image_path' => 'images/ishihara/plate-15.jpg', 'correct_answer' => '42', 'difficulty_level' => 2],
            ['plate_number' => 'PLATE-016', 'image_path' => 'images/ishihara/plate-16.jpg', 'correct_answer' => '35', 'difficulty_level' => 2],
            ['plate_number' => 'PLATE-017', 'image_path' => 'images/ishihara/plate-17.jpg', 'correct_answer' => '96', 'difficulty_level' => 3],
            ['plate_number' => 'PLATE-018', 'image_path' => 'images/ishihara/plate-18.jpg', 'correct_answer' => '14', 'difficulty_level' => 2],
            ['plate_number' => 'PLATE-019', 'image_path' => 'images/ishihara/plate-19.jpg', 'correct_answer' => '57', 'difficulty_level' => 3],
            ['plate_number' => 'PLATE-020', 'image_path' => 'images/ishihara/plate-20.jpg', 'correct_answer' => '88', 'difficulty_level' => 3],
            ['plate_number' => 'PLATE-021', 'image_path' => 'images/ishihara/plate-21.jpg', 'correct_answer' => '9', 'difficulty_level' => 1],
            ['plate_number' => 'PLATE-022', 'image_path' => 'images/ishihara/plate-22.jpg', 'correct_answer' => '25', 'difficulty_level' => 2],
            ['plate_number' => 'PLATE-023', 'image_path' => 'images/ishihara/plate-23.jpg', 'correct_answer' => '38', 'difficulty_level' => 2],
            ['plate_number' => 'PLATE-024', 'image_path' => 'images/ishihara/plate-24.jpg', 'correct_answer' => '47', 'difficulty_level' => 3],
            ['plate_number' => 'PLATE-025', 'image_path' => 'images/ishihara/plate-25.jpg', 'correct_answer' => '56', 'difficulty_level' => 3],
            ['plate_number' => 'PLATE-026', 'image_path' => 'images/ishihara/plate-26.jpg', 'correct_answer' => '64', 'difficulty_level' => 3],
            ['plate_number' => 'PLATE-027', 'image_path' => 'images/ishihara/plate-27.jpg', 'correct_answer' => '72', 'difficulty_level' => 3],
            ['plate_number' => 'PLATE-028', 'image_path' => 'images/ishihara/plate-28.jpg', 'correct_answer' => '81', 'difficulty_level' => 4],
            ['plate_number' => 'PLATE-029', 'image_path' => 'images/ishihara/plate-29.jpg', 'correct_answer' => '93', 'difficulty_level' => 4],
            ['plate_number' => 'PLATE-030', 'image_path' => 'images/ishihara/plate-30.jpg', 'correct_answer' => '99', 'difficulty_level' => 4],
        ];

        // Insert data
        foreach ($plates as $index => $plate) {
            IshiharaPlate::updateOrCreate(
                ['plate_number' => $plate['plate_number']], // Cek berdasarkan plate_number
                array_merge($plate, [
                    'is_active' => true,
                    'display_order' => $index + 1,
                    'description' => 'Pelat Ishihara ' . $plate['plate_number'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('Successfully seeded ' . count($plates) . ' Ishihara plates!');
    }
}
