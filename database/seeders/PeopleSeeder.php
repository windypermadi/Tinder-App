<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Person;
use Illuminate\Support\Facades\DB;

class PeopleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $people = [
            [
                'name' => 'John Doe',
                'age' => 25,
                'pictures' => json_encode([
                    'https://randomuser.me/api/portraits/men/1.jpg',
                    'https://randomuser.me/api/portraits/men/2.jpg'
                ]),
                'location' => 'Jakarta',
                'likes_count' => 0,
                'email_sent' => false,
            ],
            [
                'name' => 'Jane Smith',
                'age' => 23,
                'pictures' => json_encode([
                    'https://randomuser.me/api/portraits/women/1.jpg',
                    'https://randomuser.me/api/portraits/women/2.jpg'
                ]),
                'location' => 'Bandung',
                'likes_count' => 0,
                'email_sent' => false,
            ],
            [
                'name' => 'Michael Johnson',
                'age' => 28,
                'pictures' => json_encode([
                    'https://randomuser.me/api/portraits/men/3.jpg',
                    'https://randomuser.me/api/portraits/men/4.jpg'
                ]),
                'location' => 'Surabaya',
                'likes_count' => 0,
                'email_sent' => false,
            ],
            [
                'name' => 'Emily Davis',
                'age' => 26,
                'pictures' => json_encode([
                    'https://randomuser.me/api/portraits/women/3.jpg',
                    'https://randomuser.me/api/portraits/women/4.jpg'
                ]),
                'location' => 'Bali',
                'likes_count' => 0,
                'email_sent' => false,
            ],
            [
                'name' => 'David Wilson',
                'age' => 30,
                'pictures' => json_encode([
                    'https://randomuser.me/api/portraits/men/5.jpg',
                    'https://randomuser.me/api/portraits/men/6.jpg'
                ]),
                'location' => 'Yogyakarta',
                'likes_count' => 0,
                'email_sent' => false,
            ],
            [
                'name' => 'Sarah Brown',
                'age' => 24,
                'pictures' => json_encode([
                    'https://randomuser.me/api/portraits/women/5.jpg',
                    'https://randomuser.me/api/portraits/women/6.jpg'
                ]),
                'location' => 'Jakarta',
                'likes_count' => 0,
                'email_sent' => false,
            ],
            [
                'name' => 'James Taylor',
                'age' => 27,
                'pictures' => json_encode([
                    'https://randomuser.me/api/portraits/men/7.jpg',
                    'https://randomuser.me/api/portraits/men/8.jpg'
                ]),
                'location' => 'Medan',
                'likes_count' => 0,
                'email_sent' => false,
            ],
            [
                'name' => 'Jessica Martinez',
                'age' => 22,
                'pictures' => json_encode([
                    'https://randomuser.me/api/portraits/women/7.jpg',
                    'https://randomuser.me/api/portraits/women/8.jpg'
                ]),
                'location' => 'Semarang',
                'likes_count' => 0,
                'email_sent' => false,
            ],
            [
                'name' => 'Robert Anderson',
                'age' => 29,
                'pictures' => json_encode([
                    'https://randomuser.me/api/portraits/men/9.jpg',
                    'https://randomuser.me/api/portraits/men/10.jpg'
                ]),
                'location' => 'Jakarta',
                'likes_count' => 0,
                'email_sent' => false,
            ],
            [
                'name' => 'Amanda Thomas',
                'age' => 25,
                'pictures' => json_encode([
                    'https://randomuser.me/api/portraits/women/9.jpg',
                    'https://randomuser.me/api/portraits/women/10.jpg'
                ]),
                'location' => 'Bandung',
                'likes_count' => 0,
                'email_sent' => false,
            ],
            [
                'name' => 'Christopher Lee',
                'age' => 31,
                'pictures' => json_encode([
                    'https://randomuser.me/api/portraits/men/11.jpg',
                    'https://randomuser.me/api/portraits/men/12.jpg'
                ]),
                'location' => 'Surabaya',
                'likes_count' => 0,
                'email_sent' => false,
            ],
            [
                'name' => 'Lisa White',
                'age' => 23,
                'pictures' => json_encode([
                    'https://randomuser.me/api/portraits/women/11.jpg',
                    'https://randomuser.me/api/portraits/women/12.jpg'
                ]),
                'location' => 'Bali',
                'likes_count' => 0,
                'email_sent' => false,
            ],
            [
                'name' => 'Daniel Harris',
                'age' => 26,
                'pictures' => json_encode([
                    'https://randomuser.me/api/portraits/men/13.jpg',
                    'https://randomuser.me/api/portraits/men/14.jpg'
                ]),
                'location' => 'Yogyakarta',
                'likes_count' => 0,
                'email_sent' => false,
            ],
            [
                'name' => 'Michelle Clark',
                'age' => 24,
                'pictures' => json_encode([
                    'https://randomuser.me/api/portraits/women/13.jpg',
                    'https://randomuser.me/api/portraits/women/14.jpg'
                ]),
                'location' => 'Jakarta',
                'likes_count' => 0,
                'email_sent' => false,
            ],
            [
                'name' => 'Matthew Lewis',
                'age' => 28,
                'pictures' => json_encode([
                    'https://randomuser.me/api/portraits/men/15.jpg',
                    'https://randomuser.me/api/portraits/men/16.jpg'
                ]),
                'location' => 'Medan',
                'likes_count' => 0,
                'email_sent' => false,
            ],
        ];

        foreach ($people as $person) {
            Person::create($person);
        }

        $this->command->info('Created ' . count($people) . ' people successfully!');
    }
}

