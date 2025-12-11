@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow">
            <div class="card-header bg-white">
                <h4 class="text-purple mb-0">Create New Poll</h4>
                <small class="text-muted">Event: {{ $event->eventname }}</small>
            </div>
            
            <div class="card-body">
                <form action="{{ route('polls.store', $event->eventid) }}" method="POST" id="pollForm">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Poll Title <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}" 
                               placeholder="e.g., What time works best for you?"
                               maxlength="100"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                  id="content" 
                                  name="content" 
                                  rows="3"
                                  placeholder="Describe what you want participants to vote on..."
                                  required>{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Options <span class="text-danger">*</span></label>
                        <small class="text-muted d-block mb-2">Add at least 2 options for participants to vote on.</small>
                        
                        <div id="optionsContainer">
                            <div class="input-group mb-2">
                                <input type="text" 
                                       class="form-control" 
                                       name="options[]" 
                                       placeholder="Option 1"
                                       maxlength="255"
                                       required>
                                <button type="button" class="btn btn-outline-danger remove-option" disabled>
                                    <i class="bi bi-trash"></i> &times;
                                </button>
                            </div>
                            <div class="input-group mb-2">
                                <input type="text" 
                                       class="form-control" 
                                       name="options[]" 
                                       placeholder="Option 2"
                                       maxlength="255"
                                       required>
                                <button type="button" class="btn btn-outline-danger remove-option" disabled>
                                    <i class="bi bi-trash"></i> &times;
                                </button>
                            </div>
                        </div>
                        
                        @error('options')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                        @error('options.*')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                        
                        <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="addOption">
                            + Add Option
                        </button>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('polls.index', $event->eventid) }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-purple">Create Poll</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('optionsContainer');
    const addButton = document.getElementById('addOption');
    let optionCount = 2;
    
    addButton.addEventListener('click', function() {
        optionCount++;
        const div = document.createElement('div');
        div.className = 'input-group mb-2';
        div.innerHTML = `
            <input type="text" 
                   class="form-control" 
                   name="options[]" 
                   placeholder="Option ${optionCount}"
                   maxlength="255"
                   required>
            <button type="button" class="btn btn-outline-danger remove-option">
                &times;
            </button>
        `;
        container.appendChild(div);
        updateRemoveButtons();
    });
    
    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-option') || e.target.closest('.remove-option')) {
            const button = e.target.classList.contains('remove-option') ? e.target : e.target.closest('.remove-option');
            const inputGroup = button.closest('.input-group');
            if (container.querySelectorAll('.input-group').length > 2) {
                inputGroup.remove();
                updateRemoveButtons();
            }
        }
    });
    
    function updateRemoveButtons() {
        const groups = container.querySelectorAll('.input-group');
        groups.forEach(group => {
            const btn = group.querySelector('.remove-option');
            btn.disabled = groups.length <= 2;
        });
    }
});
</script>
@endsection
