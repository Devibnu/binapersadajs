<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogCommentController extends Controller
{
    public function store(Request $request, Blog $blog): RedirectResponse
    {
        abort_unless(
            $blog->is_published && (! $blog->published_at || $blog->published_at->isPast()),
            404
        );

        $message = 'Komentar Anda berhasil dikirim dan menunggu moderasi.';

        if ($request->filled('website_url')) {
            return back()->with('comment_success', $message);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'comment' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $blog->comments()->create([
            ...$validated,
            'parent_id' => null,
            'status' => 'pending',
            'is_admin_reply' => false,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 1000, '') ?: null,
        ]);

        return back()->with('comment_success', $message);
    }
}
