@extends('emails.layouts.master')

@section('title', 'Reset Your Password - Pickup Puck')
@section('preheader', 'Instructions to reset your Pickup Puck account password.')
@section('actionUrl', $url)

@section('content')
    <!-- Visual Badge Icon -->
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin-bottom: 20px;">
        <tr>
            <td style="width: 46px; height: 46px; background-color: rgba(167, 233, 255, 0.12); border: 1px solid rgba(167, 233, 255, 0.3); border-radius: 10px; text-align: center; vertical-align: middle;">
                <span style="font-size: 22px; line-height: 1;">🔐</span>
            </td>
        </tr>
    </table>

    <!-- Heading -->
    <h1 style="margin: 0 0 16px 0; font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: -0.02em;">
        Password Reset Request
    </h1>

    <!-- Greeting & Intro -->
    <p style="margin: 0 0 14px 0; font-size: 15px; line-height: 24px; color: #f2fbff;">
        Hi <strong>{{ $user->name ?? 'Player' }}</strong>,
    </p>
    <p style="margin: 0 0 20px 0; font-size: 15px; line-height: 24px; color: #cbd5e1;">
        We received a request to reset your password for your <strong>Pickup Puck</strong> account. Click the button below to select a new password and get back on the ice:
    </p>

    <!-- Primary Action Button -->
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin: 28px 0;">
        <tr>
            <td align="center" style="border-radius: 8px; background-color: #a7e9ff;">
                <a href="{{ $url }}" target="_blank" class="email-btn" style="background-color: #a7e9ff; border: 1px solid #7dd3fc; border-radius: 8px; color: #051426 !important; display: inline-block; font-size: 15px; font-weight: 700; letter-spacing: 0.02em; padding: 14px 32px; text-decoration: none; box-shadow: 0 4px 14px rgba(167, 233, 255, 0.25);">
                    Reset Password &rarr;
                </a>
            </td>
        </tr>
    </table>

    <!-- Expiration Warning Callout -->
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #071524; border-left: 3px solid #38bdf8; border-radius: 6px; margin-bottom: 24px;">
        <tr>
            <td style="padding: 12px 16px;">
                <p style="margin: 0; font-size: 13px; line-height: 20px; color: #94a3b8;">
                    ⏳ This password reset link is valid for <strong style="color: #e2e8f0;">{{ $count ?? 60 }} minutes</strong>.
                </p>
            </td>
        </tr>
    </table>

    <!-- Security Reassurance -->
    <p style="margin: 0; font-size: 13px; line-height: 20px; color: #64748b;">
        If you did not make this request, you can safely disregard this email. Your password will not change until you access the link above and create a new one.
    </p>
@endsection
