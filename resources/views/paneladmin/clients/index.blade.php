@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <div>
          <h6>Klien / Our Clients</h6>
          <p class="text-sm mb-0">Kelola logo dan nama client yang tampil di homepage.</p>
        </div>
        @if(auth()->user()?->canAccess('clients.create'))
          <a href="{{ route('paneladmin.clients.create') }}" class="btn bg-gradient-primary mb-0">Tambah Client</a>
        @endif
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Client</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Website</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Urutan</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($clients as $client)
                <tr>
                  <td>
                    <div class="d-flex px-2 py-1 align-items-center">
                      <div class="avatar avatar-lg bg-white border me-3 d-flex align-items-center justify-content-center">
                        @if($client->logo)
                          <img src="{{ $client->logoUrl() }}" alt="{{ $client->name }}" style="max-height: 44px; max-width: 58px;">
                        @else
                          <i class="fas fa-building text-secondary"></i>
                        @endif
                      </div>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">{{ $client->name }}</h6>
                        <p class="text-xs text-secondary mb-0">{{ $client->logo ? 'Logo tersedia' : 'Tanpa logo' }}</p>
                      </div>
                    </div>
                  </td>
                  <td>
                    @if($client->website_url)
                      <a href="{{ $client->website_url }}" target="_blank" rel="noopener" class="text-xs font-weight-bold">{{ \Illuminate\Support\Str::limit($client->website_url, 40) }}</a>
                    @else
                      <span class="text-xs text-secondary">-</span>
                    @endif
                  </td>
                  <td class="align-middle text-center text-sm">
                    <span class="badge badge-sm {{ $client->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                      {{ $client->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                  </td>
                  <td class="align-middle text-center">
                    <span class="text-secondary text-xs font-weight-bold">{{ $client->sort_order }}</span>
                  </td>
                  <td class="align-middle">
                    @if(auth()->user()?->canAccess('clients.view'))
                      <a href="{{ route('paneladmin.clients.show', $client) }}" class="text-secondary font-weight-bold text-xs me-3">Lihat</a>
                    @endif
                    @if(auth()->user()?->canAccess('clients.update'))
                      <a href="{{ route('paneladmin.clients.edit', $client) }}" class="text-secondary font-weight-bold text-xs me-3">Edit</a>
                    @endif
                    @if(auth()->user()?->canAccess('clients.delete'))
                      <form method="POST" action="{{ route('paneladmin.clients.destroy', $client) }}" class="d-inline js-confirm-delete">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">Hapus</button>
                      </form>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-4 text-sm text-secondary">Belum ada data client.</td>
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
