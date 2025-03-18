<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleRequest extends Model
{
    use HasFactory;

    //belongs to applicant
    public function applicant()
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    //belongs to vehicle
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    //boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model = self::do_prepare($model);
        });

        static::updating(function ($model) {
            $model = self::do_prepare($model);
        });
    }

    public static function do_prepare($model)
    {
        $vehicle = Vehicle::find($model->vehicle_id);
        if ($vehicle == null) {
            throw new \Exception("Vehicle not found");
        }

        $applicant = User::find($model->applicant_id);
        if ($applicant == null) {
            throw new \Exception("Applicant not found");
        }

        if ($model->hod_status != 'Approved') {
            $model->gm_status = 'Pending';
            $model->security_exit_status = 'Pending';
            $model->security_return_status = 'Pending';
        }

        if ($model->gm_status != 'Approved') {
            $model->security_exit_status = 'Pending';
            $model->security_return_status = 'Pending';
        }

        if ($model->security_exit_status != 'Approved') {
            $model->security_return_status = 'Pending';
        }

        if ($model->security_return_status == 'Approved') {
            $model->return_state = 'Returned';
        }

        if ($model->security_exit_status == 'Approved') {
            $model->exit_state = 'Approved';
        }

        return $model;
    }
}
