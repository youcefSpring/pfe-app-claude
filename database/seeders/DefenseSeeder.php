<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Defense;
use App\Models\DefenseJury;
use App\Models\Subject;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DefenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing data
        $subject = Subject::first();
        $rooms = Room::take(5)->get();
        $teachers = User::where('role', 'teacher')->get();

        if (!$subject || $rooms->count() < 3 || $teachers->count() < 3) {
            $this->command->warn('Not enough data to create defenses. Need at least 1 subject, 3 rooms, and 3 teachers.');
            return;
        }

        // Generate defenses for the next 3 months
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->addMonths(3)->endOfMonth();

        $defenseData = [
            [
                'defense_date' => $startDate->copy()->addDays(5)->format('Y-m-d'),
                'defense_time' => '09:00:00',
                'status' => 'scheduled',
                'notes' => 'First semester defense session'
            ],
            [
                'defense_date' => $startDate->copy()->addDays(12)->format('Y-m-d'),
                'defense_time' => '14:00:00',
                'status' => 'scheduled',
                'notes' => 'Afternoon defense session'
            ],
            [
                'defense_date' => $startDate->copy()->addDays(19)->format('Y-m-d'),
                'defense_time' => '10:30:00',
                'status' => 'completed',
                'notes' => 'Completed defense - excellent presentation'
            ],
            [
                'defense_date' => $startDate->copy()->addDays(26)->format('Y-m-d'),
                'defense_time' => '15:30:00',
                'status' => 'in_progress',
                'notes' => 'Currently ongoing defense'
            ],
            [
                'defense_date' => $startDate->copy()->addMonths(1)->addDays(3)->format('Y-m-d'),
                'defense_time' => '08:30:00',
                'status' => 'scheduled',
                'notes' => 'Second month defense batch'
            ],
            [
                'defense_date' => $startDate->copy()->addMonths(1)->addDays(10)->format('Y-m-d'),
                'defense_time' => '13:00:00',
                'status' => 'scheduled',
                'notes' => 'Mid-month defense session'
            ],
            [
                'defense_date' => $startDate->copy()->addMonths(1)->addDays(17)->format('Y-m-d'),
                'defense_time' => '16:00:00',
                'status' => 'scheduled',
                'notes' => 'Late afternoon defense'
            ],
            [
                'defense_date' => $startDate->copy()->addMonths(2)->addDays(8)->format('Y-m-d'),
                'defense_time' => '09:30:00',
                'status' => 'scheduled',
                'notes' => 'Third month defense session'
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($defenseData as $index => $data) {
                // Create defense
                $defense = Defense::create([
                    'subject_id' => $subject->id,
                    'defense_date' => $data['defense_date'],
                    'defense_time' => $data['defense_time'],
                    'room_id' => $rooms[$index % $rooms->count()]->id,
                    'duration' => 90,
                    'status' => $data['status'],
                    'notes' => $data['notes'],
                    'scheduled_by' => 1, // Admin user
                    'scheduled_at' => now(),
                ]);

                // Create jury assignments (supervisor, president, examiner)
                $availableTeachers = $teachers->shuffle()->take(3)->values();

                // Ensure we have different teachers for each role
                $supervisorId = $subject->teacher_id ?? $availableTeachers[0]->id;
                $presidentId = $availableTeachers->firstWhere('id', '!=', $supervisorId)->id ?? $availableTeachers[1]->id;
                $examinerId = $availableTeachers->reject(function($teacher) use ($supervisorId, $presidentId) {
                    return $teacher->id == $supervisorId || $teacher->id == $presidentId;
                })->first()->id ?? $availableTeachers[2]->id;

                DefenseJury::create([
                    'defense_id' => $defense->id,
                    'teacher_id' => $supervisorId,
                    'role' => 'supervisor'
                ]);

                DefenseJury::create([
                    'defense_id' => $defense->id,
                    'teacher_id' => $presidentId,
                    'role' => 'president'
                ]);

                DefenseJury::create([
                    'defense_id' => $defense->id,
                    'teacher_id' => $examinerId,
                    'role' => 'examiner'
                ]);
            }

            DB::commit();
            $this->command->info('Successfully created ' . count($defenseData) . ' defenses with jury assignments.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->command->error('Failed to create defenses: ' . $e->getMessage());
        }
    }
}
