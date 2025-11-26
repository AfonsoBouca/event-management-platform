@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-purple text-white">
                    <h4 class="mb-0">Edit User Information</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user.update') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" name="password" id="password" class="form-control">
                            <div class="form-text">Leave blank to keep current password. Minimum 6 characters.</div>
                        </div>

                        <div class="mb-3">
                            <label for="profileimage" class="form-label">Profile Image</label>
                            <input type="file" name="profileimage" id="profileimage" class="form-control" accept="image/*">
                        </div>

                        <button type="submit" class="btn btn-purple w-100">Save Changes</button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mt-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Danger Zone</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Deleting your account is permanent and cannot be undone.</p>
                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        Delete Account
                    </button>
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="{{ route('user.profile', $user->username) }}" class="text-purple">← Back to Profile</a>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Account Deletion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete your account? This action <strong>cannot be undone</strong>.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('user.delete') }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Yes, delete my account</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
