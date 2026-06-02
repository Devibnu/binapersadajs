<?php

namespace App\Http\Controllers;

use App\Models\InquiryQuotation;
use App\Models\InquiryQuotationAttachment;
use App\Models\InvoiceReport;
use App\Models\IqmUser;
use App\Models\ProjectReport;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class IqmPortalController extends Controller
{
    public function landing()
    {
        return view('iqm.landing', ['websiteSetting' => WebsiteSetting::first()]);
    }

    public function login()
    {
        if (Auth::guard('iqm')->check()) {
            return redirect()->route('iqm.dashboard');
        }

        return view('iqm.login', ['websiteSetting' => WebsiteSetting::first()]);
    }

    public function authenticate(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login = $validated['username'];
        $user = IqmUser::where('status', 'active')
            ->where(function ($query) use ($login) {
                $query->where('username', $login)
                    ->orWhere('email', $login);
            })
            ->first();

        if ($user && Hash::check($validated['password'], $user->password)) {
            Auth::guard('iqm')->login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            $user->forceFill(['last_login_at' => now()])->save();

            return redirect()->route('iqm.landing');
        }

        return back()->withInput($request->only('username', 'remember'))->withErrors([
            'username' => 'Username/email atau password tidak valid, atau akun tidak aktif.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('iqm')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('iqm.login');
    }

    public function dashboard()
    {
        $user = Auth::guard('iqm')->user();
        $query = $this->ownedInquiries($user->id)->with('attachments');

        return view('iqm.dashboard', [
            'user' => $user,
            'inquiries' => (clone $query)->paginate(10),
            'totalInquiry' => (clone $query)->count(),
            'siteSurvey' => (clone $query)->where('site_survey_status', 'scheduled')->count(),
            'quotationActive' => (clone $query)->whereIn('quotation_status', ['draft', 'process', 'submitted', 'revision'])->count(),
            'quotationDone' => (clone $query)->whereIn('quotation_status', ['approved', 'closed'])->count(),
        ]);
    }

    public function inquiries()
    {
        $user = Auth::guard('iqm')->user();

        return view('iqm.inquiries', [
            'user' => $user,
            'inquiries' => $this->ownedInquiries($user->id)->with('attachments')->paginate(10),
        ]);
    }

    public function quotations()
    {
        $user = Auth::guard('iqm')->user();

        return view('iqm.quotations', [
            'user' => $user,
            'inquiries' => $this->ownedInquiries($user->id)
                ->whereNotNull('quotation_number')
                ->paginate(10),
        ]);
    }

    public function attachments()
    {
        $user = Auth::guard('iqm')->user();

        return view('iqm.attachments', [
            'user' => $user,
            'attachments' => InquiryQuotationAttachment::query()
                ->whereHas('inquiryQuotation', fn ($query) => $this->applyVisibleToIqmUser($query, $user->id))
                ->with('inquiryQuotation')
                ->latest()
                ->paginate(12),
        ]);
    }

    public function profile()
    {
        return view('iqm.profile', ['user' => Auth::guard('iqm')->user()]);
    }

    public function updateProfilePassword(Request $request)
    {
        $user = Auth::guard('iqm')->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak sesuai.',
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak sesuai.',
            ])->withInput();
        }

        if (Hash::check($validated['password'], $user->password)) {
            return back()->withErrors([
                'password' => 'Password baru harus berbeda dari password saat ini.',
            ])->withInput();
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    public function show(InquiryQuotation $inquiryQuotation)
    {
        $user = Auth::guard('iqm')->user();

        abort_unless(
            $inquiryQuotation->isPublic()
                || $inquiryQuotation->iqmUsers()->where('iqm_users.id', $user->id)->exists(),
            403
        );

        $inquiryQuotation->load('attachments');

        return view('iqm.show', ['entry' => $inquiryQuotation, 'user' => $user]);
    }

    public function projectReports()
    {
        $user = Auth::guard('iqm')->user();

        return view('iqm.project-reports.index', [
            'user' => $user,
            'projectReports' => $this->visibleProjectReports($user->id)->paginate(10),
        ]);
    }

    public function showProjectReport(ProjectReport $projectReport)
    {
        $user = Auth::guard('iqm')->user();

        abort_unless($this->projectReportVisibleToUser($projectReport, $user->id), 403);

        return view('iqm.project-reports.show', [
            'user' => $user,
            'projectReport' => $projectReport,
        ]);
    }

    public function invoiceReports()
    {
        $user = Auth::guard('iqm')->user();

        return view('iqm.invoice-reports.index', [
            'user' => $user,
            'invoiceReports' => $this->visibleInvoiceReports($user->id)->paginate(10),
        ]);
    }

    public function showInvoiceReport(InvoiceReport $invoiceReport)
    {
        $user = Auth::guard('iqm')->user();

        abort_unless($this->invoiceReportVisibleToUser($invoiceReport, $user->id), 403);

        return view('iqm.invoice-reports.show', [
            'user' => $user,
            'invoiceReport' => $invoiceReport,
        ]);
    }

    private function ownedInquiries(int $iqmUserId)
    {
        return $this->applyVisibleToIqmUser(InquiryQuotation::query(), $iqmUserId)->latest();
    }

    private function applyVisibleToIqmUser($query, int $iqmUserId)
    {
        return $query->where(function ($query) use ($iqmUserId) {
            $query->where('visibility', 'public')
                ->orWhereHas('iqmUsers', function ($query) use ($iqmUserId) {
                    $query->where('iqm_users.id', $iqmUserId);
                });
        });
    }

    private function visibleProjectReports(int $iqmUserId)
    {
        return ProjectReport::active()
            ->where(function ($query) use ($iqmUserId) {
                $query->where('visibility', 'public')
                    ->orWhereHas('iqmUsers', function ($query) use ($iqmUserId) {
                        $query->where('iqm_users.id', $iqmUserId);
                    });
            })
            ->ordered();
    }

    private function projectReportVisibleToUser(ProjectReport $projectReport, int $iqmUserId): bool
    {
        if (! $projectReport->is_active) {
            return false;
        }

        return $projectReport->isPublic()
            || $projectReport->iqmUsers()->where('iqm_users.id', $iqmUserId)->exists();
    }

    private function visibleInvoiceReports(int $iqmUserId)
    {
        return InvoiceReport::active()
            ->where(function ($query) use ($iqmUserId) {
                $query->where('visibility', 'public')
                    ->orWhereHas('iqmUsers', function ($query) use ($iqmUserId) {
                        $query->where('iqm_users.id', $iqmUserId);
                    });
            })
            ->ordered();
    }

    private function invoiceReportVisibleToUser(InvoiceReport $invoiceReport, int $iqmUserId): bool
    {
        if (! $invoiceReport->is_active) {
            return false;
        }

        return $invoiceReport->isPublic()
            || $invoiceReport->iqmUsers()->where('iqm_users.id', $iqmUserId)->exists();
    }
}
