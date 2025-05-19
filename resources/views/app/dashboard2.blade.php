@extends('app.layouts_copy.main')
@section('breadcrumbtitle', 'Home')
@section('breadcrumbtitle2', 'Dashboard')

@section('styles')
<style>
    .width60 *{
        word-break: break-all;
        white-space: wrap;
    }
</style>
@endsection

@section('content')

<div class="page-wrapper">
    <div class="row">
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
                                <!-- <th>Log</th> -->
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($imports as $import)
                            <tr>
                                <td>{{ $import->id }}</td>
                                <td class="width60"><a href="{{ $import->zip_url }}" target="_blank">{{ $import->zip_url}}</a></td>
                                <td>{{ ucfirst($import->status) }}</td>
                                <td>{{ $import->created_at }}</td>
                                <!-- <td>
                                    <pre>{{ $import->log }}</pre>
                                </td> -->
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