@extends('layouts.main')

@section('breadcrumbtitle', 'Tenants')
@section('breadcrumbtitle2', 'Create Tenant')
@section('content')


@section('styles')
<style>
</style>
@endsection

<div class="row">
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    <!-- Form Card -->
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-header">
                <h5>Create Tenant</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('tenants.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Tenant Name</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="text" class="form-control" name="email" value="{{ old('email') }}">
                    </div>
                    <!-- <div class="mb-3">
                        <label class="form-label">Domain Name</label>
                        <input type="text" class="form-control" name="domain_name" value="{{ old('domain_name') }}">
                    </div> -->

                    <?php

                    use Illuminate\Support\Facades\DB;

                    $merchants = DB::table('apps_countries')->get();
                    ?>

                    <div class="mb-3">
                        <label class="form-label">Domain Name</label>
                        <select class="form-control" name="domain_name" id="country-select">
                            <option value="" disabled selected>Select Country</option>
                            @foreach ($merchants as $merchant)
                            <option value="{{ strtolower($merchant->country_code) }}">
                                {{ $merchant->country_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Box to show selected option value (country code) -->
                    <div class="mb-3" id="selected-value-box" style="display:none;">
                        <label class="form-label">Domain Prefix</label>
                        <input type="text" class="form-control" id="selected-value" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Language Code</label>
                        <input type="text" class="form-control" name="language" value="{{ old('language') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="text" class="form-control" name="password">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="text" class="form-control" name="password_confirmation">
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary me-2">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.getElementById('country-select').addEventListener('change', function() {
        const selectedValue = this.value; // This gets the value attribute of the selected option
        if (selectedValue) {
            document.getElementById('selected-value').value = selectedValue;
            document.getElementById('selected-value-box').style.display = 'block';
        } else {
            document.getElementById('selected-value-box').style.display = 'none';
            document.getElementById('selected-value').value = '';
        }
    });
</script>s
@endsection