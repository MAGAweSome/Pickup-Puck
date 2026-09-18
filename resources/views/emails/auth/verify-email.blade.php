@extends('emails.layouts.master')

@section('title', 'Verify Your Email Address - Pickup Puck')
@section('preheader', 'Confirm your email address to complete your Pickup Puck registration.')
@section('actionUrl', $url)

@section('content')
    <!-- Visual Badge Icon -->
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin-bottom: 20px;">
        <tr>
            <td style="width: 46px; height: 46px; background-color: rgba(167, 233, 255, 0.12); border: 1px solid rgba(167, 233, 255, 0.3); border-radius: 10px; text-align: center; vertical-align: middle;">
                <span style="font-size: 22px; line-height: 1;">✉️</span>
            </td>
        </tr>
    </table>

    <!-- Heading -->
    <h1 style="margin: 0 0 16px 0; font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: -0.02em;">
        Verify Your Email Address
    </h1>

    <!-- Greeting & Intro -->
    <p style="margin: 0 0 14px 0; font-size: 15px; line-height: 24px; color: #f2fbff;">
        Welcome to Pickup Puck, <strong>{{ $user->name ?? 'Player' }}</strong>!
    </p>
    <p style="margin: 0 0 20px 0; font-size: 15px; line-height: 24px; color: #cbd5e1;">
        Thanks for joining our pickup hockey league! To complete your registration and unlock game RSVPs, automated balanced rosters, and attendance tracking, please verify your email address by tapping the button below:
    </p>

    <!-- Primary Action Button -->
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin: 28px 0;">
        <tr>
            <td align="center" style="border-radius: 8px; background-color: #a7e9ff;">
                <a href="{{ $url }}" target="_blank" class="email-btn" style="background-color: #a7e9ff; border: 1px solid #7dd3fc; border-radius: 8px; color: #051426 !important; display: inline-block; font-size: 15px; font-weight: 700; letter-spacing: 0.02em; padding: 14px 32px; text-decoration: none; box-shadow: 0 4px 14px rgba(167, 233, 255, 0.25);">
                    Verify Email Address &rarr;
                </a>
            </td>
        </tr>
    </table>

    <!-- What You Get Card -->
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #071524; border: 1px solid #1a3048; border-radius: 8px; margin-bottom: 24px;">
        <tr>
            <td style="padding: 16px;">
                <p style="margin: 0 0 8px 0; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #a7e9ff;">
                    What's Next Once Verified:
                </p>
                <p style="margin: 0 0 6px 0; font-size: 13px; line-height: 20px; color: #cbd5e1;">
                    🏒 <strong>Sign up for upcoming games</strong> with your preferred player or goalie role.
                </p>
                <p style="margin: 0 0 6px 0; font-size: 13px; line-height: 20px; color: #cbd5e1;">
                    ⚖️ <strong>Live Balanced Team Reveal</strong> automatically unlocked at T-30 minutes.
                </p>
                <p style="margin: 0; font-size: 13px; line-height: 20px; color: #cbd5e1;">
                    📅 <strong>1-click Add to Calendar</strong> (.ics with rink alerts) so you never miss puck drop.
                </p>
            </td>
        </tr>
    </table>

    <!-- Disclaimer -->
    <p style="margin: 0; font-size: 13px; line-height: 20px; color: #64748b;">
        If you did not create a Pickup Puck account, no further action is required and you can safely disregard this email.
    </p>
@endsection
