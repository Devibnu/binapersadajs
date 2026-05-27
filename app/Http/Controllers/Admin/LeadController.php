<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $status = in_array($request->query('status'), ['new', 'contacted', 'qualified', 'converted', 'closed'], true)
            ? $request->query('status')
            : null;
        $source = filled($request->query('source')) ? (string) $request->query('source') : null;

        $leads = Lead::query()
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($source, fn ($query) => $query->where('source', $source))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhere('company', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = Lead::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $sources = Lead::query()->whereNotNull('source')->distinct()->orderBy('source')->pluck('source');

        return view('paneladmin.leads.index', compact('leads', 'counts', 'sources', 'search', 'status', 'source'));
    }

    public function show(Lead $lead): View
    {
        return view('paneladmin.leads.show', compact('lead'));
    }

    public function updateStatus(Request $request, Lead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['new', 'contacted', 'qualified', 'converted', 'closed'])],
        ]);

        $oldStatus = $lead->status;
        $lead->update($validated);
        app(ActivityLogger::class)->log('update', 'Leads', 'Status lead diperbarui: ' . $lead->email, $lead, [
            'status_from' => $oldStatus,
            'status_to' => $lead->status,
        ]);

        return back()->with('success', 'Status lead berhasil diperbarui.');
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        app(ActivityLogger::class)->log('delete', 'Leads', 'Lead dihapus: ' . $lead->email, $lead);
        $lead->delete();

        return redirect()->route('paneladmin.leads.index')->with('success', 'Lead berhasil dihapus.');
    }
}
