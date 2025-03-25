<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
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
            ->title(env('APP_NAME') . ' - Dashboard')
            ->description('Hello ' . $u->name . "!");
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

                $u = Admin::user();
                $data = [];
                $records = [];
                $prev = 0;
                for ($i = 29; $i >= -1; $i--) {
                    $min = new Carbon();
                    $max = new Carbon();
                    $max->subDays($i);
                    $min->subDays(($i + 1));

                    $max = Carbon::parse($max->format('Y-m-d'));
                    $min = Carbon::parse($min->format('Y-m-d'));

                    $vehicles = VehicleRequest::whereBetween('created_at', [$min, $max])
                        ->where([
                            'type' => 'Vehicle',
                            'is_closed' => 'No'
                        ])
                        ->count('id');

                    $materials = VehicleRequest::whereBetween('created_at', [$min, $max])
                        ->where([
                            'type' => 'Materials',
                            'is_closed' => 'No'
                        ])
                        ->count('id');

                    $personels = VehicleRequest::whereBetween('created_at', [$min, $max])
                        ->where([
                            'type' => 'Personnel',
                            'is_closed' => 'No'
                        ])
                        ->count('id');

                    $data['vehicles'][] = $vehicles;
                    $data['materials'][] = $materials;
                    $data['personels'][] = $personels;
                    $data['labels'][] = Utils::my_day($min);

                    $rec['day'] = Utils::my_date_1($min);
                    $rec['requests'] = VehicleRequest::whereBetween('created_at', [$min, $max])
                        ->where('is_closed', 'No')
                        ->count('id');
                    $rec['progress'] = 0;

                    $data['records'][] = $rec;
                }

                $data['records'] = array_reverse($data['records']);
                $column->append(view('admin.charts.requests-frequency', $data));
            });

            $row->column(6, function (Column $column) {

                $u = Admin::user();
                $vehicles_out_ids = VehicleRequest::where('security_exit_status', 'Approved')
                    ->where('type', 'Vehicle')
                    ->where('security_return_status', 'Pending')
                    ->where('is_closed', 'No')
                    ->get()->pluck('vehicle_id')->toArray();
                $all_cars_ids = Vehicle::where([])->get()->pluck('id')->toArray();
                $vehicles_in_ids = array_diff($all_cars_ids, $vehicles_out_ids);

                $data['vehicles_out_count'] = count($vehicles_out_ids);
                $data['vehicles_in_count'] = count($vehicles_in_ids);
                $data['labels'] = ['Vehicles out (' . $data['vehicles_out_count'] . ')', 'Vehicles Available (' . $data['vehicles_in_count'] . ')'];

                $column->append(view('admin.charts.vehicles-availability', $data));
            });
        });
        return $content;
    }
}
