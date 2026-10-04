<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class AiTestCommand extends Command
{
    protected $signature = 'ai:test';
    protected $description = 'اختبار اتصال مفتاح الذكاء الاصطناعي';

    public function handle(): int
    {
        if (! config('services.ai.key')) {
            $this->error('❌ لا يوجد AI_API_KEY في .env');
            return self::FAILURE;
        }

        try {
            $res = Http::withToken(config('services.ai.key'))
                ->timeout(30)
                ->post(rtrim(config('services.ai.base'), '/') . '/chat/completions', [
                    'model' => config('services.ai.model'),
                    'messages' => [['role' => 'user', 'content' => 'رد بكلمة: جاهز']],
'max_tokens' => 100,
                ]);

            if ($res->successful()) {
                $this->info('✅ الاتصال ناجح — الرد: ' . $res->json('choices.0.message.content'));
                return self::SUCCESS;
            }

            $this->error('❌ فشل: ' . $res->status() . ' — ' . $res->body());
            return self::FAILURE;
        } catch (\Throwable $e) {
            $this->error('❌ خطأ: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}