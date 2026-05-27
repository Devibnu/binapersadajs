@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>Pesan Kontak</h6>
        <p class="text-sm mb-3">Pesan yang dikirim pengunjung melalui halaman kontak website.</p>
        <div class="d-flex flex-wrap gap-2 mb-3">
          <a href="{{ route('paneladmin.contact-messages.index') }}" class="btn btn-sm {{ $status === null ? 'bg-gradient-primary text-white' : 'btn-outline-secondary' }} mb-0">
            Semua
          </a>
          <a href="{{ route('paneladmin.contact-messages.index', ['status' => 'unread']) }}" class="btn btn-sm {{ $status === 'unread' ? 'bg-gradient-primary text-white' : 'btn-outline-secondary' }} mb-0">
            Belum Dibaca ({{ $counts['unread'] ?? 0 }})
          </a>
          <a href="{{ route('paneladmin.contact-messages.index', ['status' => 'read']) }}" class="btn btn-sm {{ $status === 'read' ? 'bg-gradient-primary text-white' : 'btn-outline-secondary' }} mb-0">
            Dibaca ({{ $counts['read'] ?? 0 }})
          </a>
          <a href="{{ route('paneladmin.contact-messages.index', ['status' => 'replied']) }}" class="btn btn-sm {{ $status === 'replied' ? 'bg-gradient-primary text-white' : 'btn-outline-secondary' }} mb-0">
            Dibalas ({{ $counts['replied'] ?? 0 }})
          </a>
        </div>
        <form method="GET" action="{{ route('paneladmin.contact-messages.index') }}" class="row g-2 align-items-center">
          @if($status)
            <input type="hidden" name="status" value="{{ $status }}">
          @endif
          <div class="col-md-5">
            <input type="search" name="q" value="{{ $search }}" class="form-control" placeholder="Cari nama, email, atau subjek">
          </div>
          <div class="col-auto">
            <button class="btn bg-gradient-primary mb-0" type="submit">Cari</button>
          </div>
          @if($search !== '')
            <div class="col-auto">
              <a href="{{ route('paneladmin.contact-messages.index', array_filter(['status' => $status])) }}" class="btn btn-outline-secondary mb-0">Reset</a>
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
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Subjek</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tanggal</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($messages as $message)
                <tr>
                  <td class="px-4">
                    <h6 class="mb-0 text-sm">{{ $message->name }}</h6>
                  </td>
                  <td>
                    <p class="text-xs font-weight-bold mb-0">{{ $message->email }}</p>
                    <p class="text-xs text-secondary mb-0">{{ $message->phone ?: '-' }}</p>
                  </td>
                  <td>
                    <p class="text-xs font-weight-bold mb-0">{{ $message->subject ?: '-' }}</p>
                    <p class="text-xs text-secondary mb-0">{{ \Illuminate\Support\Str::limit($message->message, 55) }}</p>
                  </td>
                  <td class="align-middle text-center text-sm">
                    <span class="badge badge-sm {{ $message->statusBadgeClass() }}">{{ $message->statusLabel() }}</span>
                  </td>
                  <td><p class="text-xs text-secondary mb-0">{{ $message->created_at->format('d/m/Y H:i') }}</p></td>
                  <td class="align-middle text-nowrap">
                    <a href="{{ route('paneladmin.contact-messages.show', $message) }}" class="text-secondary font-weight-bold text-xs me-3">Lihat</a>
                    @if($message->status === 'unread')
                      <form method="POST" action="{{ route('paneladmin.contact-messages.read', $message) }}" class="d-inline js-confirm-submit">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-link text-info font-weight-bold text-xs p-0 me-3 mb-0">Dibaca</button>
                      </form>
                    @endif
                    @if($message->status !== 'replied')
                      <form method="POST" action="{{ route('paneladmin.contact-messages.replied', $message) }}" class="d-inline js-confirm-submit">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-link text-success font-weight-bold text-xs p-0 me-3 mb-0">Dibalas</button>
                      </form>
                    @endif
                    <form method="POST" action="{{ route('paneladmin.contact-messages.destroy', $message) }}" class="d-inline js-confirm-delete">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-4 text-sm text-secondary">Belum ada pesan kontak pada filter ini.</td>
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
