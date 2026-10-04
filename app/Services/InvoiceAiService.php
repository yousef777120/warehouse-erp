<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class InvoiceAiService
{
    public function analyze(string $diskPath): array
    {
        $base64 = base64_encode(Storage::disk('local')->get($diskPath));
        $mime = Storage::disk('local')->mimeType($diskPath) ?: 'image/jpeg';

        $res = Http::withToken(config('services.ai.key'))
            ->timeout(120)
            ->post(rtrim(config('services.ai.base'), '/') . '/chat/completions', [
                'model' => config('services.ai.model'),
                'temperature' => 0,
'max_tokens' => 800,
                'messages' => [
                    ['role' => 'system', 'content' => $this->prompt()],
                    ['role' => 'user', 'content' => [
                        ['type' => 'text', 'text' => 'حلّل صورة الفاتورة وأعد JSON فقط.'],
                        ['type' => 'image_url', 'image_url' => ['url' => "data:{$mime};base64,{$base64}"]],
                    ]],
                ],
            ]);

        $res->throw();

        return $this->extractJson($res->json('choices.0.message.content'));
    }

    private function prompt(): string
    {
        return <<<'PROMPT'
أنت محاسب قانوني خبير. حلّل الفاتورة وأعد JSON بالشكل:
{
  "supplier_name":"", "invoice_number":"", "invoice_date":"YYYY-MM-DD", "currency":"SAR",
  "items":[{"description":"","quantity":1,"unit_price":0}],
  "subtotal":0, "tax_amount":0, "total_amount":0,
  "invoice_type":"purchase|expense",
  "suggested_expense_account":"رقم من: 1200,5000,5200,5300,5400",
  "confidence":0.95, "notes":""
}
قواعد: بضاعة لإعادة البيع = 1200 | إيجار = 5200 | كهرباء/ماء = 5300 | غير ذلك = 5400
PROMPT;
    }

    private function extractJson(string $text): array
    {
        if (preg_match('/\{.*\}/s', $text, $m)) {
            $data = json_decode($m[0], true);
            if (is_array($data)) return $data;
        }
        throw new \RuntimeException('تعذر استخراج JSON من استجابة الذكاء الاصطناعي');
    }
}