@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6>Detail Komentar</h6>
        <span class="badge badge-sm {{ $blogComment->statusBadgeClass() }}">{{ $blogComment->statusLabel() }}</span>
      </div>
      <div class="card-body">
        <p class="text-xs text-uppercase text-secondary font-weight-bolder mb-1">Artikel</p>
        <p class="text-sm font-weight-bold">{{ $blogComment->blog?->title ?? '-' }}</p>
        <div class="row mb-3">
          <div class="col-md-6">
            <p class="text-xs text-uppercase text-secondary font-weight-bolder mb-1">Nama</p>
            <p class="text-sm">{{ $blogComment->name }}</p>
          </div>
          <div class="col-md-6">
            <p class="text-xs text-uppercase text-secondary font-weight-bolder mb-1">Email</p>
            <p class="text-sm">{{ $blogComment->email }}</p>
          </div>
        </div>
        <p class="text-xs text-uppercase text-secondary font-weight-bolder mb-1">Komentar</p>
        <p class="text-sm text-dark border-radius-lg bg-gray-100 p-3">{{ $blogComment->comment }}</p>
        @if($blogComment->replies->isNotEmpty())
          <p class="text-xs text-uppercase text-secondary font-weight-bolder mb-2 mt-4">Balasan Admin</p>
          @foreach($blogComment->replies as $reply)
            <div class="border-radius-lg bg-gray-100 p-3 mb-2 border-start border-success border-3">
              <p class="text-xs font-weight-bold text-success mb-1">Tim Bina Persada</p>
              <p class="text-sm text-dark mb-1">{{ $reply->comment }}</p>
              <p class="text-xs text-secondary mb-0">{{ $reply->created_at->format('d/m/Y H:i') }}</p>
            </div>
          @endforeach
        @endif
        <p class="text-xs text-secondary mb-0">
          Dikirim {{ $blogComment->created_at->format('d/m/Y H:i') }}
          @if($blogComment->ip_address)
            dari IP {{ $blogComment->ip_address }}
          @endif
        </p>
      </div>
      <div class="card-footer pt-0">
        <a href="{{ route('paneladmin.blog-comments.index') }}" class="btn btn-outline-secondary mb-0 me-2">Kembali</a>
        @if($blogComment->status !== 'approved')
          <form method="POST" action="{{ route('paneladmin.blog-comments.approve', $blogComment) }}" class="d-inline js-confirm-submit">
            @csrf
            @method('PATCH')
            <button class="btn bg-gradient-success mb-0 me-2" type="submit">Setujui</button>
          </form>
        @endif
        @if($blogComment->status !== 'rejected')
          <form method="POST" action="{{ route('paneladmin.blog-comments.reject', $blogComment) }}" class="d-inline js-confirm-submit">
            @csrf
            @method('PATCH')
            <button class="btn bg-gradient-warning mb-0 me-2" type="submit">Tolak</button>
          </form>
        @endif
        <form method="POST" action="{{ route('paneladmin.blog-comments.destroy', $blogComment) }}" class="d-inline js-confirm-delete">
          @csrf
          @method('DELETE')
          <button class="btn bg-gradient-danger mb-0" type="submit">Hapus</button>
        </form>
      </div>
    </div>
  </div>
  @if($blogComment->parent_id === null)
    <div class="col-lg-8" id="balas-komentar">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h6>Balas Komentar</h6>
          <p class="text-sm mb-0">Balasan akan langsung tampil di website sebagai Tim Bina Persada.</p>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('paneladmin.blog-comments.reply', $blogComment) }}" class="js-confirm-submit">
            @csrf
            <div class="form-group">
              <label for="admin-reply">Isi Balasan</label>
              <textarea id="admin-reply" name="comment" class="form-control @error('comment') is-invalid @enderror" rows="4" required minlength="5" maxlength="1000">{{ old('comment') }}</textarea>
              @error('comment')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <button class="btn bg-gradient-primary mb-0" type="submit">Kirim Balasan</button>
          </form>
        </div>
      </div>
    </div>
  @endif
</div>
@endsection
