<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\AiModel\Models\AiModel;

class AiModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $models = [
            'google/gemini-2.5-pro-preview',
            'microsoft/phi-4-reasoning-plus:free',
            'microsoft/phi-4-reasoning:free',
            'qwen/qwen3-0.6b-04-28:free',
            'opengvlab/internvl3-14b:free',
            'deepseek/deepseek-prover-v2:free',
            'qwen/qwen3-30b-a3b:free',
            'tngtech/deepseek-r1t-chimera:free',
            'thudm/glm-4-9b:free',
            'shisa-ai/shisa-v2-llama3.3-70b:free',
            'arliai/qwq-32b-arliai-rpr-v1:free',
            'moonshotai/kimi-vl-a3b-thinking:free',
        ];

        foreach ($models as $modelIdentifier) {
            AiModel::firstOrCreate([
                'model_identifier' => $modelIdentifier,
            ]);
        }
    }
}
