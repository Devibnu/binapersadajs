@extends('layouts.user_type.auth')

@section('content')
<style>
    .iq-show { font-size: 13px; }
    .iq-show .card { border-radius: 12px; }
    .iq-show .detail-title { font-size: 15px; font-weight: 700; margin: 0; }
    .iq-show .detail-row { display: grid; grid-template-columns: 150px 1fr; gap: 12px; padding: 9px 0; border-bottom: 1px solid #edf0f5; }
    .iq-show .detail-row:last-child { border-bottom: 0; }
    .iq-show .detail-label { color: #67748e; font-weight: 600; }
    .iq-show .detail-value { color: #344767; overflow-wrap: anywhere; }
    .iq-attachment-card { border: 1px solid #edf0f5; border-radius: 12px; padding: 12px; height: 100%; }
    .iq-attachment-preview { height: 150px; border-radius: 10px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .iq-attachment-preview img { width: 100%; height: 100%; object-fit: cover; }
    .iq-client-logo-card { border: 1px solid #edf0f5; border-radius: 12px; background: #f8fafc; padding: 14px; display: inline-flex; align-items: center; justify-content: center; min-height: 120px; min-width: 180px; }
    .iq-client-logo-card img { max-height: 120px; object-fit: contain; }
    @media (max-width: 575.98px) {
        .iq-show .detail-row { grid-template-columns: 1fr; gap: 3px; }
    }
</style>

<div class="container-fluid py-4 iq-show">
    @php
        $entry = $inquiryQuotation ?? $item ?? null;
    @endphp

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-lg-7">
                    <p class="text-xs text-uppercase text-muted mb-1">Inquiry Number</p>
                    <h4 class="font-weight-bold mb-1">{{ $entry?->inquiry_number }}</h4>
                    <p class="text-sm text-muted mb-0">{{ $entry?->subject }}</p>
                </div>
                <div class="col-lg-5">
                    <div class="d-flex flex-column flex-sm-row gap-2 justify-content-lg-end align-items-sm-center">
                        <div class="text-sm">
                            <span class="text-muted me-2">Quotation</span>
                            <strong>{{ $entry?->quotation_number ?? '-' }}</strong>
                        </div>
                        <span class="badge {{ $entry?->quotationStatusBadgeClass() }}">{{ $entry?->quotationStatusLabel() }}</span>
                        @if(auth()->user()?->canAccess('inquiry-quotation.edit'))
                            <a href="{{ route('paneladmin.inquiry-quotations.edit', $entry) }}" class="btn btn-sm btn-warning mb-0"><i class="fas fa-edit me-1"></i> Edit</a>
                        @endif
                        <a href="{{ route('paneladmin.inquiry-quotations.index') }}" class="btn btn-sm btn-secondary mb-0"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                        @if(auth()->user()?->canAccess('inquiry-quotation.delete'))
                            <form action="{{ route('paneladmin.inquiry-quotations.destroy', $entry) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus inquiry ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger mb-0 w-100"><i class="fas fa-trash me-1"></i> Delete</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-transparent border-0 pb-0"><h5 class="detail-title">Inquiry Information</h5></div>
                <div class="card-body">
                    <div class="detail-row"><div class="detail-label">Date</div><div class="detail-value">{{ optional($entry?->inquiry_date)->format('d/m/Y') }}</div></div>
                    <div class="detail-row"><div class="detail-label">Inquiry By</div><div class="detail-value">{{ $entry?->inquiryByLabel() }}</div></div>
                    <div class="detail-row"><div class="detail-label">Subject</div><div class="detail-value">{{ $entry?->subject }}</div></div>
                    <div class="detail-row"><div class="detail-label">Description</div><div class="detail-value">{{ $entry?->description ?? '-' }}</div></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-transparent border-0 pb-0"><h5 class="detail-title">Client Information</h5></div>
                <div class="card-body">
                    <div class="detail-row">
                        <div class="detail-label">Logo</div>
                        <div class="detail-value">
                            @if($entry?->client_logo)
                                <div class="iq-client-logo-card">
                                    <img src="{{ $entry->clientLogoUrl() }}" alt="Logo {{ $entry->client_name }}" class="img-fluid rounded shadow-sm">
                                </div>
                            @else
                                <span class="text-muted">Logo belum diupload</span>
                            @endif
                        </div>
                    </div>
                    <div class="detail-row"><div class="detail-label">Client</div><div class="detail-value">{{ $entry?->client_name }}</div></div>
                    <div class="detail-row"><div class="detail-label">PIC</div><div class="detail-value">{{ $entry?->client_pic ?? '-' }}</div></div>
                    <div class="detail-row"><div class="detail-label">Phone</div><div class="detail-value">{{ $entry?->client_phone ?? '-' }}</div></div>
                    <div class="detail-row"><div class="detail-label">Email</div><div class="detail-value">{{ $entry?->client_email ?? '-' }}</div></div>
                    <div class="detail-row"><div class="detail-label">Address</div><div class="detail-value">{{ $entry?->client_address ?? '-' }}</div></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-transparent border-0 pb-0"><h5 class="detail-title">Survey Information</h5></div>
                <div class="card-body">
                    <div class="detail-row"><div class="detail-label">Status</div><div class="detail-value"><span class="badge {{ $entry?->surveyStatusBadgeClass() }}">{{ $entry?->surveyStatusLabel() }}</span></div></div>
                    <div class="detail-row"><div class="detail-label">Date</div><div class="detail-value">{{ optional($entry?->site_survey_date)->format('d/m/Y') ?? '-' }}</div></div>
                    <div class="detail-row"><div class="detail-label">Notes</div><div class="detail-value">{{ $entry?->site_survey_notes ?? '-' }}</div></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-transparent border-0 pb-0"><h5 class="detail-title">Quotation Information</h5></div>
                <div class="card-body">
                    <div class="detail-row"><div class="detail-label">Number</div><div class="detail-value">{{ $entry?->quotation_number ?? '-' }}</div></div>
                    <div class="detail-row"><div class="detail-label">Date</div><div class="detail-value">{{ optional($entry?->quotation_date)->format('d/m/Y') ?? '-' }}</div></div>
                    <div class="detail-row"><div class="detail-label">Deadline</div><div class="detail-value">{{ optional($entry?->deadline)->format('d/m/Y') ?? '-' }}</div></div>
                    <div class="detail-row"><div class="detail-label">Amount</div><div class="detail-value font-weight-bold">{{ $entry?->formattedAmount() }}</div></div>
                    <div class="detail-row"><div class="detail-label">Status</div><div class="detail-value"><span class="badge {{ $entry?->quotationStatusBadgeClass() }}">{{ $entry?->quotationStatusLabel() }}</span></div></div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-0 pb-0"><h5 class="detail-title">Notes</h5></div>
                <div class="card-body text-sm text-muted">{{ $entry?->notes ?? '-' }}</div>
            </div>
        </div>
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-0 pb-0"><h5 class="detail-title">Portal Access</h5></div>
                <div class="card-body">
                    <div class="detail-row">
                        <div class="detail-label">Visibility</div>
                        <div class="detail-value">
                            <span class="badge {{ $entry?->isPublic() ? 'bg-gradient-info' : 'bg-gradient-secondary' }}">
                                {{ $entry?->isPublic() ? 'PUBLIC' : 'PRIVATE' }}
                            </span>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">User IQM</div>
                        <div class="detail-value">
                            @if($entry?->isPublic())
                                Semua user IQM
                            @elseif($entry?->iqmUsers?->isNotEmpty())
                                <ul class="mb-0 ps-3">
                                    @foreach($entry->iqmUsers as $portalUser)
                                        <li>{{ $portalUser->company_name }} - {{ $portalUser->pic_name }} - {{ $portalUser->email ?: $portalUser->username }}</li>
                                    @endforeach
                                </ul>
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-0 pb-0"><h5 class="detail-title">Attachments</h5></div>
                <div class="card-body">
                    @if($entry?->attachments->isEmpty())
                        <p class="text-muted mb-0">Belum ada attachment.</p>
                    @else
                        <div class="row g-3">
                            @foreach($entry->attachments as $attachment)
                                <div class="col-sm-6 col-lg-4">
                                    <div class="iq-attachment-card">
                                        <div class="iq-attachment-preview mb-3">
                                            @if($attachment->isImage())
                                                <img src="{{ $attachment->fileUrl() }}" alt="{{ $attachment->original_name }}">
                                            @elseif($attachment->isPdf())
                                                <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                            @else
                                                <i class="fas fa-file fa-3x text-secondary"></i>
                                            @endif
                                        </div>
                                        <h6 class="text-sm font-weight-bold mb-1">{{ $attachment->original_name }}</h6>
                                        <p class="text-xs text-muted mb-3">{{ $attachment->formattedSize() }} • {{ strtoupper($attachment->file_type) }}</p>
                                        <a href="{{ $attachment->fileUrl() }}" target="_blank" class="btn btn-sm btn-outline-primary w-100 mb-0">
                                            <i class="fas fa-download me-1"></i> Download
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('paneladmin.partials.portal-conversations', [
        'conversations' => $entry->portalConversations,
        'storeRoute' => route('paneladmin.inquiry-quotations.conversations.store', $entry),
    ])
</div>
@endsection
