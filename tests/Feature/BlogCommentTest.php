<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_reader_can_submit_pending_comment_which_is_not_shown_before_approval(): void
    {
        $blog = $this->blog();

        $this->withServerVariables([
            'REMOTE_ADDR' => '203.0.113.8',
            'HTTP_USER_AGENT' => 'Browser Test',
        ])->post(route('website.blog.comments.store', $blog), [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'comment' => 'Artikel ini sangat membantu pekerjaan kami.',
            'parent_id' => 999,
            'is_admin_reply' => true,
        ])
            ->assertRedirect()
            ->assertSessionHas('comment_success', 'Komentar Anda berhasil dikirim dan menunggu moderasi.');

        $this->assertDatabaseHas('blog_comments', [
            'blog_id' => $blog->id,
            'name' => 'Budi',
            'status' => 'pending',
            'parent_id' => null,
            'is_admin_reply' => false,
            'ip_address' => '203.0.113.8',
        ]);

        $this->get(route('website.blog.show', $blog->slug))
            ->assertOk()
            ->assertSee('Belum ada komentar.')
            ->assertDontSee('Artikel ini sangat membantu pekerjaan kami.');
    }

    public function test_honeypot_submission_returns_success_without_storing_comment(): void
    {
        $blog = $this->blog();

        $this->post(route('website.blog.comments.store', $blog), [
            'website_url' => 'https://spam.invalid',
            'name' => 'Bot',
            'email' => 'bot@example.com',
            'comment' => 'Komentar spam otomatis.',
        ])
            ->assertRedirect()
            ->assertSessionHas('comment_success');

        $this->assertDatabaseCount('blog_comments', 0);
    }

    public function test_only_approved_comments_are_displayed_on_blog_detail(): void
    {
        $blog = $this->blog();

        $this->comment($blog, ['comment' => 'Masih ditinjau admin.']);
        $this->comment($blog, ['comment' => 'Komentar ditolak.', 'status' => 'rejected']);
        $this->comment($blog, [
            'name' => 'Sari',
            'comment' => 'Pelayanan proyek sangat baik.',
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $this->get(route('website.blog.show', $blog->slug))
            ->assertOk()
            ->assertSee('1 Komentar')
            ->assertSee('Sari')
            ->assertSee('Pelayanan proyek sangat baik.')
            ->assertDontSee('Masih ditinjau admin.')
            ->assertDontSee('Komentar ditolak.');
    }

    public function test_admin_can_filter_view_approve_reject_and_delete_comments(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'moderator@example.com',
            'password' => bcrypt('secret'),
        ]);
        $blog = $this->blog();
        $pending = $this->comment($blog);

        $this->actingAs($admin)
            ->get(route('paneladmin.blog-comments.index', ['status' => 'pending']))
            ->assertOk()
            ->assertSee('Komentar Blog')
            ->assertSee($pending->name)
            ->assertSee('Menunggu');

        $this->actingAs($admin)
            ->get(route('paneladmin.blog-comments.show', $pending))
            ->assertOk()
            ->assertSee('Detail Komentar')
            ->assertSee($pending->comment);

        $this->actingAs($admin)
            ->patch(route('paneladmin.blog-comments.approve', $pending))
            ->assertRedirect(route('paneladmin.blog-comments.index'))
            ->assertSessionHas('success', 'Komentar berhasil disetujui.');

        $pending->refresh();
        $this->assertSame('approved', $pending->status);
        $this->assertNotNull($pending->approved_at);

        $this->get(route('website.blog.show', $blog->slug))
            ->assertSee($pending->comment);

        $this->actingAs($admin)
            ->patch(route('paneladmin.blog-comments.reject', $pending))
            ->assertRedirect(route('paneladmin.blog-comments.index'));

        $this->assertDatabaseHas('blog_comments', [
            'id' => $pending->id,
            'status' => 'rejected',
            'approved_at' => null,
        ]);

        $this->actingAs($admin)
            ->delete(route('paneladmin.blog-comments.destroy', $pending))
            ->assertRedirect(route('paneladmin.blog-comments.index'));

        $this->assertDatabaseMissing('blog_comments', ['id' => $pending->id]);
    }

    public function test_admin_can_reply_to_approved_comment_and_reply_is_nested_on_frontend(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'reply-admin@example.com',
            'password' => bcrypt('secret'),
        ]);
        $blog = $this->blog();
        $comment = $this->comment($blog, [
            'name' => 'Ibnu',
            'comment' => 'Apakah tersedia dukungan pekerjaan shutdown?',
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('paneladmin.blog-comments.index'))
            ->assertOk()
            ->assertSee('Balas');

        $this->actingAs($admin)
            ->get(route('paneladmin.blog-comments.show', $comment))
            ->assertOk()
            ->assertSee('Balas Komentar')
            ->assertSee('Kirim Balasan');

        $this->actingAs($admin)
            ->post(route('paneladmin.blog-comments.reply', $comment), [
                'comment' => 'Terima kasih. Tim kami siap membantu koordinasi pekerjaan shutdown.',
            ])
            ->assertRedirect(route('paneladmin.blog-comments.show', $comment))
            ->assertSessionHas('success', 'Balasan berhasil dikirim.');

        $reply = $comment->replies()->firstOrFail();
        $this->assertTrue($reply->is_admin_reply);
        $this->assertSame('approved', $reply->status);
        $this->assertSame($comment->id, $reply->parent_id);
        $this->assertSame('Tim Bina Persada', $reply->name);

        $this->get(route('website.blog.show', $blog->slug))
            ->assertOk()
            ->assertSee('1 Komentar')
            ->assertSee('Ibnu')
            ->assertSee('Tim Bina Persada')
            ->assertSee('Terima kasih. Tim kami siap membantu koordinasi pekerjaan shutdown.');

        $this->actingAs($admin)
            ->post(route('paneladmin.blog-comments.reply', $reply), [
                'comment' => 'Balasan bertingkat tidak boleh dibuat.',
            ])
            ->assertNotFound();
    }

    private function blog(): Blog
    {
        return Blog::create([
            'title' => 'Fabrication Pipe Installation Project',
            'slug' => 'fabrication-pipe-installation-project',
            'excerpt' => 'Ringkasan artikel.',
            'content' => '<p>Isi artikel proyek.</p>',
            'category' => 'Fabrication',
            'author_name' => 'Admin',
            'published_at' => now()->subDay(),
            'is_published' => true,
            'sort_order' => 1,
        ]);
    }

    private function comment(Blog $blog, array $attributes = []): BlogComment
    {
        return $blog->comments()->create(array_merge([
            'name' => 'Pengunjung',
            'email' => 'pengunjung@example.com',
            'comment' => 'Komentar menunggu persetujuan.',
            'status' => 'pending',
        ], $attributes));
    }
}
