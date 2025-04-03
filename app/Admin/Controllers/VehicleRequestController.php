<?php

namespace App\Admin\Controllers;

use App\Models\Utils;
use App\Models\VehicleRequest;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class VehicleRequestController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Vehicle Exit Requests';
    //set title for the page using function


    protected function title()
    {
        $segs = request()->segments();

        if (in_array('vehicle-requests', $segs)) {

            $title = 'Vehicle Exit Requests';
        } elseif (in_array('leave-requests', $segs)) {
            $title = 'Leave Requests';
        } elseif (in_array('materials-requests', $segs)) {
            $title = 'Materials Requests';
        } else if (in_array('all-requests', $segs)) {
            $title = 'All Requests';
        } else if (in_array('archived-requests', $segs)) {
            $title = 'Archived Requests';
        } else {
            throw new \Exception("Invalid route");
        }
        return   $title;
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new VehicleRequest());
        $conds = [];
        $segs = request()->segments();

        $u = Admin::user();
        //is_closed archived-requests

        if (in_array('vehicle-requests', $segs)) {
            if ($u->isRole('hod')) {
                $conds['hod_status'] = 'Pending';
            }
            if ($u->isRole('gm')) {
                $conds['hod_status'] = 'Approved';
                $conds['gm_status'] = 'Pending';
            }

            if ($u->isRole('security')) {
                $conds['gm_status'] = 'Approved';
                $conds['security_exit_status'] = 'Pending';
                $conds['type'] = 'Vehicle';
            }
        } else if (in_array('all-requests', $segs)) {
            if ($u->isRole('employee')) {
                $conds['applicant_id'] = $u->id;
            }
        } else if (in_array('archived-requests', $segs)) {
            $conds['is_closed'] = 'Yes';
        } else if (in_array('materials-requests', $segs)) {
            $conds['type'] = 'Materials';
        } else if (in_array('leave-requests', $segs)) {
            $conds['type'] = 'Personnel';
        } else {
            throw new \Exception("Invalid route");
        }

        if (
            !in_array('vehicle-requests', $segs) &&
            !in_array('materials-requests', $segs) &&
            !in_array('leave-requests', $segs)
        ) {
            $grid->disableCreateButton();
        }

        if (!$u->isRole('gm')) {
            $grid->disableExport();
        }

        if ($u->isRole('employee')) {
            $conds['applicant_id'] = $u->id;
        }
        if ($u->isRole('security')) {
            $conds['gm_status'] = 'Approved';
        }

        $grid->model()
            ->where($conds)
            ->orderBy('id', 'desc');
        $grid->disableBatchActions();
        $grid->column('created_at', __('Date'))->display(function ($created_at) {
            return date('d-m-Y', strtotime($created_at));
        })->sortable();
        $grid->column('applicant_id', __('Applicant'))
            ->display(function ($applicant_id) {
                if ($this->applicant) {
                    return $this->applicant->name;
                } else {
                    return 'N/A';
                }
            })->sortable();
        $grid->column('vehicle_id', __('Items requested for'))
            ->display(function ($vehicle_id) {
                return $this->getTitle();
            })->sortable();
        // type request type
        $grid->column('type', __('Type'))->sortable()
            ->dot([
                'Vehicle' => 'info',
                'Materials' => 'success',
                'Personnel' => 'warning',
            ])->filter([
                'Vehicle' => 'Vehicle',
                'Materials' => 'Materials',
                'Personnel' => 'Personnel',
            ]);

        $grid->column('requested_departure_time', __('Requested Departure Time'))
            ->display(function ($requested_departure_time) {
                return date('d-m-Y H:i:s', strtotime($requested_departure_time));
            })->sortable();
        $grid->column('requested_return_time', __('Requested Return Time'))
            ->display(function ($requested_return_time) {
                return date('d-m-Y H:i:s', strtotime($requested_return_time));
            })->sortable();
        $grid->column('actual_return_time', __('Actual Return Time'))
            ->display(function ($actual_return_time) {
                return date('d-m-Y H:i:s', strtotime($actual_return_time));
            })->sortable()
            ->hide();
        $grid->column('actual_departure_time', __('Actual Departure Time'))
            ->display(function ($actual_departure_time) {
                return date('d-m-Y H:i:s', strtotime($actual_departure_time));
            })->sortable()
            ->hide();
        $grid->column('destination', __('Destination'))
            ->display(function ($destination) {
                return $destination;
            })->sortable()->hide();
        $grid->column('justification', __('Justification'))
            ->display(function ($justification) {
                return $justification;
            })->sortable();
        /*   $grid->column('status', __('Status'))
            ->label([
                'Pending' => 'warning',
                'Approved' => 'success',
                'Rejected' => 'danger',
            ])->sortable(); */
        $grid->column('hod_status', __('HOD Status'))
            ->label([
                'Pending' => 'warning',
                'Approved' => 'success',
                'Rejected' => 'danger',
            ])->sortable();
        $grid->column('gm_status', __('GM Status'))
            ->label([
                'Pending' => 'warning',
                'Approved' => 'success',
                'Rejected' => 'danger',
            ])->sortable();
        $grid->column('security_exit_status', __('Exit Status'))
            ->display(function ($value) {
                return $value;
            })
            ->label([
                'Pending' => 'warning',
                'Approved' => 'success',
                'Rejected' => 'danger',
            ])
            ->sortable();

        $grid->column('security_return_status', __('Return status'))
            ->display(function ($value) {
                return $value;
            })
            ->label([
                'Pending' => 'warning',
                'Approved' => 'success',
                'Rejected' => 'danger',
            ])
            ->sortable();

        $grid->column('exit_state', __('Exit state'))
            ->display(function ($value) {
                return $value;
            })
            ->sortable()->hide();
        $grid->column('return_state', __('Return state'))
            ->display(function ($value) {
                return $value;
            })
            ->sortable()
            ->hide();

        $grid->column('over_stayed', __('Over stayed'))
            ->display(function ($value) {
                return $value;
            })
            ->sortable()->hide();



        $grid->column('exit_comment', __('Exit comment'))
            ->display(function ($value) {
                return $value;
            })
            ->sortable()
            ->hide();

        $grid->column('return_comment', __('Return comment'))
            ->display(function ($value) {
                return $value;
            })
            ->sortable()->hide();

        $grid->column('hod_comment', __('Hod comment'))
            ->display(function ($value) {
                return $value;
            })
            ->sortable()->hide();

        $grid->column('gm_comment', __('Gm comment'))
            ->display(function ($value) {
                return $value;
            })
            ->sortable()->hide();

        //print
        $grid->column('print', __('Print'))->display(function () {
            //if gm not appoved, return N/A
            if ($this->gm_status != 'Approved') {
                return 'N/A';
            }
            $url = url('print-gatepass') . '?gatepass_id=' . $this->id;
            return '<a href="' . $url . '" target="_blank" class="btn btn-xs btn-primary">Print</a>';
        })->width(100);


        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $vehicleRequest = VehicleRequest::findOrFail($id);
        $show = new Show($vehicleRequest);

        // Basic Information
        $show->field('id', __('ID'))->display(function ($id) {
            return $id;
        });
        $show->field('created_at', __('Created At'))->display(function ($created_at) {
            return date('d-m-Y', strtotime($created_at));
        });
        $show->field('updated_at', __('Updated At'))->display(function ($updated_at) {
            return date('d-m-Y', strtotime($updated_at));
        });

        // Applicant and Vehicle Information
        $show->field('applicant_id', __('Applicant'))->display(function ($applicant_id) {
            return $this->applicant ? $this->applicant->name : 'N/A';
        });
        $show->field('vehicle_id', __('Vehicle'))->display(function ($vehicle_id) {
            if ($this->vehicle) {
                return $this->vehicle->registration_number . ' - ' . $this->vehicle->brand
                    . ' - ' . $this->vehicle->model . ' - ' . $this->vehicle->vehicle_type;
            }
            return 'N/A';
        });

        // Time Information
        $show->field('requested_departure_time', __('Requested Departure Time'))->display(function ($time) {
            return date('d-m-Y H:i:s', strtotime($time));
        });
        $show->field('requested_return_time', __('Requested Return Time'))->display(function ($time) {
            return date('d-m-Y H:i:s', strtotime($time));
        });
        $show->field('actual_departure_time', __('Actual Departure Time'))->display(function ($time) {
            return $time ? date('d-m-Y H:i:s', strtotime($time)) : 'N/A';
        });
        $show->field('actual_return_time', __('Actual Return Time'))->display(function ($time) {
            return $time ? date('d-m-Y H:i:s', strtotime($time)) : 'N/A';
        });

        // Request Details
        $show->field('destination', __('Destination'))->display(function ($destination) {
            return $destination ?: 'N/A';
        });
        $show->field('justification', __('Justification'))->display(function ($justification) {
            return $justification ?: 'N/A';
        });
        $show->field('status', __('Status'))->display(function ($status) {
            return $status ?: 'N/A';
        });

        // Approval and Security Statuses
        $show->field('hod_status', __('HOD Status'))->display(function ($status) {
            return $status ?: 'N/A';
        });
        $show->field('gm_status', __('GM Status'))->display(function ($status) {
            return $status ?: 'N/A';
        });
        $show->field('security_exit_status', __('Security Exit Status'))->display(function ($status) {
            return $status ?: 'N/A';
        });
        $show->field('security_return_status', __('Security Return Status'))->display(function ($status) {
            return $status ?: 'N/A';
        });

        // Additional Details
        $show->field('return_state', __('Return State'))->display(function ($state) {
            return $state ?: 'N/A';
        });
        $show->field('over_stayed', __('Over Stayed'))->display(function ($value) {
            return $value ?: 'N/A';
        });
        $show->field('exit_state', __('Exit State'))->display(function ($state) {
            return $state ?: 'N/A';
        });
        $show->field('exit_comment', __('Exit Comment'))->display(function ($comment) {
            return $comment ?: 'N/A';
        });
        $show->field('return_comment', __('Return Comment'))->display(function ($comment) {
            return $comment ?: 'N/A';
        });
        $show->field('hod_comment', __('HOD Comment'))->display(function ($comment) {
            return $comment ?: 'N/A';
        });
        $show->field('gm_comment', __('GM Comment'))->display(function ($comment) {
            return $comment ?: 'N/A';
        });



        return $show;
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new VehicleRequest());
        $segs = request()->segments();
        $name_of_range = "";

        if ($form->isCreating()) {
            $u = Admin::user();

            $form->hidden('applicant_id', __('Applicant'))->default($u->id);
            $form->display('applicant_name', __('Applicant'))->default($u->name);


            if (in_array('vehicle-requests', $segs)) {
                $name_of_range = "Departure and Return time";
                $form->hidden('type', __('Type'))->default('Vehicle');
                $form->select('vehicle_id', __('Vehicle'))->options(Utils::get_dropdown(\App\Models\Vehicle::class, ['registration_number', 'id', 'brand', 'model', 'vehicle_type']))->rules('required');
                $users = \App\Models\User::where('id', '!=', $u->id)->get();
                //has many drivers 
                $form->hasMany('drivers', 'Click on "NEW" to add Driver', function (Form\NestedForm $form) use ($users) {
                    $form->select('driver_id', 'Driver')->options($users->pluck('name', 'id'))->rules('required');
                });
                $form->divider();
                $form->textarea('materials_requested', 'Passenger information (name, contact) seperated by comma.')->rules('required');
            }
            if (in_array('materials-requests', $segs)) {
                $name_of_range  = "Requested time and Return time";
                $form->hidden('type', __('Type'))->default('Materials');
                //many MaterialItem
                $form->hasMany('materialItems', 'Click on "NEW" to add Material Item Requested For', function (Form\NestedForm $form) {
                    $form->text('name', 'Material Name')->rules('required');
                    $form->decimal('quantity', 'Material Quantity')->rules('required');
                    $form->text('unit', 'Unit')->rules('required');
                    $form->image('description', 'Photo');
                });
            } else if (in_array('leave-requests', $segs)) {
                $name_of_range  = "Leave start time and Leave end time";
                $form->hidden('type', __('Type'))->default('Personnel');
                $form->radio('materials_requested', 'Leave Type')->options(['Annual' => 'Annual', 'Sick' => 'Sick', 'Maternity' => 'Maternity', 'Paternity' => 'Paternity', 'Study' => 'Study', 'Compassionate' => 'Compassionate', 'Special' => 'Special'])->rules('required');
            }

            $form->divider();
            $form->datetimeRange('requested_departure_time', 'requested_return_time', $name_of_range)->rules('required');
            $form->text('destination', __('Destination'))->rules('required');
            $form->textarea('justification', __('Justification'))->rules('required');
            $form->hidden('status', __('Status'))->default('Pending');
            $form->hidden('hod_status', __('Status'))->default('Pending');
            $form->hidden('gm_status', __('Status'))->default('Pending');
            $form->hidden('security_exit_status', __('Status'))->default('Pending');
            $form->hidden('security_return_status', __('Status'))->default('Pending');
        } else {
            $id = request()->segments()[1];
            $record = VehicleRequest::find($id);

            if ($record == null) {
                throw new \Exception("Record not found");
            }
            $form->display('applicant_name', __('Applicant name'))->default($record->applicant->name);

            //if type is vehicle
            if ($record->type == 'Vehicle') {
                $form->display('vehicle_name', __('Vehicle'))->default($record->vehicle->registration_number . ' - ' . $record->vehicle->brand . ' - ' . $record->vehicle->model . ' - ' . $record->vehicle->vehicle_type);
            } else if ($record->type == 'Materials') {
                /* $marerials = "";
                foreach ($record->materialItems as $item) {
                    $marerials .= $item->name . " - " . $item->quantity . " " . $item->unit . ", ";
                }
                $form->display('materials_requested', __('Materials requested'))->default($marerials);  */

                $form->hasMany('materialItems', 'Click on "NEW" to add Material Item Requested For', function (Form\NestedForm $form) {
                    $form->text('type', 'Material Requested')->rules('required');
                    $form->decimal('quantity', 'Material Quantity')->rules('required');
                    $form->text('unit', 'Unit')->rules('required');
                    $form->image('description', 'Photo');
                });
            } else if ($record->type == 'Personnel') {
                $form->display('leave_type', __('Leave type'))->default($record->materials_requested);
            }

            $u = Admin::user();
            if ($u->isRole('hod')) {
                $form->radio('hod_status', 'HOD Status')->options([
                    'Pending' => 'Pending',
                    'Approved' => 'Approved',
                    'Rejected' => 'Rejected'
                ])->rules('required');
                $form->text('hod_comment', 'HOD Remarks');
            }
            if ($u->isRole('gm') && $record->hod_status == 'Approved') {
                $form->radio('gm_status', 'GM Status')->options([
                    'Pending' => 'Pending',
                    'Approved' => 'Approved',
                    'Rejected' => 'Rejected'
                ])->rules('required');
                $form->text('gm_comment', 'GM Remarks');
            }
            if ($u->isRole('security') && $record->gm_status == 'Approved') {
                $form->radio('security_exit_status', 'Security Exit Status')->options([
                    'Pending' => 'Pending',
                    'Approved' => 'Approved',
                    'Rejected' => 'Rejected'
                ])->rules('required')
                    ->when('Approved', function (Form $form) {
                        $form->datetime('actual_departure_time', 'Actual departure time')->rules('required');
                        $form->radio('exit_state', 'Exit State')->options(['Good' => 'Good', 'Fair' => 'Fair', 'Bad' => 'Bad'])->rules('required');
                    });
            }

            if ($u->isRole('security') && $record->security_exit_status == 'Approved') {
                $form->radio('security_return_status', 'Security Return Status')->options([
                    'Pending' => 'Pending',
                    'Approved' => 'Approved',
                    'Rejected' => 'Rejected'
                ])->rules('required')
                    ->when('Approved', function (Form $form) {
                        $form->datetime('actual_return_time', 'Actual return time')->rules('required');
                        $form->radio('return_state', 'Return State')->options(['Good' => 'Good', 'Fair' => 'Fair', 'Bad' => 'Bad'])->rules('required');
                        $form->radio('over_stayed', 'Over Stayed')->options(['Yes' => 'Yes', 'No' => 'No'])->rules('required');
                        $form->text('return_comment', 'Return Remarks');
                    });
                $form->radio('is_closed', 'Archive/Close This Request')->options(['Yes' => 'Yes', 'No' => 'No'])->rules('required');
            }
        }


        $form->disableCreatingCheck();
        $form->disableViewCheck();
        $form->disableReset();

        return $form;
    }
}
