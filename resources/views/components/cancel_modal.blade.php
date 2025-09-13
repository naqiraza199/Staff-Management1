@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('open-cancel-modal', function () {
        Swal.fire({
            title: 'Choose cancel reason',
            html: `
                <div style="text-align: left;">
                    <!-- Cancelled by client -->
                    <div style="margin-bottom: 1rem;">
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                            <input type="radio" name="cancelReason" value="client">
                            <span><b>Cancelled by client</b></span>
                            <span class="tooltip-wrapper">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;cursor:pointer;color:#3b82f6;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13 16h-1v-4h-1m1-4h.01M12 20.5a8.5 8.5 0 100-17 8.5 8.5 0 000 17z"/>
                                </svg>
                                <span class="tooltip-text">
                                    Use this if the client has cancelled with late notice and should be charged. 
                                    It will also allow the staff to be paid.
                                </span>
                            </span><br>
                        </label>
                        <span style="font-size:12px">Choose this option if client should be charged or absent.</span>
                    </div>

                    <!-- Extra fields for client cancel -->
                    <div id="client-extra-fields" style="display:none; margin-top:1rem;margin-bottom: 1rem;">
                        <select id="cancelType" style="width:100%;padding:6px;margin-top:4px;">
                            <option value="">Select NDIS cancellation reason</option>
                            <option value="No show due to health reason">No show due to health reason</option>
                            <option value="No show due to family issues">No show due to family issues</option>
                            <option value="No show due to unavailability of transport">No show due to unavailability of transport</option>
                            <option value="Other">Other</option>
                        </select>
                        <textarea id="cancelNotes" style="width:100%;padding:6px;margin-top:4px;" rows="3" placeholder="Reason"></textarea>
                    </div>

                    <!-- Cancelled by us -->
                    <div style="margin-bottom: 1rem;">
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                            <input type="radio" name="cancelReason" value="us">
                            <span><b>Cancelled by us</b></span>
                            <span class="tooltip-wrapper">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;cursor:pointer;color:#3b82f6;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13 16h-1v-4h-1m1-4h.01M12 20.5a8.5 8.5 0 100-17 8.5 8.5 0 000 17z"/>
                                </svg>
                                <span class="tooltip-text">
                                    This option will not charge the client. 
                                    The worker will not be paid for the shift, 
                                    on the app the shift will be listed as cancelled.
                                </span>
                            </span><br>
                        </label>
                        <span style="font-size:12px">Suitable for staff no show or could not be booked.</span>
                    </div>

                    <!-- Extra fields for us cancel -->
                    <div id="us-extra-fields" style="display:none; margin-top:1rem;margin-bottom: 1rem;">
                        <textarea id="cancelNotesUs" style="width:100%;padding:6px;margin-top:4px;" rows="3" placeholder="Reason"></textarea>
                    </div>

                    <!-- Notify Carer toggle -->
                    <div style="margin-top: 1.5rem;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" id="notifyCarer">
                            <span><b>Notify Carer</b></span>
                        </label>
                    </div>

                    <style>
                        .tooltip-wrapper { position: relative; display: inline-block; }
                        .tooltip-text {
                            visibility: hidden;
                            background-color: #333;
                            color: #fff;
                            text-align: left;
                            border-radius: 4px;
                            padding: 6px;
                            position: absolute;
                            z-index: 1;
                            bottom: 125%;
                            left: 50%;
                            transform: translateX(-50%);
                            width: 220px;
                            font-size: 12px;
                        }
                        .tooltip-wrapper:hover .tooltip-text { visibility: visible; }
                    </style>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Submit',
            focusConfirm: false,
            preConfirm: () => {
                // Gather values from modal
                const reason = document.querySelector('input[name="cancelReason"]:checked')?.value || null;

                let type = null;
                let notes = null;

                if (reason === 'client') {
                    type = document.getElementById('cancelType')?.value || null;
                    notes = document.getElementById('cancelNotes')?.value || null;
                } else if (reason === 'us') {
                    notes = document.getElementById('cancelNotesUs')?.value || null;
                }

                const notifyCarer = document.getElementById('notifyCarer')?.checked || false;

                if (!reason) {
                    Swal.showValidationMessage('Please select a cancel reason');
                    return false;
                }

                return {
                    reason,
                    type,
                    notes,
                    notifyCarer,
                };
            },
            didOpen: () => {
                // Show/hide client fields
                document.querySelectorAll('input[name="cancelReason"]').forEach(radio => {
                    radio.addEventListener('change', function() {
                        document.getElementById('client-extra-fields').style.display =
                            this.value === 'client' ? 'block' : 'none';

                        document.getElementById('us-extra-fields').style.display =
                            this.value === 'us' ? 'block' : 'none';
                    });
                });
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                // Show loading state and keep modal open
                Swal.showLoading();
                Swal.getConfirmButton().disabled = true; // Disable submit to prevent multiple clicks
                Swal.getCancelButton().style.display = 'none'; // Hide cancel button during loading

                // Dispatch to Livewire with form data
                Livewire.dispatch('cancelShift', {
                    reason: result.value.reason,
                    type: result.value.type,
                    notes: result.value.notes,
                    notifyCarer: result.value.notifyCarer
                });
            }
        });
    });

    // Handle shift-cancelled event to show success toast and close modal
    window.addEventListener('shift-cancelled', function (e) {
        Swal.close(); // Close the modal
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: e.detail.message ?? 'Done',
            showConfirmButton: false,
            timer: 3000
        });
    });
</script>
@endpush