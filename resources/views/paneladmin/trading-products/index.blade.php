@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <div>
          <h6>Trading Products</h6>
          <p class="text-sm mb-0">Kelola katalog produk trading yang tampil di website.</p>
        </div>
        @if(auth()->user()?->canAccess('trading-products.create'))
          <a href="{{ route('paneladmin.trading-products.create') }}" class="btn bg-gradient-primary mb-0">Tambah Produk</a>
        @endif
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Produk</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kategori</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Urutan</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($tradingProducts as $tradingProduct)
                <tr>
                  <td>
                    <div class="d-flex px-2 py-1 align-items-center">
                      <img src="{{ $tradingProduct->imageUrl() }}" class="avatar avatar-lg me-3" alt="{{ $tradingProduct->name }}">
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">{{ $tradingProduct->name }}</h6>
                        <p class="text-xs text-secondary mb-0">{{ $tradingProduct->slug }}</p>
                      </div>
                    </div>
                  </td>
                  <td><span class="text-xs font-weight-bold">{{ $tradingProduct->category }}</span></td>
                  <td class="align-middle text-center text-sm">
                    <span class="badge badge-sm {{ $tradingProduct->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                      {{ $tradingProduct->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                  </td>
                  <td class="align-middle text-center"><span class="text-secondary text-xs font-weight-bold">{{ $tradingProduct->sort_order }}</span></td>
                  <td class="align-middle">
                    @if(auth()->user()?->canAccess('trading-products.view'))
                      <a href="{{ route('paneladmin.trading-products.show', $tradingProduct) }}" class="text-secondary font-weight-bold text-xs me-3">Lihat</a>
                    @endif
                    @if(auth()->user()?->canAccess('trading-products.update'))
                      <a href="{{ route('paneladmin.trading-products.edit', $tradingProduct) }}" class="text-secondary font-weight-bold text-xs me-3">Edit</a>
                    @endif
                    @if(auth()->user()?->canAccess('trading-products.delete'))
                      <form method="POST" action="{{ route('paneladmin.trading-products.destroy', $tradingProduct) }}" class="d-inline js-confirm-delete">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">Hapus</button>
                      </form>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-4 text-sm text-secondary">Belum ada trading product.</td>
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
