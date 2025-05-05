<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Departmet;
use App\Models\Utils;
use App\Models\Vehicle;
use App\Models\VehicleRequest;
use Carbon\Carbon;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        $u = Auth::user();

        $content
            ->title('Hello ' . $u->name . ", ")
            ->description('Welcome to ' . env('APP_NAME') . '.');
        $content->row(function (Row $row) {
            $u = Admin::user();
            $conditions = ['is_closed' => 'No']; // Add this condition to all queries

            // If the user is an employee, show only their counts:
            if (
                !$u->isRole('admin') &&
                !$u->isRole('ura') &&
                !$u->isRole('hod') &&
                !$u->isRole('gm') &&
                !$u->isRole('security')
            ) {
                $conditions['applicant_id'] = $u->id;
            }

            // Pending
            $row->column(3, function (Column $column) use ($conditions) {
                $count = VehicleRequest::where($conditions)
                    ->where('hod_status', 'Pending')
                    ->count();
                $column->append(view('widgets.box-5', [
                    'is_dark' => false,
                    'title' => 'Pending requests',
                    'sub_title' => 'Requests not attended to yet.',
                    'number' => number_format($count),
                    'link' => 'all-requests'
                ]));
            });

            // HOD Review
            $row->column(3, function (Column $column) use ($conditions) {
                $count = VehicleRequest::where($conditions)
                    ->where('hod_status', 'Pending')
                    ->count();
                $column->append(view('widgets.box-5', [
                    'is_dark' => false,
                    'title' => 'HOD Review',
                    'sub_title' => 'Requests that need HOD review.',
                    'number' => number_format($count),
                    'link' => 'all-requests'
                ]));
            });

            // GM Review
            $row->column(3, function (Column $column) use ($conditions) {
                $count = VehicleRequest::where($conditions)
                    ->where('hod_status', 'Approved')
                    ->where('gm_status', 'Pending')
                    ->count();
                $column->append(view('widgets.box-5', [
                    'is_dark' => false,
                    'title' => 'GM Review',
                    'sub_title' => 'Requests that need GM review.',
                    'number' => number_format($count),
                    'link' => 'all-requests'
                ]));
            });

            // Approved
            $row->column(3, function (Column $column) use ($conditions) {
                $count = VehicleRequest::where($conditions)
                    ->where('hod_status', 'Approved')
                    ->where('gm_status', 'Approved')
                    ->count();
                $column->append(view('widgets.box-5', [
                    'is_dark' => true,
                    'title' => 'Approved',
                    'sub_title' => 'Approved requests.',
                    'number' => number_format($count),
                    'link' => 'all-requests'
                ]));
            });
        });

        $content->row(function (Row $row) {
            $row->column(6, function (Column $column) {
                $data = [
                    'labels'    => [],
                    'vehicles'  => [],
                    'materials' => [],
                    'personels' => [],
                    'records'   => [],
                ];
            
                // last 12 months
                for ($i = 11; $i >= 0; $i--) {
                    $monthDate = Carbon::now()->subMonths($i);
                    $start     = $monthDate->copy()->startOfMonth();
                    $end       = $monthDate->copy()->endOfMonth();
            
                    // counts by type
                    $vehiclesCount  = VehicleRequest::whereBetween('created_at', [$start, $end])
                        ->where('type', 'Vehicle')
                        ->where('is_closed', 'No')
                        ->count();
            
                    $materialsCount = VehicleRequest::whereBetween('created_at', [$start, $end])
                        ->where('type', 'Materials')
                        ->where('is_closed', 'No')
                        ->count();
            
                    $personelsCount = VehicleRequest::whereBetween('created_at', [$start, $end])
                        ->where('type', 'Personnel')
                        ->where('is_closed', 'No')
                        ->count();
            
                    // push into arrays
                    $data['labels'][]    = $monthDate->format('M Y');      // e.g. "Jun 2024"
                    $data['vehicles'][]  = $vehiclesCount;
                    $data['materials'][] = $materialsCount;
                    $data['personels'][] = $personelsCount;
            
                    // optional: roll-up record for tables
                    $data['records'][] = [
                        'month'    => $monthDate->format('F Y'),
                        'requests' => $vehiclesCount + $materialsCount + $personelsCount,
                        'progress' => 0,
                    ];
                }
            
                $column->append(view('admin.charts.requests-frequency', $data));
            });
       
            
            $row->column(6, function (Column $column) {
                $labels = [];
                $values = [];
            
                // Gather counts per department
                foreach (Departmet::all() as $department) {
                    $labels[] = $department->name;
                    $values[] = VehicleRequest::where('department_id', $department->id)
                        ->where('is_closed', 'No')
                        ->count();
                }
            
                $column->append(view('admin.charts.vehicles-availability', [
                    'labels' => $labels,
                    'values' => $values,
                ]));
            });
            
        });
        return $content;
    }
}
