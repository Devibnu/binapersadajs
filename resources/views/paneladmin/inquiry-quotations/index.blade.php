@extends('layouts.user_type.auth')

@section('content')
<style>
    .iq-page { font-size: 13px; }
    .iq-page .page-title { font-size: 22px; font-weight: 700; letter-spacing: 0; }
    .iq-stat-card { border-radius: 12px; }
    .iq-filter-card, .iq-table-card { border-radius: 12px; overflow: hidden; }
    .iq-filter-card .form-control, .iq-filter-card .form-select { min-height: 42px; font-size: 13px; }
    .iq-filter-grid { display: grid; grid-template-columns: minmax(260px, 1.8fr) repeat(3, minmax(150px, 1fr)) auto; gap: 12px; align-items: end; }
    .iq-table-card { box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06) !important; }
    .iq-table { table-layout: auto; min-width: 1800px; }
    .iq-table thead th { position: sticky; top: 0; z-index: 1; background: #f8f9fa; color: #67748e; font-size: 13px; font-weight: 700; white-space: nowrap; padding: 14px 20px; border-bottom: 1px solid #edf0f5; }
    .iq-table tbody td { font-size: 13px; vertical-align: middle; padding: 14px 20px; height: 56px; border-bottom: 1px solid #edf0f5; }
    .iq-table tbody tr:nth-child(even) { background: #fbfdff; }
    .iq-table tbody tr:hover { background-color: #f8fafc; }
    .iq-table th:nth-child(1), .iq-table td:nth-child(1) { width: 80px; }
    .iq-table th:nth-child(2), .iq-table td:nth-child(2) { width: 140px; }
    .iq-table th:nth-child(3), .iq-table td:nth-child(3) { width: 120px; }
    .iq-table th:nth-child(4), .iq-table td:nth-child(4) { width: 220px; }
    .iq-table th:nth-child(5), .iq-table td:nth-child(5) { width: 220px; max-width: 220px; }
    .iq-table th:nth-child(6), .iq-table td:nth-child(6) { width: 120px; }
    .iq-table th:nth-child(7), .iq-table td:nth-child(7) { width: 140px; }
    .iq-table th:nth-child(8), .iq-table td:nth-child(8) { width: 260px; max-width: 260px; }
    .iq-table th:nth-child(9), .iq-table td:nth-child(9) { width: 160px; }
    .iq-table th:nth-child(10), .iq-table td:nth-child(10) { width: 120px; }
    .iq-table th:nth-child(11), .iq-table td:nth-child(11) { width: 170px; }
    .iq-table th:nth-child(12), .iq-table td:nth-child(12) { width: 140px; }
    .iq-client-logo-thumb { width: 40px; height: 40px; border-radius: 999px; object-fit: cover; border: 1px solid #edf0f5; background: #fff; }
    .iq-client-logo-placeholder { width: 40px; height: 40px; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; background: #f1f5f9; color: #94a3b8; }
    .iq-ellipsis { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .iq-table .badge, .iq-mobile-card .badge { border-radius: 999px; font-size: 11px; line-height: 1; padding: 0.35rem 0.55rem; min-height: 22px; display: inline-flex; align-items: center; }
    .iq-actions { display: flex; gap: 8px; justify-content: center; align-items: center; }
    .iq-action-btn { width: 34px; height: 34px; border-radius: 999px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
    .iq-action-btn i { font-size: 12px; margin: 0; }
    .iq-actions form { margin: 0; }
    .iq-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .iq-table-wrap::-webkit-scrollbar { height: 0; }
    .iq-mobile-list { display: none; }
    .iq-mobile-card { border: 1px solid #edf0f5; border-radius: 12px; padding: 16px; background: #fff; box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04); }
    .iq-mobile-card + .iq-mobile-card { margin-top: 12px; }
    .iq-mobile-label { color: #67748e; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 3px; }
    .iq-mobile-value { color: #344767; font-size: 13px; overflow-wrap: anywhere; }
    @media (max-width: 991.98px) {
        .iq-filter-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .iq-filter-grid .iq-search, .iq-filter-grid .iq-filter-actions { grid-column: span 2; }
    }
    @media (max-width: 575.98px) {
        .iq-page .container-fluid { padding-left: 14px; padding-right: 14px; }
        .iq-filter-grid { grid-template-columns: 1fr; }
        .iq-filter-grid .iq-search, .iq-filter-grid .iq-filter-actions { grid-column: auto; }
    }
    @media (max-width: 767.98px) {
        .iq-table-wrap { display: none; }
        .iq-mobile-list { display: block; padding: 14px; background: #f8fafc; }
        .iq-mobile-card .iq-actions { justify-content: flex-start; padding-top: 12px; border-top: 1px solid #edf0f5; margin-top: 12px; }
    }
</style>

<div class="iq-page">
    <div class="container-fluid py-4">
        @php
            $inquiryQuotations = $inquiryQuotations ?? $items ?? collect();
            $totalInquiries = $totalInquiries ?? ($inquiryQuotations->total() ?? $inquiryQuotations->count());
            $totalQuotationValue = $totalQuotationValue ?? 0;
        @endphp

        <div class="row g-3 align-items-center mb-3">
            <div class="col-lg-6">
                <h1 class="page-title mb-1">Inquiry & Quotation</h1>
                <p class="text-muted mb-0">Kelola inquiry, site survey, dan quotation proyek.</p>
            </div>
            <div class="col-lg-6">
                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-lg-end">
                    <div class="card iq-stat-card shadow-sm border-0 flex-fill flex-lg-grow-0">
                        <div class="card-body py-3 px-4">
                            <p class="text-xs text-uppercase text-muted mb-1">Total Inquiry</p>
                            <h5 class="mb-0">{{ number_format($totalInquiries, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                    <div class="card iq-stat-card shadow-sm border-0 flex-fill flex-lg-grow-0">
                        <div class="card-body py-3 px-4">
                            <p class="text-xs text-uppercase text-muted mb-1">Nilai Quotation</p>
                            <h5 class="mb-0">Rp {{ number_format($totalQuotationValue, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                    @if(auth()->user()?->canAccess('inquiry-quotation.create'))
                        <a href="{{ route('paneladmin.inquiry-quotations.create') }}" class="btn btn-sm btn-primary align-self-sm-center mb-0">
                            <i class="fas fa-plus me-1"></i> Tambah Inquiry
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="card iq-filter-card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('paneladmin.inquiry-quotations.index') }}" class="iq-filter-grid">
                    <div class="iq-search">
                        <label class="form-label text-xs font-weight-bold">Search</label>
                        <input type="text" name="q" class="form-control" placeholder="Cari inquiry, quotation, client, subjek" value="{{ request('q') }}">
                    </div>
                    <div>
                        <label class="form-label text-xs font-weight-bold">Inquiry By</label>
                        <select name="inquiry_by" class="form-select">
                            <option value="">Semua</option>
                            @foreach(['email' => 'Email', 'whatsapp' => 'WhatsApp', 'phone' => 'Telepon', 'site_instruction' => 'Instruksi Lokasi', 'meeting' => 'Meeting', 'referral' => 'Referral', 'other' => 'Lainnya'] as $value => $label)
                                <option value="{{ $value }}" @selected(request('inquiry_by') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label text-xs font-weight-bold">Survey Status</label>
                        <select name="site_survey_status" class="form-select">
                            <option value="">Semua</option>
                            <option value="not_required" @selected(request('site_survey_status') === 'not_required')>Tidak Diperlukan</option>
                            <option value="scheduled" @selected(request('site_survey_status') === 'scheduled')>Dijadwalkan</option>
                            <option value="done" @selected(request('site_survey_status') === 'done')>Selesai</option>
                            <option value="cancelled" @selected(request('site_survey_status') === 'cancelled')>Dibatalkan</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label text-xs font-weight-bold">Status</label>
                        <select name="quotation_status" class="form-select">
                            <option value="">Semua</option>
                            @foreach(['draft' => 'Draft','process' => 'Proses','approved' => 'Disetujui','rejected' => 'Ditolak'] as $value => $label)
                                <option value="{{ $value }}" @selected(request('quotation_status') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="iq-filter-actions d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-info mb-0 flex-fill"><i class="fas fa-filter me-1"></i> Filter</button>
                        <a href="{{ route('paneladmin.inquiry-quotations.index') }}" class="btn btn-sm btn-outline-secondary mb-0 flex-fill">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card iq-table-card shadow-sm border-0">
            <div class="iq-table-wrap table-responsive">
                <table class="table iq-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Inquiry No</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Subject</th>
                            <th>Inquiry By</th>
                            <th>Survey Status</th>
                            <th>Quotation Number</th>
                            <th class="text-end">Amount</th>
                            <th>Status</th>
                            <th>Access</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inquiryQuotations as $inquiry)
                            <tr>
                                <td>
                                    @if($inquiry->client_logo)
                                        <img src="{{ $inquiry->clientLogoUrl() }}" alt="Logo {{ $inquiry->client_name }}" class="iq-client-logo-thumb">
                                    @else
                                        <span class="iq-client-logo-placeholder"><i class="fas fa-building"></i></span>
                                    @endif
                                </td>
                                <td class="font-weight-bold text-dark">{{ $inquiry->inquiry_number }}</td>
                                <td>{{ optional($inquiry->inquiry_date)->format('d/m/Y') }}</td>
                                <td>{{ $inquiry->client_name }}</td>
                                <td><span class="iq-ellipsis" title="{{ $inquiry->subject }}">{{ $inquiry->subject }}</span></td>
                                <td>{{ $inquiry->inquiryByLabel() }}</td>
                                <td><span class="badge {{ $inquiry->surveyStatusBadgeClass() }}">{{ $inquiry->surveyStatusLabel() }}</span></td>
                                <td><span class="iq-ellipsis" title="{{ $inquiry->quotation_number ?? '-' }}">{{ $inquiry->quotation_number ?? '-' }}</span></td>
                                <td class="text-end">{{ $inquiry->formattedAmount() }}</td>
                                <td><span class="badge {{ $inquiry->quotationStatusBadgeClass() }}">{{ $inquiry->quotationStatusLabel() }}</span></td>
                                <td>
                                    <span class="badge {{ $inquiry->isPublic() ? 'bg-gradient-info' : 'bg-gradient-secondary' }} mb-1">
                                        {{ $inquiry->isPublic() ? 'PUBLIC' : 'PRIVATE' }}
                                    </span>
                                    @if(! $inquiry->isPublic())
                                        <span class="d-block text-xs text-muted">
                                            {{ $inquiry->iqmUsers->count() }} users
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="iq-actions">
                                        <a href="{{ route('paneladmin.inquiry-quotations.show', $inquiry) }}" class="btn btn-sm btn-info iq-action-btn mb-0" title="Detail" aria-label="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(auth()->user()?->canAccess('inquiry-quotation.edit'))
                                            <a href="{{ route('paneladmin.inquiry-quotations.edit', $inquiry) }}" class="btn btn-sm btn-warning iq-action-btn mb-0" title="Edit" aria-label="Edit">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        @endif
                                        @if(auth()->user()?->canAccess('inquiry-quotation.delete'))
                                            <form action="{{ route('paneladmin.inquiry-quotations.destroy', $inquiry) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus inquiry ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger iq-action-btn mb-0" title="Hapus" aria-label="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted py-4">Tidak ada inquiry yang ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="iq-mobile-list">
                @forelse($inquiryQuotations as $inquiry)
                    <div class="iq-mobile-card">
                        <div class="d-flex justify-content-between gap-3 mb-3">
                            <div class="d-flex align-items-center gap-3">
                                @if($inquiry->client_logo)
                                    <img src="{{ $inquiry->clientLogoUrl() }}" alt="Logo {{ $inquiry->client_name }}" class="iq-client-logo-thumb">
                                @else
                                    <span class="iq-client-logo-placeholder"><i class="fas fa-building"></i></span>
                                @endif
                                <div>
                                    <div class="iq-mobile-label">Inquiry No</div>
                                    <div class="iq-mobile-value font-weight-bold text-dark">{{ $inquiry->inquiry_number }}</div>
                                </div>
                            </div>
                            <span class="badge {{ $inquiry->quotationStatusBadgeClass() }}">{{ $inquiry->quotationStatusLabel() }}</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="iq-mobile-label">Client</div>
                                <div class="iq-mobile-value">{{ $inquiry->client_name }}</div>
                            </div>
                            <div class="col-12">
                                <div class="iq-mobile-label">Subject</div>
                                <div class="iq-mobile-value" title="{{ $inquiry->subject }}">{{ $inquiry->subject }}</div>
                            </div>
                            <div class="col-6">
                                <div class="iq-mobile-label">Amount</div>
                                <div class="iq-mobile-value font-weight-bold">{{ $inquiry->formattedAmount() }}</div>
                            </div>
                            <div class="col-6">
                                <div class="iq-mobile-label">Status</div>
                                <span class="badge {{ $inquiry->quotationStatusBadgeClass() }}">{{ $inquiry->quotationStatusLabel() }}</span>
                            </div>
                            <div class="col-12">
                                <div class="iq-mobile-label">Access</div>
                                <div class="iq-mobile-value">
                                    @if($inquiry->isPublic())
                                        PUBLIC
                                    @else
                                        PRIVATE: {{ $inquiry->iqmUsers->count() }} users
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="iq-actions">
                            <a href="{{ route('paneladmin.inquiry-quotations.show', $inquiry) }}" class="btn btn-sm btn-info iq-action-btn mb-0" title="Detail" aria-label="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if(auth()->user()?->canAccess('inquiry-quotation.edit'))
                                <a href="{{ route('paneladmin.inquiry-quotations.edit', $inquiry) }}" class="btn btn-sm btn-warning iq-action-btn mb-0" title="Edit" aria-label="Edit">
                                    <i class="fas fa-pen"></i>
                                </a>
                            @endif
                            @if(auth()->user()?->canAccess('inquiry-quotation.delete'))
                                <form action="{{ route('paneladmin.inquiry-quotations.destroy', $inquiry) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus inquiry ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger iq-action-btn mb-0" title="Hapus" aria-label="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">Tidak ada inquiry yang ditemukan.</div>
                @endforelse
            </div>
            <div class="card-footer d-flex flex-column flex-md-row gap-2 justify-content-between align-items-md-center">
                <span class="text-muted">Total: {{ $inquiryQuotations->total() ?? 0 }} inquiry</span>
                {{ $inquiryQuotations->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
