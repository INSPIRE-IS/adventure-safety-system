<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EquipmentInspectionDetail;
use App\Models\Inspection;
use App\Models\SafetyInspectionDetail;
use Illuminate\Http\Request;
use PDF; // Important!

class ReportController extends Controller
{


    public function generateReport()
    {
        $data = [
            'title' => 'Monthly Report',
            'date' => now()->format('F j, Y'),
            'items' => [/* your data here */]
        ];
        /** @disregard P1009 */
        $pdf = PDF::loadView('reports.monthly', $data);

        return $pdf->stream('monthly_report.pdf');
    }

    // PPE Report
    public function equipment(Request $request)
    {
        //dd($request);
        $inspection_id = $request->input('inspection_id');

        $inspection = Inspection::with('site', 'assigned_to')->findOrFail($inspection_id);
        $inspection_details = EquipmentInspectionDetail::where('inspection_id', $inspection_id)
            ->with(['category_item:id,name', 'category_detail'])
            ->get();
        /** @disregard P1009 */
        $pdf = PDF::loadView('reports.inspection-pdf', compact('inspection', 'inspection_details'));

        return $pdf->stream("inspection-report-{$inspection_id}.pdf");
        //return $pdf->download("inspection-report-{$inspection_id}.pdf");
    }
    // Elements Monthly Safety Inspection Report
    public function inspection(Request $request)
    {
        //dd($request->all());
        $inspection_id = $request->input('inspection_id');

        $inspection = Inspection::with('site', 'assigned_to')->findOrFail($inspection_id);

        $inspection_details = SafetyInspectionDetail::where('inspection_id', $inspection->id)
            ->with(['files', 'category_item:id,name', 'category_detail'])
            ->get();
        // dd($inspection_details[5]);
        // Group data similar to React logic
        $groupedData = [];
        $issueCount = 0;

        foreach ($inspection_details as $item) {
            $categoryName = $item->category_item->name ?? 'Unknown Category';
            $detailName = $item->category_detail->name ?? 'Unknown Detail';

            if (!isset($groupedData[$categoryName])) {
                $groupedData[$categoryName] = [
                    'category_name' => $categoryName,
                    'issues' => [],
                    'count' => 0,
                ];
            }

            $groupedData[$categoryName]['issues'][] = [
                'category_detail_name' => $detailName,
                'condition' => $item->condition,
                'files' => $item->files->map(function ($file) {
                    return [
                        'file_name' => $file->file_name,
                        'file_type' => $file->file_type,
                        'path' => $file->path, // 'storage/' . $file->path, // assumes file is in public/storage
                        'created_at' => $file->created_at
                    ];
                })->toArray()
            ];

            if ($item->condition !== null || $item->files->count() > 0) {
                $groupedData[$categoryName]['count']++;
                $issueCount++;
            }
        }
        //dd($groupedData);

        /** @disregard P1009 */
        $pdf = PDF::loadView('reports.safety-inspection-pdf', [
            'inspection' => $inspection,
            'groupedData' => $groupedData,
            'issueCount' => $issueCount
        ]);

        return $pdf->stream("safety-inspection-{$inspection->id}.pdf");
        //return $pdf->download("safety-inspection-{$inspection->id}.pdf");
    }
}
