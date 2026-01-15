@php
    use Illuminate\Support\Str;
@endphp

@if($history->isEmpty())
    <div class="empty-state">
        <strong>No history available yet.</strong>
        <p class="mb-0">Completed consultations and results will appear here.</p>
    </div>
@else
    @foreach($historyByProfile as $profileId => $items)
        @php
            $profile = null;
            $profileName = '';
            
            // First, try to get profile from the profiles collection
            if ($profileId !== null) {
                $profile = $profiles->firstWhere('id', $profileId);
            }
            
            // If not found in collection, try to get from the first history item's relationship
            if (!$profile && $items->isNotEmpty()) {
                $firstItem = $items->first();
                $profile = $firstItem->personalInformation;
            }
            
            // If still no profile and profileId is null, try default profile
            if (!$profile && $profileId === null) {
                $profile = $profiles->where('is_default', true)->first() ?? $profiles->first();
            }
            
            // Build the name from the profile
            if ($profile) {
                // Build name from first_name, middle_initial, and last_name
                $nameParts = [];
                if (!empty($profile->first_name)) {
                    $nameParts[] = $profile->first_name;
                }
                if (!empty($profile->middle_initial)) {
                    $nameParts[] = $profile->middle_initial . '.';
                }
                if (!empty($profile->last_name)) {
                    $nameParts[] = $profile->last_name;
                }
                
                if (!empty($nameParts)) {
                    $profileName = trim(implode(' ', $nameParts));
                } else {
                    // Try the full_name accessor as fallback
                    $profileName = trim($profile->full_name ?? '');
                }
            }
            
            // If still no name found, try to get user's name as fallback
            if (empty($profileName) && $items->isNotEmpty()) {
                $firstItem = $items->first();
                $user = $firstItem->patient ?? null;
                if ($user && !empty($user->name)) {
                    $profileName = $user->name;
                }
            }
            
            // Final fallback - should rarely happen
            if (empty($profileName)) {
                $profileName = 'Unnamed';
            }
        @endphp
        <div class="profile-section">
            <div class="profile-title">
                <span>👤</span>
                <span>{{ $profileName }}</span>
                @if($profile && $profile->is_default)
                    <span class="badge bg-success">Default</span>
                @endif
            </div>
            <div class="table-responsive table-history mt-3" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table mb-0" style="min-width: 900px;">
                    <thead>
                        <tr class="history-row">
                            <th>Service</th>
                            <th>Date</th>
                            <th>Branch</th>
                            <th>Doctor</th>
                            <th>Follow-up</th>
                            <th>Prescription</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $item)
                        @php
                            $slotDate = ($item->appointment && $item->appointment->timeSlot && $item->appointment->timeSlot->date) 
                                ? $item->appointment->timeSlot->date->format('M d, Y') 
                                : 'Date TBD';
                            $branchName = $item->appointment->branch->name ?? 'N/A';
                            $serviceName = $item->appointment->service->name ?? 'Consultation';
                            $consultationData = null;
                            if ($item->consultation_result) {
                                $decoded = json_decode($item->consultation_result, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                    $consultationData = $decoded;
                                    foreach (['before','after'] as $key) {
                                        if (!empty($consultationData[$key]['photos'])) {
                                            $consultationData[$key]['photos'] = array_map(fn($p) => asset('storage/' . $p), $consultationData[$key]['photos']);
                                        }
                                    }
                                }
                            }
                        @endphp
                        <tr>
                            <td>
                                <div style="line-height: 1.5;">
                                    <span class="badge badge-service">{{ $serviceName }}</span>
                                </div>
                            </td>
                            <td>
                                <div style="line-height: 1.5; font-weight: 500;">{{ $slotDate }}</div>
                            </td>
                            <td>
                                <div style="line-height: 1.5;">
                                    <span class="badge badge-branch">{{ $branchName }}</span>
                                </div>
                            </td>
                            <td>
                                <div style="line-height: 1.5; font-weight: 500;">{{ $item->doctor->name ?? 'Doctor TBD' }}</div>
                            </td>
                            <td>
                                <div style="line-height: 1.5;">
                                    @if($item->follow_up_required)
                                        <span class="badge badge-followup">Yes</span>
                                        @if($item->follow_up_date)
                                            <div class="text-muted small" style="margin-top: 0.35rem;">{{ $item->follow_up_date->format('M d, Y') }}</div>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div style="line-height: 1.5;">
                                    @if($item->prescription)
                                        <span class="badge badge-prescription">Has Rx</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end">
                                @php
                                    $payload = [
                                        'service' => $serviceName,
                                        'date' => $slotDate,
                                        'branch' => $branchName,
                                        'doctor' => $item->doctor->name ?? 'Doctor TBD',
                                        'created_at' => $item->created_at->format('M d, Y H:i'),
                                        'consultation_data' => $consultationData,
                                        'consultation_raw' => $item->consultation_result,
                                        'prescription' => $item->prescription,
                                        'follow_up_required' => $item->follow_up_required,
                                        'follow_up_date' => $item->follow_up_date ? $item->follow_up_date->format('M d, Y') : null,
                                        'notes' => $item->notes,
                                    ];
                                @endphp
                                <button class="btn btn-sm btn-outline-primary show-result-btn"
                                        data-history='@json($payload)'>
                                    Show Result
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endif

