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
                'name' => 'Amphitheater A',
                'capacity' => 200,
                'equipment' => json_encode(['projector', 'microphone', 'audio_system', 'recording']),
            ],
            [
                'name' => 'Amphitheater B',
                'capacity' => 150,
                'equipment' => json_encode(['projector', 'microphone', 'audio_system']),
            ],
            [
                'name' => 'Conference Room 101',
                'capacity' => 30,
                'equipment' => json_encode(['projector', 'whiteboard', 'video_conference']),
            ],
            [
                'name' => 'Conference Room 102',
                'capacity' => 25,
                'equipment' => json_encode(['projector', 'whiteboard']),
            ],
            [
                'name' => 'Conference Room 201',
                'capacity' => 40,
                'equipment' => json_encode(['projector', 'whiteboard', 'video_conference', 'recording']),
            ],
            [
                'name' => 'Seminar Room 301',
                'capacity' => 20,
                'equipment' => json_encode(['projector', 'whiteboard']),
            ],
            [
                'name' => 'Seminar Room 302',
                'capacity' => 15,
                'equipment' => json_encode(['projector', 'whiteboard']),
            ],
            [
                'name' => 'Computer Lab 1',
                'capacity' => 30,
                'equipment' => json_encode(['computers', 'projector', 'whiteboard']),
            ],
            [
                'name' => 'Computer Lab 2',
                'capacity' => 25,
                'equipment' => json_encode(['computers', 'projector']),
            ],
            [
                'name' => 'Research Lab A',
                'capacity' => 12,
                'equipment' => json_encode(['computers', 'specialized_equipment', 'whiteboard']),
            ],
            [
                'name' => 'Executive Meeting Room',
                'capacity' => 15,
                'equipment' => json_encode(['projector', 'video_conference', 'recording', 'premium_furniture']),
            ],
        ];

        foreach ($rooms as $roomData) {
            Room::create($roomData);
        }

        $this->command->info('Created ' . count($rooms) . ' rooms for defense scheduling');
    }
}
