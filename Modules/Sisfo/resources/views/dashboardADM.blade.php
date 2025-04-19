@extends('sisfo::layouts.template')

@section('content')
    {{-- <div class="card"> --}}
    <div class="card-body">
        <div class="row">

            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary text-white shadow rounded">
                    <div class="inner">
                        <h4 class="font-weight-bold">E-Form</h4>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-alt fa-3x"></i>
                    </div>
                    <a href="#" class="small-box-footer text-white">Akses Menu  <i
                        class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success text-white shadow rounded">
                    <div class="inner">
                        <h4 class="font-weight-bold">Berita</h4>
                    </div>
                    <div class="icon">
                        <i class="fas fa-newspaper fa-3x"></i>
                    </div>
                    <a href="#" class="small-box-footer text-white">Akses Menu  <i
                        class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning text-white shadow rounded">
                    <div class="inner">
                        <h4 class="font-weight-bold">Pengumuman</h4>
                    </div>
                    <div class="icon">
                        <i class="fas fa-bullhorn fa-3x"></i>
                    </div>
                    <a href="#" class="small-box-footer text-white">Akses Menu  <i
                        class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info text-white shadow rounded">
                    <div class="inner">
                        <h4 class="font-weight-bold">Galeri</h4>
                    </div>
                    <div class="icon">
                        <i class="fas fa-images fa-3x"></i>
                    </div>
                    <a href="#" class="small-box-footer text-white">Akses Menu  <i
                        class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </div>
    {{-- </div> --}}
@endsection
