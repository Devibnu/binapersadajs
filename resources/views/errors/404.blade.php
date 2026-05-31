@extends('layouts.website')

@section('title', '404 - Halaman Tidak Ditemukan')

@push('styles')
<style>
  .error-page-section {
    background: #f7f9fb;
    padding: 100px 0;
  }

  .error-content {
    margin: 0 auto;
    max-width: 720px;
  }

  .error-icon {
    align-items: center;
    background: #ffffff;
    border-radius: 50%;
    box-shadow: 0 18px 45px rgba(12, 30, 53, .12);
    color: #1f8f5f;
    display: inline-flex;
    font-size: 42px;
    height: 96px;
    justify-content: center;
    margin-bottom: 28px;
    width: 96px;
  }

  .error-code {
    color: #0c1e35;
    font-size: 120px;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 14px;
  }

  .error-content h1 {
    color: #0c1e35;
    font-weight: 700;
    margin-bottom: 14px;
  }

  .error-content p {
    color: #6c757d;
    font-size: 16px;
    line-height: 1.8;
    margin-bottom: 30px;
  }

  .error-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center;
  }

  .error-actions .btn {
    min-width: 180px;
  }

  .error-actions .btn-outline-primary {
    border-color: #0c1e35;
    color: #0c1e35;
  }

  .error-actions .btn-outline-primary:hover,
  .error-actions .btn-outline-primary:focus {
    background: #0c1e35;
    color: #ffffff;
  }

  @media (max-width: 767px) {
    .error-page-section {
      padding: 70px 0;
    }

    .error-icon {
      font-size: 32px;
      height: 78px;
      width: 78px;
    }

    .error-code {
      font-size: 82px;
    }

    .error-content h1 {
      font-size: 26px;
    }

    .error-actions .btn {
      width: 100%;
    }
  }
</style>
@endpush

@section('content')
<section class="error-page-section">
  <div class="container">
    <div class="error-content text-center">
      <div class="error-icon" aria-hidden="true">
        <i class="fas fa-search"></i>
      </div>
      <div class="error-code">404</div>
      <h1>Halaman Tidak Ditemukan</h1>
      <p>Maaf, halaman yang Anda cari tidak tersedia atau sudah dipindahkan.</p>
      <div class="error-actions">
        <a href="{{ route('website.home') }}" class="btn btn-primary">Kembali ke Beranda</a>
        <a href="{{ route('website.contact') }}" class="btn btn-outline-primary">Hubungi Kami</a>
      </div>
    </div>
  </div>
</section>
@endsection
