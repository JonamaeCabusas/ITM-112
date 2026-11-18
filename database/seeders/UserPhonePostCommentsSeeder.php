<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Phone;
use App\Models\Post;
use App\Models\User;
use App\Models\UserPhone;
use Illuminate\Database\Seeder;

class UserPhonePostCommentsSeeder extends Seeder
{
    public function run(): void
    {
        // Use an existing user as the author so seeded posts have a valid user relationship.
        $author = User::query()->orderBy('id')->firstOrFail();

        // Create owner and phone records for the one-to-one phone relationship.
        foreach ([
            ['name' => 'Alex Morgan', 'phone_number' => '09120522719'],
            ['name' => 'Sam Rivera', 'phone_number' => '+63917543210'],
            ['name' => 'Jordan Lee', 'phone_number' => '09123456789'],
            ['name' => 'Taylor Smith', 'phone_number' => '+639123456789'],
            ['name' => 'Casey Johnson', 'phone_number' => '09129876543'],
        ] as $contact) {
            $userPhone = UserPhone::query()->firstOrCreate([
                'name' => $contact['name'],
            ]);

            Phone::query()->firstOrCreate(
                ['user_phone_id' => $userPhone->id],
                ['phone_number' => $contact['phone_number']],
            );
        }

        // Create posts and their related comments for the one-to-many relationships.
        foreach ([
            [
                'title' => 'Welcome to the Community',
                'body' => 'Sharing a few updates and looking forward to hearing from everyone.',
                'comments' => [
                    'Thanks for sharing this update.',
                    'Looking forward to more news.',
                ],
            ],
            [
                'title' => 'Weekend Project',
                'body' => 'We finished planning a small community garden this weekend.',
                'comments' => [
                    'That sounds like a great project.',
                    'Count me in to help.',
                ],
            ],
        ] as $postData) {
            $post = Post::query()->firstOrCreate(
                [
                    'user_id' => $author->id,
                    'title' => $postData['title'],
                ],
                ['body' => $postData['body']],
            );

            foreach ($postData['comments'] as $body) {
                Comment::query()->firstOrCreate([
                    'post_id' => $post->id,
                    'body' => $body,
                ]);
            }
        }
    }
}
