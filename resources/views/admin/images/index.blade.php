@extends('layouts.master')

@section('title')
    Image Management
@endsection

@section('css')
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet" />
    <!-- Bootstrap Bundle JS (includes Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection

@section('content')
  @component('components.breadcrumb')
    @slot('li_1') Images @endslot
    @slot('li_2') Image Management @endslot
    @slot('title') Image List @endslot
  @endcomponent

  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center gy-3">
            <div class="col-sm">
              <h5 class="card-title mb-0">Image List</h5>
            </div>
            <div class="col-sm-auto">
              <div class="d-flex flex-wrap gap-1">
                <a href="images/upload" class="btn btn-primary create-btn">
                    <i class="ri-add-line me-1 align-bottom"></i> Add New Image
                </a>
              </div>
            </div>
          </div>
        </div>

        <div class="card-body">
            <table id="imageTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
      </div>
    </div>
  </div>

  @section('script')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(function () {
            $('#imageTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('images.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { 
                        data: 'image', 
                        name: 'image', 
                        orderable: false, 
                        searchable: false,
                        render: function(data, type, row) {
                            return '<img src="' + data + '" width="60" height="60" class="rounded" />';
                        }
                    },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });
    </script>
  @endsection
@endsection
