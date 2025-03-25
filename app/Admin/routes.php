<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->resource('departmets', DepartmetController::class);
    $router->resource('vehicles', VehicleController::class);
    $router->resource('vehicle-requests', VehicleRequestController::class);
    $router->resource('materials-requests', VehicleRequestController::class);
    $router->resource('leave-requests', VehicleRequestController::class);
    $router->resource('all-requests', VehicleRequestController::class);
    $router->resource('archived-requests', VehicleRequestController::class);
    $router->resource('companies', CompanyController::class);

    
    $router->resource('training-provider-accreditations', TrainingProviderAccreditationController::class);


    //$router->resource('/', RentingController::class); 
    $router->get('/', 'HomeController@index')->name('home');
    $router->get('/calendar', 'HomeController@calendar')->name('calendar');

    $router->resource('applications-filing', ApplicationController::class);
    $router->resource('applications-defense', ApplicationController::class);
    $router->resource('applications-scheduled', ApplicationController::class);
    $router->resource('applications-mention', ApplicationController::class);
    $router->resource('applications-mediation', ApplicationController::class);
    $router->resource('applications-hearing', ApplicationController::class);
    $router->resource('applications-closed', ApplicationController::class);
    $router->resource('applications-submission', ApplicationController::class);
    $router->resource('applications-archived', ApplicationController::class);
    $router->resource('applications-pending', ApplicationController::class);
    /* 


    1. [Application Filing](filing)
2. [Defense Response Stage](defense)
3. [Scheduled Applications](scheduled)
4. [Mediation](mediation)
5. [Hearing and Ruling Stage](#hearing)
6. [Cloased](#closed)

    */


    $router->resource('applications', ApplicationController::class);
    $router->resource('my-applications', ApplicationController::class);
    $router->resource('cases-pending', ApplicationController::class);
    $router->resource('cases-hearing', ApplicationController::class);
    $router->resource('cases-mediation', ApplicationController::class);
    $router->resource('cases-court', ApplicationController::class);
    $router->resource('cases-closed', ApplicationController::class);
    $router->resource('cases', ApplicationController::class);


    $router->resource('attarchments', AttarchmentController::class);
    $router->resource('districts', DistrictController::class);
    $router->resource('sub-counties', SubcountyController::class);

    //$router->resource('cases', CaseModelController::class);
    $router->resource('offences', OffenceController::class);


    $router->resource('quotations', QuotationController::class);
    $router->resource('invoices', InvoiceController::class);
    $router->resource('invoice-items', InvoiceItemController::class);
    $router->resource('deliveries', DeliveryController::class);


    /* ========================START OF NEW THINGS===========================*/
    $router->resource('candidates', CandidateController::class);
    $router->resource('musaned', MusanedController::class);
    $router->resource('interpol', InterpolController::class);
    $router->resource('shared-cvs', SharedCvController::class);
    $router->resource('emis', EmisUploadController::class);
    $router->resource('training', TrainingController::class);
    $router->resource('ministry', MinistryController::class);
    $router->resource('enjaz', EnjazController::class);
    $router->resource('embasy', SubmitedEmbasyController::class);
    $router->resource('ready-for-departure', ReadyForDepatureController::class);
    $router->resource('traveled', DepaturedController::class);
    $router->resource('failed', FailedController::class);

    $router->resource('crops', CropController::class);
    $router->resource('crop-protocols', CropProtocolController::class);
    $router->resource('gardens', GardenController::class);
    $router->resource('garden-activities', GardenActivityController::class);

    /* ========================END OF NEW THINGS=============================*/

    $router->resource('service-providers', ServiceProviderController::class);
    $router->resource('groups', GroupController::class);
    $router->resource('associations', AssociationController::class);
    $router->resource('people', PersonController::class);
    $router->resource('disabilities', DisabilityController::class);
    $router->resource('institutions', InstitutionController::class);
    $router->resource('counselling-centres', CounsellingCentreController::class);
    $router->resource('jobs', JobController::class);
    $router->resource('job-applications', JobApplicationController::class);

    $router->resource('course-categories', CourseCategoryController::class);
    $router->resource('courses', CourseController::class);
    $router->resource('settings', UserController::class);
    $router->resource('participants', ParticipantController::class);
    $router->resource('members', MembersController::class);
    $router->resource('post-categories', PostCategoryController::class);
    $router->resource('news-posts', NewsPostController::class);
    $router->resource('events', EventController::class);
    $router->resource('event-bookings', EventBookingController::class);
    $router->resource('products', ProductController::class);
    $router->resource('product-orders', ProductOrderController::class);
});
