<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCategoryDetailRequest;
use App\Http\Requests\StoreCategoryDetailRequest;
use App\Http\Requests\UpdateCategoryDetailRequest;
use App\Models\CategoryDetail;
use App\Models\CategoryItem;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CategoryDetailController extends Controller
{
    public function index(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('category_detail_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = auth()->user();
        $teamId = Session::get('team_id');
        $data = $request->get('data', '');
        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'category_item_id'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        $categoryDetails = CategoryDetail::with(['category_item'])
            ->where('name', 'like', '%' . $searchTerm . '%')
            // ->orWhereHas('category_item', function ($query) use ($searchTerm, $teamId) {
            //     $query->where('name', $searchTerm);
            // })
            ->whereHas('category_item', function ($query) use ($data, $teamId) {
                $query->where('type_inspection_id', $data)
                    ->where('site_id', $teamId);
            })
            //->where('site_id', $teamId)
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(500);
        //dd($categoryDetails);
        $categoryItems = CategoryItem::select('name as label', 'id as value')->get();

        /** @disregard P1013 */
        return Inertia::render('CategoryDetail/Index', [
            'categoryItems' => $categoryItems,
            'categoryDetails' => $categoryDetails,
            'searchTerm' => $searchTerm,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
            'can' => [
                'category_detail_show' => auth()->user()->can('category_detail_show'),
                'category_detail_edit' => auth()->user()->can('category_detail_edit'),
                'category_detail_delete' => auth()->user()->can('category_detail_delete'),
                'category_detail_create' => auth()->user()->can('category_detail_create'),
            ],
        ]);
    }

    public function create()
    {
        //abort_if(Gate::denies('category_detail_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $category_details = CategoryItem::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.categoryDetails.create', compact('category_details'));
    }

    public function store(StoreCategoryDetailRequest $request)
    {
        $categoryDetail = CategoryDetail::create($request->all());

        return redirect()->route('admin.category-details.index');
    }

    public function edit(CategoryDetail $categoryDetail)
    {
        //abort_if(Gate::denies('category_detail_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $category_details = CategoryItem::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $categoryDetail->load('category_detail');

        return view('admin.categoryDetails.edit', compact('categoryDetail', 'category_details'));
    }

    public function update(UpdateCategoryDetailRequest $request, CategoryDetail $categoryDetail)
    {
        $categoryDetail->update($request->all());

        return redirect()->route('admin.category-details.index');
    }

    public function show(CategoryDetail $categoryDetail)
    {
        //abort_if(Gate::denies('category_detail_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categoryDetail->load('category_detail');

        return view('admin.categoryDetails.show', compact('categoryDetail'));
    }

    public function destroy(CategoryDetail $categoryDetail)
    {
        //abort_if(Gate::denies('category_detail_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categoryDetail->delete();

        return back();
    }
}
