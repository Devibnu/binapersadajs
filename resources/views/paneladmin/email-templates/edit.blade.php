@extends('layouts.user_type.auth')

@section('content')
@php
  $previewBody = '<h2 style="margin:0 0 14px;">Preview Email Template</h2><p style="margin:0 0 12px;">Ini adalah contoh isi pesan yang akan dibungkus oleh header dan footer global.</p><p style="margin:0;">Tombol, warna, logo, dan informasi perusahaan mengikuti pengaturan template.</p>';
@endphp

<style>
  .email-template-preview {
    width: 100%;
    max-width: 760px;
    margin: 0 auto;
    transition: max-width .25s ease;
  }

  .email-template-preview.mobile {
    max-width: 390px;
  }

  .email-token {
    display: inline-block;
    background: #f1f4f8;
    border-radius: 8px;
    color: #344767;
    font-size: 12px;
    font-weight: 600;
    margin: 0 6px 6px 0;
    padding: 6px 10px;
  }
</style>

<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div>
          <h6>Email Templates</h6>
          <p class="text-sm mb-0">Kelola branding dan tampilan global email sistem.</p>
        </div>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-outline-primary btn-sm mb-0 js-preview-size" data-size="desktop">Preview Desktop</button>
          <button type="button" class="btn btn-outline-secondary btn-sm mb-0 js-preview-size" data-size="mobile">Preview Mobile</button>
        </div>
      </div>
      <div class="card-body">
        @if(session('success'))
          <div class="alert alert-success text-white">{{ session('success') }}</div>
        @endif
        @if(session('error'))
          <div class="alert alert-danger text-white">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('paneladmin.email-templates.update') }}" enctype="multipart/form-data" class="js-confirm-submit">
          @csrf
          @method('PUT')

          <h6 class="text-uppercase text-xs text-secondary font-weight-bolder mb-3">Branding</h6>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Nama Perusahaan</label>
                <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', $template->company_name) }}" required>
                @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Email Pengirim</label>
                <input type="email" name="sender_email" class="form-control @error('sender_email') is-invalid @enderror" value="{{ old('sender_email', $template->sender_email) }}" required>
                @error('sender_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Nama Pengirim</label>
                <input type="text" name="sender_name" class="form-control @error('sender_name') is-invalid @enderror" value="{{ old('sender_name', $template->sender_name) }}" required>
                @error('sender_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Website</label>
                <input type="url" name="website" class="form-control @error('website') is-invalid @enderror" value="{{ old('website', $template->website) }}" placeholder="https://binapersadajs.co.id">
                @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Telepon</label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $template->phone) }}">
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>WhatsApp</label>
                <input type="text" name="whatsapp" class="form-control @error('whatsapp') is-invalid @enderror" value="{{ old('whatsapp', $template->whatsapp) }}">
                @error('whatsapp')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Alamat Perusahaan</label>
                <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $template->address) }}</textarea>
                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          <hr class="horizontal dark">
          <h6 class="text-uppercase text-xs text-secondary font-weight-bolder mb-3">Tampilan</h6>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Logo Email</label>
                <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp,.svg">
                @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @if($template->logoUrl())<small class="text-secondary">Logo aktif: {{ basename($template->logo) }}</small>@endif
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Background Header</label>
                <input type="file" name="header_background" class="form-control @error('header_background') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp">
                @error('header_background')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Background Footer</label>
                <input type="file" name="footer_background" class="form-control @error('footer_background') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp">
                @error('footer_background')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            @foreach([
              'header_color' => 'Warna Header',
              'footer_color' => 'Warna Footer',
              'button_color' => 'Warna Tombol',
              'text_color' => 'Warna Text',
            ] as $field => $label)
              <div class="col-md-3 col-6">
                <div class="form-group">
                  <label>{{ $label }}</label>
                  <input type="color" name="{{ $field }}" class="form-control form-control-color w-100 @error($field) is-invalid @enderror" value="{{ old($field, $template->{$field}) }}">
                  @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
            @endforeach
          </div>

          <hr class="horizontal dark">
          <div class="mb-3">
            <h6 class="text-uppercase text-xs text-secondary font-weight-bolder mb-2">Token Template</h6>
            @foreach(['{{logo}}', '{{company_name}}', '{{address}}', '{{phone}}', '{{whatsapp}}', '{{email}}', '{{website}}'] as $token)
              <span class="email-token">{{ $token }}</span>
            @endforeach
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Header Template HTML</label>
                <textarea name="header_html" rows="8" class="form-control font-monospace @error('header_html') is-invalid @enderror">{{ old('header_html', $template->header_html) }}</textarea>
                @error('header_html')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Footer Template HTML</label>
                <textarea name="footer_html" rows="8" class="form-control font-monospace @error('footer_html') is-invalid @enderror">{{ old('footer_html', $template->footer_html) }}</textarea>
                @error('footer_html')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Disclaimer HTML</label>
                <textarea name="disclaimer_html" rows="4" class="form-control font-monospace @error('disclaimer_html') is-invalid @enderror">{{ old('disclaimer_html', $template->disclaimer_html) }}</textarea>
                @error('disclaimer_html')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn bg-gradient-primary mb-0">Simpan Email Template</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>Preview Email</h6>
        <p class="text-sm mb-0">Preview mengikuti data terbaru pada form. Simpan template untuk menerapkan ke email sistem.</p>
      </div>
      <div class="card-body bg-gray-100">
        <div class="email-template-preview" id="emailTemplatePreview">
          <div style="margin:0; padding:0; background:#f3f5f7; font-family:Arial, sans-serif;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f5f7; padding:28px 12px;">
              <tr>
                <td align="center">
                  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px; background:#ffffff; border:1px solid #e5e9ed; border-radius:10px; overflow:hidden;">
                    <tr>
                      <td id="previewHeader" style="padding:26px 30px; color:#ffffff;"></td>
                    </tr>
                    <tr>
                      <td id="previewBody" style="padding:32px 30px; font-size:15px; line-height:1.8;"></td>
                    </tr>
                    <tr>
                      <td id="previewFooter" style="padding:24px 30px; color:#ffffff; font-size:13px; line-height:1.7;"></td>
                    </tr>
                    <tr id="previewDisclaimerRow">
                      <td id="previewDisclaimer" style="padding:16px 30px; background:#f8f9fb; color:#66727e; font-size:12px; line-height:1.6;"></td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>Kirim Test Email</h6>
        <p class="text-sm mb-0">Menggunakan SMTP aktif dari Email Settings.</p>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('paneladmin.email-templates.test') }}" class="js-confirm-submit">
          @csrf
          <div class="form-group">
            <label>Email Tujuan</label>
            <input type="email" name="test_email" class="form-control @error('test_email') is-invalid @enderror" value="{{ old('test_email') }}" required>
            @error('test_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label>Subject</label>
            <input type="text" name="test_subject" class="form-control @error('test_subject') is-invalid @enderror" value="{{ old('test_subject', 'Test Email Template BPJS') }}" required>
            @error('test_subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label>Isi Pesan</label>
            <textarea name="test_body" rows="6" class="form-control @error('test_body') is-invalid @enderror" required>{{ old('test_body', 'Halo, ini adalah test email dari Email Template Management.') }}</textarea>
            @error('test_body')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <button type="submit" class="btn bg-gradient-info w-100 mb-0">Kirim Test Email</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('dashboard')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const preview = document.getElementById('emailTemplatePreview');
    const form = document.querySelector('form[action="{{ route('paneladmin.email-templates.update') }}"]');
    const previewHeader = document.getElementById('previewHeader');
    const previewBody = document.getElementById('previewBody');
    const previewFooter = document.getElementById('previewFooter');
    const previewDisclaimer = document.getElementById('previewDisclaimer');
    const previewDisclaimerRow = document.getElementById('previewDisclaimerRow');
    const storedImages = {
      logo: @json($template->logoUrl()),
      header_background: @json($template->headerBackgroundUrl()),
      footer_background: @json($template->footerBackgroundUrl()),
    };
    const liveImages = Object.assign({}, storedImages);
    const previewBodyHtml = @json($previewBody);

    function field(name) {
      return form ? form.querySelector('[name="' + name + '"]') : null;
    }

    function value(name, fallback = '') {
      const input = field(name);
      return input ? input.value : fallback;
    }

    function escapeHtml(text) {
      return String(text || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    function nl2br(text) {
      return escapeHtml(text).replace(/\n/g, '<br>');
    }

    function logoHtml() {
      if (!liveImages.logo) {
        return '<div style="font-size:20px;font-weight:bold;">' + escapeHtml(value('company_name') || 'PT. Bina Persada Jaya Sejahtera') + '</div>';
      }

      return '<img src="' + liveImages.logo + '" alt="' + escapeHtml(value('company_name')) + '" style="display:block;max-height:64px;max-width:220px;">';
    }

    function renderTokens(html) {
      html = String(html || '');

      const tokens = {
        logo: logoHtml(),
        company_name: escapeHtml(value('company_name') || 'PT. Bina Persada Jaya Sejahtera'),
        sender_name: escapeHtml(value('sender_name')),
        address: nl2br(value('address')),
        phone: escapeHtml(value('phone') || '-'),
        whatsapp: escapeHtml(value('whatsapp') || '-'),
        email: escapeHtml(value('sender_email')),
        website: escapeHtml(value('website')),
      };

      Object.keys(tokens).forEach(function (key) {
        html = html.replace(new RegExp('\\{\\{\\s*' + key + '\\s*\\}\\}', 'g'), tokens[key]);
        html = html.replace(new RegExp('&lcub;&lcub;\\s*' + key + '\\s*&rcub;&rcub;', 'gi'), tokens[key]);
        html = html.replace(new RegExp('&#123;&#123;\\s*' + key + '\\s*&#125;&#125;', 'gi'), tokens[key]);
      });

      return html;
    }

    function removeRawTokens(html) {
      return String(html || '')
        .replace(/\{\{\s*(logo|company_name|sender_name|address|phone|whatsapp|email|website)\s*\}\}/g, '')
        .replace(/&lcub;&lcub;\s*(logo|company_name|sender_name|address|phone|whatsapp|email|website)\s*&rcub;&rcub;/gi, '')
        .replace(/&#123;&#123;\s*(logo|company_name|sender_name|address|phone|whatsapp|email|website)\s*&#125;&#125;/gi, '');
    }

    function applyButtonColor() {
      if (!preview) return;
      preview.querySelectorAll('a, button, .btn').forEach(function (element) {
        element.style.backgroundColor = value('button_color', '#1f8f5f');
        element.style.borderColor = value('button_color', '#1f8f5f');
        element.style.color = '#ffffff';
      });
    }

    function backgroundStyle(color, imageUrl) {
      let style = 'background-color:' + (color || '#0c1e35') + ';';
      if (imageUrl) {
        style += 'background-image:url(' + imageUrl + ');background-size:cover;background-position:center;';
      }
      return style;
    }

    function renderPreview() {
      if (!form || !previewHeader || !previewBody || !previewFooter || !previewDisclaimer) return;

      previewHeader.style.cssText = backgroundStyle(value('header_color', '#0c1e35'), liveImages.header_background) + 'padding:26px 30px;color:#ffffff;';
      previewFooter.style.cssText = backgroundStyle(value('footer_color', '#0c1e35'), liveImages.footer_background) + 'padding:24px 30px;color:#ffffff;font-size:13px;line-height:1.7;';
      previewBody.style.cssText = 'padding:32px 30px;font-size:15px;line-height:1.8;color:' + value('text_color', '#263544') + ';';

      previewHeader.innerHTML = removeRawTokens(renderTokens(value('header_html')));
      previewBody.innerHTML = previewBodyHtml.replace('<h2 style="margin:0 0 14px;">', '<h2 style="margin:0 0 14px;color:' + value('text_color', '#263544') + ';">');
      previewFooter.innerHTML = removeRawTokens(renderTokens(value('footer_html')));

      const disclaimer = removeRawTokens(renderTokens(value('disclaimer_html')));
      previewDisclaimer.innerHTML = disclaimer;
      previewDisclaimerRow.style.display = disclaimer.trim() === '' ? 'none' : '';
      applyButtonColor();
    }

    function bindFilePreview(name) {
      const input = field(name);
      if (!input) return;
      input.addEventListener('change', function () {
        const file = input.files && input.files[0];
        if (!file) {
          liveImages[name] = storedImages[name];
          renderPreview();
          return;
        }

        const reader = new FileReader();
        reader.onload = function (event) {
          liveImages[name] = event.target.result;
          renderPreview();
        };
        reader.readAsDataURL(file);
      });
    }

    document.querySelectorAll('.js-preview-size').forEach(function (button) {
      button.addEventListener('click', function () {
        if (!preview) return;
        renderPreview();
        preview.classList.toggle('mobile', button.dataset.size === 'mobile');
      });
    });

    if (form) {
      form.querySelectorAll('input[type="text"], input[type="email"], input[type="url"], input[type="color"], textarea').forEach(function (input) {
        input.addEventListener('input', renderPreview);
        input.addEventListener('change', renderPreview);
      });
      ['logo', 'header_background', 'footer_background'].forEach(bindFilePreview);
      renderPreview();
    }
  });
</script>
@endpush
