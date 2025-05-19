@extends('app.layouts_copy.main')
@section('breadcrumbtitle', 'Home')
@section('breadcrumbtitle2', 'Dashboard')

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
                    <h5>Settings</h5>
                </div>
                <div class="card-body">
                    <div class="relative overflow-x-auto">
                        <table id="simpletable" class="table table-bordered nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th> Email</th>
                                    <th> Role</th>
                                    <th> Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                <tr>
                                    <th>{{$user->name}}
                                    </th>
                                    <td>
                                        {{$user->email}}
                                    </td>
                                    <td>
                                        @foreach ($user->roles as $role)
                                        {{$role->name}}{{$loop->last ? "" : ","}}
                                        @endforeach
                                    </td>
                                    <td>
                                        <a href="{{route('users.edit', $user->id)}}">Edit</a>
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