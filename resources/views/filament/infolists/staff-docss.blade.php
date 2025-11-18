@php
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use App\Models\Document;
use Carbon\Carbon;
use Illuminate\Support\Str;

$authUser = Auth::user();
$staff = $getRecord();
$companyId = Company::where('user_id', $authUser->id)->value('id');

// Get all documents (we’ll paginate in JS)
$documents = Document::where('user_id', $staff->id)->get();
$perPage = 5;
@endphp

<style>
    .table-wrapper {
        border: 1px solid #e5e7eb;
        overflow: hidden;
        background: #ffffff;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    }
    .data-table thead {
        background: #f9fafb;
    }
    .data-table th {
        padding: 0.75rem 1rem;
        font-weight: 600;
        text-align: left;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }
    .data-table td {
        padding: 0.65rem 1rem;
        font-size: 0.8rem;
        color: #374151;
    }
    .data-table tr:nth-child(even) {
        background: #fafafa;
    }
    .data-table tr:hover {
        background: #f3f4f6;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.15rem 0.5rem;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 500;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        color: #374151;
    }

    .pagination-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding-top: 0.75rem;
        font-size: 0.8rem;
        color: #6b7280;
        padding-bottom: 15px;
    }
    .pagination-buttons {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    .pagination-btn {
        border-radius: 999px;
        border: 1px solid #e5e7eb;
        padding: 0.25rem 0.6rem;
        font-size: 0.75rem;
        background: #ffffff;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .pagination-btn:hover {
        background: #f3f4f6;
    }
    .pagination-btn.active {
        background: #111827;
        color: white;
        border-color: #111827;
    }
    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: default;
        background: #f9fafb;
    }

    @media (max-width: 768px) {
        .data-table th, .data-table td {
            padding: 0.5rem 0.5rem;
        }
        .pagination-container {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="table-wrapper">
    <div class="overflow-x-auto">
        <table class="data-table w-full text-sm border-collapse" id="documents-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Document</th>
                    <th>Expired</th>
                    <th>No Expiration</th>
                    <th>Last Update</th>
                </tr>
            </thead>
            <tbody id="documents-tbody">
                @forelse($documents as $index => $document)
                    <tr class="document-row border-b hover:bg-gray-50 cursor-pointer" data-index="{{ $index }}">
                        <td data-label="Type">
                            <span class="badge">{{ $document->type }}</span>
                        </td>
                        <td data-label="Category">
                            {{ $document->category ? $document->category->name : '-' }}
                        </td>
                        <td data-label="Document">
                            {{ Str::after($document->name, 'documents/') }}
                        </td>
                        <td data-label="Expired">
                            {{ $document->expired_at ? Carbon::parse($document->expired_at)->format('d M Y') : '-' }}
                        </td>
                        <td data-label="No Expiration">
                            {{ $document->no_expiration ? 'Yes' : 'No' }}
                        </td>
                        <td data-label="Last Update">
                            {{ $document->updated_at ? Carbon::parse($document->updated_at)->diffForHumans(['parts' => 2]) : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-4">
                            No documents found for this staff.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- JS Pagination UI --}}
    @if($documents->count() > 0)
        <div class="px-4 pb-3 pagination-container"
             data-total="{{ $documents->count() }}"
             data-per-page="{{ $perPage }}"
             id="documents-pagination">
            <div>
                Showing
                <span id="doc-from">1</span>–
                <span id="doc-to">{{ min($perPage, $documents->count()) }}</span>
                of
                <span id="doc-total">{{ $documents->count() }}</span>
                documents
            </div>
            <div class="pagination-buttons">
                <button class="pagination-btn" id="doc-prev">Prev</button>
                <div id="doc-pages"></div>
                <button class="pagination-btn" id="doc-next">Next</button>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rows = Array.from(document.querySelectorAll('.document-row'));
        const perPage = parseInt(document.getElementById('documents-pagination')?.dataset.perPage || 5, 10);
        const total = rows.length;
        if (!total) return;

        let currentPage = 1;
        const totalPages = Math.ceil(total / perPage);

        const fromEl = document.getElementById('doc-from');
        const toEl = document.getElementById('doc-to');
        const totalEl = document.getElementById('doc-total');
        const prevBtn = document.getElementById('doc-prev');
        const nextBtn = document.getElementById('doc-next');
        const pagesContainer = document.getElementById('doc-pages');

        totalEl.textContent = total;

        function renderPageButtons() {
            pagesContainer.innerHTML = '';
            for (let i = 1; i <= totalPages; i++) {
                const btn = document.createElement('button');
                btn.textContent = i;
                btn.classList.add('pagination-btn');
                if (i === currentPage) {
                    btn.classList.add('active');
                }
                btn.addEventListener('click', function () {
                    goToPage(i);
                });
                pagesContainer.appendChild(btn);
            }
        }

        function updateRows() {
            const start = (currentPage - 1) * perPage;
            const end = start + perPage;

            rows.forEach((row, index) => {
                if (index >= start && index < end) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            fromEl.textContent = start + 1;
            toEl.textContent = Math.min(end, total);

            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages;

            // update active button
            Array.from(pagesContainer.children).forEach((btn, idx) => {
                btn.classList.toggle('active', idx + 1 === currentPage);
            });
        }

        function goToPage(page) {
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            updateRows();
        }

        prevBtn.addEventListener('click', function () {
            if (currentPage > 1) {
                goToPage(currentPage - 1);
            }
        });

        nextBtn.addEventListener('click', function () {
            if (currentPage < totalPages) {
                goToPage(currentPage + 1);
            }
        });

        renderPageButtons();
        updateRows();
    });
</script>
