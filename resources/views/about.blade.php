@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <h3 class="text-purple mb-3">About Wevent</h3>

        <p>
            <strong>Wevent</strong> is an event management platform for organizing,
            discovering, and participating in events.
        </p>

        <p>
            Wevent is a web platform created with the aim of facilitating the event management and participation. 
            This platform seeks to simplify the communication between organizers and participants, while also helping people organize and keep track of their schedules
        </p>

        <p>
            Whether planning a small family birthday party or coordinating a large corporate summit, Wevent provides a seamless experience with an user-friendly platform. The main goal is to centralize the entire process in one place, from invitations to participation management and feedback.
        </p>    
        <p>
            The platform supports both public or private events, free or paid participation, maximum capacity or not. A Guest user will be able to explore upcoming public events. Registered users are able to create and join events as well as interacting through comments and polls. Real time notifications are also supported, so the user doesn't miss any update.
        </p>    

        <h3 class="text-purple mb-3">Main Features</h3>
<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th scope="col" style="width: 25%;">Category</th>
                <th scope="col">Functionality</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>User Management</strong></td>
                <td>
                    User registration and authentication<br>
                    Profile costumization <br>
                    Attendee, Organizer and Admin roles
                </td>
            </tr>

            <tr>
                <td><strong>Event Management</strong></td>
                <td>
                    Create, edit, cancel and delete events<br>
                    Public and private events<br>
                </td>
            </tr>

            <tr>
                <td><strong>Participation System</strong></td>
                <td>
                    Join requests with approval or rejection<br>
                    Invitations to private events<br>
                </td>
            </tr>

            <tr>
                <td><strong>Interaction</strong></td>
                <td>
                    Comment system with replies<br>
                    Comment voting<br>
                    Polls creation and voting inside events
                </td>
            </tr>

            <tr>
                <td><strong>Notifications</strong></td>
                <td>
                    Real-time notifications using Pusher<br>
                    Event updates and changes<br>
                    Invitations, join request responses and poll alerts
                </td>
            </tr>

            <tr>
                <td><strong>Search & Discovery</strong></td>
                <td>
                    Full-text search on events<br>
                    Filtering by categories and tags<br>
                </td>
            </tr>

            <tr>
                <td><strong>Reports & Moderation</strong></td>
                <td>
                    Report events, comments and users<br><br>
                </td>
            </tr>

            <tr>
                <td><strong>Media Management</strong></td>
                <td>
                    Event photo uploads<br>
                    User profile images<br>
                </td>
            </tr>
        </tbody>
    </table>
</div>

        <p class="text-muted small">
            Built with Laravel and PostgreSQL.
        </p>
    </div>
</div>
@endsection
