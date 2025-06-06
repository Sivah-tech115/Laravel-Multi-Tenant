@extends('layouts.main')
@section('breadcrumbtitle', 'Tenants')
@section('breadcrumbtitle2', 'All Tenants')

@section('styles')
<style>
</style>
@endsection

@section('content')

<div class="page-wrapper">
    <div class="row">
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5>Tenants</h5>
                    <a href="{{ route('tenants.create') }}" target="_blank" class="btn btn-primary btn-sm" >Add Tenant</a>
                </div>
                <div class="card-body">
                    <div class="relative overflow-x-auto">
                        <table id="simpletable" class="table table-bordered nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Domain</th>
                                    <th>Go To URL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tenants as $tenant)
                                <tr>
                                    <td>{{ $tenant->name }}</td>
                                    <td>{{ $tenant->email }}</td>
                                    <td>
                                        @foreach ($tenant->domains as $domain)
                                        {{ $domain->domain }}{{ $loop->last ? '' : ', ' }}
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($tenant->domains->isNotEmpty())
                                        <a href="http://{{ $tenant->domains->first()->domain }}:8000" target="_blank" class="btn btn-primary btn-sm">
                                            Visit
                                        </a>
                                        @else
                                        No Domain
                                        @endif
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
            responsive: true,
            initComplete: function() {
                $('#simpletable').wrap('<div class="OverXTable overflow-x-auto"></div>');
            }
        });
    });
</script>


@endsection