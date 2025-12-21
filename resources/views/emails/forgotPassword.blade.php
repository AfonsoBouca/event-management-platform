<h3>Hi {{ $mailData['name'] }},</h3>

<p>You requested a password reset.</p>

<p>
    Click the link below to reset your password:
</p>

<p>
    <a href="{{ $mailData['reset_link'] }}">
        Reset password
    </a>
</p>

<p>If you didn’t request this, ignore this email.</p>

<h5>— Wevent Team</h5>
