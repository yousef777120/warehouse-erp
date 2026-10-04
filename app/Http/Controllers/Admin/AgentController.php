<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiInvoice;
use App\Models\Employee;
use App\Models\JobTitle;
use App\Models\JournalEntry;
use App\Services\AccountingService;
use App\Services\InvoiceAiService;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function __construct(
        private InvoiceAiService $ai,
        private AccountingService $accounting,
    ) {}

    public function index()
    {
        return view('admin.agent.index', [
            'invoices'  => AiInvoice::latest()->paginate(8),
            'entries'   => JournalEntry::withCount('lines')->latest()->paginate(8),
            'titles'    => JobTitle::withCount('employees')->orderByDesc('salary')->get(),
            'employees' => Employee::with('jobTitle')->where('is_active', true)->get(),
        ]);
    }

    public function showEntry(JournalEntry $entry)
    {
        $entry->load('lines.account');
        return view('admin.agent.show', compact('entry'));
    }

    public function analyze(Request $request)
    {
        if (! config('services.ai.key')) {
    return back()->with('error', '⚠️ أضف AI_API_KEY في ملف .env لتفعيل تحليل الفواتير');
}
        $request->validate(['invoice' => 'required|image|max:8192']);

        $path = $request->file('invoice')->store('ai-invoices', 'local');
        $invoice = AiInvoice::create(['file_path' => $path, 'status' => 'pending']);

        try {
            $data = $this->ai->analyze($path);
            $invoice->update([
                'extracted_data' => $data,
                'confidence'     => $data['confidence'] ?? null,
                'ai_notes'       => $data['notes'] ?? null,
            ]);
            $this->accounting->createInvoiceEntry($invoice->fresh());

            return redirect()->route('admin.agent.index')
                ->with('success', '✅ حلّل الوكيل الفاتورة وأنشأ قيداً مقترحاً');
        } catch (\Throwable $e) {
            $invoice->update(['status' => 'rejected', 'ai_notes' => $e->getMessage()]);
            return back()->with('error', 'فشل التحليل: ' . $e->getMessage());
        }
    }

    public function approve(AiInvoice $invoice, AkauntingClient $akaunting)
{
    $entry = JournalEntry::findOrFail($invoice->journal_entry_id);
    $this->accounting->post($entry);
    $invoice->update(['status' => 'posted']);

    $msg = "تم ترحيل القيد {$entry->entry_number}";

    if ($akaunting->isEnabled()) {
        try {
            $akaunting->pushEntry($entry);
            $msg .= ' ودُفع إلى Akaunting ✅';
        } catch (\Throwable $e) {
            $msg .= ' (تعذر الدفع لـ Akaunting)';
            \Log::warning('Akaunting: ' . $e->getMessage());
        }
    }

    return back()->with('success', $msg);
}

    public function reject(AiInvoice $invoice)
    {
        if ($invoice->journal_entry_id) {
            JournalEntry::findOrFail($invoice->journal_entry_id)->delete();
        }
        $invoice->update(['status' => 'rejected']);

        return back()->with('success', 'تم الرفض');
    }

    public function payroll(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $entry = $this->accounting->runPayroll($month);
        $this->accounting->post($entry);

        return back()->with('success', "تم ترحيل قيد رواتب {$month}");
    }

    public function storeEmployee(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'job_title_id' => 'required|exists:job_titles,id',
            'salary'       => 'required|numeric|between:1000,10000',
        ]);

        Employee::create($data + ['hire_date' => now()->toDateString()]);

        return back()->with('success', 'تمت إضافة الموظف');
    }
    public function printEntry(JournalEntry $entry)
{
    $entry->load('lines.account');
    return view('admin.agent.print', compact('entry'));
}
}