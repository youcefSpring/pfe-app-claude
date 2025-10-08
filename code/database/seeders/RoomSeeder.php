<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            [
                'name' => 'Amphi 100',
                'capacity' => 200,
                'equipment' => 'projector'
            ],
            [
                'name' => 'Amphi 101',
                'capacity' => 150,
                'equipment' => 'projector',
            ],
            [
                'name' => '4.101',
                'capacity' => 15,
                'equipment' => 'projector'
            ],
            [
                'name' => '4.102',
                'capacity' => 25,
                'equipment' => 'projector'
            ],
            [
                'name' => '4.201',
                'capacity' => 15,
                'equipment' => 'projector',
            ],
            [
                'name' => '4.202',
                'capacity' => 15,
                'equipment' => '',
            ],
            [
                'name' => '4.203',
                'capacity' => 15,
                'equipment' => 'projector',
            ],
            [
                'name' => '4.301',
                'capacity' => 15,
                'equipment' => 'projector',
            ],
            [
                'name' => '4.302',
                'capacity' => 15,
                'equipment' => '',
            ],
            [
                'name' => '4.303',
                'capacity' => 15,
                'equipment' => 'projector',
            ],
            [
                'name' => '4.305',
                'capacity' => 15,
                'equipment' => '',
            ],

            [
                'name' => 'Conference Room Bloc 5',
                'capacity' => 25,
                'equipment' => 'projector',
            ]
        ];

        foreach ($rooms as $roomData) {
            Room::create($roomData);
        }

        $this->command->info('Created ' . count($rooms) . ' rooms for defense scheduling');
    }
}
