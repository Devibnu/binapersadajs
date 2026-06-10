<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IqmUser;
use App\Models\PortalConversation;
use App\Models\ProjectReport;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectReportController extends Controller
{
    public function index(Request $request)
    {
        $query = ProjectReport::with('iqmUsers');

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($query) use ($q) {
                $query->where('project_no', 'like', "%{$q}%")
                    ->orWhere('job_title', 'like', "%{$q}%")
                    ->orWhere('corporation', 'like', "%{$q}%")
                    ->orWhere('department', 'like', "%{$q}%")
                    ->orWhere('user_pic', 'like', "%{$q}%");
            });
        }

        if ($request->filled('visibility')) {
            $query->where('visibility', $request->input('visibility'));
        }

        if ($request->filled('report_status')) {
            $query->where('report_status', $request->input('report_status'));
        }

        $projectReports = $query->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('paneladmin.project-reports.index', compact('projectReports'));
    }

    public function create()
    {
        return view('paneladmin.project-reports.create', [
            'projectReport' => new ProjectReport([
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
        $this->normalizeMoneyFields($data, $request);

        $projectReport = ProjectReport::create($data);
        $projectReport->iqmUsers()->sync($data['visibility'] === 'private' ? $iqmUserIds : []);
        app(ActivityLogger::class)->log('create', 'Project Reports', 'Project report ditambahkan: ' . $projectReport->job_title, $projectReport);

        return redirect()
            ->route('paneladmin.project-reports.index')
            ->with('success', 'Project report berhasil ditambahkan.');
    }

    public function show(ProjectReport $projectReport)
    {
        PortalConversation::forModule(PortalConversation::MODULE_PROJECT_REPORT, $projectReport->id)
            ->where('sender_type', 'client')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $projectReport->load(['iqmUsers', 'portalConversations.senderAdmin', 'portalConversations.senderClient']);

        return view('paneladmin.project-reports.show', compact('projectReport'));
    }

    public function edit(ProjectReport $projectReport)
    {
        $projectReport->load('iqmUsers');

        return view('paneladmin.project-reports.edit', [
            'projectReport' => $projectReport,
            'iqmUsers' => $this->activeIqmUsers(),
        ]);
    }

    public function update(Request $request, ProjectReport $projectReport)
    {
        $data = $this->validatedData($request);
        $iqmUserIds = $this->portalUserIds($request);
        unset($data['iqm_user_ids']);
        $this->normalizeMoneyFields($data, $request);

        $projectReport->update($data);
        $projectReport->iqmUsers()->sync($data['visibility'] === 'private' ? $iqmUserIds : []);
        app(ActivityLogger::class)->log('update', 'Project Reports', 'Project report diperbarui: ' . $projectReport->job_title, $projectReport);

        return redirect()
            ->route('paneladmin.project-reports.index')
            ->with('success', 'Project report berhasil diperbarui.');
    }

    public function destroy(ProjectReport $projectReport)
    {
        app(ActivityLogger::class)->log('delete', 'Project Reports', 'Project report dihapus: ' . $projectReport->job_title, $projectReport);
        $projectReport->delete();

        return redirect()
            ->route('paneladmin.project-reports.index')
            ->with('success', 'Project report berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'project_no' => ['nullable', 'string', 'max:100'],
            'job_title' => ['required', 'string', 'max:255'],
            'quotation_price' => ['nullable', 'string', 'max:30'],
            'contract_number' => ['nullable', 'string', 'max:150'],
            'contract_price' => ['nullable', 'string', 'max:30'],
            'invoice_amount' => ['nullable', 'string', 'max:30'],
            'corporation' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'user_pic' => ['nullable', 'string', 'max:255'],
            'remark' => ['nullable', 'string'],
            'e_wo_status' => ['nullable', 'string', 'max:100'],
            'report_status' => ['nullable', 'string', 'max:100'],
            'visibility' => ['required', Rule::in(['private', 'public'])],
            'iqm_user_ids' => ['required_if:visibility,private', 'array'],
            'iqm_user_ids.*' => ['integer', 'exists:iqm_users,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['required', Rule::in(['0', '1'])],
        ], [
            'iqm_user_ids.required_if' => 'Minimal satu IQM User wajib dipilih jika visibility private.',
        ]);
    }

    private function normalizeMoneyFields(array &$data, Request $request): void
    {
        foreach (['quotation_price', 'contract_price', 'invoice_amount'] as $field) {
            $data[$field] = $this->normalizeMoney($request->input($field));
        }

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

    private function portalUserIds(Request $request): array
    {
        return collect($request->input('iqm_user_ids', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function activeIqmUsers()
    {
        return IqmUser::where('status', 'active')
            ->orderBy('company_name')
            ->orderBy('pic_name')
            ->get();
    }
}
