<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Inertia\Inertia;

class SystemCalendarController extends Controller
{
    public $sources = [
        [
            'model'      => '\App\Models\Inspection',
            'date_field' => 'due_date',
            'field'      => 'due_date',
            'prefix'     => 'Inspection',
            'suffix'     => '',
            'route'      => 'inspections.index',
        ],
        [
            'model'      => '\App\Models\Drill',
            'date_field' => 'created_at',
            'field'      => 'due_date',
            'prefix'     => 'drill',
            'suffix'     => '',
            'route'      => 'drills.index',
        ],
    ];

    public function index()
    {
        //dd('hee');
        $events = [];
        foreach ($this->sources as $source) {
            foreach ($source['model']::all() as $model) {
                $crudFieldValue = $model->getAttributes()[$source['date_field']];

                if (! $crudFieldValue) {
                    continue;
                }

                $events[] = [
                    'title' => trim($source['prefix'] . ' ' . $model->{$source['field']} . ' ' . $source['suffix']),
                    'start' => $crudFieldValue,
                    'url'   => route($source['route'], $model->id),
                ];
            }
        }
        return Inertia::render('Calendar/Index', [
            'auth' => auth()->user(),
            'events' => $events,
        ]);
        //return view('calendar.calendar', compact('events'));
    }
}
