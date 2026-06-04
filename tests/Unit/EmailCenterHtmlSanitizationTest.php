<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\EmailCenterController;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class EmailCenterHtmlSanitizationTest extends TestCase
{
    public function test_it_preserves_safe_editor_html(): void
    {
        $html = $this->sanitize('<p class="p1">Isi <strong>email</strong></p><ul><li>Item</li></ul>');

        $this->assertStringContainsString('<p>Isi <strong>email</strong></p>', $html);
        $this->assertStringContainsString('<ul><li>Item</li></ul>', $html);
        $this->assertStringNotContainsString('class=', $html);
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

    public function test_it_escapes_plain_text_and_keeps_line_breaks(): void
    {
        $html = $this->sanitize("Halo <user>\nBaris kedua");

        $this->assertSame('Halo &lt;user&gt;<br />' . "\n" . 'Baris kedua', $html);
    }

    private function sanitize(string $html): string
    {
        $method = new ReflectionMethod(EmailCenterController::class, 'sanitizeOutgoingEmailHtml');
        $method->setAccessible(true);

        return $method->invoke(new EmailCenterController(), $html);
    }
}
