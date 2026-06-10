<?php

namespace App\Http\Controllers\Iqm;

use App\Http\Controllers\Controller;
use App\Models\InquiryQuotation;
use App\Models\InvoiceReport;
use App\Models\PortalConversation;
use App\Models\ProjectReport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('iqm')->user();
        $iqmUserId = $user->id;

        $inquiryQuery = $this->visibleInquiries($iqmUserId);
        $projectQuery = $this->visibleProjectReports($iqmUserId);
        $invoiceQuery = $this->visibleInvoiceReports($iqmUserId);

        $totalInquiry = (clone $inquiryQuery)->count();
        $totalProject = (clone $projectQuery)->count();
        $totalQuotationActive = (clone $inquiryQuery)
            ->whereIn('quotation_status', ['draft', 'process', 'submitted', 'revision'])
            ->count();
        $totalInvoiceActive = (clone $invoiceQuery)->count();

        $projects = (clone $projectQuery)
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $invoices = (clone $invoiceQuery)
            ->orderByDesc('invoice_date')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $inquiries = (clone $inquiryQuery)
            ->orderByDesc('inquiry_date')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $activeProjectReport = $projects->first();
        $activeProject = $activeProjectReport ? $this->projectOverview($activeProjectReport) : null;
        $projectTimeline = $projects->map(fn (ProjectReport $projectReport) => [
            'name' => $projectReport->job_title,
            'status' => $projectReport->report_status ?: '-',
            'date' => $projectReport->updated_at,
            'detail_url' => route('iqm.project-reports.show', $projectReport),
        ]);

        $latestInvoices = $invoices->map(fn (InvoiceReport $invoiceReport) => [
            'id' => $invoiceReport->id,
            'invoice_no' => $invoiceReport->invoice_no ?: '-',
            'date' => $invoiceReport->invoice_date,
            'amount' => $invoiceReport->formattedMoney('total_amount'),
            'detail_url' => route('iqm.invoice-reports.show', $invoiceReport),
        ]);

        $latestInquiries = $inquiries->map(fn (InquiryQuotation $inquiry) => [
            'id' => $inquiry->id,
            'inquiry_no' => $inquiry->inquiry_number,
            'subject' => $inquiry->subject,
            'status' => $inquiry->quotationStatusLabel(),
            'status_class' => $inquiry->quotationStatusBadgeClass(),
            'date' => $inquiry->inquiry_date,
        ]);

        $latestActivities = $this->latestActivities(
            (clone $inquiryQuery)->orderByDesc('updated_at')->limit(10)->get(),
            (clone $projectQuery)->orderByDesc('updated_at')->limit(10)->get(),
            (clone $invoiceQuery)->orderByDesc('created_at')->limit(10)->get()
        );
        $conversationSummary = $this->conversationSummary($iqmUserId);

        return view('iqm.dashboard', compact(
            'user',
            'totalInquiry',
            'totalProject',
            'totalQuotationActive',
            'totalInvoiceActive',
            'activeProject',
            'projectTimeline',
            'latestActivities',
            'latestInvoices',
            'latestInquiries',
            'conversationSummary'
        ));
    }

    private function visibleInquiries(int $iqmUserId): Builder
    {
        return InquiryQuotation::query()->visibleToIqmUser($iqmUserId);
    }

    private function visibleProjectReports(int $iqmUserId): Builder
    {
        return ProjectReport::active()->visibleToIqmUser($iqmUserId);
    }

    private function visibleInvoiceReports(int $iqmUserId): Builder
    {
        return InvoiceReport::active()->visibleToIqmUser($iqmUserId);
    }

    private function latestActivities(Collection $inquiries, Collection $projects, Collection $invoices): Collection
    {
        return $inquiries
            ->map(fn (InquiryQuotation $inquiry) => [
                'date' => $inquiry->quotation_date ?: $inquiry->inquiry_date ?: $inquiry->updated_at,
                'title' => $this->inquiryActivityTitle($inquiry),
                'description' => $inquiry->quotation_number ?: $inquiry->inquiry_number,
                'type' => $inquiry->quotation_number ? 'Quotation' : 'Inquiry',
                'icon' => $inquiry->quotation_number ? 'fa-file-signature' : 'fa-file-lines',
                'class' => $inquiry->quotation_number ? 'bg-gradient-primary' : 'bg-gradient-secondary',
            ])
            ->concat($projects->map(fn (ProjectReport $projectReport) => [
                'date' => $projectReport->updated_at,
                'title' => $projectReport->report_status
                    ? 'Status project: ' . $projectReport->report_status
                    : 'Project report diperbarui',
                'description' => Str::limit($projectReport->job_title, 56),
                'type' => 'Project',
                'icon' => 'fa-clipboard-list',
                'class' => 'bg-gradient-info',
            ]))
            ->concat($invoices->map(fn (InvoiceReport $invoiceReport) => [
                'date' => $invoiceReport->invoice_date ?: $invoiceReport->created_at,
                'title' => 'Invoice diterbitkan',
                'description' => $invoiceReport->invoice_no ?: Str::limit($invoiceReport->job_title, 56),
                'type' => 'Invoice',
                'icon' => 'fa-file-invoice-dollar',
                'class' => 'bg-gradient-warning',
            ]))
            ->filter(fn (array $activity) => ! empty($activity['date']))
            ->sortByDesc('date')
            ->take(10)
            ->values();
    }

    private function projectOverview(ProjectReport $projectReport): array
    {
        return [
            'id' => $projectReport->id,
            'name' => $projectReport->job_title,
            'status' => $projectReport->report_status ?: '-',
            'pic' => $projectReport->user_pic ?: '-',
            'last_update' => $projectReport->updated_at,
            'detail_url' => route('iqm.project-reports.show', $projectReport),
            'project_no' => $projectReport->project_no ?: '-',
            'remark' => $projectReport->remark ?: null,
        ];
    }

    private function inquiryActivityTitle(InquiryQuotation $inquiry): string
    {
        return match ($inquiry->quotation_status) {
            'approved' => 'Quotation disetujui',
            'submitted' => 'Quotation dikirim',
            'revision' => 'Quotation direvisi',
            'rejected' => 'Quotation ditolak',
            default => $inquiry->quotation_number ? 'Quotation diperbarui' : 'Inquiry diterima',
        };
    }

    private function conversationSummary(int $iqmUserId): array
    {
        $modules = [
            PortalConversation::MODULE_INQUIRY => [
                'label' => 'Inquiry',
                'ids' => $this->visibleInquiries($iqmUserId)->pluck('id')->all(),
            ],
            PortalConversation::MODULE_PROJECT_REPORT => [
                'label' => 'Project Report',
                'ids' => $this->visibleProjectReports($iqmUserId)->pluck('id')->all(),
            ],
            PortalConversation::MODULE_INVOICE_REPORT => [
                'label' => 'Invoice Report',
                'ids' => $this->visibleInvoiceReports($iqmUserId)->pluck('id')->all(),
            ],
        ];

        $items = collect($modules)->map(function (array $module, string $moduleType) {
            $query = empty($module['ids'])
                ? null
                : PortalConversation::query()
                    ->where('module_type', $moduleType)
                    ->whereIn('module_id', $module['ids'])
                    ->where('sender_type', 'admin')
                    ->where('is_read', false);

            return [
                'label' => $module['label'],
                'count' => $query ? (clone $query)->count() : 0,
                'latest_at' => $query ? (clone $query)->latest('created_at')->value('created_at') : null,
            ];
        })->values();

        return [
            'total' => $items->sum('count'),
            'latest_at' => $items->pluck('latest_at')->filter()->max(),
            'items' => $items,
        ];
    }

}
