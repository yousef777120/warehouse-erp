<?php

namespace App\Services;

use App\Models\StockBalance;
use App\Models\StockTransaction;
use App\Models\StockReceipt;
use App\Models\StockIssue;
use App\Models\StockTransfer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockMovementService
{
    // ===== سندات الإدخال =====
    public function confirmReceipt(StockReceipt $receipt): void
    {
        if ($receipt->status !== 'draft') {
            throw new \Exception('يمكن تأكيد السند في حالة المسودة فقط');
        }
        if ($receipt->items()->count() === 0) {
            throw new \Exception('لا يمكن تأكيد سند فارغ');
        }

        DB::transaction(function () use ($receipt) {
            foreach ($receipt->items as $item) {
                $balance = StockBalance::firstOrCreate(
                    ['item_id' => $item->item_id, 'warehouse_id' => $receipt->warehouse_id],
                    ['quantity' => 0]
                );
                $balance = StockBalance::where('id', $balance->id)->lockForUpdate()->first();
                $balance->update(['quantity' => (float)$balance->quantity + (float)$item->quantity]);

                StockTransaction::create([
                    'transaction_type' => 'receipt',
                    'reference_type' => StockReceipt::class,
                    'reference_id' => $receipt->id,
                    'warehouse_id' => $receipt->warehouse_id,
                    'item_id' => $item->item_id,
                    'quantity' => $item->quantity,
                    'movement' => 'in',
                    'user_id' => Auth::id(),
                    'notes' => "تأكيد سند إدخال رقم {$receipt->serial}",
                ]);
            }
            $receipt->update(['status' => 'confirmed', 'confirmed_by' => Auth::id(), 'confirmed_at' => now()]);
        });
    }

    public function cancelReceipt(StockReceipt $receipt): void
    {
        if ($receipt->status !== 'confirmed') {
            throw new \Exception('يمكن إلغاء السند المؤكد فقط');
        }
        DB::transaction(function () use ($receipt) {
            foreach ($receipt->items as $item) {
                $balance = StockBalance::where('item_id', $item->item_id)
                    ->where('warehouse_id', $receipt->warehouse_id)->lockForUpdate()->first();
                if (!$balance) continue;
                $newQty = (float)$balance->quantity - (float)$item->quantity;
                if ($newQty < 0) throw new \Exception("الرصيد غير كافٍ لعكس الحركة للصنف #{$item->item_id}");
                $balance->update(['quantity' => $newQty]);
            }
            $receipt->update(['status' => 'cancelled']);
        });
    }

    // ===== سندات الصرف =====
    public function confirmIssue(StockIssue $issue): void
    {
        if ($issue->status !== 'draft') {
            throw new \Exception('يمكن تأكيد السند في حالة المسودة فقط');
        }
        if ($issue->items()->count() === 0) {
            throw new \Exception('لا يمكن تأكيد سند فارغ');
        }

        DB::transaction(function () use ($issue) {
            // التحقق من الأرصدة أولاً
            foreach ($issue->items as $item) {
                $balance = StockBalance::where('item_id', $item->item_id)
                    ->where('warehouse_id', $issue->warehouse_id)->lockForUpdate()->first();
                $current = $balance ? (float)$balance->quantity : 0;
                if ($current < (float)$item->quantity) {
                    throw new \Exception("الرصيد غير كافٍ للصنف #{$item->item_id}: المتاح {$current}، المطلوب {$item->quantity}");
                }
            }

            // تنفيذ الخصم
            foreach ($issue->items as $item) {
                $balance = StockBalance::where('item_id', $item->item_id)
                    ->where('warehouse_id', $issue->warehouse_id)->lockForUpdate()->first();
                $balance->update(['quantity' => (float)$balance->quantity - (float)$item->quantity]);

                StockTransaction::create([
                    'transaction_type' => 'issue',
                    'reference_type' => StockIssue::class,
                    'reference_id' => $issue->id,
                    'warehouse_id' => $issue->warehouse_id,
                    'item_id' => $item->item_id,
                    'quantity' => $item->quantity,
                    'movement' => 'out',
                    'user_id' => Auth::id(),
                    'notes' => "تأكيد سند صرف رقم {$issue->serial}",
                ]);
            }
            $issue->update(['status' => 'confirmed', 'confirmed_by' => Auth::id(), 'confirmed_at' => now()]);
        });
    }

    public function cancelIssue(StockIssue $issue): void
    {
        if ($issue->status !== 'confirmed') {
            throw new \Exception('يمكن إلغاء السند المؤكد فقط');
        }
        DB::transaction(function () use ($issue) {
            foreach ($issue->items as $item) {
                $balance = StockBalance::firstOrCreate(
                    ['item_id' => $item->item_id, 'warehouse_id' => $issue->warehouse_id],
                    ['quantity' => 0]
                );
                $balance = StockBalance::where('id', $balance->id)->lockForUpdate()->first();
                $balance->update(['quantity' => (float)$balance->quantity + (float)$item->quantity]);
            }
            $issue->update(['status' => 'cancelled']);
        });
    }

    // ===== التحويلات بين المخازن =====
    public function sendTransfer(StockTransfer $transfer): void
    {
        if ($transfer->status !== 'draft') {
            throw new \Exception('يمكن إرسال التحويل في حالة المسودة فقط');
        }
        if ($transfer->items()->count() === 0) {
            throw new \Exception('لا يمكن إرسال تحويل فارغ');
        }

        DB::transaction(function () use ($transfer) {
            // التحقق من الأرصدة في المخزن المصدر
            foreach ($transfer->items as $item) {
                $balance = StockBalance::where('item_id', $item->item_id)
                    ->where('warehouse_id', $transfer->from_warehouse_id)->lockForUpdate()->first();
                $current = $balance ? (float)$balance->quantity : 0;
                if ($current < (float)$item->quantity) {
                    throw new \Exception("الرصيد غير كافٍ في المخزن المصدر للصنف #{$item->item_id}: المتاح {$current}، المطلوب {$item->quantity}");
                }
            }

            // خصم من المخزن المصدر
            foreach ($transfer->items as $item) {
                $balance = StockBalance::where('item_id', $item->item_id)
                    ->where('warehouse_id', $transfer->from_warehouse_id)->lockForUpdate()->first();
                $balance->update(['quantity' => (float)$balance->quantity - (float)$item->quantity]);

                StockTransaction::create([
                    'transaction_type' => 'transfer_send',
                    'reference_type' => StockTransfer::class,
                    'reference_id' => $transfer->id,
                    'warehouse_id' => $transfer->from_warehouse_id,
                    'item_id' => $item->item_id,
                    'quantity' => $item->quantity,
                    'movement' => 'out',
                    'user_id' => Auth::id(),
                    'notes' => "إرسال تحويل رقم {$transfer->serial} إلى المخزن #{$transfer->to_warehouse_id}",
                ]);
            }

            $transfer->update(['status' => 'in_transit', 'sent_by' => Auth::id(), 'sent_at' => now()]);
        });
    }

    public function receiveTransfer(StockTransfer $transfer): void
    {
        if ($transfer->status !== 'in_transit') {
            throw new \Exception('يمكن استلام التحويل في حالة "قيد النقل" فقط');
        }

        DB::transaction(function () use ($transfer) {
            foreach ($transfer->items as $item) {
                $balance = StockBalance::firstOrCreate(
                    ['item_id' => $item->item_id, 'warehouse_id' => $transfer->to_warehouse_id],
                    ['quantity' => 0]
                );
                $balance = StockBalance::where('id', $balance->id)->lockForUpdate()->first();
                $balance->update(['quantity' => (float)$balance->quantity + (float)$item->quantity]);

                StockTransaction::create([
                    'transaction_type' => 'transfer_receive',
                    'reference_type' => StockTransfer::class,
                    'reference_id' => $transfer->id,
                    'warehouse_id' => $transfer->to_warehouse_id,
                    'item_id' => $item->item_id,
                    'quantity' => $item->quantity,
                    'movement' => 'in',
                    'user_id' => Auth::id(),
                    'notes' => "استلام تحويل رقم {$transfer->serial} من المخزن #{$transfer->from_warehouse_id}",
                ]);
            }

            $transfer->update(['status' => 'received', 'received_by' => Auth::id(), 'received_at' => now()]);
        });
    }

    public function cancelTransfer(StockTransfer $transfer): void
    {
        if ($transfer->status === 'cancelled') {
            throw new \Exception('التحويل ملغى مسبقاً');
        }

        DB::transaction(function () use ($transfer) {
            if ($transfer->status === 'in_transit') {
                // إلغاء أثناء النقل: إرجاع الكميات للمخزن المصدر
                foreach ($transfer->items as $item) {
                    $balance = StockBalance::firstOrCreate(
                        ['item_id' => $item->item_id, 'warehouse_id' => $transfer->from_warehouse_id],
                        ['quantity' => 0]
                    );
                    $balance = StockBalance::where('id', $balance->id)->lockForUpdate()->first();
                    $balance->update(['quantity' => (float)$balance->quantity + (float)$item->quantity]);
                }
            } elseif ($transfer->status === 'received') {
                // إلغاء بعد الاستلام: إرجاع الكميات من الوجهة للمصدر
                foreach ($transfer->items as $item) {
                    // خصم من الوجهة
                    $toBalance = StockBalance::where('item_id', $item->item_id)
                        ->where('warehouse_id', $transfer->to_warehouse_id)->lockForUpdate()->first();
                    if ($toBalance && (float)$toBalance->quantity >= (float)$item->item->quantity) {
                        $toBalance->update(['quantity' => (float)$toBalance->quantity - (float)$item->quantity]);
                    }
                    // إضافة للمصدر
                    $fromBalance = StockBalance::firstOrCreate(
                        ['item_id' => $item->item_id, 'warehouse_id' => $transfer->from_warehouse_id],
                        ['quantity' => 0]
                    );
                    $fromBalance = StockBalance::where('id', $fromBalance->id)->lockForUpdate()->first();
                    $fromBalance->update(['quantity' => (float)$fromBalance->quantity + (float)$item->quantity]);
                }
            }

            $transfer->update(['status' => 'cancelled']);
        });
    }
}