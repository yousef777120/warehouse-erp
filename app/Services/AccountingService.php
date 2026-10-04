<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AiInvoice;
use App\Models\Employee;
use App\Models\JournalEntry;
use App\Models\StockReceipt;
use Illuminate\Support\Facades\DB;

class AccountingService
{

    /** قيد من فاتورة حللها الوكيل */
    public function createInvoiceEntry(AiInvoice $invoice): JournalEntry
    {
        $d = $invoice->extracted_data;

        $expense = Account::where('code', $d['suggested_expense_account'] ?? '5400')->firstOrFail();
        $tax = Account::where('code', '2200')->firstOrFail();
        $supplier = Account::where('code', '2000')->firstOrFail();

        $subtotal = (float) ($d['subtotal'] ?? $d['total_amount']);
        $taxAmt = (float) ($d['tax_amount'] ?? 0);
        $total = (float) $d['total_amount'];

        return DB::transaction(function () use ($invoice, $d, $expense, $tax, $supplier, $subtotal, $taxAmt, $total) {
            $entry = $this->newEntry(
                "قيد آلي: فاتورة {$d['supplier_name']} رقم {$d['invoice_number']}",
                $d['invoice_date'] ?? now()->toDateString(),
                'ai_invoice'
            );

            $entry->lines()->create(['account_id' => $expense->id, 'debit' => $subtotal, 'credit' => 0]);
            if ($taxAmt > 0) $entry->lines()->create(['account_id' => $tax->id, 'debit' => $taxAmt, 'credit' => 0]);
            $entry->lines()->create(['account_id' => $supplier->id, 'debit' => 0, 'credit' => $total]);

            $invoice->update(['journal_entry_id' => $entry->id]);
            return $entry;
        });
    }

    /** 🔗 ربط المخازن: قيد شراء تلقائي عند تأكيد سند إدخال */
   public function createPurchaseEntryFromReceipt(StockReceipt $receipt): JournalEntry
{
    // تحميل الأصناف إن لم تكن محمّلة
    if (!$receipt->relationLoaded('items')) {
        $receipt->load('items');
    }

    $total = $receipt->items->sum(fn ($it) => (float) $it->quantity * (float) ($it->unit_price ?? 0));

    // إذا كانت unit_price فارغة، استخدم quantity فقط كإجمالي تقريبي
    if ($total <= 0) {
        $total = $receipt->items->sum('quantity');
    }

    $inventory = Account::where('code', '1200')->firstOrFail();
    $supplier  = Account::where('code', '2000')->firstOrFail();

    return DB::transaction(function () use ($receipt, $inventory, $supplier, $total) {
        $entry = $this->newEntry(
            "قيد تلقائي من المخازن: سند إدخال {$receipt->serial} (مخزن: " . ($receipt->warehouse->name ?? '-') . ")",
            $receipt->receipt_date instanceof \DateTimeInterface
                ? $receipt->receipt_date->toDateString()
                : $receipt->receipt_date,
            'warehouse'
        );

        $entry->lines()->create([
            'account_id' => $inventory->id,
            'debit' => $total, 'credit' => 0,
            'notes' => 'إضافة مخزون',
        ]);

        $entry->lines()->create([
            'account_id' => $supplier->id,
            'debit' => 0, 'credit' => $total,
            'notes' => $receipt->supplier_name ?? 'مورد',
        ]);

        return $entry;
    });
}

    /** قيد الرواتب الشهري مفصلاً لكل مسمى وظيفي */
    public function runPayroll(string $month): JournalEntry
    {
        $employees = Employee::with('jobTitle')->where('is_active', true)->get();
        $expense = Account::where('code', '5100')->firstOrFail();
        $payable = Account::where('code', '2100')->firstOrFail();
        $total = $employees->sum('salary');

        return DB::transaction(function () use ($employees, $expense, $payable, $total, $month) {
            $entry = $this->newEntry("قيد إثبات الرواتب لشهر {$month}", now()->toDateString(), 'payroll');

            foreach ($employees->groupBy('job_title_id') as $group) {
                $entry->lines()->create([
                    'account_id' => $expense->id,
                    'debit' => $group->sum('salary'),
                    'credit' => 0,
                    'notes' => 'رواتب: ' . $group->first()->jobTitle->name,
                ]);
            }

            $entry->lines()->create(['account_id' => $payable->id, 'debit' => 0, 'credit' => $total]);
            return $entry;
        });
    }

    /** ترحيل بعد التحقق من التوازن */
    public function post(JournalEntry $entry): void
    {
        $debit = $entry->lines()->sum('debit');
        $credit = $entry->lines()->sum('credit');

        if (abs($debit - $credit) > 0.01) {
            throw new \RuntimeException("القيد غير متوازن: {$debit} / {$credit}");
        }

        $entry->update(['status' => 'posted']);
    }

    private function newEntry(string $description, string $date, string $source): JournalEntry
    {
        return JournalEntry::create([
            'entry_number' => 'JE-' . now()->format('Ymd') . '-' . str_pad((string) (JournalEntry::count() + 1), 4, '0', STR_PAD_LEFT),
            'date' => $date,
            'description' => $description,
            'source' => $source,
            'status' => 'draft',
            'by_agent' => true,
        ]);
    }
}