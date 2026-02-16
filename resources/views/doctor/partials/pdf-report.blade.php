<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Report - {{ $admin->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        h1 {
            color: #197a8c;
            border-bottom: 3px solid #ffd700;
            padding-bottom: 10px;
        }
        h2 {
            color: #197a8c;
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #197a8c;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85em;
        }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-confirmed { background-color: #d1ecf1; color: #0c5460; }
        .status-completed { background-color: #d4edda; color: #155724; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
        .header-info {
            margin-bottom: 20px;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header-info">
        <h1>Admin Report: {{ $admin->name }}</h1>
        <p><strong>Branch:</strong> {{ $admin->branch->name ?? 'N/A' }}</p>
        <p><strong>Generated:</strong> {{ now()->format('M d, Y H:i:s') }}</p>
        <p><strong>Total Records:</strong> {{ $appointments->count() }}</p>
    </div>

    <h2>Patient Consultations</h2>
    <table>
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Patient ID</th>
                <th>Contact</th>
                <th>Date of Consult</th>
                <th>Service</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($appointments as $appointment)
                @php
                    $patient = $appointment->patient;
                    $patientName = $patient 
                        ? $patient->name 
                        : trim($appointment->first_name . ' ' . ($appointment->middle_initial ?? '') . ' ' . $appointment->last_name);
                    $patientId = $patient ? $patient->id : 'N/A';
                    $contact = $patient 
                        ? ($patient->phone ?? $patient->email ?? 'N/A')
                        : ($appointment->contact_phone ?? 'N/A');
                    $consultDate = $appointment->timeSlot 
                        ? \Carbon\Carbon::parse($appointment->timeSlot->date)->format('M d, Y')
                        : ($appointment->created_at ? $appointment->created_at->format('M d, Y') : 'N/A');
                    $service = $appointment->service 
                        ? $appointment->service->name 
                        : ($appointment->consultation_type ?? 'Consultation');
                @endphp
                <tr>
                    <td>{{ $patientName }}</td>
                    <td>{{ $patientId }}</td>
                    <td>{{ $contact }}</td>
                    <td>{{ $consultDate }}</td>
                    <td>{{ $service }}</td>
                    <td>
                        <span class="status-badge status-{{ $appointment->status }}">
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">
                        No appointments found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: center; color: #666; font-size: 0.9em;">
        <p>This report was generated on {{ now()->format('F d, Y \a\t H:i:s') }}</p>
    </div>
</body>
</html>


