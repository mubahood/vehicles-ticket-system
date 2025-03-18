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

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new VehicleRequest());

        $grid->model()->orderBy('id', 'desc');
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
        $grid->column('vehicle_id', __('Vehicle'))
            ->display(function ($vehicle_id) {
                if ($this->vehicle) {
                    return $this->vehicle->registration_number . ' - ' . $this->vehicle->brand . ' - ' . $this->vehicle->model . ' - ' . $this->vehicle->vehicle_type;
                } else {
                    return 'N/A';
                }
            })->sortable();

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
            })->sortable();
        $grid->column('actual_departure_time', __('Actual Departure Time'))
            ->display(function ($actual_departure_time) {
                return date('d-m-Y H:i:s', strtotime($actual_departure_time));
            })->sortable();
        $grid->column('destination', __('Destination'))
            ->display(function ($destination) {
                return $destination;
            })->sortable();
        $grid->column('justification', __('Justification'))
            ->display(function ($justification) {
                return $justification;
            })->sortable();
        /*   $grid->column('status', __('Status'))
            ->label([
                'Pending' => 'info',
                'Approved' => 'success',
                'Rejected' => 'danger',
            ])->sortable(); */
        $grid->column('hod_status', __('Hod Status'))
            ->label([
                'Pending' => 'info',
                'Approved' => 'success',
                'Rejected' => 'danger',
            ])->sortable();
        $grid->column('gm_status', __('GM Status'))
            ->label([
                'Pending' => 'info',
                'Approved' => 'success',
                'Rejected' => 'danger',
            ])->sortable();
        $grid->column('security_exit_status', __('Security Exit Status'))
            ->display(function ($value) {
                return $value;
            })
            ->label([
                'Pending' => 'info',
                'Approved' => 'success',
                'Rejected' => 'danger',
            ])
            ->sortable();

        $grid->column('security_return_status', __('Security return status'))
            ->display(function ($value) {
                return $value;
            })
            ->label([
                'Not Returned' => 'info',
                'Returned' => 'success',
                'Rejected' => 'danger',
            ])
            ->sortable();

        $grid->column('return_state', __('Return state'))
            ->display(function ($value) {
                return $value;
            })
            ->sortable();

        $grid->column('over_stayed', __('Over stayed'))
            ->display(function ($value) {
                return $value;
            })
            ->sortable();

        $grid->column('exit_state', __('Exit state'))
            ->display(function ($value) {
                return $value;
            })
            ->sortable();

        $grid->column('exit_comment', __('Exit comment'))
            ->display(function ($value) {
                return $value;
            })
            ->sortable();

        $grid->column('return_comment', __('Return comment'))
            ->display(function ($value) {
                return $value;
            })
            ->sortable();

        $grid->column('hod_comment', __('Hod comment'))
            ->display(function ($value) {
                return $value;
            })
            ->sortable();

        $grid->column('gm_comment', __('Gm comment'))
            ->display(function ($value) {
                return $value;
            })
            ->sortable();

        $grid->column('mail_sent_to_hod', __('Mail sent to hod'))
            ->display(function ($value) {
                return $value;
            })
            ->sortable();

        $grid->column('mail_sent_to_gm', __('Mail sent to gm'))
            ->display(function ($value) {
                return $value;
            })
            ->sortable();

        $grid->column('mail_sent_to_security_exit', __('Mail sent to security exit'))
            ->display(function ($value) {
                return $value;
            })
            ->sortable();

        $grid->column('mail_sent_to_security_return', __('Mail sent to security return'))
            ->display(function ($value) {
                return $value;
            })
            ->sortable();

        $grid->column('mail_sent_to_applicant_on_hod_approval', __('Mail sent to applicant on hod approval'))
            ->display(function ($value) {
                return $value;
            })
            ->sortable();

        $grid->column('mail_sent_to_applicant_on_gm_approval', __('Mail sent to applicant on gm approval'))
            ->display(function ($value) {
                return $value;
            })
            ->sortable();

        $grid->column('mail_sent_to_applicant_on_security_exit_approval', __('Mail sent to applicant on security exit approval'))
            ->display(function ($value) {
                return $value;
            })
            ->sortable();

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(VehicleRequest::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('vehicle_id', __('Vehicle id'));
        $show->field('applicant_id', __('Applicant id'));
        $show->field('requested_departure_time', __('Requested departure time'));
        $show->field('requested_return_time', __('Requested return time'));
        $show->field('actual_return_time', __('Actual return time'));
        $show->field('actual_departure_time', __('Actual departure time'));
        $show->field('destination', __('Destination'));
        $show->field('justification', __('Justification'));
        $show->field('status', __('Status'));
        $show->field('hod_status', __('Hod status'));
        $show->field('gm_status', __('Gm status'));
        $show->field('security_exit_status', __('Security exit status'));
        $show->field('security_return_status', __('Security return status'));
        $show->field('return_state', __('Return state'));
        $show->field('over_stayed', __('Over stayed'));
        $show->field('exit_state', __('Exit state'));
        $show->field('exit_comment', __('Exit comment'));
        $show->field('return_comment', __('Return comment'));
        $show->field('hod_comment', __('Hod comment'));
        $show->field('gm_comment', __('Gm comment'));
        $show->field('mail_sent_to_hod', __('Mail sent to hod'));
        $show->field('mail_sent_to_gm', __('Mail sent to gm'));
        $show->field('mail_sent_to_security_exit', __('Mail sent to security exit'));
        $show->field('mail_sent_to_security_return', __('Mail sent to security return'));
        $show->field('mail_sent_to_applicant_on_hod_approval', __('Mail sent to applicant on hod approval'));
        $show->field('mail_sent_to_applicant_on_gm_approval', __('Mail sent to applicant on gm approval'));
        $show->field('mail_sent_to_applicant_on_security_exit_approval', __('Mail sent to applicant on security exit approval'));

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


        if ($form->isCreating()) {
            $u = Admin::user();

            $form->hidden('applicant_id', __('Applicant'))->default($u->id);
            $form->display('applicant_name', __('Applicant'))->default($u->name);
            $form->select('vehicle_id', __('Vehicle'))->options(Utils::get_dropdown(\App\Models\Vehicle::class, ['registration_number', 'id', 'brand', 'model', 'vehicle_type']))->rules('required');
            $form->datetimeRange('requested_departure_time', 'requested_return_time', __('Departure and return time'))->rules('required');
            $form->text('destination', __('Destination'))->rules('required');
            $form->textarea('justification', __('Justification'))->rules('required');
            $form->hidden('status', __('Status'))->default('Pending');
            $form->hidden('hod_status', __('Status'))->default('Pending');
            $form->hidden('gm_status', __('Status'))->default('Pending');
            $form->hidden('security_exit_status', __('Status'))->default('Pending');
            $form->hidden('security_return_status', __('Status'))->default('Pending');
        } else {
            $record = VehicleRequest::find(request()->route('vehicle_request'));
            if ($record == null) {
                throw new \Exception("Record not found");
            }
            $form->display('applicant_name', __('Applicant name'))->default($record->applicant->name);
            $form->display('vehicle_name', __('Vehicle'))->default($record->vehicle->registration_number . ' - ' . $record->vehicle->brand . ' - ' . $record->vehicle->model . ' - ' . $record->vehicle->vehicle_type);
            $u = Admin::user();
            if ($u->isRole('hod')) {
                $form->radio('hod_status', 'HOD Status')->options(['Approved' => 'Approved', 'Rejected' => 'Rejected'])->rules('required');
                $form->textarea('hod_comment', 'HOD Remarks')->rules('required');
            }
            if ($u->isRole('gm') && $record->hod_status == 'Approved') {
                $form->radio('gm_status', 'GM Status')->options(['Approved' => 'Approved', 'Rejected' => 'Rejected'])->rules('required');
                $form->textarea('gm_comment', 'GM Remarks')->rules('required');
            }
            if ($u->isRole('security')) {
                $form->radio('security_exit_status', 'Security Exit Status')->options(['Approved' => 'Approved', 'Rejected' => 'Rejected'])->rules('required')
                    ->when('Approved', function (Form $form) {
                        $form->datetime('actual_departure_time', 'Actual departure time')->rules('required');
                        $form->radio('exit_state', 'Exit State')->options(['Good' => 'Good', 'Fair' => 'Fair', 'Bad' => 'Bad'])->rules('required');
                    });
            }

            if ($u->isRole('security') && $record->security_exit_status == 'Approved') {
                $form->radio('security_return_status', 'Security Return Status')->options(['Returned' => 'Returned', 'Not Returned' => 'Not Returned'])->rules('required')
                    ->default('Not Returned')
                    ->when('Returned', function (Form $form) {
                        $form->datetime('actual_return_time', 'Actual return time')->rules('required');
                        $form->radio('return_state', 'Return State')->options(['Good' => 'Good', 'Fair' => 'Fair', 'Bad' => 'Bad'])->rules('required');
                        $form->radio('over_stayed', 'Over Stayed')->options(['Yes' => 'Yes', 'No' => 'No'])->rules('required');
                        $form->textarea('return_comment', 'Return Remarks')->rules('required');
                    });
            }
        }


        $form->disableCreatingCheck();
        $form->disableViewCheck();
        $form->disableReset();

        return $form;
    }
}
