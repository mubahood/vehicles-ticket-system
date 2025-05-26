@if (!isset($item))
    @php throw new Exception('VehicleRequest item not set for PDF generation'); @endphp
@endif

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gate Pass Request #{{ $item->id }}</title>
    {{-- Ensure CSS is correctly linked using public_path for dompdf --}}
    <link rel="stylesheet" href="{{ public_path('css/print.css') }}">
    {{-- You might need a specific print CSS, Bootstrap print, or custom styles --}}
    <style>
        body {
            font-family: sans-serif;
            line-height: 1.4;
            font-size: 12px;
        }

        .container {
            padding: 20px;
        }

        .header,
        .footer {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            max-width: 150px;
            margin-bottom: 10px;
        }

        .header h2,
        .header p {
            margin: 0;
        }

        .divider {
            border-top: 2px solid black;
            margin: 15px 0;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-top: 15px;
            margin-bottom: 5px;
            text-decoration: underline;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .details-table th,
        .details-table td {
            border: 3px solid #000000;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        .details-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .label {
            font-weight: bold;
            display: inline-block;
            min-width: 150px;
        }

        .value {
            display: inline-block;
        }

        .approval-section {
            margin-top: 20px;
        }

        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-block {
            display: inline-block;
            width: 30%;
            margin-right: 3%;
            text-align: center;
        }

        .signature-line {
            border-bottom: 1px solid black;
            margin-top: 30px;
            margin-bottom: 5px;
        }

        .text-center {
            text-align: center;
        }

        .text-danger {
            color: #dc3545;
        }

        .text-success {
            color: #28a745;
        }

        .text-warning {
            color: #ffc107;
        }

        .my-th {
            style="background-color: #c9c3c3; font-weight: bold; text-align: center!mportant; font-size: 14px;"
        }

        .my-th td {
            background-color: #c9c3c3;
            font-weight: bold;
            text-align: center !important;
            font-size: 14px;
        }

        .my-td {
            text-align: center !important;
            font-size: 14px;
        }

        .my-td td {
            text-align: center !important;
            font-size: 14px;
        }

        /* Add other styles as needed */
    </style>
</head>

<body>
    <div class="container">
        <table
            style="width: 100%; margin-bottom: 20px; background-color: #17406D; color: white; padding: 10px; padding-bottom: 20px;">
            <tr>
                <td>
                    <h2 style="margin: 0; color: white; font-size: 20px; font-weight: bold;">
                        Resident & Vehicle Off Site Travel Approval</h2>
                </td>
                <td>
                    <img style="width: 150px; height: 50px; float: right;" {{-- Use public_path for images in PDF --}}
                        src="{{ public_path('assets/images/logo.jpg') }}" alt="Company Logo">
                </td>
            </tr>
        </table>

        <p class="text-center" style="font-weight: bold; color: red; font-size: 18px; margin-bottom: 24px;">
            Off-site travel for all Syama camp residents and SOMISY vehicles is restricted to <u>essential business
                purposes</u> only
        </p>


        <table class="details-table" style="margin-bottom: 20px;">
            <tr class="my-th">
                <td>SOMISY VEHICLE</td>
                <td>CAMP RESIDENT</td>
                <td>EXPATRIATE</td>
            </tr>
            <tr class="my-td">
                <td>{{ $item->is_somisy_vehicle ? 'Yes' : 'No' }}</td>
                <td>{{ $item->is_camp_resident ? 'Yes' : 'No' }}</td>
                <td>{{ $item->expatirate_type ?? 'N/A' }}</td>
            </tr>
        </table>
        <table class="details-table" style="margin-bottom: 20px;">
            <tr class="my-th">
                <td colspan="4"><b>VEHICLE AND DRIVER</b></td>
            </tr>
            <tr class="my-td">
                <td><b>Driver name</b></td>
                <td>{!! $item->drivers->pluck('driver.name')->implode('<br>') ?? 'N/A' !!}</td>
                <td>
                    Phone
                </td>
                <td>{!! $item->drivers->pluck('driver.phone_number')->implode('<br>') ?? 'N/A' !!}</td>
            </tr>
            <tr>
                <td><b>Licence(s)</b></td>
                <td colspan="3">{{ $item->licence_type }}</td>
            </tr>
            <tr>
                <td><b>Vehicle</b></td>
                <td>{{ $item->vehicle->registration_number ?? 'N/A' }}</td>
                <td><b>Make/Model</b></td>
                <td>{{ $item->vehicle->brand ?? '' }} {{ $item->vehicle->model ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><b>Department</b></td>
                <td>{{ $item->applicant && $item->applicant->department ? $item->applicant->department->name : 'N/A' }}
                </td>
                <td><b>Company</b></td>
                <td>{{ $item->applicant && $item->applicant->company ? $item->applicant->company->name : 'N/A' }}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-center">
                    <br>
                    <div class="section-title">Conditions</div>
                    <ul style="font-size: 16px; margin-left: 20px; color: #333;">
                        <li style="color: red;">Recurring trips will be approved for a maximum of one business week in
                            advance
                            (Tuesday - Wednesday).</li>
                        <li>If more than one resident, HOD who owns the vehicle signs off.</li>
                        <li>If no site vehicle is used, each resident completes their own form.</li>
                        <li>Licence must be carried by driver and all local road rules obeyed.</li>
                        <li>The driver is responsible for the security of the vehicle at all times.</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Driver signature</b>
                </td>
                <td></td>
                <td><b>Date</b></td>
                <td>
                    {{ \Carbon\Carbon::now()->format('d-M-Y') }} 
                </td>
            </tr>
        </table>


        <div class="divider"></div>

        <div class="section-title">Request Details</div>
        <table class="details-table">
            <tr>
                <th>Request ID</th>
                <td>{{ $item->id }}</td>
                <th>Request Type</th>
                <td>{{ $item->type }}</td>
            </tr>
            <tr>
                <th>Date Requested</th>
                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d-M-Y H:i') }}</td>
                <th>Status</th>
                <td>
                    @if (
                        $item->hod_status === 'Rejected' ||
                            $item->gm_status === 'Rejected' ||
                            $item->security_exit_status === 'Rejected' ||
                            $item->security_return_status === 'Rejected')
                        <span class="text-danger">Rejected</span>
                    @elseif($item->security_return_status === 'Approved')
                        <span class="text-success">Completed</span>
                    @elseif($item->security_exit_status === 'Approved')
                        <span class="text-success">Exited</span>
                    @elseif($item->gm_status === 'Approved')
                        <span class="text-success">Approved (Pending Exit)</span>
                    @elseif($item->hod_status === 'Approved')
                        <span class="text-warning">Pending GM Approval</span>
                    @else
                        <span class="text-warning">Pending HOD Approval</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Applicant Name</th>
                <td>{{ $item->applicant->name ?? 'N/A' }}</td>
                <th>Applicant ID</th>
                <td>{{ $item->applicant_id }}</td>
            </tr>
        </table>

        <div class="section-title">Justification & Destination</div>
        <table class="details-table">
            <tr>
                <th>Destination</th>
                <td>{{ $item->destination ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Justification</th>
                <td>{{ $item->justification ?? 'N/A' }}</td>
            </tr>
        </table>

        {{-- Request Type Specific Details --}}
        @if ($item->type == 'Vehicle')
            <div class="section-title">Vehicle & Driver Details</div>
            <table class="details-table">
                <tr>
                    <th>Registration No.</th>
                    <td>{{ $item->vehicle->registration_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Make/Model</th>
                    <td>{{ $item->vehicle->brand ?? '' }} {{ $item->vehicle->model ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Vehicle Type</th>
                    <td>{{ $item->vehicle->vehicle_type ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Is somisy vehicle?</th>
                    <td>{{ $item->is_somisy_vehicle ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Is Camp Resident?</th>
                    <td>{{ $item->is_camp_resident ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Expatriate</th>
                    <td>{{ $item->expatirate_type ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Licence type</th>
                    <td>{{ $item->licence_type ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Driver(s)</th>
                    <td>
                        @forelse($item->drivers as $driverEntry)
                            {{ $driverEntry->driver->name ?? 'N/A' }} (ID: {{ $driverEntry->driver_id }})<br>
                        @empty
                            No driver assigned.
                        @endforelse
                    </td>
                </tr>
                <tr>
                    <th>Passenger Info</th>
                    <td>{{ $item->materials_requested ?? 'N/A' }}</td>
                </tr>{{-- Reusing field for passenger info as per form --}}
            </table>
        @elseif($item->type == 'Materials')
            <div class="section-title">Material Details</div>
            <table class="details-table">
                <thead>
                    <tr>
                        <th>Material Name/Type</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Photo Provided</th> {{-- Dompdf might struggle with dynamic images here unless paths are absolute --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse($item->materialItems as $material)
                        <tr>
                            <td>{{ $material->type ?? ($material->name ?? 'N/A') }}</td> {{-- Adjusted field name based on form vs model --}}
                            <td>{{ $material->quantity ?? 'N/A' }}</td>
                            <td>{{ $material->unit ?? 'N/A' }}</td>
                            <td>{{ $material->description ? 'Yes' : 'No' }}</td> {{-- Assuming description holds photo path --}}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No material details provided.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @elseif($item->type == 'Personnel')
            <div class="section-title">Personnel Leave Details</div>
            <table class="details-table">
                <tr>
                    <th>Leave Type</th>
                    <td>{{ $item->materials_requested ?? 'N/A' }}</td>
                </tr> {{-- Reusing field for leave type as per form --}}
                {{-- Add other relevant personnel details if available in the model --}}
            </table>
        @endif

        <div class="section-title">Timing Details</div>
        <table class="details-table">
            <tr>
                <th>Requested Departure</th>
                <td>{{ $item->requested_departure_time ? \Carbon\Carbon::parse($item->requested_departure_time)->format('d-M-Y H:i') : 'N/A' }}
                </td>
                <th>Actual Departure</th>
                <td>{{ $item->actual_departure_time ? \Carbon\Carbon::parse($item->actual_departure_time)->format('d-M-Y H:i') : 'N/A' }}
                </td>
            </tr>
            <tr>
                <th>Requested Return</th>
                <td>{{ $item->requested_return_time ? \Carbon\Carbon::parse($item->requested_return_time)->format('d-M-Y H:i') : 'N/A' }}
                </td>
                <th>Actual Return</th>
                <td>{{ $item->actual_return_time ? \Carbon\Carbon::parse($item->actual_return_time)->format('d-M-Y H:i') : 'N/A' }}
                </td>
            </tr>
            @if ($item->type == 'Vehicle')
                <tr>
                    <th>Overstayed?</th>
                    <td>{{ $item->over_stayed ?? 'N/A' }}</td>
                    <th></th>
                    <td></td> {{-- Placeholder for alignment --}}
                </tr>
            @endif
        </table>

        <div class="section-title approval-section">Approval & Verification Trail</div>
        <table class="details-table">
            <thead>
                <tr>
                    <th>Stage</th>
                    <th>Status</th>
                    <th>Processed By / On</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>HOD Approval</td>
                    <td>{{ $item->hod_status ?? 'Pending' }}</td>
                    <td>{{-- Add logic to display HOD user and timestamp if available --}}</td>
                    <td>{{ $item->hod_comment ?? '' }}</td>
                </tr>
                @if ($item->hod_status == 'Approved')
                    <tr>
                        <td>GM Approval</td>
                        <td>{{ $item->gm_status ?? 'Pending' }}</td>
                        <td>{{-- Add logic to display GM user and timestamp if available --}}</td>
                        <td>{{ $item->gm_comment ?? '' }}</td>
                    </tr>
                @endif
                @if ($item->gm_status == 'Approved')
                    <tr>
                        <td>Security Exit</td>
                        <td>{{ $item->security_exit_status ?? 'Pending' }}</td>
                        <td>{{-- Add logic to display Security user and timestamp if available --}}</td>
                        <td>{{ $item->exit_comment ?? '' }} (State: {{ $item->exit_state ?? 'N/A' }})</td>
                    </tr>
                @endif
                @if ($item->security_exit_status == 'Approved')
                    <tr>
                        <td>Security Return</td>
                        <td>{{ $item->security_return_status ?? 'Pending' }}</td>
                        <td>{{-- Add logic to display Security user and timestamp if available --}}</td>
                        <td>{{ $item->return_comment ?? '' }} (State: {{ $item->return_state ?? 'N/A' }})</td>
                    </tr>
                @endif
            </tbody>
        </table>


        <div class="signature-section">
            <div class="signature-block">
                <div class="signature-line"></div>
                Applicant Signature
            </div>
            <div class="signature-block">
                <div class="signature-line"></div>
                HOD Signature
            </div>
            <div class="signature-block">
                <div class="signature-line"></div>
                GM Signature
            </div>
            <div class="signature-block" style="margin-top: 20px;">
                <div class="signature-line"></div>
                Security (Exit) Signature
            </div>
            <div class="signature-block" style="margin-top: 20px;">
                <div class="signature-line"></div>
                Security (Return) Signature
            </div>
        </div>

        <div class="footer">
            <p>Printed on: {{ date('d-M-Y H:i:s') }}</p>
            {{-- Add any footer text required --}}
        </div>
    </div>
</body>

</html>
