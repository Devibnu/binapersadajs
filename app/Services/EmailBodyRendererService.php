<?php

namespace App\Services;

use Illuminate\Support\Str;

class EmailBodyRendererService
{
    private const ALLOWED_TAGS = '<html><body><head><style><table><thead><tbody><tfoot><tr><td><th><div><span><img><a><p><br><ul><ol><li><strong><b><em><i><u><blockquote><center><font><hr><h1><h2><h3><h4><h5><h6>';

    public function render(?string $htmlBody = null, ?string $plainBody = null): array
    {
        $htmlBody = trim((string) $htmlBody);
        $plainBody = $this->cleanPlainText((string) $plainBody);

        if ($htmlBody !== '') {
            $html = $this->sanitizeHtml($htmlBody);

            return [
                'html' => $html,
                'text' => $this->plainTextFromHtml($html) ?: $plainBody,
                'has_html' => true,
            ];
        }

        return [
            'html' => $plainBody !== '' ? nl2br(e($plainBody)) : '',
            'text' => $plainBody,
            'has_html' => false,
        ];
    }

    public function sanitizeHtml(string $html): string
    {
        $html = trim(str_replace(["\r\n", "\r"], "\n", $html));
        if ($html === '') {
            return '';
        }

        $decodedHtml = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if ($decodedHtml !== $html && $this->containsHtml($decodedHtml)) {
            $html = $decodedHtml;
        }

        if (! $this->containsHtml($html)) {
            return nl2br(e($html));
        }

        $html = preg_replace('/<\s*(script|iframe|object|embed)[^>]*>.*?<\s*\/\s*\1\s*>/is', '', $html) ?? $html;
        $html = preg_replace('/<\s*(script|iframe|object|embed)[^>]*\/?>/is', '', $html) ?? $html;
        $html = preg_replace('/\s(on[a-z]+)\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/is', '', $html) ?? $html;
        $html = preg_replace('/(href|src)\s*=\s*("|\')\s*javascript:.*?\2/is', '$1="#"', $html) ?? $html;
        $html = strip_tags($html, self::ALLOWED_TAGS);

        return trim($html);
    }

    public function cleanPlainText(string $text): string
    {
        if ($text === '') {
            return '';
        }

        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/--[a-z0-9=_+\-.]{12,}.*/i', '', $text) ?? $text;
        $text = preg_replace('/^(content-type|content-transfer-encoding|content-disposition|mime-version|boundary):.*$/mi', '', $text) ?? $text;
        $text = preg_replace('/\b[A-Za-z0-9+\/]{120,}={0,2}\b/', '', $text) ?? $text;
        $text = preg_replace("/[ \t]+\n/", "\n", $text) ?? $text;
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;

        return trim($text);
    }

    public function plainTextFromHtml(string $html): string
    {
        if ($html === '') {
            return '';
        }

        $html = preg_replace('/<\s*(style|script)[^>]*>.*?<\s*\/\s*\1\s*>/is', ' ', $html) ?? $html;
        $text = strip_tags($html);

        return Str::of(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'))->squish()->toString();
    }

    public function preview(?string $htmlBody = null, ?string $plainBody = null, int $limit = 160): string
    {
        $rendered = $this->render($htmlBody, $plainBody);

        return Str::of($rendered['text'] ?: '-')->squish()->limit($limit)->toString();
    }

    private function containsHtml(string $html): bool
    {
        return preg_match('/<\s*\/?\s*(html|body|head|style|table|thead|tbody|tfoot|tr|td|th|div|span|img|a|p|br|ul|ol|li|strong|b|em|i|u|blockquote|center|font|hr|h[1-6])\b/i', $html) === 1
            || preg_match('/<\s*[a-z][a-z0-9:-]*\s+[^>]*>/i', $html) === 1;
    }
}
