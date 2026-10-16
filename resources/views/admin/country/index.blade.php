@extends('admin.layouts.master')

@section('content')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Country Management</h4>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <!-- Phần Bảng Danh Sách (List Country) -->
        <div class="col-lg-8 col-md-7">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">List Country</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title (Name)</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($countries as $key => $country)
                                <tr>
                                    <td>{{ $country->id }}</td>
                                    <td>{{ $country->name }}</td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.country.delete', $country->id) }}" method="POST"  onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Chưa có quốc gia nào.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Phần Form Thêm Mới (Create/Add Country) -->
        <div class="col-lg-4 col-md-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Create Country</h4>
                    <form action="{{ route('admin.country.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Title (*)</label>
                            <input type="text" name="name" class="form-control form-control-line @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Nhập tên quốc gia...">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">Create Country</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection