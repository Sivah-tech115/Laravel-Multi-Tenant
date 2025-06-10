@extends('app.layouts_copy.main')
@section('breadcrumbtitle', 'Feeds')
@section('breadcrumbtitle2', 'Feed Manager')

@section('styles')
<style>
    .width60 * {
        word-break: break-all;
        white-space: wrap;
    }

    .status {
        padding: 5px;
        border-radius: 4px;
        display: inline-block;
        font-weight: bold;
        text-transform: capitalize;
    }

    .status.completed {
        color: #28a745;
    }

    .status.processing {
        color: #ffc107;
    }

    .status.pending {
        color: #007bff;
    }

    .status.failed {
        color: #dc3545;
    }

    .table-responsive {
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 10px;
        max-height: 700px;
        overflow-y: auto;
    }

    .table th,
    .table td {
        vertical-align: middle !important;
        word-break: break-word;
    }

    .column-filter {
        font-size: 12px;
        padding: 2px;
    }

    /* Sticky headers and filter row */
    thead tr:nth-child(1) th,
    thead tr:nth-child(2) th {
        position: sticky;
        top: 0;
        /* for first row */
        background-color: #f8f9fa;
        z-index: 5;
    }

    thead tr:nth-child(2) th {
        top: 42px;
        /* height of first header row (adjust if needed) */
        z-index: 4;
    }
</style>

@endsection

@section('content')

<div class="page-wrapper">
    <div class="row">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5>Bulk Feed Import</h5>
                </div>
                <div class="card-body">
                    <input type="hidden" class="form-control mb-3" id="searchInput" placeholder="Global search...">

                    <div class="table-responsive" style="max-height: 700px; overflow-y: auto;">
                    <button id="clearFilters" class="btn btn-secondary mb-3">Clear Filters</button>
                        <table class="table table-bordered table-striped" id="feedTable">
                            <thead>
                                <tr>
                                    @foreach($headers as $header)
                                    <th>{{ $header }}</th>
                                    @endforeach
                                    <th>Action</th>
                                </tr>
                                <tr>
                                    @foreach($headers as $index => $header)
                                    @if(in_array($header, ['Advertiser ID', 'Advertiser Name', 'Primary Region']))
                                    <th>
                                        <select class="form-select column-filter" data-column="{{ $index }}">
                                            <option value="">All</option>
                                        </select>
                                    </th>
                                    @else
                                    <th></th>
                                    @endif
                                    @endforeach
                                    <th></th> <!-- for Action column -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $row)
                                <tr>
                                    @foreach($row as $cell)
                                    <td>{{ $cell }}</td>
                                    @endforeach
                                    <td>
                                        <form action="{{ route('import.submit') }}" method="POST" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="zip_url" value="{{ $row[count($row) - 1] }}">
                                            <button type="submit" class="btn btn-sm btn-success">Import</button>
                                        </form>
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>



            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
   document.addEventListener('DOMContentLoaded', function() {
    const table = document.querySelector('#feedTable');
    const globalSearch = document.querySelector('#searchInput');
    const clearFiltersBtn = document.querySelector('#clearFilters');
    const columnFilters = document.querySelectorAll('.column-filter');

    // Global search input filter
    globalSearch.addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        filterRows(value, getActiveFilters());
    });

    // Populate filter selects and set change event
    columnFilters.forEach(select => {
        const colIndex = parseInt(select.dataset.column);
        const values = new Set();

        table.querySelectorAll('tbody tr').forEach(row => {
            const cell = row.cells[colIndex];
            if (cell) values.add(cell.textContent.trim());
        });

        Array.from(values).sort().forEach(val => {
            const option = document.createElement('option');
            option.value = val;
            option.textContent = val;
            select.appendChild(option);
        });

        select.addEventListener('change', () => {
            filterRows(globalSearch.value.toLowerCase(), getActiveFilters());
        });
    });

    // Clear filters button
    clearFiltersBtn.addEventListener('click', () => {
        globalSearch.value = '';
        columnFilters.forEach(sel => sel.value = '');
        filterRows('', []);
    });

    // Helper to get active filters as array of { colIndex, value }
    function getActiveFilters() {
        const filters = [];
        columnFilters.forEach(sel => {
            if (sel.value.trim() !== '') {
                filters.push({ colIndex: parseInt(sel.dataset.column), value: sel.value.toLowerCase() });
            }
        });
        return filters;
    }

    // Main filtering function
    function filterRows(globalSearchTerm, activeFilters) {
        table.querySelectorAll('tbody tr').forEach(row => {
            const rowText = row.textContent.toLowerCase();

            // Check global search match
            const matchesGlobal = globalSearchTerm === '' || rowText.includes(globalSearchTerm);

            // Check all active column filters match
            const matchesColumns = activeFilters.every(filter => {
                const cell = row.cells[filter.colIndex];
                if (!cell) return false;
                return cell.textContent.toLowerCase().includes(filter.value);
            });

            row.style.display = (matchesGlobal && matchesColumns) ? '' : 'none';
        });
    }
});

</script>



@endsection