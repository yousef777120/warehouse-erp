<?php

namespace App\Services;

use App\Models\StockReceipt;
use App\Models\StockIssue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SerialNumberService
{
    public function generateReceiptSerial(?Carbon $date = null): string
    {
        return $this->generate('IN', StockReceipt::class, $date);
    }

    public function generateIssueSerial(?Carbon $date = null): string
    {
        return $this->generate('OUT', StockIssue::class, $date);
    }
     /**
     * توليد رقم مسلسل للتحويلات
     */
    public function generateTransferSerial(?Carbon $date = null): string
    {
        $prefix = 'TRF';
        $dateStr = $date ? $date->format('Ymd') : now()->format('Ymd');
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$dateStr}-{$random}";
    }

    private function generate(string $prefix, string $modelClass, ?Carbon $date = null): string
    {
        $date = $date ?? now();
        $year = $date->format('Y');
        $prefixFull = $prefix . '-' . $year . '-';

        return DB::transaction(function () use ($modelClass, $prefixFull) {
            $lastRecord = $modelClass::withTrashed()
                ->where('serial', 'like', $prefixFull . '%')
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $nextNumber = $lastRecord
                ? (int) substr($lastRecord->serial, strlen($prefixFull)) + 1
                : 1;

            return $prefixFull . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        });
    }
}