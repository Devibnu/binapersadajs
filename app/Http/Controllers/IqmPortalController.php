<?php

namespace App\Http\Controllers;

use App\Models\InquiryQuotation;
use App\Models\InquiryQuotationAttachment;
use App\Models\IqmUser;
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
}
