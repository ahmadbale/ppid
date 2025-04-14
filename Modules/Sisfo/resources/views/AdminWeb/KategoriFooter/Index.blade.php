<!-- views/AdminWeb/KategoriFooter/index.blade.php -->

@extends('sisfo::layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('adminweb/kategori-footer/addData') }}')"
                    class="btn btn-sm btn-success mt-1">
                    <i class="fas fa-plus"></i> Tambah
                </button>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="table-wrapper-responsive">
                <table class="table table-responsive-stack table-bordered table-striped table-hover table-sm"
                    id="table_kategori_footer">
                    <thead class="text-center">
                        <tr>
                            <th>Nomor</th>
                            {{-- <th>Kode Footer</th> --}}
                            <th>Nama Footer</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded by DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for CRUD operations -->
    <div id="myModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <!-- Modal content will be loaded here -->
            </div>
        </div>
    </div>
@endsection

@push('css')
@endpush

@push('js')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#table_kategori_footer').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '{{ url('adminweb/kategori-footer/getData') }}',
                    type: 'GET',
                },
                columns: [{
                        data: 0
                    },
                    {
                        data:2
                    },
                    {
                        data: 3,
                        orderable: false
                    },
                ],
                createdRow: function(row, data, dataIndex) {
                    $('td', row).eq(0).attr('table-data-label', 'Nomor') .addClass('text-center');
                    // $('td', row).eq(1).attr('table-data-label', 'Kode Footer') .addClass('text-center');
                    $('td', row).eq(1).attr('table-data-label', 'Nama Footer');
                    $('td', row).eq(2).attr('table-data-label', 'Aksi') .addClass('text-center');
                },
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
                }
            });
        });

        //load data
        function modalAction(url) {
            $('#myModal .modal-content').html(
                '<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Loading...</p></div>'
                );
            $('#myModal').modal('show');

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $('#myModal .modal-content').html(response);
                },
                error: function(xhr) {
                    $('#myModal .modal-content').html(
                        '<div class="modal-header"><h5 class="modal-title">Error</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body"><div class="alert alert-danger">Terjadi kesalahan saat memuat data. Silakan coba lagi.</div></div>'
                        );
                }
            });
        }

        function showDetailKategoriFooter(id) {
            modalAction('{{ url('adminweb/kategori-footer/detailData') }}/' + id);
        }

        // Function to handle delete kategori footer
        function deleteKategoriFooter(id) {
            modalAction('{{ url('adminweb/kategori-footer/deleteData') }}/' + id);
        }
    </script>
@endpush
