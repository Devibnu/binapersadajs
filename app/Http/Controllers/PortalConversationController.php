<?php

namespace App\Http\Controllers;

use App\Models\InquiryQuotation;
use App\Models\InvoiceReport;
use App\Models\PortalConversation;
use App\Models\ProjectReport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalConversationController extends Controller
{
    public function storeClientInquiry(Request $request, InquiryQuotation $inquiryQuotation): RedirectResponse
    {
        return $this->storeClientMessage($request, $inquiryQuotation, PortalConversation::MODULE_INQUIRY);
    }

    public function storeClientProjectReport(Request $request, ProjectReport $projectReport): RedirectResponse
    {
        return $this->storeClientMessage($request, $projectReport, PortalConversation::MODULE_PROJECT_REPORT);
    }

    public function storeClientInvoiceReport(Request $request, InvoiceReport $invoiceReport): RedirectResponse
    {
        return $this->storeClientMessage($request, $invoiceReport, PortalConversation::MODULE_INVOICE_REPORT);
    }

    public function storeAdminInquiry(Request $request, InquiryQuotation $inquiryQuotation): RedirectResponse
    {
        return $this->storeAdminMessage($request, $inquiryQuotation, PortalConversation::MODULE_INQUIRY);
    }

    public function storeAdminProjectReport(Request $request, ProjectReport $projectReport): RedirectResponse
    {
        return $this->storeAdminMessage($request, $projectReport, PortalConversation::MODULE_PROJECT_REPORT);
    }

    public function storeAdminInvoiceReport(Request $request, InvoiceReport $invoiceReport): RedirectResponse
    {
        return $this->storeAdminMessage($request, $invoiceReport, PortalConversation::MODULE_INVOICE_REPORT);
    }

    private function storeClientMessage(Request $request, Model $module, string $moduleType): RedirectResponse
    {
        $user = Auth::guard('iqm')->user();

        abort_unless($module->canBeViewedByIqmUser($user), 403);

        $this->createMessage($request, $module, $moduleType, 'client', $user->id);

        return back()->with('success', 'Pesan berhasil dikirim.');
    }

    private function storeAdminMessage(Request $request, Model $module, string $moduleType): RedirectResponse
    {
        $user = Auth::user();

        $this->createMessage($request, $module, $moduleType, 'admin', $user->id);

        return back()->with('success', 'Balasan berhasil dikirim.');
    }

    private function createMessage(Request $request, Model $module, string $moduleType, string $senderType, int $senderId): void
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        PortalConversation::create([
            'module_type' => $moduleType,
            'module_id' => $module->getKey(),
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'message' => $validated['message'],
            'is_read' => false,
        ]);
    }
}
