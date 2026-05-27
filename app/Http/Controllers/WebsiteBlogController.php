<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\PageHero;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class WebsiteBlogController extends Controller
{
    public function index(Request $request)
    {
        $allBlogs = $this->publishedBlogs();
        $posts = $allBlogs;

        if ($request->filled('kategori')) {
            $posts = $posts->where('category', $request->string('kategori')->toString());
        }

        if ($request->filled('tag')) {
            $tag = $request->string('tag')->toString();
            $posts = $posts->filter(fn (Blog $blog) => in_array($tag, $blog->tagList(), true));
        }

        if ($request->filled('arsip')) {
            $archive = $request->string('arsip')->toString();
            $posts = $posts->filter(fn (Blog $blog) => $blog->published_at?->format('Y-m') === $archive);
        }

        return view('website.blog.index', array_merge([
            'posts' => $posts->values(),
            'pageHero' => $this->pageHero(),
        ], $this->sidebarData($allBlogs)));
    }

    public function show(string $slug)
    {
        abort_unless(Schema::hasTable('blogs'), 404);

        $post = Blog::published()->where('slug', $slug)->firstOrFail();
        $allBlogs = $this->publishedBlogs();
        $comments = $post->comments()
            ->approved()
            ->whereNull('parent_id')
            ->with(['replies' => fn ($query) => $query->approved()->oldest()])
            ->latest('approved_at')
            ->latest()
            ->get();

        return view('website.blog.show', array_merge([
            'post' => $post,
            'pageHero' => $this->pageHero(),
            'comments' => $comments,
        ], $this->sidebarData($allBlogs, $post)));
    }

    private function publishedBlogs(): Collection
    {
        if (! Schema::hasTable('blogs')) {
            return collect();
        }

        return Blog::published()
            ->orderBy('sort_order')
            ->latest('published_at')
            ->get();
    }

    private function sidebarData(Collection $blogs, ?Blog $current = null): array
    {
        $recentPosts = $blogs
            ->sortByDesc(fn (Blog $blog) => ($blog->published_at ?: $blog->created_at)?->timestamp ?? 0)
            ->when($current, fn (Collection $items) => $items->where('id', '!=', $current->id))
            ->take(5)
            ->values();

        $categories = $blogs
            ->countBy('category')
            ->sortDesc();

        $archives = $blogs
            ->filter(fn (Blog $blog) => $blog->published_at)
            ->groupBy(fn (Blog $blog) => $blog->published_at->format('Y-m'))
            ->map(function (Collection $items, string $key) {
                return [
                    'key' => $key,
                    'label' => $items->first()->published_at->locale('id')->translatedFormat('F Y'),
                    'count' => $items->count(),
                ];
            })
            ->sortKeysDesc()
            ->values();

        $tags = $blogs
            ->flatMap(fn (Blog $blog) => $blog->tagList())
            ->countBy()
            ->sortDesc();

        return compact('recentPosts', 'categories', 'archives', 'tags');
    }

    private function pageHero(): ?PageHero
    {
        if (! Schema::hasTable('page_heroes')) {
            return null;
        }

        return PageHero::where('page_key', 'blog')
            ->where('is_active', true)
            ->first();
    }
}
