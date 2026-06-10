<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InquiryQuotationRequest;
use App\Models\InquiryQuotation;
use App\Models\IqmUser;
use App\Models\PortalConversation;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class InquiryQuotationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:inquiry-quotation.view')->only(['index', 'show']);
        $this->middleware('permission:inquiry-quotation.create')->only(['create', 'store']);
        $this->middleware('permission:inquiry-quotation.edit')->only(['edit', 'update']);
        $this->middleware('permission:inquiry-quotation.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = InquiryQuotation::query()->with(['iqmUser', 'iqmUsers']);

        // search across several columns
        if ($request->filled('q')) {
            $q = $request->get('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('inquiry_number', 'like', "%{$q}%")
                    ->orWhere('quotation_number', 'like', "%{$q}%")
                    ->orWhere('client_name', 'like', "%{$q}%")
                    ->orWhere('subject', 'like', "%{$q}%");
            });
        }

        // filters
        foreach (['inquiry_by', 'site_survey_status', 'quotation_status'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->get($filter));
            }
        }

        $perPage = (int) $request->get('per_page', 15);
        $totalInquiries = (clone $query)->count();
        $totalQuotationValue = (clone $query)->sum('amount');
        $inquiryQuotations = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();

        return view('paneladmin.inquiry-quotations.index', compact('inquiryQuotations', 'totalInquiries', 'totalQuotationValue'));
    }

    public function create()
    {
        return view('paneladmin.inquiry-quotations.create', [
            'iqmUsers' => IqmUser::where('status', 'active')->orderBy('company_name')->get(),
        ]);
    }

    public function store(InquiryQuotationRequest $request)
    {
        $data = $request->validated();
        $iqmUserIds = $this->portalUserIds($request);
        unset($data['client_logo'], $data['iqm_user_ids']);
        $data['iqm_user_id'] = $data['visibility'] === 'private' ? ($iqmUserIds[0] ?? null) : null;

        $data['amount'] = $this->normalizeAmount($request->input('amount'));

        if ($request->hasFile('client_logo')) {
            $data['client_logo'] = $this->saveClientLogo($request->file('client_logo'));
        }

        if (empty($data['inquiry_number'])) {
            $data['inquiry_number'] = $this->generateInquiryNumber($data['inquiry_date'] ?? null);
        }

        $inquiry = InquiryQuotation::create($data);
        $inquiry->iqmUsers()->sync($data['visibility'] === 'private' ? $iqmUserIds : []);

        if (empty($inquiry->quotation_number) && ! empty($data['quotation_date']) && ! empty($data['amount']) && ! empty($data['quotation_status'])) {
            $inquiry->quotation_number = $this->generateQuotationNumber($data['quotation_date']);
            $inquiry->save();
        }

        if ($request->hasFile('attachments')) {
            $this->saveAttachments($inquiry, $request->file('attachments'), optional($request->user())->id);
        }

        app(ActivityLogger::class)->log('create', 'Inquiry & Quotation', 'Created inquiry: ' . $inquiry->inquiry_number, $inquiry);

        return redirect()->route('paneladmin.inquiry-quotations.show', $inquiry->id)->with('success', 'Inquiry berhasil dibuat.');
    }

    public function show(InquiryQuotation $inquiryQuotation)
    {
        PortalConversation::forModule(PortalConversation::MODULE_INQUIRY, $inquiryQuotation->id)
            ->where('sender_type', 'client')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $inquiryQuotation->load(['attachments', 'iqmUsers', 'portalConversations.senderAdmin', 'portalConversations.senderClient']);
        return view('paneladmin.inquiry-quotations.show', compact('inquiryQuotation'));
    }

    public function edit(InquiryQuotation $inquiryQuotation)
    {
        $inquiryQuotation->load(['attachments', 'iqmUsers']);
        return view('paneladmin.inquiry-quotations.edit', [
            'inquiryQuotation' => $inquiryQuotation,
            'iqmUsers' => IqmUser::where('status', 'active')->orderBy('company_name')->get(),
        ]);
    }

    public function update(InquiryQuotationRequest $request, InquiryQuotation $inquiryQuotation)
    {
        $data = $request->validated();
        $iqmUserIds = $this->portalUserIds($request);
        unset($data['client_logo'], $data['iqm_user_ids']);
        $data['iqm_user_id'] = $data['visibility'] === 'private' ? ($iqmUserIds[0] ?? null) : null;

        $data['amount'] = $this->normalizeAmount($request->input('amount'));

        if ($request->hasFile('client_logo')) {
            if ($inquiryQuotation->client_logo &&
                Storage::disk('public')->exists($inquiryQuotation->client_logo)) {
                Storage::disk('public')->delete($inquiryQuotation->client_logo);
            }

            $data['client_logo'] = $request->file('client_logo')->store('inquiry-quotations/logos', 'public');
        }

        $inquiryQuotation->update($data);
        $inquiryQuotation->iqmUsers()->sync($data['visibility'] === 'private' ? $iqmUserIds : []);

        if (empty($inquiryQuotation->quotation_number) && ! empty($data['quotation_date']) && ! empty($data['amount']) && ! empty($data['quotation_status'])) {
            $inquiryQuotation->quotation_number = $this->generateQuotationNumber($data['quotation_date']);
            $inquiryQuotation->save();
        }

        if ($request->hasFile('attachments')) {
            $this->saveAttachments($inquiryQuotation, $request->file('attachments'), optional($request->user())->id);
        }

        app(ActivityLogger::class)->log('update', 'Inquiry & Quotation', 'Updated inquiry: ' . $inquiryQuotation->inquiry_number, $inquiryQuotation);

        return redirect()->route('paneladmin.inquiry-quotations.edit', $inquiryQuotation->id)->with('success', 'Inquiry berhasil diperbarui.');
    }

    protected function saveAttachments(InquiryQuotation $inquiryQuotation, array $files, $uploadedBy = null): void
    {
        foreach ($files as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $extension = strtolower($file->getClientOriginalExtension());
            $filename = now()->format('YmdHis') . '_' . uniqid() . '.' . $extension;
            $path = $file->storeAs('inquiry-quotations', $filename, 'public');

            $inquiryQuotation->attachments()->create([
                'file_name' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $extension,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'attachment_type' => 'other',
                'uploaded_by' => $uploadedBy,
            ]);
        }
    }

    private function portalUserIds(InquiryQuotationRequest $request): array
    {
        return collect($request->input('iqm_user_ids', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function normalizeAmount($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $digits = preg_replace('/[^0-9]/', '', (string) $value);

        return $digits === '' ? null : (int) $digits;
    }

    protected function saveClientLogo(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = now()->format('YmdHis') . '_' . uniqid('client_logo_', true) . '.' . $extension;

        return $file->storeAs('inquiry-quotations/logos', $filename, 'public');
    }

    public function destroy(Request $request, InquiryQuotation $inquiryQuotation)
    {
        app(ActivityLogger::class)->log('delete', 'Inquiry & Quotation', 'Deleted inquiry: ' . $inquiryQuotation->inquiry_number, $inquiryQuotation);

        if ($inquiryQuotation->client_logo) {
            Storage::disk('public')->delete($inquiryQuotation->client_logo);
        }

        $inquiryQuotation->delete();

        return redirect()->route('paneladmin.inquiry-quotations.index')->with('success', 'Inquiry berhasil dihapus.');
    }

    protected function generateInquiryNumber(?string $date = null): string
    {
        $year = $date ? date('Y', strtotime($date)) : date('Y');
        $prefix = "INQ-{$year}-";

        $last = InquiryQuotation::where('inquiry_number', 'like', "{$prefix}%")->orderByDesc('id')->first();

        $next = 1;
        if ($last && preg_match('/-(\d{4})$/', $last->inquiry_number, $m)) {
            $next = intval($m[1]) + 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    protected function generateQuotationNumber(?string $date = null): string
    {
        $year = $date ? date('Y', strtotime($date)) : date('Y');
        $prefix = "QTN-{$year}-";

        $last = InquiryQuotation::where('quotation_number', 'like', "{$prefix}%")->orderByDesc('id')->first();

        $next = 1;
        if ($last && preg_match('/-(\d{4})$/', $last->quotation_number, $m)) {
            $next = intval($m[1]) + 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
