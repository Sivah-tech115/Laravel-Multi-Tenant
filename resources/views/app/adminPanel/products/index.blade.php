@extends('app.layouts_copy.main')
@section('breadcrumbtitle', 'Offers')
@section('breadcrumbtitle2', 'All Offers')

@section('styles')
<style>
    .dt-responsive .dataTables_length,
    .dt-responsive .dataTables_filter,
    .dt-responsive .dataTables_info,
    .dt-responsive .dataTables_paginate {
        padding-left: 20px !important;
        padding-right: 20px !important;
    }

    .noOverflow {
        overflow-x: initial !important;
        -webkit-overflow-scrolling: initial !important;
    }
</style>
@endsection
@section('content')

<div class="page-wrapper">
    <div class="row">
        <!-- Table Card -->
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5>Offers List</h5>
                </div>
                <div class="card-body">

                    <table id="simpletable" class="table table-bordered nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Slug</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                    </table>

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
            processing: true,
            serverSide: true,
            ajax: '{{ route("products.ajax") }}',
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'product_name',
                    name: 'product_name'
                },
                {
                    data: 'slug',
                    name: 'slug'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            initComplete: function() {
                $('#simpletable').wrap('<div class="OverXTable overflow-x-auto"></div>');
            }
        });
    });
</script>
@endsection