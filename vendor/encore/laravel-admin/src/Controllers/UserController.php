<?php

namespace Encore\Admin\Controllers;

use App\Models\Company;
use App\Models\Departmet;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Hash;

class UserController extends AdminController
{
    /**
     * {@inheritdoc}
     */
    protected function title()
    {
        return 'Users';
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $userModel = config('admin.database.users_model');

        $grid = new Grid(new $userModel());

        //add filter options
        $grid->filter(function ($filter) {

            $filter->disableIdFilter();
            //by department
            $departments = Departmet::all()->pluck('name', 'id');
            $filter->equal('department_id', 'Department')
                ->select($departments);

            //by company
            $companies = Company::all()->pluck('name', 'id');

            $filter->equal('company_id', 'Company')
                ->select($companies);
        });

        $grid->quickSearch('name', 'username', 'phone_number')->placeholder('Search by name, username');

        $grid->disableBatchActions();
        $grid->column('id', 'ID')->sortable();
        $grid->column('avatar', __('Photo'))
            ->width(80)
            ->lightbox(['width' => 60, 'height' => 60])
            ->hide();

        $grid->column('email', 'email address')->sortable();
        $grid->model()->orderBy('id', 'desc');
        $grid->column('name', trans('admin.name'))->sortable();
        $grid->column('sex', 'Gender')->hide();

        //department_id
        $grid->column('company_id', 'Company ')
            ->display(function ($department_id) {
                if ($this->company) {
                    return $this->company->name;
                } else {
                    return 'N/A';
                }
            })->sortable();

        $grid->column('department_id', 'Department')
            ->display(function ($department_id) {
                if ($this->department) {
                    return $this->department->name;
                } else {
                    return 'N/A';
                }
            });
        
        //Postion
        $grid->column('position', 'Position')
            ->display(function () {
                return $this->position ?? 'N/A';
            })->sortable();

        $grid->column('roles', trans('admin.roles'))
            ->pluck('name')->label();
        $grid->column('created_at', 'Registered')->sortable()
            ->hide();

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            if ($actions->getKey() == 1) {
                $actions->disableDelete();
            }
        });

        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $actions) {
                //$actions->disableDelete();
            });
        });

        //action send message 
        $grid->column('send_message', 'Send Password Reset Mail')
            ->display(function () {
                $url = url('send-new-password?user_id=' . $this->id);
                return '<a href="' . $url . '" class="btn btn-xs btn-primary" target="_blank">Send Password Reset Mail</a>';
            });



        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        $userModel = config('admin.database.users_model');

        $show = new Show($userModel::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('username', trans('admin.username'));
        $show->field('name', trans('admin.name'));
        $show->field('roles', trans('admin.roles'))->as(function ($roles) {
            return $roles->pluck('name');
        })->label();
        $show->field('permissions', trans('admin.permissions'))->as(function ($permission) {
            return $permission->pluck('name');
        })->label();
        $show->field('created_at', 'Registered');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $userModel = config('admin.database.users_model');
        $permissionModel = config('admin.database.permissions_model');
        $roleModel = config('admin.database.roles_model');

        $form = new Form(new $userModel());

        $userTable = config('admin.database.users_table');
        $connection = config('admin.database.connection');

        $form->display('id', 'ID');
        $form->text('email', 'Email Address')
            ->creationRules(['required', "unique:{$connection}.{$userTable}"])
            ->updateRules(['required', "unique:{$connection}.{$userTable},email,{{id}}"]);

        $form->text('name', trans('admin.name'))->rules('required');
        $form->radio('sex', 'Gender')
            ->options(['Male' => 'Male', 'Female' => 'Female']);
        $departments = Departmet::all()->pluck('name', 'id');
        //companies
        $companies = Company::all()->pluck('name', 'id');
        $form->select('company_id', 'Company')->options($companies)->rules('required');
        //departments
        $form->select('department_id', 'Department')->options($departments)->rules('required');

        //pos

        //position
        $form->text('position', 'Position')
            ->help('Enter the position of the user in the company.')
            ->rules('nullable|max:255'); 

        $form->file('whatsapp', 'Signature file')
            ->help('Upload your signature file. It will be used in the system for signing documents.')
            ->rules('nullable|mimes:jpg,jpeg,png,gif,svg,bmp|max:2048')
            ->uniqueName()
            ->removable();

        /* 
                        $table->string('change_password')->default('No')->nullable();
            $table->string('has_changed_password')->default('No')->nullable();
            $table->string('notify_account_created_by_email')->default('Yes')->nullable();
            */

        $form->checkbox('roles', trans('admin.roles'))->options($roleModel::all()->pluck('name', 'id'))
        ->stacked();
        $form->radio('change_password', 'Change Password')
            ->options(['Yes' => 'Yes', 'No' => 'No'])
            ->default('No')
            ->rules('required')
            ->when('Yes', function (Form $form) {
                $form->password('password', trans('admin.password'))->rules('required|confirmed');
                $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
                    ->default(function ($form) {
                        return $form->model()->password;
                    });
            });

        //notify_account_created_by_email notify user by email when account is created

        if ($form->isCreating()) {
            $form->radio('notify_account_created_by_email', 'Notify User by Email on Account Creation') 
                ->options(['Yes' => 'Yes', 'No' => 'No'])
                ->default('Yes')
                ->rules('required');
        }

        $form->ignore(['password_confirmation']);


        /*         $form->multipleSelect('permissions', trans('admin.permissions'))->options($permissionModel::all()->pluck('name', 'id')); */

        /*         $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));
 */
        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = Hash::make($form->password);
            }

            if ($form->password == null || $form->password == '') {
                //set password default as 4321
                $form->password = Hash::make('4321');
            }
        });

        return $form;
    }
}
