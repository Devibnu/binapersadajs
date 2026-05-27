<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogComment;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogCommentController extends Controller
{
    public function index(Request $request): View
    {
        $status = in_array($request->query('status'), ['pending', 'approved', 'rejected'], true)
            ? $request->query('status')
            : null;

        $comments = BlogComment::query()
            ->with('blog')
            ->whereNull('parent_id')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->get();

        $counts = BlogComment::query()
            ->whereNull('parent_id')
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('paneladmin.blog-comments.index', compact('comments', 'counts', 'status'));
    }

    public function show(BlogComment $blogComment): View
    {
        $blogComment->load(['blog', 'replies' => fn ($query) => $query->oldest()]);

        return view('paneladmin.blog-comments.show', compact('blogComment'));
    }

    public function approve(BlogComment $blogComment): RedirectResponse
    {
        $blogComment->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);
        app(ActivityLogger::class)->log('approve', 'Blog Comments', 'Komentar disetujui dari ' . $blogComment->name, $blogComment);

        return redirect()
            ->route('paneladmin.blog-comments.index')
            ->with('success', 'Komentar berhasil disetujui.');
    }

    public function reject(BlogComment $blogComment): RedirectResponse
    {
        $blogComment->update([
            'status' => 'rejected',
            'approved_at' => null,
        ]);
        app(ActivityLogger::class)->log('reject', 'Blog Comments', 'Komentar ditolak dari ' . $blogComment->name, $blogComment);

        return redirect()
            ->route('paneladmin.blog-comments.index')
            ->with('success', 'Komentar berhasil ditolak.');
    }

    public function reply(Request $request, BlogComment $blogComment): RedirectResponse
    {
        abort_if($blogComment->parent_id !== null, 404);

        $validated = $request->validate([
            'comment' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $reply = $blogComment->replies()->create([
            'blog_id' => $blogComment->blog_id,
            'name' => 'Tim Bina Persada',
            'email' => (string) ($request->user()?->email ?? 'admin@binapersadajs.co.id'),
            'comment' => $validated['comment'],
            'status' => 'approved',
            'is_admin_reply' => true,
            'approved_at' => now(),
        ]);
        app(ActivityLogger::class)->log('reply', 'Blog Comments', 'Balasan admin dikirim untuk komentar ' . $blogComment->name, $reply);

        return redirect()
            ->route('paneladmin.blog-comments.show', $blogComment)
            ->with('success', 'Balasan berhasil dikirim.');
    }

    public function destroy(BlogComment $blogComment): RedirectResponse
    {
        app(ActivityLogger::class)->log('delete', 'Blog Comments', 'Komentar dihapus dari ' . $blogComment->name, $blogComment);
        $blogComment->delete();

        return redirect()
            ->route('paneladmin.blog-comments.index')
            ->with('success', 'Komentar berhasil dihapus.');
    }
}
