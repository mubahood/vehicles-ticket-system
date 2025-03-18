<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingProviderAccreditationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_provider_accreditations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(\App\Models\User::class)->nullable();
            $table->text('application_date')->nullable();

            // Establishment Details
            $table->text('establishment_name')->nullable();
            $table->text('street_address')->nullable();
            $table->text('postal_address')->nullable();
            $table->text('website_address')->nullable();

            // Contact Details
            $table->text('contact_person')->nullable();
            $table->text('telephone_number')->nullable();
            $table->text('mobile_number')->nullable();
            $table->text('email_address')->nullable();

            // Training Provider Application Details
            $table->text('applied_fields')->nullable();
            $table->text('applied_levels')->nullable();
            $table->text('number_of_registered_trainers')->nullable();
            $table->text('number_of_accredited_trainers')->nullable();
            $table->text('official_examination_awarding_body')->nullable();
            $table->text('number_of_trainees_male_pt')->nullable();
            $table->text('number_of_trainees_male_ft')->nullable();
            $table->text('number_of_trainees_female_pt')->nullable();
            $table->text('number_of_trainees_female_ft')->nullable();

            // List of Trainers
            $table->text('trainer_name')->nullable();
            $table->text('naqaa_accreditation_number')->nullable();
            $table->text('trainer_qualifications')->nullable();
            $table->text('accredited_areas')->nullable();

            // Enrolment
            $table->text('enrolment_dates')->nullable();

            // Declaration
            $table->text('declaration_principal_ceo_contact_person_name')->nullable();
            $table->text('declaration_principal_ceo_contact_person_signature')->nullable();
            $table->text('declaration_principal_ceo_contact_person_date')->nullable();
            $table->text('declaration_chairperson_name')->nullable();
            $table->text('declaration_chairperson_signature')->nullable();
            $table->text('declaration_chairperson_date')->nullable();

            // Submission of Application
            $table->text('submitted_by_name')->nullable();
            $table->text('submitted_by_signature')->nullable();
            $table->text('submitted_by_date')->nullable();
            $table->text('received_by_name')->nullable();
            $table->text('received_by_signature')->nullable();
            $table->text('received_by_date')->nullable();

            // Accreditation Standards & Supporting Evidence
            $table->text('adequate_facilities')->nullable();
            $table->text('clean_water_supply')->nullable();
            $table->text('adequate_power_supply')->nullable();
            $table->text('library_resource_centre')->nullable();
            $table->text('staffroom')->nullable();
            $table->text('centre_manager_office')->nullable();
            $table->text('student_recreational_waiting_area')->nullable();
            $table->text('sitting_arrangement')->nullable();
            $table->text('relevant_teaching_materials')->nullable();
            $table->text('relevant_tools')->nullable();
            $table->text('relevant_equipment')->nullable();

            // Governance and Management
            $table->text('functional_board_of_directors')->nullable();
            $table->text('management_structure')->nullable();
            $table->text('service_rules')->nullable();
            $table->text('staff_appointment_letters')->nullable();
            $table->text('payment_evidence')->nullable();
            $table->text('filing_recording_system')->nullable();
            $table->text('proper_storage')->nullable();
            $table->text('adequate_furniture')->nullable();

            // Qualification of Teaching Staff
            $table->text('registered_accredited_trainers')->nullable();
            $table->text('tutors_teaching_qualification')->nullable();
            $table->text('teaching_certificate_or_inservice_training')->nullable();
            $table->text('proof_of_experience')->nullable();
            $table->text('volunteers_training')->nullable();
            $table->text('tutors_appraisal_mechanism')->nullable();

            // Relevance of Programmes
            $table->text('training_request_response')->nullable();
            $table->text('employment_demand_response')->nullable();
            $table->text('curriculum_suitability')->nullable();
            $table->text('curriculum_practice_theory_balance')->nullable();
            $table->text('practical_sessions')->nullable();
            $table->text('timetable')->nullable();
            $table->text('lesson_plans')->nullable();
            $table->text('teaching_aids')->nullable();
            $table->text('reference_materials')->nullable();
            $table->text('code_of_conduct')->nullable();
            $table->text('guidance_counseling_officer')->nullable();
            $table->text('learner_grievance_procedures')->nullable();
            $table->text('confidentiality_guarantee')->nullable();
            $table->text('facility_accessibility')->nullable();
            $table->text('tuition_fee_policy')->nullable();
            $table->text('learning_environment')->nullable();
            $table->text('entry_requirements')->nullable();
            $table->text('health_safety_policy')->nullable();
            $table->text('health_safety_facilities')->nullable();

            // Checklist for Applicant
            $table->text('course_syllabus')->nullable();
            $table->text('course_timetable')->nullable();
            $table->text('course_reference_materials')->nullable();
            $table->text('entry_requirements_document')->nullable();

            // Fees
            $table->text('non_refundable_accreditation_fee')->nullable();
            $table->text('refundable_accreditation_fee_per_programme')->nullable();
            $table->text('accreditation_fee_refund_policy')->nullable();
            $table->text('status')->nullable()->default('Pending');
            $table->string('payment_status')->nullable()->default('Not Paid');
            $table->text('payment_date')->nullable();
            $table->text('payment_reference')->nullable();
            $table->text('payment_details')->nullable();
            $table->text('payment_amount')->nullable();
            $table->text('payment_method')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('training_provider_accreditations');
    }
}
