@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('open-add-notes-modal', function () {
    Swal.fire({
        title: '📝 Add Shift Note',
        html: `
            <style>
                .swal2-show{
                    width: 60%;
                }
                .swal2-container .swal2-html-container {
                    width: 100%;
                }
                .swal-form-group {
                    margin-bottom: 1rem;
                    text-align: left;
                }
                .swal-label {
                    display: block;
                    font-weight: 600;
                    margin-bottom: 0.3rem;
                    color: #374151;
                }
                .swal-input, .swal-textarea, .swal-select, .swal-file {
                    width: 100%;
                    padding: 8px 10px;
                    border: 1px solid #d1d5db;
                    border-radius: 6px;
                    font-size: 14px;
                    transition: border 0.2s;
                }
                .swal-input:focus, .swal-textarea:focus, .swal-select:focus, .swal-file:focus {
                    outline: none;
                    border-color: #3b82f6;
                    box-shadow: 0 0 0 1px #3b82f6;
                }
                .swal-checkbox {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    cursor: pointer;
                    font-weight: 500;
                }
                .file-list {
                    margin-top: 0.5rem;
                    border: 1px dashed #d1d5db;
                    border-radius: 6px;
                    padding: 6px;
                    background: #f9fafb;
                    max-height: 120px;
                    overflow-y: auto;
                }
                .file-item {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 4px 6px;
                    border-radius: 4px;
                    margin-bottom: 4px;
                    background: #fff;
                    font-size: 13px;
                    border: 1px solid #e5e7eb;
                }
                .file-item:last-child {
                    margin-bottom: 0;
                }
                .file-remove {
                    cursor: pointer;
                    color: #ef4444;
                    display: flex;
                    align-items: center;
                }
                .file-remove:hover {
                    color: #dc2626;
                }
            </style>

            <div class="swal-form-group">
                <label for="noteType" class="swal-label">Note Type</label>
                <select id="noteType" class="swal-select">
                    <option value="Progress Notes">Progress Notes</option>
                    <option value="Vacant Shift">Vacant Shift</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="swal-form-group">
                <label for="noteBody" class="swal-label">Note Body</label>
                <textarea id="noteBody" class="swal-textarea" rows="3" placeholder="Enter your note..."></textarea>
            </div>

            <div class="swal-form-group">
                <label class="swal-checkbox">
                    <input type="checkbox" id="keepPrivate">
                    Keep note private
                </label>
            </div>

            <div class="swal-form-group">
                <label for="attachments" class="swal-label">Attach Documents</label>
                <input type="file" id="attachments" class="swal-file" multiple accept=".jpg,.jpeg,.gif,.png,.tif,.doc,.docx,.xls,.xlsx,.csv,.pdf,.txt,.zip,.eml">
                <div id="attachedFiles" class="file-list"></div>
                <small style="color:#6b7280;font-size:12px;">Allowed: jpg, jpeg, gif, png, tif, doc, docx, xls, xlsx, csv, pdf, txt, zip, eml</small>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Add Note',
        focusConfirm: false,
        preConfirm: () => {
            const noteType = document.getElementById('noteType').value;
            const noteBody = document.getElementById('noteBody').value.trim();
            const keepPrivate = document.getElementById('keepPrivate').checked;
            const attachments = document.getElementById('attachments').files;

            if (!noteBody) {
                Swal.showValidationMessage('Please enter a note body');
                return false;
            }

            return {
                noteType,
                noteBody,
                keepPrivate,
                attachments
            };
        },
        didOpen: () => {
            const attachmentsInput = document.getElementById('attachments');
            const attachedFilesDiv = document.getElementById('attachedFiles');

            function refreshFileList(files) {
                attachedFilesDiv.innerHTML = '';
                Array.from(files).forEach((file, i) => {
                    const fileDiv = document.createElement('div');
                    fileDiv.classList.add('file-item');
                    fileDiv.innerHTML = `
                        <span>${file.name}</span>
                        <span class="file-remove" data-index="${i}">
                            ✖
                        </span>
                    `;
                    attachedFilesDiv.appendChild(fileDiv);
                });

                attachedFilesDiv.querySelectorAll('.file-remove').forEach(icon => {
                    icon.addEventListener('click', function() {
                        const index = parseInt(this.getAttribute('data-index'));
                        const dt = new DataTransfer();
                        const fileArray = Array.from(attachmentsInput.files);
                        fileArray.splice(index, 1); // remove only clicked file
                        fileArray.forEach(file => dt.items.add(file));
                        attachmentsInput.files = dt.files;
                        refreshFileList(attachmentsInput.files);
                    });
                });
            }

            attachmentsInput.addEventListener('change', function(e) {
                refreshFileList(e.target.files);
            });
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            Swal.showLoading();
            Swal.getConfirmButton().disabled = true;
            Swal.getCancelButton().style.display = 'none';

            Livewire.dispatch('saveNotes', {
                noteType: result.value.noteType,
                noteBody: result.value.noteBody,
                keepPrivate: result.value.keepPrivate,
                attachments: result.value.attachments
            });
        }
    });
});

// ✅ Success toast
window.addEventListener('note-added', function (e) {
    Swal.close();
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: e.detail.message ?? 'Note added successfully!',
        showConfirmButton: false,
        timer: 3000
    });
});
</script>
@endpush
