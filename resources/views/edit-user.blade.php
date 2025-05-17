@extends('layout')

@section('content')
    <div class="container">
        <h4 class="mb-4 text-center">Edit User</h4>

        @if(session('success'))
            <div class="alert alert-success mb-3">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="col-md-6 col-lg-4 mx-auto">
            <form action="{{ route('add.user') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $data['id'] }}">

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" required autofocus value="{{ $data['name'] }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" required value="{{ $data['username'] }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required value="{{ $data['email'] }}">
                </div>
                @if(Auth::user()->role == 'admin')
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="member" {{ $data['role'] === 'member' ? 'selected' : '' }}>Member</option>
                        <option value="admin" {{ $data['role'] === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                @else
                    <input type="hidden" class="form-control" name="role" required value="{{ $data['role'] }}">
                @endif
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
