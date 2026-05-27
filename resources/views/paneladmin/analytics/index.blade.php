@extends('layouts.user_type.auth')

@section('content')
<div class="row mb-4">
  <div class="col-lg-7">
    <h5 class="mb-1">Analytics Visitor</h5>
    <p class="text-sm text-secondary mb-0">Statistik kunjungan halaman publik website Bina Persada JS.</p>
  </div>
  <div class="col-lg-5 mt-3 mt-lg-0">
    <form method="GET" action="{{ route('paneladmin.analytics.index') }}" class="row g-2 justify-content-end">
      <div class="col-md-4">
        <select name="period" class="form-control" onchange="this.form.submit()">
          <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Hari ini</option>
          <option value="7" {{ $period === '7' ? 'selected' : '' }}>7 hari</option>
          <option value="30" {{ $period === '30' ? 'selected' : '' }}>30 hari</option>
          <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Custom date</option>
        </select>
      </div>
      <div class="col-md-4">
        <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}" aria-label="Dari tanggal">
      </div>
      <div class="col-md-4">
        <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}" aria-label="Sampai tanggal">
      </div>
      <div class="col-auto">
        <button type="submit" class="btn bg-gradient-primary btn-sm mb-0">Terapkan</button>
      </div>
    </form>
  </div>
</div>

@php
  $summaryCards = [
    ['label' => 'Total Visitor', 'value' => $summary['total_visitors'], 'icon' => 'fa-users', 'color' => 'bg-gradient-primary'],
    ['label' => 'Unique Visitor', 'value' => $summary['unique_visitors'], 'icon' => 'fa-user-check', 'color' => 'bg-gradient-success'],
    ['label' => 'Page Views', 'value' => $summary['page_views'], 'icon' => 'fa-eye', 'color' => 'bg-gradient-info'],
    ['label' => 'Visitor Hari Ini', 'value' => $summary['today_visitors'], 'icon' => 'fa-calendar-day', 'color' => 'bg-gradient-dark'],
    ['label' => 'Visitor 7 Hari', 'value' => $summary['seven_day_visitors'], 'icon' => 'fa-calendar-week', 'color' => 'bg-gradient-warning'],
    ['label' => 'Visitor 30 Hari', 'value' => $summary['thirty_day_visitors'], 'icon' => 'fa-chart-line', 'color' => 'bg-gradient-secondary'],
  ];
@endphp
<div class="row">
  @foreach($summaryCards as $card)
    <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
      <div class="card h-100">
        <div class="card-body p-3">
          <div class="d-flex justify-content-between">
            <div>
              <p class="text-xs font-weight-bold text-secondary mb-1">{{ $card['label'] }}</p>
              <h5 class="font-weight-bolder mb-0">{{ number_format($card['value']) }}</h5>
            </div>
            <div class="icon icon-shape {{ $card['color'] }} shadow text-center border-radius-md">
              <i class="fas {{ $card['icon'] }} text-white opacity-10" aria-hidden="true"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endforeach
</div>

<div class="row mb-4">
  <div class="col-lg-5 mb-4 mb-lg-0">
    <div class="card h-100">
      <div class="card-header pb-0">
        <h6>Visitor 7 Hari Terakhir</h6>
      </div>
      <div class="card-body">
        <canvas id="analyticsChart7" height="220" data-chart="visitor-7-days"></canvas>
      </div>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="card h-100">
      <div class="card-header pb-0">
        <h6>Visitor 30 Hari Terakhir</h6>
      </div>
      <div class="card-body">
        <canvas id="analyticsChart30" height="220" data-chart="visitor-30-days"></canvas>
      </div>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-lg-5 mb-4 mb-lg-0">
    <div class="card h-100">
      <div class="card-header pb-0">
        <h6>Halaman Teratas</h6>
        <p class="text-xs text-secondary mb-0">Periode {{ $from->format('d M Y') }} - {{ $to->format('d M Y') }}</p>
      </div>
      <div class="card-body px-0 pb-2">
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Halaman</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Views</th>
              </tr>
            </thead>
            <tbody>
              @forelse($topPages as $page)
                <tr>
                  <td class="px-4">
                    <p class="text-sm font-weight-bold mb-0">{{ $page->page_title ?: $page->path }}</p>
                    <p class="text-xs text-secondary mb-0">{{ $page->path }}</p>
                  </td>
                  <td class="text-center text-sm font-weight-bold">{{ number_format($page->views) }}</td>
                </tr>
              @empty
                <tr><td colspan="2" class="text-center py-4 text-sm text-secondary">Belum ada data halaman.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3 mb-4 mb-lg-0">
    <div class="card h-100">
      <div class="card-header pb-0"><h6>Perangkat</h6></div>
      <div class="card-body pt-3">
        @forelse($deviceBreakdown as $device => $stats)
          <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
              <span class="text-sm text-capitalize">{{ $device ?: 'Unknown' }}</span>
              <span class="text-sm font-weight-bold">{{ $stats['percentage'] }}%</span>
            </div>
            <div class="progress"><div class="progress-bar bg-gradient-success" style="width: {{ $stats['percentage'] }}%"></div></div>
          </div>
        @empty
          <p class="text-sm text-secondary">Belum ada data perangkat.</p>
        @endforelse
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header pb-0"><h6>Browser & Sumber Trafik</h6></div>
      <div class="card-body pt-3">
        <h6 class="text-uppercase text-xs text-secondary">Browser</h6>
        @forelse($browserBreakdown as $browser => $stats)
          <div class="d-flex justify-content-between py-1">
            <span class="text-sm">{{ $browser ?: 'Unknown' }}</span>
            <span class="text-sm font-weight-bold">{{ $stats['count'] }} <span class="text-secondary">({{ $stats['percentage'] }}%)</span></span>
          </div>
        @empty
          <p class="text-sm text-secondary">Belum ada data browser.</p>
        @endforelse
        <hr class="horizontal dark my-3">
        <h6 class="text-uppercase text-xs text-secondary">Referer</h6>
        @forelse($topReferers as $referer => $views)
          <div class="d-flex justify-content-between py-1">
            <span class="text-sm">{{ $referer }}</span>
            <span class="text-sm font-weight-bold">{{ $views }}</span>
          </div>
        @empty
          <p class="text-sm text-secondary">Belum ada sumber trafik.</p>
        @endforelse
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header pb-0">
        <h6>Visitor Terbaru</h6>
        <p class="text-sm text-secondary mb-0">IP address dimasking untuk tampilan admin.</p>
      </div>
      <div class="card-body px-0 pb-2">
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Waktu</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Halaman</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Browser / Device</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Referer</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">IP</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentVisitors as $visit)
                <tr>
                  <td class="px-4 text-sm">{{ $visit->visited_at->format('d M Y H:i') }}</td>
                  <td>
                    <p class="text-sm font-weight-bold mb-0">{{ $visit->page_title ?: $visit->path }}</p>
                    <p class="text-xs text-secondary mb-0">{{ $visit->path }}</p>
                  </td>
                  <td class="text-sm">{{ $visit->browser ?: 'Unknown' }} / <span class="text-capitalize">{{ $visit->device_type ?: 'Unknown' }}</span></td>
                  <td class="text-sm">{{ $visit->refererLabel() }}</td>
                  <td class="text-sm" data-masked-ip="{{ $visit->maskedIp() }}">{{ $visit->maskedIp() }}</td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center py-5 text-sm text-secondary">Belum ada kunjungan pada periode ini.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    function makeChart(id, data) {
      var element = document.getElementById(id);
      if (!element || typeof Chart === 'undefined') return;
      new Chart(element.getContext('2d'), {
        type: 'line',
        data: {
          labels: data.labels,
          datasets: [
            { label: 'Visitor', data: data.visitors, borderColor: '#1f8f5f', backgroundColor: 'rgba(31,143,95,.12)', fill: true, tension: .35 },
            { label: 'Page Views', data: data.views, borderColor: '#0c1e35', backgroundColor: 'transparent', tension: .35 }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { position: 'bottom' } },
          scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
      });
    }
    makeChart('analyticsChart7', @json($chart7));
    makeChart('analyticsChart30', @json($chart30));
  });
</script>
@endpush
