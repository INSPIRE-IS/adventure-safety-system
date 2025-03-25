<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyTypeQuestionRequest;
use App\Http\Requests\StoreTypeQuestionRequest;
use App\Http\Requests\UpdateTypeQuestionRequest;
use App\Models\TypeQuestion;
use Gate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class TypeQuestionController extends Controller
{
    public function index(Request $request)
    {

        /** @disregard P1013 */
        abort_if(!auth()->user()->can('type_question_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'name'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        $typeQuestions = TypeQuestion::where('name', 'like', '%' . $searchTerm . '%')
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(25);
        /** @disregard P1013 */
        return Inertia::render('TypeQuestion/Index', [
            'typeQuestions' => $typeQuestions,
            'searchTerm' => $searchTerm,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
            'can' => [
                'type_question_show' => false, //auth()->user()->can('type_question_show'),
                'type_question_create' => auth()->user()->can('type_question_create'),
                'type_question_edit' => auth()->user()->can('type_question_edit'),
                'type_question_delete' => auth()->user()->can('type_question_delete'),

            ],
        ]);
    }

    public function store(StoreTypeQuestionRequest $request)
    {
        $typeQuestion = TypeQuestion::create($request->all());

        return redirect()->route('type-questions.index');
    }

    public function update(UpdateTypeQuestionRequest $request, TypeQuestion $typeQuestion)
    {
        $typeQuestion->update($request->all());

        return redirect()->route('type-questions.index');
    }

    public function show(TypeQuestion $typeQuestion)
    {
        //abort_if(Gate::denies('type_question_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('typeQuestions.show', compact('typeQuestion'));
    }

    public function destroy(TypeQuestion $typeQuestion)
    {
        //abort_if(Gate::denies('type_question_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $typeQuestion->delete();

        return back();
    }
}
