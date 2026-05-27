@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <div>
          <h6>Blog</h6>
          <p class="text-sm mb-0">Kelola artikel dan informasi yang tampil di website.</p>
        </div>
        <a href="{{ route('paneladmin.blogs.create') }}" class="btn bg-gradient-primary mb-0">Tambah Artikel</a>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Artikel</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kategori</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Publish</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Urutan</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($blogs as $blog)
                <tr>
                  <td>
                    <div class="d-flex px-2 py-1">
                      <img src="{{ $blog->featuredImageUrl() }}" class="avatar avatar-lg me-3" alt="{{ $blog->title }}">
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">{{ $blog->title }}</h6>
                        <p class="text-xs text-secondary mb-0">{{ \Illuminate\Support\Str::limit($blog->excerpt ?: '-', 60) }}</p>
                      </div>
                    </div>
                  </td>
                  <td><p class="text-xs font-weight-bold mb-0">{{ $blog->category }}</p></td>
                  <td><p class="text-xs text-secondary mb-0">{{ $blog->displayDate() ?: '-' }}</p></td>
                  <td class="align-middle text-center text-sm">
                    <span class="badge badge-sm {{ $blog->is_published ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                      {{ $blog->is_published ? 'Terbit' : 'Draft' }}
                    </span>
                  </td>
                  <td class="align-middle text-center">
                    <span class="text-secondary text-xs font-weight-bold">{{ $blog->sort_order }}</span>
                  </td>
                  <td class="align-middle">
                    <a href="{{ route('paneladmin.blogs.edit', $blog) }}" class="text-secondary font-weight-bold text-xs me-3">Edit</a>
                    <form method="POST" action="{{ route('paneladmin.blogs.destroy', $blog) }}" class="d-inline js-confirm-delete">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-4 text-sm text-secondary">Belum ada artikel.</td>
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
