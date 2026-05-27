@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>Komentar Blog</h6>
        <p class="text-sm mb-3">Moderasi komentar pembaca sebelum tampil di website.</p>
        <div class="d-flex flex-wrap gap-2">
          <a href="{{ route('paneladmin.blog-comments.index') }}" class="btn btn-sm {{ $status === null ? 'bg-gradient-primary text-white' : 'btn-outline-secondary' }} mb-0">
            Semua
          </a>
          <a href="{{ route('paneladmin.blog-comments.index', ['status' => 'pending']) }}" class="btn btn-sm {{ $status === 'pending' ? 'bg-gradient-primary text-white' : 'btn-outline-secondary' }} mb-0">
            Menunggu ({{ $counts['pending'] ?? 0 }})
          </a>
          <a href="{{ route('paneladmin.blog-comments.index', ['status' => 'approved']) }}" class="btn btn-sm {{ $status === 'approved' ? 'bg-gradient-primary text-white' : 'btn-outline-secondary' }} mb-0">
            Disetujui ({{ $counts['approved'] ?? 0 }})
          </a>
          <a href="{{ route('paneladmin.blog-comments.index', ['status' => 'rejected']) }}" class="btn btn-sm {{ $status === 'rejected' ? 'bg-gradient-primary text-white' : 'btn-outline-secondary' }} mb-0">
            Ditolak ({{ $counts['rejected'] ?? 0 }})
          </a>
        </div>
      </div>
      <div class="card-body px-0 pt-3 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Artikel</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Pengirim</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Komentar</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tanggal</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($comments as $comment)
                <tr>
                  <td class="px-4">
                    <h6 class="mb-0 text-sm">{{ $comment->blog?->title ?? '-' }}</h6>
                  </td>
                  <td>
                    <p class="text-xs font-weight-bold mb-0">{{ $comment->name }}</p>
                    <p class="text-xs text-secondary mb-0">{{ $comment->email }}</p>
                  </td>
                  <td>
                    <p class="text-xs text-secondary mb-0">{{ \Illuminate\Support\Str::limit($comment->comment, 64) }}</p>
                  </td>
                  <td class="align-middle text-center text-sm">
                    <span class="badge badge-sm {{ $comment->statusBadgeClass() }}">{{ $comment->statusLabel() }}</span>
                  </td>
                  <td><p class="text-xs text-secondary mb-0">{{ $comment->created_at->format('d/m/Y H:i') }}</p></td>
                  <td class="align-middle text-nowrap">
                    <a href="{{ route('paneladmin.blog-comments.show', $comment) }}" class="text-secondary font-weight-bold text-xs me-3">Lihat</a>
                    <a href="{{ route('paneladmin.blog-comments.show', $comment) }}#balas-komentar" class="text-primary font-weight-bold text-xs me-3">Balas</a>
                    @if($comment->status !== 'approved')
                      <form method="POST" action="{{ route('paneladmin.blog-comments.approve', $comment) }}" class="d-inline js-confirm-submit">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-link text-success font-weight-bold text-xs p-0 me-3 mb-0">Setujui</button>
                      </form>
                    @endif
                    @if($comment->status !== 'rejected')
                      <form method="POST" action="{{ route('paneladmin.blog-comments.reject', $comment) }}" class="d-inline js-confirm-submit">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-link text-warning font-weight-bold text-xs p-0 me-3 mb-0">Tolak</button>
                      </form>
                    @endif
                    <form method="POST" action="{{ route('paneladmin.blog-comments.destroy', $comment) }}" class="d-inline js-confirm-delete">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-4 text-sm text-secondary">Belum ada komentar pada status ini.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
