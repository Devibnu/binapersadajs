@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>Leads</h6>
        <p class="text-sm mb-3">Calon customer dari newsletter dan permintaan penawaran website.</p>
        <div class="d-flex flex-wrap gap-2 mb-3">
          <a href="{{ route('paneladmin.leads.index') }}" class="btn btn-sm {{ $status === null ? 'bg-gradient-primary text-white' : 'btn-outline-secondary' }} mb-0">Semua</a>
          @foreach(['new' => 'Baru', 'contacted' => 'Dihubungi', 'qualified' => 'Prospek', 'converted' => 'Konversi', 'closed' => 'Ditutup'] as $key => $label)
            <a href="{{ route('paneladmin.leads.index', ['status' => $key]) }}" class="btn btn-sm {{ $status === $key ? 'bg-gradient-primary text-white' : 'btn-outline-secondary' }} mb-0">
              {{ $label }} ({{ $counts[$key] ?? 0 }})
            </a>
          @endforeach
        </div>
        <form method="GET" action="{{ route('paneladmin.leads.index') }}" class="row g-2 align-items-center">
          <div class="col-md-4">
            <input type="search" name="q" value="{{ $search }}" class="form-control" placeholder="Cari nama, email, telepon, perusahaan">
          </div>
          <div class="col-md-3">
            <select name="status" class="form-control">
              <option value="">Semua Status</option>
              @foreach(['new' => 'Baru', 'contacted' => 'Dihubungi', 'qualified' => 'Prospek', 'converted' => 'Konversi', 'closed' => 'Ditutup'] as $key => $label)
                <option value="{{ $key }}" @selected($status === $key)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <select name="source" class="form-control">
              <option value="">Semua Sumber</option>
              @foreach($sources as $item)
                <option value="{{ $item }}" @selected($source === $item)>{{ (new \App\Models\Lead(['source' => $item]))->sourceLabel() }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-auto">
            <button class="btn bg-gradient-primary mb-0" type="submit">Filter</button>
          </div>
          @if($search !== '' || $status || $source)
            <div class="col-auto">
              <a href="{{ route('paneladmin.leads.index') }}" class="btn btn-outline-secondary mb-0">Reset</a>
            </div>
          @endif
        </form>
      </div>
      <div class="card-body px-0 pt-3 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kontak</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Minat / Sumber</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tanggal</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($leads as $lead)
                <tr>
                  <td class="px-4">
                    <h6 class="mb-0 text-sm">{{ $lead->name ?: 'Subscriber' }}</h6>
                    <p class="text-xs text-secondary mb-0">{{ $lead->company ?: '-' }}</p>
                  </td>
                  <td>
                    <p class="text-xs font-weight-bold mb-0">{{ $lead->email }}</p>
                    <p class="text-xs text-secondary mb-0">{{ $lead->phone ?: '-' }}</p>
                  </td>
                  <td>
                    <p class="text-xs font-weight-bold mb-0">{{ $lead->interest ?: '-' }}</p>
                    <p class="text-xs text-secondary mb-0">{{ $lead->sourceLabel() }}</p>
                  </td>
                  <td class="align-middle text-center text-sm">
                    <span class="badge badge-sm {{ $lead->statusBadgeClass() }}">{{ $lead->statusLabel() }}</span>
                  </td>
                  <td><p class="text-xs text-secondary mb-0">{{ $lead->created_at->format('d/m/Y H:i') }}</p></td>
                  <td class="align-middle text-nowrap">
                    <a href="{{ route('paneladmin.leads.show', $lead) }}" class="text-secondary font-weight-bold text-xs me-3">Lihat</a>
                    @if(auth()->user()->canAccess('leads.email'))
                      <a href="{{ route('paneladmin.email-center.compose', ['to' => $lead->email, 'subject' => 'Follow Up dari PT. Bina Persada Jaya Sejahtera', 'body' => 'Yth. ' . ($lead->name ?: 'Bapak/Ibu') . ',']) }}" class="text-info font-weight-bold text-xs me-3">Kirim Email</a>
                    @endif
                    @if(auth()->user()->canAccess('leads.delete'))
                      <form method="POST" action="{{ route('paneladmin.leads.destroy', $lead) }}" class="d-inline js-confirm-delete">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">Hapus</button>
                      </form>
                    @endif
                  </td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center py-4 text-sm text-secondary">Belum ada lead pada filter ini.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($leads->hasPages())
          <div class="px-4 pt-3">{{ $leads->links() }}</div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
