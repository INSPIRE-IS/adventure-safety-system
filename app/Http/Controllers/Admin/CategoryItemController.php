<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCategoryItemRequest;
use App\Http\Requests\StoreCategoryItemRequest;
use App\Http\Requests\UpdateCategoryItemRequest;
use App\Models\CategoryDetail;
use App\Models\CategoryItem;
use App\Models\File;
use App\Models\Team;
use App\Models\Site;
use App\Models\TypeBool;
use App\Models\TypeInspection;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CategoryItemController extends Controller
{
    public function index(Request $request)
    {
        //dd($request->get('data', ''));
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('category_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = auth()->user();
        $teamId = Session::get('team_id');
        $data = $request->get('data', '');
        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'name'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        $categoryItems = CategoryItem::with(['site', 'type', 'certificate_recieved', 'certificate_file', 'identifications'])
            ->where('name', 'like', '%' . $searchTerm . '%')
            ->where('type_inspection_id', '=', $data)
            ->where('site_id', $teamId)
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(25);
        $type_items = $data == 1 ? [1, 2] : [3];
        $type_inspections = TypeInspection::whereIn('id', $type_items)->select('name as label', 'id as value')->get();
        $teams = Team::select('name as label', 'id as value')->get();
        $type_bools = TypeBool::select('name as label', 'id as value')->get();

        /** @disregard P1013 */
        return Inertia::render('CategoryItem/Index', [
            'categoryItems' => $categoryItems,
            'type_inspections' => $type_inspections,
            'data' => $request->get('data', ''),
            'teams' => $teams,
            'type_bools' => $type_bools,
            'searchTerm' => $searchTerm,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
            'can' => [
                'category_item_show' => auth()->user()->can('category_item_show'),
                'category_item_edit' => auth()->user()->can('category_item_edit'),
                'category_item_delete' => auth()->user()->can('category_item_delete'),
                'category_item_create' => auth()->user()->can('category_item_create'),
            ],
        ]);
    }


    public function store(StoreCategoryItemRequest $request)
    {

        //dd($request);
        if ($request->type_inspection_id == 3) {
            $categoryItem = CategoryItem::create($request->all());
            //$categoryItem = CategoryItem::create($validated);

            if (!empty($request->identifications)) {
                $in_use_date = $request->input('in_use_date') ?? null; // Ensure nullable support
                foreach ($request->identifications as $identification) {
                    //dd($identification);
                    $categoryItem->identifications()->create([
                        'name' => $identification,
                        'in_use_date' =>  $in_use_date,
                    ]);
                }
            }

            return redirect()->back()->with('success', 'Equipment item added successfully.');
        } else {
            $saveImage = false;
            if ($request->hasFile('image')) {

                $request->validate([
                    'image' => 'required|file|image',
                ]);
                $saveImage = true;
            }
            $categoryItem = CategoryItem::create($request->all());
            if (!empty($request->identifications)) {
                $in_use_date = $request->input('in_use_date') ?? null; // Ensure nullable support
                foreach ($request->identifications as $identification) {
                    //dd($identification);
                    $categoryItem->identifications()->create([
                        'name' => $identification,
                        'in_use_date' =>  $in_use_date,
                    ]);
                }
            }


            if ($saveImage) {
                $image_file = $request->file('image');

                $fileName = time() . '_' . $image_file->getClientOriginalName();
                $directoryPath = 'uploads/certification/' . $categoryItem->id;

                $filePath = $image_file->storeAs($directoryPath, $fileName, 'public');
                $file = File::create([
                    'site_id'   => $categoryItem->site_id,
                    'table_name' => 'category_items',
                    'file_name' => $fileName,
                    'type_file_id' => 8, // certificate
                    'row' => $categoryItem->id,
                    'path' => $filePath,
                ]);

                $categoryItem->certificate_file_id = $file->id;
                $categoryItem->save();
            }
            return redirect()->back()->with('success', 'Element item added successfully.');
            //return redirect()->route('category-items.index');
        }
    }


    public function update(UpdateCategoryItemRequest $request, CategoryItem $categoryItem)
    {

        //dd($request, $categoryItem);
        if ($request->type_inspection_id == 3) {
            $categoryItem->update($request->all());
            //$categoryItem = CategoryItem::create($validated);
            $categoryItem->identifications()->delete();

            if (!empty($request->identifications)) {
                $in_use_date = $request->input('in_use_date') ?? null; // Ensure nullable support
                foreach ($request->identifications as $identification) {
                    //dd($identification);
                    $categoryItem->identifications()->create([
                        'name' => $identification,
                        'in_use_date' =>  $in_use_date,
                    ]);
                }
            }

            return redirect()->back()->with('success', 'Equipment item added successfully.');
        } else {
            $saveImage = false;
            if ($request->hasFile('image')) {

                $request->validate([
                    'image' => 'required|file|image',
                ]);
                $saveImage = true;
            }
            $categoryItem->update($request->all());
            $categoryItem->identifications()->delete();

            if (!empty($request->identifications)) {
                $in_use_date = $request->input('in_use_date') ?? null; // Ensure nullable support
                foreach ($request->identifications as $identification) {
                    //dd($identification);
                    $categoryItem->identifications()->create([
                        'name' => $identification,
                        'in_use_date' =>  $in_use_date,
                    ]);
                }
            }


            if ($saveImage) {
                $image_file = $request->file('image');

                $fileName = time() . '_' . $image_file->getClientOriginalName();
                $directoryPath = 'uploads/certification/' . $categoryItem->id;

                $filePath = $image_file->storeAs($directoryPath, $fileName, 'public');
                $file = File::create([
                    'site_id'   => $categoryItem->site_id,
                    'table_name' => 'category_items',
                    'file_name' => $fileName,
                    'type_file_id' => 8, // certificate
                    'row' => $categoryItem->id,
                    'path' => $filePath,
                ]);
                $categoryItem->certificate_file_id = $file->id;
                $categoryItem->save();
            }
            return redirect()->back()->with('success', 'Element item added successfully.');
            //return redirect()->route('category-items.index');
        }

        // $saveImage = false;
        // if ($request->hasFile('image')) {

        //     $request->validate([
        //         'image' => 'required|file|image',
        //     ]);
        //     $saveImage = true;
        // }

        // $categoryItem->update($request->all());



        // return redirect()->route('category-items.index');
    }

    public function show(CategoryItem $categoryItem)
    {
        //abort_if(Gate::denies('category_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categoryItem->load('site', 'type', 'certificate_file');

        return view('categoryItems.show', compact('categoryItem'));
    }

    public function destroy(CategoryItem $categoryItem)
    {
        //abort_if(Gate::denies('category_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categoryItem->delete();

        return back();
    }
}
