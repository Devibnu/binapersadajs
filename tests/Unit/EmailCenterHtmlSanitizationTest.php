<?php

namespace Tests\Unit;

use App\Services\EmailBodyRendererService;
use PHPUnit\Framework\TestCase;

class EmailCenterHtmlSanitizationTest extends TestCase
{
    public function test_it_preserves_safe_editor_html(): void
    {
        $html = $this->sanitize('<p class="p1">Isi <strong>email</strong></p><ul><li>Item</li></ul>');

        $this->assertStringContainsString('<p class="p1">Isi <strong>email</strong></p>', $html);
        $this->assertStringContainsString('<ul><li>Item</li></ul>', $html);
        $this->assertStringNotContainsString('&lt;p', $html);
    }

    public function test_it_removes_unsafe_html(): void
    {
        $html = $this->sanitize('<p onclick="alert(1)">Halo</p><script>alert(1)</script><a href="javascript:alert(1)">Klik</a>');

        $this->assertStringContainsString('<p>Halo</p>', $html);
        $this->assertStringContainsString('<a href="#">Klik</a>', $html);
        $this->assertStringNotContainsString('onclick', $html);
        $this->assertStringNotContainsString('<script', $html);
    }

    public function test_it_decodes_legacy_escaped_editor_html(): void
    {
        $html = $this->sanitize('&lt;p class=&quot;p1&quot;&gt;Isi &lt;span style=&quot;color:red&quot;&gt;email&lt;/span&gt;&lt;/p&gt;');

        $this->assertSame('<p class="p1">Isi <span style="color:red">email</span></p>', $html);
    }

    public function test_it_preserves_html_email_layout_attributes(): void
    {
        $html = $this->sanitize('<table width="600" bgcolor="#ffffff"><tbody><tr><td align="center" style="padding:20px;"><img src="https://example.com/logo.png" width="120" height="40"><a href="https://example.com" style="background:#2152ff;color:#fff;">Open</a></td></tr></tbody></table>');

        $this->assertStringContainsString('<table width="600" bgcolor="#ffffff">', $html);
        $this->assertStringContainsString('<td align="center" style="padding:20px;">', $html);
        $this->assertStringContainsString('<img src="https://example.com/logo.png" width="120" height="40">', $html);
        $this->assertStringContainsString('<a href="https://example.com" style="background:#2152ff;color:#fff;">Open</a>', $html);
    }

    public function test_it_escapes_plain_text_and_keeps_line_breaks(): void
    {
        $html = $this->sanitize("Halo <user>\nBaris kedua");

        $this->assertSame('Halo &lt;user&gt;<br />' . "\n" . 'Baris kedua', $html);
    }

    private function sanitize(string $html): string
    {
        return (new EmailBodyRendererService())->sanitizeHtml($html);
    }
}
