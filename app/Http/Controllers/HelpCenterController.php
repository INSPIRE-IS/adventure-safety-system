<?php

namespace App\Http\Controllers;

use App\Models\HelpCategory;
use App\Models\HelpQuestion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use League\CommonMark\GithubFlavoredMarkdownConverter;

class HelpCenterController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $teamId = session()->get('team_id');

        $searchTerm = trim((string) $request->get('search', ''));
        $terms = array_values(array_filter(preg_split('/\s+/', $searchTerm) ?: []));

        $roleSlugs = [];
        if ($user) {
            $roleSlugs = $user->getRoleNames()->values()->all();
        }

        $allCategories = HelpCategory::query()
            ->orderByRaw('COALESCE(order_id, 9999) asc')
            ->orderBy('name')
            ->get(['id', 'name', 'order_id', 'role_slugs']);

        $visibleCategories = $allCategories->filter(function (HelpCategory $category) use ($roleSlugs) {
            $required = $category->role_slugs;
            if (empty($required)) {
                return true;
            }

            return (bool) array_intersect($required, $roleSlugs);
        })->values();

        $visibleCategoryIds = $visibleCategories->pluck('id')->all();

        $questionsQuery = HelpQuestion::query()
            ->with([
                'category:id,name,order_id',
                'team:id,name',
                'answer:id,help_question_id,answer_md',
            ])
            ->whereIn('help_category_id', $visibleCategoryIds)
            ->where(function ($q) use ($teamId) {
                $q->where('team_id', $teamId)
                    ->orWhere(function ($q) use ($teamId) {
                        $q->whereNull('team_id');

                        if ($teamId !== null) {
                            $q->whereNotExists(function ($sub) use ($teamId) {
                                $sub->selectRaw('1')
                                    ->from('help_questions as hq2')
                                    ->whereColumn('hq2.help_category_id', 'help_questions.help_category_id')
                                    ->whereColumn('hq2.question', 'help_questions.question')
                                    ->whereNull('hq2.deleted_at')
                                    ->where('hq2.team_id', $teamId);
                            });
                        }
                    });
            });

        if (count($terms) > 0) {
            $questionsQuery->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $escaped = addcslashes($term, '%_\\');
                    $like = '%' . $escaped . '%';

                    $q->orWhere('help_questions.question', 'like', $like)
                        ->orWhereHas('answer', function ($answerQuery) use ($like) {
                            $answerQuery->where('answer_md', 'like', $like);
                        })
                        ->orWhereHas('category', function ($categoryQuery) use ($like) {
                            $categoryQuery->where('name', 'like', $like);
                        });
                }
            });
        }

        $questions = $questionsQuery
            ->orderBy('question')
            ->get();

        $markdown = new GithubFlavoredMarkdownConverter([
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
        ]);

        $questionsByCategory = $questions->groupBy('help_category_id');

        $categoriesPayload = $visibleCategories->map(function (HelpCategory $category) use ($questionsByCategory, $markdown) {
            $categoryQuestions = ($questionsByCategory->get($category->id, collect()))
                ->values()
                ->map(function (HelpQuestion $question) use ($markdown) {
                    $answerMd = (string) optional($question->answer)->answer_md;

                    return [
                        'id' => $question->id,
                        'help_category_id' => $question->help_category_id,
                        'question' => $question->question,
                        'team_id' => $question->team_id,
                        'team_name' => optional($question->team)->name,
                        'is_internal' => (bool) $question->is_internal,
                        'answer_html' => (string) $markdown->convert($answerMd),
                        'updated_at' => optional($question->updated_at)->toDateTimeString(),
                    ];
                });

            if ($categoryQuestions->isEmpty()) {
                return null;
            }

            return [
                'id' => $category->id,
                'name' => $category->name,
                'order_id' => $category->order_id,
                'questions' => $categoryQuestions,
            ];
        })->filter()->values();

        $selectedQuestionId = (int) $request->get('question_id', 0);
        $flatQuestions = $categoriesPayload->pluck('questions')->flatten(1);
        if ($selectedQuestionId <= 0 || !$flatQuestions->contains('id', $selectedQuestionId)) {
            $selectedQuestionId = (int) ($flatQuestions->first()['id'] ?? 0);
        }

        return Inertia::render('HelpCenter/Index', [
            'categories' => $categoriesPayload,
            'searchTerm' => $searchTerm,
            'selectedQuestionId' => $selectedQuestionId,
        ]);
    }
}

