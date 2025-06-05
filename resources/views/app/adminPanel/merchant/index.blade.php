@extends('app.layouts_copy.main')
@section('breadcrumbtitle', 'Merchants')
@section('breadcrumbtitle2', 'All Merchants')

@section('styles')

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
                    <h5>Merchants</h5>
                </div>
                <div class="card-body">
                    <div class="hiii">
                        <table id="simpletable" class="table table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Merchant Name</th>
                                    <th>Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($merchant as $merchants)
                                <tr>
                                    <td>{{ $merchants->merchant_id }}</td>
                                    <td>{{ $merchants->merchant_name }}</td>
                                    <td>
                                        @if($merchants->image_url)
                                        <img src="{{ url($merchants->image_url) }}" alt="Merchant Image" width="100">
                                        @else
                                        No Image
                                        @endif
                                    </td>
                                    <td>
                                        <!-- Upload Image Button -->
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadImageModal{{ $merchants->merchant_id }}">Upload Image</button>
                                    </td>
                                </tr>

                                <!-- Modal for Image Upload -->
                                <div class="modal fade" id="uploadImageModal{{ $merchants->merchant_id }}" tabindex="-1" aria-labelledby="uploadImageModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="uploadImageModalLabel">Upload Image for {{ $merchants->merchant_name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('merchant.uploadImage', $merchants->merchant_id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label for="image" class="form-label">Choose Image</label>
                                                        <input type="file" class="form-control" id="image" name="image" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-success">Upload Image</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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