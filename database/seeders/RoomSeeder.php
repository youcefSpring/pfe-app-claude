<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            // Amphitheaters for large defenses
            [
                'name' => 'Amphithéâtre A',
                'capacity' => 120,
                'type' => 'amphitheater',
                'location' => 'Bloc A - Rez-de-chaussée',
                'equipment' => ['Projecteur', 'Système audio', 'Microphones', 'Tableau numérique', 'Caméras', 'Éclairage professionnel'],
                'description' => 'Grand amphithéâtre principal pour les soutenances importantes et les événements académiques.',
                'is_available' => true,
            ],
            [
                'name' => 'Amphithéâtre B',
                'capacity' => 80,
                'type' => 'amphitheater',
                'location' => 'Bloc B - 1er étage',
                'equipment' => ['Projecteur', 'Système audio', 'Microphones', 'Tableau numérique'],
                'description' => 'Amphithéâtre secondaire pour les soutenances de taille moyenne.',
                'is_available' => true,
            ],

            // Classroom for regular defenses
            [
                'name' => 'Salle C201',
                'capacity' => 40,
                'type' => 'classroom',
                'location' => 'Bloc C - 2ème étage',
                'equipment' => ['Projecteur', 'Écran', 'Tableau blanc', 'Ordinateur', 'Haut-parleurs'],
                'description' => 'Salle de classe équipée pour les soutenances de PFE standard.',
                'is_available' => true,
            ],
            [
                'name' => 'Salle C202',
                'capacity' => 35,
                'type' => 'classroom',
                'location' => 'Bloc C - 2ème étage',
                'equipment' => ['Projecteur', 'Écran', 'Tableau blanc', 'Ordinateur'],
                'description' => 'Salle de classe pour soutenances et réunions.',
                'is_available' => true,
            ],
            [
                'name' => 'Salle C203',
                'capacity' => 30,
                'type' => 'classroom',
                'location' => 'Bloc C - 2ème étage',
                'equipment' => ['Projecteur', 'Écran', 'Tableau blanc'],
                'description' => 'Salle de classe compacte pour petites soutenances.',
                'is_available' => true,
            ],

            // Computer labs for technical demonstrations
            [
                'name' => 'Laboratoire Informatique 1',
                'capacity' => 25,
                'type' => 'computer_lab',
                'location' => 'Bloc D - 1er étage',
                'equipment' => ['30 Ordinateurs', 'Projecteur', 'Serveur local', 'Réseau haut débit', 'Logiciels de développement'],
                'description' => 'Laboratoire informatique équipé pour les démonstrations de projets logiciels.',
                'is_available' => true,
            ],
            [
                'name' => 'Laboratoire Informatique 2',
                'capacity' => 20,
                'type' => 'computer_lab',
                'location' => 'Bloc D - 1er étage',
                'equipment' => ['25 Ordinateurs', 'Projecteur', 'Imprimante 3D', 'Équipements IoT'],
                'description' => 'Laboratoire spécialisé pour les projets IoT et prototypage.',
                'is_available' => true,
            ],

            // Electronics labs
            [
                'name' => 'Laboratoire Électronique 1',
                'capacity' => 15,
                'type' => 'electronics_lab',
                'location' => 'Bloc E - Rez-de-chaussée',
                'equipment' => ['Oscilloscopes', 'Générateurs de signaux', 'Alimentations', 'Multimètres', 'Postes à souder', 'Composants électroniques'],
                'description' => 'Laboratoire d\'électronique pour les démonstrations de circuits et systèmes embarqués.',
                'is_available' => true,
            ],
            [
                'name' => 'Laboratoire Électronique 2',
                'capacity' => 12,
                'type' => 'electronics_lab',
                'location' => 'Bloc E - Rez-de-chaussée',
                'equipment' => ['Cartes FPGA', 'Kits Arduino/Raspberry Pi', 'Capteurs divers', 'Équipements de test RF'],
                'description' => 'Laboratoire spécialisé pour les projets FPGA et systèmes embarqués.',
                'is_available' => true,
            ],

            // Engineering workshop
            [
                'name' => 'Atelier de Génie Mécanique',
                'capacity' => 10,
                'type' => 'workshop',
                'location' => 'Bloc F - Rez-de-chaussée',
                'equipment' => ['Machines-outils', 'Imprimante 3D industrielle', 'Scanner 3D', 'Instruments de mesure', 'Logiciels CAO'],
                'description' => 'Atelier équipé pour les démonstrations de prototypes mécaniques.',
                'is_available' => true,
            ],

            // Meeting rooms for small committees
            [
                'name' => 'Salle de Réunion 1',
                'capacity' => 8,
                'type' => 'meeting_room',
                'location' => 'Administration - 1er étage',
                'equipment' => ['Écran TV', 'Système de visioconférence', 'Tableau blanc'],
                'description' => 'Salle de réunion pour comités restreints et évaluations privées.',
                'is_available' => true,
            ],
            [
                'name' => 'Salle de Réunion 2',
                'capacity' => 6,
                'type' => 'meeting_room',
                'location' => 'Administration - 1er étage',
                'equipment' => ['Écran', 'Système audio', 'Caméra'],
                'description' => 'Petite salle de réunion pour entretiens et évaluations.',
                'is_available' => true,
            ],

            // Hybrid/Virtual room for remote defenses
            [
                'name' => 'Studio Virtuel',
                'capacity' => 5,
                'type' => 'virtual_studio',
                'location' => 'Bloc A - 3ème étage',
                'equipment' => ['Caméras professionnelles', 'Éclairage studio', 'Microphones directionnels', 'Écrans multiples', 'Streaming equipment'],
                'description' => 'Studio équipé pour les soutenances hybrides et diffusions en direct.',
                'is_available' => true,
            ],

            // Temporarily unavailable room
            [
                'name' => 'Salle C101',
                'capacity' => 45,
                'type' => 'classroom',
                'location' => 'Bloc C - 1er étage',
                'equipment' => ['En maintenance'],
                'description' => 'Salle temporairement indisponible pour travaux de rénovation.',
                'is_available' => false,
                'unavailable_reason' => 'Travaux de rénovation en cours - Fin prévue: Mars 2024',
            ],
        ];

        foreach ($rooms as $roomData) {
            Room::create([
                'name' => $roomData['name'],
                'capacity' => $roomData['capacity'],
                'type' => $roomData['type'],
                'location' => $roomData['location'],
                'equipment' => $roomData['equipment'],
                'description' => $roomData['description'],
                'is_available' => $roomData['is_available'],
                'unavailable_reason' => $roomData['unavailable_reason'] ?? null,
            ]);
        }
    }
}