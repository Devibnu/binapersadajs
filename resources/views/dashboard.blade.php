@extends('layouts.user_type.auth')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <h5 class="mb-1">Ringkasan Website</h5>
            <p class="text-sm text-secondary mb-0">Pantau konten publik dan aktivitas masuk dari satu halaman.</p>
        </div>

        @foreach($summaryCards as $card)
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100" data-summary="{{ $card['key'] }}" data-value="{{ $card['value'] }}">
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <p class="text-sm mb-1 text-capitalize font-weight-bold text-secondary">{{ $card['label'] }}</p>
                                <h5 class="font-weight-bolder mb-0">{{ number_format($card['value']) }}</h5>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape {{ $card['color'] }} shadow text-center border-radius-md">
                                    <i class="fas {{ $card['icon'] }} text-lg opacity-10 text-white" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h6 class="mb-1">Aksi Cepat</h6>
                    <p class="text-sm text-secondary mb-0">Kelola konten utama dan komunikasi website.</p>
                </div>
                <div class="card-body pt-3">
                    <div class="row g-3">
                        @foreach($quickActions as $action)
                            <div class="col-xl-3 col-md-4 col-sm-6">
                                <a href="{{ $action['url'] }}" class="btn btn-outline-dark w-100 mb-0 text-start">
                                    <i class="fas {{ $action['icon'] }} me-2"></i>{{ $action['label'] }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($canViewPortalQuestions)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                            <div>
                                <p class="text-sm text-secondary mb-1">Pertanyaan Belum Dibaca</p>
                                <h6 class="font-weight-bolder mb-0">Pertanyaan Baru: {{ $portalQuestionSummary['total'] }}</h6>
                                <p class="text-xs text-secondary mb-0">Pesan Terbaru: {{ $portalQuestionSummary['latest_at'] ? \Illuminate\Support\Carbon::parse($portalQuestionSummary['latest_at'])->format('d M Y H:i') : '-' }}</p>
                            </div>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                @foreach($portalQuestionSummary['items'] as $item)
                                    <span class="badge bg-gradient-light text-dark">{{ $item['label'] }}: {{ $item['count'] }} pesan baru</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-lg-7 mb-lg-0 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Pesan Kontak Terbaru</h6>
                    <a href="{{ route('paneladmin.contact-messages.index') }}" class="text-sm font-weight-bold">Lihat Semua</a>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Pengirim</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Subjek</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestContactMessages as $message)
                                    <tr>
                                        <td>
                                            <div class="px-3">
                                                <p class="text-sm font-weight-bold mb-0">{{ $message->name }}</p>
                                                <p class="text-xs text-secondary mb-0">{{ $message->email }}</p>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-sm">{{ \Illuminate\Support\Str::limit($message->subject ?: '-', 30) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-sm {{ $message->statusBadgeClass() }}">{{ $message->statusLabel() }}</span>
                                        </td>
                                        <td>
                                            <span class="text-xs text-secondary">{{ $message->created_at->format('d M Y') }}</span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('paneladmin.contact-messages.show', $message) }}" class="text-secondary font-weight-bold text-xs">Lihat</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-sm text-secondary">Belum ada pesan kontak.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Komentar Blog Pending</h6>
                    <a href="{{ route('paneladmin.blog-comments.index', ['status' => 'pending']) }}" class="text-sm font-weight-bold">Lihat Semua</a>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Komentar</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingComments as $comment)
                                    <tr>
                                        <td>
                                            <div class="px-3">
                                                <p class="text-sm font-weight-bold mb-0">{{ $comment->name }}</p>
                                                <p class="text-xs text-secondary mb-1">{{ $comment->blog?->title ?? 'Artikel tidak tersedia' }}</p>
                                                <p class="text-xs mb-0">{{ \Illuminate\Support\Str::limit($comment->comment, 45) }}</p>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-xs text-secondary">{{ $comment->created_at->format('d M Y') }}</span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('paneladmin.blog-comments.show', $comment) }}" class="text-secondary font-weight-bold text-xs d-block mb-2">Lihat</a>
                                            <form method="POST" action="{{ route('paneladmin.blog-comments.approve', $comment) }}" class="js-confirm-submit">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-link text-success font-weight-bold text-xs p-0 mb-0">Setujui</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-sm text-secondary">Belum ada komentar pending.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-lg-0 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Blog Terbaru</h6>
                    <a href="{{ route('paneladmin.blogs.index') }}" class="text-sm font-weight-bold">Kelola Blog</a>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Artikel</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kategori</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestBlogs as $blog)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-3 py-1">
                                                <img src="{{ $blog->featuredImageUrl() }}" class="avatar avatar-sm me-3 border-radius-lg object-fit-cover" alt="{{ $blog->title }}">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <p class="text-sm font-weight-bold mb-0">{{ \Illuminate\Support\Str::limit($blog->title, 40) }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-sm">{{ $blog->category ?: '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-sm {{ $blog->is_published ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                                                {{ $blog->is_published ? 'Terbit' : 'Draft' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-xs text-secondary">{{ optional($blog->published_at ?? $blog->created_at)->format('d M Y') }}</span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('paneladmin.blogs.edit', $blog) }}" class="text-secondary font-weight-bold text-xs">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-sm text-secondary">Belum ada blog terbaru.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h6 class="mb-1">Website Setup Checklist</h6>
                    <p class="text-sm text-secondary mb-0">Kesiapan informasi penting website.</p>
                </div>
                <div class="card-body pt-3">
                    @foreach($setupChecklist as $item)
                        <div class="d-flex justify-content-between align-items-center py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <span class="text-sm font-weight-bold">{{ $item['label'] }}</span>
                            <span class="badge badge-sm {{ $item['badge'] }}">{{ $item['status'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
