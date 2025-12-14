    {{-- Current Participants --}}
    <div class="card shadow-sm">

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($participants as $participant)
                        <tr>
                            <td>{{ $participant->username }}</td>
                            <td>{{ $participant->email }}</td>
                            <td>
                                @if($participant->pivot->role === 'Organizer')
                                    <span class="badge bg-purple">Organizer</span>
                                @else
                                    <span class="badge bg-secondary">Attendee</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>