<?php

namespace App\Services;

use App\Models\JournalEntry;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AkauntingClient
{
    public function isEnabled(): bool
    {
        return filled(config('services.akaunting.url'));
    }

    public function pushEntry(JournalEntry $entry): array
    {
        $base = rtrim(config('services.akaunting.url'), '/');

        $login = Http::asJson()->post($base . '/api/login', [
            'email'    => config('services.akaunting.email'),
            'password' => config('services.akaunting.password'),
        ]);

        $token = $login->json('data.api_key');

        if (! $token) {
            throw new \RuntimeException('فشل تسجيل الدخول إلى Akaunting');
        }

        $res = Http::withToken($token)->post($base . '/api/banking/transactions', [
            'company_id'    => config('services.akaunting.company'),
            'type'          => 'expense',
            'paid_at'       => $entry->date->toDateString(),
            'amount'        => $entry->lines()->sum('debit'),
            'currency_code' => 'SAR',
            'account_id'    => 1,
            'description'   => $entry->description . ' (' . $entry->entry_number . ')',
        ]);

        Log::info('Akaunting push status: ' . $res->status());

        return $res->json() ?? [];
    }
}