@extends('app.layouts_copy.main')
@section('breadcrumbtitle', 'Feeds')
@section('breadcrumbtitle2', 'Import Feed')

@section('styles')
<style>
    .width60 * {
        word-break: break-all;
        white-space: wrap;
    }

    /* Base styling for the status text */
    .status {
        padding: 5px;
        border-radius: 4px;
        display: inline-block;
        font-weight: bold;
        text-transform: capitalize;
        /* Ensures first letter is capitalized */
    }

    /* Different status colors */
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
                    <h5>Feed Import</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('import.submit') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Enter ZIP feed URL:</label>
                            <textarea rows="10" cols="50" class="form-control" aria-label="With textarea" name="zip_url"></textarea>

                        </div>
                        <div class="mb-3">
                            <button class="btn btn-primary me-2" type="submit">Start Import</button>
                        </div>
                    </form>
                </div>
                <br>
                <div class="card-body">
                    <div class="hiii">
                        <table id="simpletable" class="table table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th class="width60">ZIP URL</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($imports as $import)
                                <tr>
                                    <td>{{ $import->id }}</td>
                                    <td class="width60"><a href="{{ $import->zip_url }}" target="_blank">{{ $import->zip_url}}</a></td>
                                    <td class="status {{ strtolower($import->status) }}">{{ ucfirst($import->status) }}</td>
                                    <td>{{ $import->created_at }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('import.reset', $import->id) }}">
                                            @csrf
                                            <button class="btn btn-warning btn-sm" type="submit">Re-import</button>
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
    $(document).ready(function() {
        $('#simpletable').DataTable({
            initComplete: function() {
                $('#simpletable').wrap('<div class=""></div>');
            }
        });
    });
</script>


@endsection