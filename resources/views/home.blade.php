@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @role('user')
                        <span>USER</span> <br>
                    @endrole

                    @role('admin')
                        <span style="color:red">ADMIN</span> <br>
                        <a style="color:red" target="_blank" href="/admin/panel">Админка</a>
                    @endrole

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
