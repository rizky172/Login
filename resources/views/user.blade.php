@extends('layout')

@section('content')
<div class="container">
    <h2 class="text-center mb-4">Users</h2>
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
    @if(Auth::user()->role == 'admin')
        <div class="mb-3">
            @if(request('deleted') == 1)
                <a href="{{ route('index.user') }}" class="btn btn-sm btn-success">List</a>
            @else
                <a href="{{ route('form.user') }}" class="btn btn-sm btn-primary">Tambah</a>
                <a href="{{ route('index.user', ['deleted' => 1]) }}" class="btn btn-sm btn-danger">Sampah</a>
            @endif
        </div>
    @endif
    <table class="table table-bordered table-striped bg-white shadow">
        <thead class="table-dark">
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Username</th>
                <th class="text-center">Nama</th>
                <th class="text-center">Email</th>
                <th class="text-center">Role</th>
                <th class="text-center">Photo</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $i => $x)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $x['username'] }}</td>
                    <td>{{ $x['name'] }}</td>
                    <td>{{ $x['email'] }}</td>
                    <td class="text-center">{{ $x['role'] }}</td>
                    <td class="text-center">
                        @if($x['url'])
                            <a href="{{ $x['url'] }}" target="_blank">
                                <img src="{{ $x['url'] }}" alt="Foto" width="50" height="50">
                            </a>
                        @else
                            <span class="text-muted">Tidak ada foto</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if(request('deleted') == 1)
                            <a href="{{ route('restore.user', ['id' => $x['id']]) }}" 
                                class="btn btn-sm btn-success"
                                onclick="return confirm('Apakah Anda yakin ingin memulihkan data ini?')">
                                Pulihkan
                            </a>
                            <form action="{{ route('delete.user', ['id' => $x['id'], 'permanent' => 'permanent']) }}" method="POST" style="display:inline;" 
                                onsubmit="return confirm('Apakah anda yakin ingin menghapus permanent data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete Permanent</button>
                            </form>
                        @else
                            <a href="{{ route('detail', ['id' => $x['id']]) }}" class="btn btn-sm btn-primary">Edit</a>
                            <form action="{{ route('delete.user', ['id' => $x['id']]) }}" method="POST" style="display:inline;" 
                                onsubmit="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
