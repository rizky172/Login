@extends('layout')

@section('content')
    <div class="container">
        <h4 class="mb-4 text-center">Profile</h4>

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
                <input type="hidden" name="type" value="profile">

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
                <div class="mb-3">
                    <label class="form-label">Foto</label>
                    @if (!empty($data['media']))
                        <div class="mb-2">
                            <img src="{{ $data['media'] }}" width="150" height="100" style="object-fit: cover;">
                        </div>
                    @endif
                    <input type="file" class="form-control" name="photo" accept="image/*">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password <small>(kosongkan jika tidak ingin ganti)</small></label>
                    <input type="password" class="form-control" name="password">
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" name="confirm_password">
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
