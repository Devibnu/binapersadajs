<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvoiceReport;
use App\Models\InvoiceReportAttachment;
use App\Models\IqmUser;
use App\Models\PortalConversation;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class InvoiceReportController extends Controller
{
    public function index(Request $request)
    {
        $query = InvoiceReport::with('iqmUsers');

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($query) use ($q) {
                $query->where('client', 'like', "%{$q}%")
                    ->orWhere('invoice_no', 'like', "%{$q}%")
                    ->orWhere('po_wo_no', 'like', "%{$q}%")
                    ->orWhere('job_title', 'like', "%{$q}%");
            });
        }

        if ($request->filled('visibility')) {
            $query->where('visibility', $request->input('visibility'));
        }

        $invoiceReports = $query->ordered()->paginate(15)->withQueryString();

        return view('paneladmin.invoice-reports.index', compact('invoiceReports'));
    }

    public function create()
    {
        return view('paneladmin.invoice-reports.create', [
            'invoiceReport' => new InvoiceReport([
                'visibility' => 'private',
                'is_active' => true,
                'sort_order' => 0,
            ]),
            'iqmUsers' => $this->activeIqmUsers(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $iqmUserIds = $this->portalUserIds($request);
        unset($data['iqm_user_ids'], $data['attachments'], $data['delete_attachment_ids']);
        $this->normalizeNumericFields($data, $request);

        $invoiceReport = InvoiceReport::create($data);
        $invoiceReport->iqmUsers()->sync($data['visibility'] === 'private' ? $iqmUserIds : []);

        if ($request->hasFile('attachments')) {
            $this->saveAttachments($invoiceReport, $request->file('attachments'));
        }

        app(ActivityLogger::class)->log('create', 'Invoice Reports', 'Invoice report ditambahkan: ' . $invoiceReport->invoice_no, $invoiceReport);

        return redirect()->route('paneladmin.invoice-reports.index')->with('success', 'Invoice report berhasil ditambahkan.');
    }

    public function show(InvoiceReport $invoiceReport)
    {
        PortalConversation::forModule(PortalConversation::MODULE_INVOICE_REPORT, $invoiceReport->id)
            ->where('sender_type', 'client')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $invoiceReport->load(['attachments', 'iqmUsers', 'portalConversations.senderAdmin', 'portalConversations.senderClient']);

        return view('paneladmin.invoice-reports.show', compact('invoiceReport'));
    }

    public function edit(InvoiceReport $invoiceReport)
    {
        $invoiceReport->load(['attachments', 'iqmUsers']);

        return view('paneladmin.invoice-reports.edit', [
            'invoiceReport' => $invoiceReport,
            'iqmUsers' => $this->activeIqmUsers(),
        ]);
    }

    public function update(Request $request, InvoiceReport $invoiceReport)
    {
        $data = $this->validatedData($request);
        $iqmUserIds = $this->portalUserIds($request);
        unset($data['iqm_user_ids'], $data['attachments'], $data['delete_attachment_ids']);
        $this->normalizeNumericFields($data, $request);

        $invoiceReport->update($data);
        $invoiceReport->iqmUsers()->sync($data['visibility'] === 'private' ? $iqmUserIds : []);

        $this->deleteSelectedAttachments($invoiceReport, $request->input('delete_attachment_ids', []));

        if ($request->hasFile('attachments')) {
            $this->saveAttachments($invoiceReport, $request->file('attachments'));
        }

        app(ActivityLogger::class)->log('update', 'Invoice Reports', 'Invoice report diperbarui: ' . $invoiceReport->invoice_no, $invoiceReport);

        return redirect()->route('paneladmin.invoice-reports.edit', $invoiceReport)->with('success', 'Invoice report berhasil diperbarui.');
    }

    public function destroy(InvoiceReport $invoiceReport)
    {
        app(ActivityLogger::class)->log('delete', 'Invoice Reports', 'Invoice report dihapus: ' . $invoiceReport->invoice_no, $invoiceReport);
        $invoiceReport->attachments->each(fn (InvoiceReportAttachment $attachment) => Storage::disk('public')->delete($attachment->file_path));
        $invoiceReport->delete();

        return redirect()->route('paneladmin.invoice-reports.index')->with('success', 'Invoice report berhasil dihapus.');
    }

    public function previewAttachment(InvoiceReportAttachment $attachment)
    {
        abort_unless(Storage::disk('public')->exists($attachment->file_path), 404);

        return Storage::disk('public')->response($attachment->file_path, $attachment->original_name);
    }

    public function downloadAttachment(InvoiceReportAttachment $attachment)
    {
        abort_unless(Storage::disk('public')->exists($attachment->file_path), 404);

        return Storage::disk('public')->download($attachment->file_path, $attachment->original_name);
    }

    public function destroyAttachment(InvoiceReportAttachment $attachment)
    {
        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return back()->with('success', 'Attachment berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'client' => ['required', 'string', 'max:255'],
            'invoice_no' => ['nullable', 'string', 'max:150'],
            'po_wo_no' => ['nullable', 'string', 'max:150'],
            'job_title' => ['required', 'string', 'max:255'],
            'invoice_date' => ['nullable', 'date'],
            'quantity' => ['nullable', 'string', 'max:30'],
            'unit' => ['nullable', 'string', 'max:50'],
            'unit_price' => ['nullable', 'string', 'max:30'],
            'total_amount' => ['nullable', 'string', 'max:30'],
            'visibility' => ['required', Rule::in(['private', 'public'])],
            'iqm_user_ids' => ['required_if:visibility,private', 'array'],
            'iqm_user_ids.*' => ['integer', 'exists:iqm_users,id'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'delete_attachment_ids' => ['nullable', 'array'],
            'delete_attachment_ids.*' => ['integer', 'exists:invoice_report_attachments,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['required', Rule::in(['0', '1'])],
        ], [
            'iqm_user_ids.required_if' => 'Minimal satu IQM User wajib dipilih jika visibility private.',
            'attachments.*.mimes' => 'Lampiran invoice harus berupa PDF, JPG, JPEG, atau PNG.',
            'attachments.*.max' => 'Ukuran lampiran invoice maksimal 10MB per file.',
        ]);
    }

    private function saveAttachments(InvoiceReport $invoiceReport, array $files): void
    {
        foreach ($files as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            $extension = strtolower($file->getClientOriginalExtension());
            $filename = now()->format('YmdHis') . '_' . uniqid('invoice_report_', true) . '.' . $extension;
            $path = $file->storeAs('invoice-reports', $filename, 'public');

            $invoiceReport->attachments()->create([
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $extension,
                'file_size' => $file->getSize(),
            ]);
        }
    }

    private function deleteSelectedAttachments(InvoiceReport $invoiceReport, array $attachmentIds): void
    {
        if (empty($attachmentIds)) {
            return;
        }

        $invoiceReport->attachments()
            ->whereIn('id', $attachmentIds)
            ->get()
            ->each(function (InvoiceReportAttachment $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
                $attachment->delete();
            });
    }

    private function normalizeNumericFields(array &$data, Request $request): void
    {
        $data['quantity'] = $this->normalizeDecimal($request->input('quantity'));
        $data['unit_price'] = $this->normalizeMoney($request->input('unit_price'));
        $data['total_amount'] = $this->normalizeMoney($request->input('total_amount'));
        $data['sort_order'] = $data['sort_order'] ?? 0;
    }

    private function normalizeMoney($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $digits = preg_replace('/[^0-9]/', '', (string) $value);

        return $digits === '' ? null : (int) $digits;
    }

    private function normalizeDecimal($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        $normalized = str_replace(',', '.', preg_replace('/[^0-9,.]/', '', (string) $value));

        return is_numeric($normalized) ? (float) $normalized : null;
    }

    private function portalUserIds(Request $request): array
    {
        return collect($request->input('iqm_user_ids', []))->filter()->map(fn ($id) => (int) $id)->unique()->values()->all();
    }

    private function activeIqmUsers()
    {
        return IqmUser::where('status', 'active')->orderBy('company_name')->orderBy('pic_name')->get();
    }
}
