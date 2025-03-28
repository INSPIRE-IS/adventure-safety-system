<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\HomeResource;
use App\Models\CategoryItem;
use App\Models\Drill;
use App\Models\DrillDetail;
use App\Models\DrillMember;
use App\Models\Inspection;
use App\Models\PostIncident;
use App\Models\PostIncidentDetail;
use App\Models\Team;
use App\Models\TypeActivity;
use App\Models\TypeBool;
use App\Models\TypeDrill;
use App\Models\TypeFile;
use App\Models\TypeIncident;
use App\Models\TypeInspection;
use App\Models\TypeQuestion;
use App\Models\User;
use App\Models\UserAlert;
use Carbon\Carbon;
use DB;
use Gate;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\PostInc;
use Symfony\Component\HttpFoundation\Response;

class HomeApiController extends Controller
{
    public function getTaskData(Request $request)
    {

        //dd($request);
        //abort_if(Gate::denies('inspection_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $userId = auth('api')->user()->id;
        $teamId = auth('api')->user()->teams->first()->id;
        $typeInspectionId = 1;
        $safety_inspections = Inspection::select('id', 'due_date', 'complete_date', 'closing_comment', 'inspection_schedule_id', 'assigned_to_id', 'photo_file_id', 'signature_file_id', 'site_id')
            ->with(
                [
                    'inspection_schedule:id,name,type_activity_id,type_inspection_id,active_date,period_id',
                    'inspection_schedule.type_inspection:id,name',
                    //'inspection_schedule.type_activity:id,name',
                    'safety_inspection_details',
                    //'equipment_inspection_details',
                    //'assigned_to:id,name,surname,email,mobile_number',
                    'photo_file:id,path,type_file_id,row',
                    'signature_file:id,path,type_file_id,row'
                ]
            )
            //->where('assigned_to_id', $userId)
            ->where('site_id', $teamId)
            //->whereBetween('inspections.due_date', [Carbon::now(), Carbon::now()->addMonths(3)])
            ->whereHas('inspection_schedule', function ($query) use ($typeInspectionId) {
                $query->where('type_inspection_id', $typeInspectionId);
            })
            ->get();

        $typeInspectionId = 2;
        $annual_safety_inspections = Inspection::select('id', 'due_date', 'complete_date', 'closing_comment', 'inspection_schedule_id', 'assigned_to_id', 'photo_file_id', 'signature_file_id', 'site_id')
            ->with(
                [
                    'inspection_schedule:id,name,type_activity_id,type_inspection_id,active_date,period_id',
                    'inspection_schedule.type_inspection:id,name',
                    // 'inspection_schedule.type_activity:id,name',
                    'safety_inspection_details',
                    //'equipment_inspection_details',
                    //'assigned_to:id,name,surname,email,mobile_number',
                    'photo_file:id,path,type_file_id,row',
                    'signature_file:id,path,type_file_id,row'
                ]
            )
            //->where('assigned_to_id', $userId)
            ->where('site_id', $teamId)
            //->whereBetween('inspections.due_date', [Carbon::now(), Carbon::now()->addMonths(3)])
            ->whereHas('inspection_schedule', function ($query) use ($typeInspectionId) {
                $query->where('type_inspection_id', $typeInspectionId);
            })
            ->get();

        $typeInspectionId = 3;

        $equipment_inspections = Inspection::select('id', 'due_date', 'complete_date', 'closing_comment', 'inspection_schedule_id', 'assigned_to_id', 'photo_file_id', 'signature_file_id', 'site_id')
            ->with(
                [
                    'inspection_schedule:id,name,type_activity_id,type_inspection_id,active_date,period_id',
                    'inspection_schedule.type_inspection:id,name',
                    // 'inspection_schedule.type_activity:id,name',
                    //'safety_inspection_details',
                    'equipment_inspection_details',
                    //'assigned_to:id,name,surname,email,mobile_number',
                    'photo_file:id,path,type_file_id,row',
                    'signature_file:id,path,type_file_id,row'
                ]
            )
            //->where('assigned_to_id', $userId)
            ->where('site_id', $teamId)
            //->whereBetween('inspections.due_date', [Carbon::now(), Carbon::now()->addMonths(3)])
            ->whereHas('inspection_schedule', function ($query) use ($typeInspectionId) {
                $query->where('type_inspection_id', $typeInspectionId);
            })
            ->get();


        $post_incidents = PostIncident::select('id', 'due_date', 'complete_date', 'post_incident_schedule_id', 'report_file_id', 'site_id', 'assigned_to_id')
            ->with([
                'post_incident_schedule:id,name,type_incident_id,incident_id,active_date,period_id',
                'post_incident_schedule.type_incident:id,name',
                'post_incident_schedule.incident:id,type_incident_id,site_id,incident_date,description,signature_file_id,incident_file_id',
                //'assigned_to:id,name,surname,email,mobile_number',
                'report_file:id,path,type_file_id,row',
                'post_incident_details'
            ])
            //->where('assigned_to_id', $userId)
            ->where('site_id', $teamId)
            //->whereBetween('post_incidents.due_date', [Carbon::now(), Carbon::now()->addMonths(3)])
            ->get();


        $drills = Drill::select('id', 'due_date', 'complete_date', 'drill_schedule_id', 'assigned_to_id', 'photo_file_id', 'signature_file_id', 'site_id', 'type_activity_id', 'type_drill_id')
            ->with([
                'drill_schedule:id,name,type_activity_id,type_drill_id,active_date,period_id',
                'drill_schedule.type_drill:id,name',
                //'drill_schedule.type_activity:id,name',
                //'assigned_to:id,name,surname,email,mobile_number',
                'photo_file:id,path,type_file_id,row',
                'signature_file:id,path,type_file_id,row',
                'drill_details'
            ])
            //->where('assigned_to_id', $userId)
            ->where('site_id', $teamId)
            //->whereBetween('drills.due_date', [Carbon::now(), Carbon::now()->addMonths(3)])
            ->get();

        $user_alerts = UserAlert::select('id', 'alert_text', 'alert_link', 'alert_param_id')->with([
            'users:id,name',
            'users.teams:id,name'
        ])
            ->whereHas('users', function ($query) use ($teamId) {
                $query->whereHas('teams', function ($subQuery) use ($teamId) {
                    $subQuery->where('team_id', $teamId);
                });
            })->get();


        return new HomeResource(compact(
            'safety_inspections',
            'annual_safety_inspections',
            'equipment_inspections',
            'post_incidents',
            'drills',
            'user_alerts'
        ));
    }

    public function getAppData(Request $request)
    {
        //dd($request);
        //abort_if(Gate::denies('inspection_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $userId = auth('api')->user()->id;
        $teamId = auth('api')->user()->teams->first()->id;
        $typeInspectionId = 1;

        $safety_inspection_item_details = CategoryItem::select('id', 'name', 'site_id', 'type_inspection_id', 'certificate_recieved_id', 'certificate_file_id')
            ->with('identifications:id,name,category_item_id,in_use_date,out_use_date')
            ->where('site_id', $teamId)
            ->where('type_inspection_id', $typeInspectionId)
            ->get();
        //dd($safety_inspection_details);

        $typeInspectionId = 2;

        $annual_safety_inspection_item_details = CategoryItem::select('id', 'name', 'site_id', 'type_inspection_id', 'certificate_recieved_id', 'certificate_file_id')
            ->with('identifications:id,name,category_item_id,in_use_date,out_use_date')
            ->where('site_id', $teamId)
            ->where('type_inspection_id', $typeInspectionId)
            ->get();
        //dd($safety_inspection_details);

        $typeInspectionId = 3;

        $equipment_inspection_item_details = CategoryItem::select('id', 'name', 'site_id', 'type_inspection_id', 'certificate_recieved_id', 'certificate_file_id')
            ->with('identifications:id,name,category_item_id,in_use_date,out_use_date')
            ->where('site_id', $teamId)
            ->where('type_inspection_id', $typeInspectionId)
            ->get();


        $post_incident_details = PostIncidentDetail::select('id', 'answer', 'post_incident_id', 'question_id', 'user_id')
            ->with('question:id,name')
            ->get();


        $drill_details = DrillDetail::select('id', 'complete_date', 'drill_id', 'competent', 'site_id', 'instructor_id')->get();
        $drill_members = DrillMember::select('id', 'member_name', 'drill_id', 'competent', 'site_id', 'user_id', 'uuid')->get();
        $type_bools = TypeBool::select('id', 'name', 'updated_at')->get();
        $type_drills = TypeDrill::select('id', 'name', 'updated_at')->get();
        $type_activities = TypeActivity::select('id', 'name', 'updated_at')->get();
        $type_sites = Team::select('id', 'name', 'updated_at')->get();
        $type_incidents = TypeIncident::select('id', 'name', 'updated_at')->get();
        $type_inspections = TypeInspection::select('id', 'name', 'updated_at')->get();
        $type_questions = TypeQuestion::select('id', 'name', 'updated_at')->get();
        $type_files = TypeFile::select('id', 'name', 'updated_at')->get();
        $colleagues = User::select('id', 'name', 'surname', 'updated_at')
            ->whereHas('teams', function ($query) use ($teamId) {
                $query->where('team_id', $teamId);
            })
            ->whereHas('roles', function ($query) {
                $query->whereIn('role_id', [4, 5, 6]);
            })
            ->with(['teams:id,name']) // Load only role ID & name
            ->with(['roles:id,name']) // Load only role ID & name
            ->get();

        return new HomeResource(compact(
            'safety_inspection_item_details',
            'annual_safety_inspection_item_details',
            'equipment_inspection_item_details',
            'post_incident_details',
            'drill_details',
            'drill_members',
            'type_bools',
            'type_drills',
            'type_activities',
            'type_incidents',
            'type_inspections',
            'type_questions',
            'type_files',
            'colleagues'
        ));
    }
}
