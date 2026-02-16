@if($appointments->count() > 0)
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th onclick="sortTable('patient_name', '{{ request('sort_direction') === 'asc' && request('sort_column') === 'patient_name' ? 'desc' : 'asc' }}')">
                        Patient Name
                        @if(request('sort_column') === 'patient_name')
                            {{ request('sort_direction') === 'asc' ? '↑' : '↓' }}
                        @endif
                    </th>
                    <th onclick="sortTable('patient_id', '{{ request('sort_direction') === 'asc' && request('sort_column') === 'patient_id' ? 'desc' : 'asc' }}')">
                        Patient ID
                        @if(request('sort_column') === 'patient_id')
                            {{ request('sort_direction') === 'asc' ? '↑' : '↓' }}
                        @endif
                    </th>
                    <th>Contact</th>
                    <th onclick="sortTable('date', '{{ request('sort_direction') === 'asc' && request('sort_column') === 'date' ? 'desc' : 'asc' }}')">
                        Date of Consult
                        @if(request('sort_column') === 'date')
                            {{ request('sort_direction') === 'asc' ? '↑' : '↓' }}
                        @endif
                    </th>
                    <th>Service</th>
                    <th onclick="sortTable('status', '{{ request('sort_direction') === 'asc' && request('sort_column') === 'status' ? 'desc' : 'asc' }}')">
                        Status
                        @if(request('sort_column') === 'status')
                            {{ request('sort_direction') === 'asc' ? '↑' : '↓' }}
                        @endif
                    </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appointment)
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
                        $initials = collect(explode(' ', trim($patientName)))->filter()->map(fn($p) => mb_substr($p, 0, 1))->take(2)->join('');
                        if ($initials === '') { $initials = 'P'; }
                    @endphp
                    <tr>
                        <td>
                            <div class="profile-icon">{{ $initials }}</div>
                            <span class="primary-column-text">{{ $patientName }}</span>
                        </td>
                        <td>{{ $patientId }}</td>
                        <td>{{ $contact }}</td>
                        <td>{{ $consultDate }}</td>
                        <td>{{ $service }}</td>
                        <td>
                            <span class="status-badge status-{{ $appointment->status }}">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn" 
                                        onclick="viewPatientDetails({{ $appointment->id }})"
                                        title="View Details">
                                    <i data-feather="eye"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <script>
        if (window.feather && typeof window.feather.replace === 'function') {
            window.feather.replace();
        }
    </script>
@else
    <div style="text-align: center; padding: 3rem; color: #6c757d;">
        <p style="font-size: 1.1rem;">No appointments found for this admin.</p>
    </div>
@endif


