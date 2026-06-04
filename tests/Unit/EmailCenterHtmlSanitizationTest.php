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

    public function test_it_renders_only_body_content_from_full_html_email_documents(): void
    {
        $html = $this->sanitize(<<<'HTML'
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional //EN">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Facebook</title>
    <!-- tracking comment -->
  </head>
  <body>
    <table width="600" bgcolor="#ffffff">
      <tbody>
        <tr>
          <td align="center" style="padding:20px;">
            <div>Business Manager</div>
            <p>Please confirm your email address</p>
            <a href="https://facebook.com/confirm" style="background:#1877f2;color:#fff;">Confirm Now</a>
          </td>
        </tr>
      </tbody>
    </table>
  </body>
</html>
HTML);

        $this->assertStringContainsString('<table width="600" bgcolor="#ffffff">', $html);
        $this->assertStringContainsString('<div>Business Manager</div>', $html);
        $this->assertStringContainsString('<p>Please confirm your email address</p>', $html);
        $this->assertStringContainsString('<a href="https://facebook.com/confirm" style="background:#1877f2;color:#fff;">Confirm Now</a>', $html);
        $this->assertStringNotContainsStringIgnoringCase('doctype', $html);
        $this->assertStringNotContainsStringIgnoringCase('html public', $html);
        $this->assertStringNotContainsStringIgnoringCase('<html', $html);
        $this->assertStringNotContainsStringIgnoringCase('<head', $html);
        $this->assertStringNotContainsStringIgnoringCase('<meta', $html);
        $this->assertStringNotContainsStringIgnoringCase('<title', $html);
        $this->assertStringNotContainsString('tracking comment', $html);
    }

    public function test_it_removes_document_artifacts_from_preview_text(): void
    {
        $preview = (new EmailBodyRendererService())->preview(<<<'HTML'
< HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional //EN">Facebook
<html><head><title>Facebook</title></head><body>
<table><tr><td>Business Manager</td></tr></table>
<p>Please confirm your email address</p>
<a href="https://facebook.com/confirm">Confirm Now</a>
</body></html>
HTML);

        $this->assertStringContainsString('Business Manager', $preview);
        $this->assertStringContainsString('Please confirm your email address', $preview);
        $this->assertStringContainsString('Confirm Now', $preview);
        $this->assertStringNotContainsStringIgnoringCase('html public', $preview);
        $this->assertStringNotContainsStringIgnoringCase('doctype', $preview);
        $this->assertStringNotContainsString('<html', $preview);
    }

    public function test_it_cleans_document_artifacts_from_plain_text_fallback(): void
    {
        $rendered = (new EmailBodyRendererService())->render(null, <<<'TEXT'
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional //EN">
<html><head><title>Facebook</title></head><body>
<p>Please confirm your email address</p>
</body></html>
TEXT);

        $this->assertSame('Please confirm your email address', $rendered['text']);
        $this->assertStringContainsString('Please confirm your email address', $rendered['html']);
        $this->assertStringNotContainsStringIgnoringCase('html public', $rendered['html']);
        $this->assertStringNotContainsStringIgnoringCase('doctype', $rendered['html']);
        $this->assertStringNotContainsString('&lt;html', $rendered['html']);
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
