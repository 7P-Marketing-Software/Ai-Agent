<?php

namespace Modules\Answer\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Answer\Models\Answer;
use Modules\Question\Models\Question;
use Modules\Answer\Services\AiChatService;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Category\Models\Category;
use Illuminate\Support\Facades\Cache;
use Modules\AiModel\Models\AiModel;

class AnswerController extends Controller
{

    public function __construct(private AiChatService $aiChatService) {}

    public function index()
    {
        $user = auth('sanctum')->user();
        $answers = Answer::where('user_id', $user->id)->paginate();
        return $this->respondOk($answers, 'Answers fetched successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer'      => 'required|string',
        ]);

        $user = auth('sanctum')->user();
        $cacheKey = 'answer_' . $user->id;

        $question = Question::find($data['question_id']);
        $category = Category::find($question->category_id);

        $ancestors = [];
        $currentCategory = $category;
    
        while ($currentCategory && $currentCategory->parent_id) {
            $ancestors[] = $currentCategory;
    
            $currentCategory = Category::find($currentCategory->parent_id);
        }
    
        if ($currentCategory) {
            $ancestors[] = $currentCategory;
        }
    
        $categoryNames = implode(' > ', array_map(function ($ancestor) {
            return $ancestor->name;
        }, $ancestors));

        $agent    = AiModel::find($question->agent_id);

        $message = "For the categories {$categoryNames}\nQuestion: {$question->question}\nUser Answer: {$data['answer']}\nAnalyze this answer";
        // Log::info($categoryNames);
        $aiResponse = $this->aiChatService->sendMessage($message, $agent->model_identifier);
        // Log::info($aiResponse);
        $cachedAnswers = Cache::get($cacheKey, []);

        $cachedAnswers[] = [
            'question'     => $question->question,
            'category'     => $question->category_id,
            'ai_response'  => $aiResponse,
            'timestamp'    => now()->toDateTimeString(),
        ];

        Cache::put($cacheKey, $cachedAnswers, now()->addHour());
        
        return $this->respondCreated(null, 'Answer generated successfully');
    }
    public function generateReport()
    {
        $user = auth('sanctum')->user();
        $cacheKey = 'answer_' . $user->id;
        $cachedAnswers = Cache::get($cacheKey);

        if (empty($cachedAnswers)) {
            return $this->respondNotFound(null, 'No answers found to generate report.');
        }
        
        $category = Category::find($cachedAnswers[0]['category']);
        $agent = AiModel::find($category->agent_id);
        $modelIdentifier = $agent->model_identifier;
    
        $reportPrompt = "Here's a summary of the AI analysis on user answers to questions. Provide an analysis for this:\n\n";

        foreach ($cachedAnswers as $index => $answer) {
            $categoryName = Category::find($answer['category'])->name ?? 'Unknown Category';
            $reportPrompt .= "Category: " . $categoryName . "\n";
            $reportPrompt .= "Question: " . $answer['question'] . "\n";
            $reportPrompt .= "AI Response: " . $answer['ai_response'] . "\n";
            $reportPrompt .= "------------------------------------------------------------------------" . "\n\n";
        }

        // Log::info('Report Prompt: ' . $reportPrompt);
        // Log::info('model identifier: ' . $modelIdentifier);
    
        $reportContent = $this->aiChatService->sendMessage($reportPrompt, $modelIdentifier);

        if (str_contains($reportContent, 'No valid response from AI') || 
            str_contains($reportContent, 'AI request failed')) {
            return $this->respondError(null, 'Failed to generate report.');
        }

        $pdf = Pdf::loadView('answer::ai_report', [
            'reportContent' => $reportContent
        ]);

        $fileName = 'reports/report_' . Str::uuid() . '.pdf';
        Storage::disk('public')->put($fileName, $pdf->output());

        $url = Storage::disk('public')->url($fileName);

        $answer = Answer::create([
            'user_id' => $user->id,
            'sub_category_id' => $category->id,
            'report_link' => $url,
        ]);
        
        Cache::forget($cacheKey);

        return $this->respondOk($answer, 'Report generated successfully');
    }
}
