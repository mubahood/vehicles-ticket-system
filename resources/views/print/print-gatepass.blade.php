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
        body { font-family: sans-serif; line-height: 1.4; font-size: 12px; }
        .container { padding: 20px; }
        .header, .footer { text-align: center; margin-bottom: 20px; }
        .header img { max-width: 150px; margin-bottom: 10px; }
        .header h2, .header p { margin: 0; }
        .divider { border-top: 2px solid black; margin: 15px 0; }
        .section-title { font-weight: bold; font-size: 14px; margin-top: 15px; margin-bottom: 5px; text-decoration: underline; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .details-table th, .details-table td { border: 1px solid #ccc; padding: 6px; text-align: left; vertical-align: top; }
        .details-table th { background-color: #f2f2f2; font-weight: bold; }
        .label { font-weight: bold; display: inline-block; min-width: 150px; }
        .value { display: inline-block; }
        .approval-section { margin-top: 20px; }
        .signature-section { margin-top: 40px; page-break-inside: avoid; }
        .signature-block { display: inline-block; width: 30%; margin-right: 3%; text-align: center; }
        .signature-line { border-bottom: 1px solid black; margin-top: 30px; margin-bottom: 5px; }
        .text-center { text-align: center; }
        .text-danger { color: #dc3545; }
        .text-success { color: #28a745; }
        .text-warning { color: #ffc107; }
        /* Add other styles as needed */
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            {{-- Use public_path for images in PDF --}}
            <img src="{{ public_path('assets/images/logo.jpg') }}" alt="Company Logo"> 
            <h2>{{ env('APP_NAME', 'Gatepass Management System') }}</h2>
            <p>Exit/Entry Pass - Request #{{ $item->id }}</p>
            <p>Date Generated: {{ date('d-M-Y H:i') }}</p> 
        </div>

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
                    @if($item->hod_status === 'Rejected' || $item->gm_status === 'Rejected' || $item->security_exit_status === 'Rejected' || $item->security_return_status === 'Rejected')
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
        @if($item->type == 'Vehicle')
            <div class="section-title">Vehicle & Driver Details</div>
            <table class="details-table">
                <tr><th>Registration No.</th><td>{{ $item->vehicle->registration_number ?? 'N/A' }}</td></tr>
                <tr><th>Make/Model</th><td>{{ $item->vehicle->brand ?? '' }} {{ $item->vehicle->model ?? 'N/A' }}</td></tr>
                <tr><th>Vehicle Type</th><td>{{ $item->vehicle->vehicle_type ?? 'N/A' }}</td></tr>
                <tr>
                    <th>Driver(s)</th>
                    <td>
                        @forelse($item->drivers as $driverEntry)
                           {{ $driverEntry->user->name ?? 'N/A' }} (ID: {{ $driverEntry->driver_id }})<br>
                        @empty
                            No driver assigned.
                        @endforelse
                    </td>
                </tr>
                 <tr><th>Passenger Info</th><td>{{ $item->materials_requested ?? 'N/A' }}</td></tr>{{-- Reusing field for passenger info as per form--}}
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
                        <tr><td colspan="4">No material details provided.</td></tr>
                    @endforelse
                </tbody>
            </table>
        @elseif($item->type == 'Personnel')
            <div class="section-title">Personnel Leave Details</div>
             <table class="details-table">
                <tr><th>Leave Type</th><td>{{ $item->materials_requested ?? 'N/A' }}</td></tr> {{-- Reusing field for leave type as per form --}}
                {{-- Add other relevant personnel details if available in the model --}}
             </table>
        @endif

        <div class="section-title">Timing Details</div>
        <table class="details-table">
            <tr>
                <th>Requested Departure</th>
                <td>{{ $item->requested_departure_time ? \Carbon\Carbon::parse($item->requested_departure_time)->format('d-M-Y H:i') : 'N/A' }}</td>
                <th>Actual Departure</th>
                <td>{{ $item->actual_departure_time ? \Carbon\Carbon::parse($item->actual_departure_time)->format('d-M-Y H:i') : 'N/A' }}</td>
            </tr>
            <tr>
                <th>Requested Return</th>
                <td>{{ $item->requested_return_time ? \Carbon\Carbon::parse($item->requested_return_time)->format('d-M-Y H:i') : 'N/A' }}</td>
                <th>Actual Return</th>
                 <td>{{ $item->actual_return_time ? \Carbon\Carbon::parse($item->actual_return_time)->format('d-M-Y H:i') : 'N/A' }}</td>
            </tr>
             @if($item->type == 'Vehicle')
             <tr>
                <th>Overstayed?</th>
                 <td>{{ $item->over_stayed ?? 'N/A' }}</td>
                 <th></th><td></td> {{-- Placeholder for alignment --}}
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
                @if($item->hod_status == 'Approved')
                <tr>
                    <td>GM Approval</td>
                    <td>{{ $item->gm_status ?? 'Pending' }}</td>
                     <td>{{-- Add logic to display GM user and timestamp if available --}}</td>
                    <td>{{ $item->gm_comment ?? '' }}</td>
                </tr>
                @endif
                 @if($item->gm_status == 'Approved')
                <tr>
                    <td>Security Exit</td>
                    <td>{{ $item->security_exit_status ?? 'Pending' }}</td>
                     <td>{{-- Add logic to display Security user and timestamp if available --}}</td>
                    <td>{{ $item->exit_comment ?? '' }} (State: {{ $item->exit_state ?? 'N/A' }})</td>
                </tr>
                @endif
                @if($item->security_exit_status == 'Approved')
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