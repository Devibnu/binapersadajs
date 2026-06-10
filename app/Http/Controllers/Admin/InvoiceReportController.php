<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvoiceReport;
use App\Models\IqmUser;
use App\Models\PortalConversation;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
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
        unset($data['iqm_user_ids']);
        $this->normalizeNumericFields($data, $request);

        $invoiceReport = InvoiceReport::create($data);
        $invoiceReport->iqmUsers()->sync($data['visibility'] === 'private' ? $iqmUserIds : []);
        app(ActivityLogger::class)->log('create', 'Invoice Reports', 'Invoice report ditambahkan: ' . $invoiceReport->invoice_no, $invoiceReport);

        return redirect()->route('paneladmin.invoice-reports.index')->with('success', 'Invoice report berhasil ditambahkan.');
    }

    public function show(InvoiceReport $invoiceReport)
    {
        PortalConversation::forModule(PortalConversation::MODULE_INVOICE_REPORT, $invoiceReport->id)
            ->where('sender_type', 'client')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $invoiceReport->load(['iqmUsers', 'portalConversations.senderAdmin', 'portalConversations.senderClient']);

        return view('paneladmin.invoice-reports.show', compact('invoiceReport'));
    }

    public function edit(InvoiceReport $invoiceReport)
    {
        $invoiceReport->load('iqmUsers');

        return view('paneladmin.invoice-reports.edit', [
            'invoiceReport' => $invoiceReport,
            'iqmUsers' => $this->activeIqmUsers(),
        ]);
    }

    public function update(Request $request, InvoiceReport $invoiceReport)
    {
        $data = $this->validatedData($request);
        $iqmUserIds = $this->portalUserIds($request);
        unset($data['iqm_user_ids']);
        $this->normalizeNumericFields($data, $request);

        $invoiceReport->update($data);
        $invoiceReport->iqmUsers()->sync($data['visibility'] === 'private' ? $iqmUserIds : []);
        app(ActivityLogger::class)->log('update', 'Invoice Reports', 'Invoice report diperbarui: ' . $invoiceReport->invoice_no, $invoiceReport);

        return redirect()->route('paneladmin.invoice-reports.index')->with('success', 'Invoice report berhasil diperbarui.');
    }

    public function destroy(InvoiceReport $invoiceReport)
    {
        app(ActivityLogger::class)->log('delete', 'Invoice Reports', 'Invoice report dihapus: ' . $invoiceReport->invoice_no, $invoiceReport);
        $invoiceReport->delete();

        return redirect()->route('paneladmin.invoice-reports.index')->with('success', 'Invoice report berhasil dihapus.');
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
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['required', Rule::in(['0', '1'])],
        ], [
            'iqm_user_ids.required_if' => 'Minimal satu IQM User wajib dipilih jika visibility private.',
        ]);
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
