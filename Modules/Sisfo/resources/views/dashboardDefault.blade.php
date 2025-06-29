@extends('sisfo::layouts.template')

@section('content')
    <div class="card">
        <h5 class="card-header font-weight-bold">Level Pengguna Saat Ini</h5>
        <div class="card-body">
            <div class="row">
                @php
                    $roles = [
                        ['title' => 'Super Admin', 'icon' => 'fas fa-user-shield', 'color' => 'bg-primary'],
                        ['title' => 'Administrator', 'icon' => 'fas fa-user-cog', 'color' => 'bg-success'],
                        ['title' => 'Pimpinan', 'icon' => 'fas fa-chalkboard-teacher', 'color' => 'bg-warning'],
                        ['title' => 'Verifikator', 'icon' => 'fas fa-user-check', 'color' => 'bg-info'],
                    ];
                @endphp

                @foreach ($roles as $role)
                    <div class="col-lg-3 col-6 mb-4">
                        <div class="small-box {{ $role['color'] }} text-white shadow rounded d-flex flex-column justify-content-between" style="min-height: 100px; padding: 1rem;">
                            <div class="inner">
                                <h4 class="font-weight-bold">{{ $role['title'] }}</h4>
                            </div>
                            <div class="icon mt-auto text-end">
                                <i class="{{ $role['icon'] }} fa-3x"></i>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
@endsection
