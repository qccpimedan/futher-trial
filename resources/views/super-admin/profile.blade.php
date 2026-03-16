@extends('layouts.app')
@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h1 class="text-dark">Form Profile</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Profile</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <div class="col-md-12 mb-lg-0">
            <div class="card card-primary card-outline">
                <div class="card-header pb-0 p-3 ">
                    <div class="row">
                        <div class="col-6 d-flex align-items-center">
                            <span>
                            <h3 class="card-title"><i class="fas fa-plus"></i> Tambah Akun</h3>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <div class="row">
                        <div class="col-md-6 mb-md-0 mb-4">
                            <div class="card card-plain border-radius-lg d-flex align-items-center flex-row">
                                <input type="text" name="name" class="form-control mb-0" placeholder="Nama Lengkap" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-md-0 mb-4">
                            <div class="card card-plain border-radius-lg d-flex align-items-center flex-row">
                                <input type="text" name="username" class="form-control mb-0" placeholder="Username" required>
                            </div>
                        </div>
                            <div class="col-md-6">
                                <div class="card card-plain border-radius-lg d-flex align-items-center flex-row">
                                    <input type="password" name="password" class="form-control mb-0" placeholder="Password" required>
                                </div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="card card-plain border-radius-lg d-flex align-items-center flex-row">
                                    <input type="email" name="email" class="form-control mb-0" placeholder="Email" required>
                                </div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="card card-plain border-radius-lg d-flex align-items-center flex-row">
                                    <select name="id_plan" class="form-control" required>
                                        <option value="">Pilih Plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}">{{ $plan->nama_plan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="card card-plain border-radius-lg d-flex align-items-center flex-row">
                                    <select name="id_role" class="form-select form-control mb-0" aria-label="Database Role" required>
                                        <option value="">Pilih Role Akses</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-plus"></i> Tambah Akun</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-cube"></i> Informasi Akun</h3>
            </div>
            <div class="card-body table-responsive">
                <div class="container-fluid">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Terjadi kesalahan:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                </div>
                <!-- <div class="form-group search-box">
                    <input type="text" class="form-control " placeholder="Cari akun...">
                </div> -->
                <table id="myTable" class="table text-center table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role Database</th>
                            <th>Plan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->roleModel)
                                    <span class="badge badge-primary">
                                        <i class="fas fa-user-tag mr-1"></i>{{ $user->roleModel->role_name }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $user->plan->nama_plan ?? '-' }}</td>
                            <td>
                                <a href="{{ route('users.edit', $user->uuid) }}" class="btn btn-warning btn-sm" title="Edit User">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('users.destroy', $user->uuid) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" title="Hapus User" onclick="return confirm('Hapus data {{$user->name}}')">
                                        <i class="fas fa-trash"></i> 
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection