<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\UserPhone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardContentController extends Controller
{
    public function storePhone(Request $request): RedirectResponse
    {
        // E validate ang owner ug phone  before ma create ang one-to-one records.
        $validated = $request->validate([
            'owner_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:50'],
        ]);

        // Create ang owner ug iyang related phone in one transaction
        DB::transaction(function () use ($validated): void {
            $userPhone = UserPhone::create(['name' => $validated['owner_name']]);
            $userPhone->phone()->create(['phone_number' => $validated['phone_number']]);
        });

        return to_route('dashboard')->with('status', 'Phone contact added.');
    }

    public function storePost(Request $request): RedirectResponse
    {
        // e validate ang content sa post before ma associate sa new post with the signed-in author.
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        Post::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'body' => $validated['body'],
        ]);

        return to_route('dashboard')->with('status', 'Post published.');
    }

    public function storeComment(Request $request, Post $post): RedirectResponse
    {
        // Validate the comment, then create it through the post relationship to set post_id.
        $validated = $request->validate([
            'comment_body' => ['required', 'string', 'max:2000'],
        ]);

        $post->comments()->create([
            'body' => $validated['comment_body'],
        ]);

        return to_route('dashboard')->with('status', 'Comment added.');
    }
}
