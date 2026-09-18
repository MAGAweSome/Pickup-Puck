<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="color-scheme" content="light dark" />
    <meta name="supported-color-schemes" content="light dark" />
    <title>@yield('title', 'Pickup Puck')</title>
    <style type="text/css">
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        body { margin: 0; padding: 0; width: 100% !important; min-width: 100%; background-color: #051426; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        .email-btn:hover { background-color: #f2fbff !important; color: #051426 !important; }
        @media only screen and (max-width: 620px) {
            .email-container { width: 100% !important; max-width: 100% !important; }
            .card-padding { padding: 28px 20px !important; }
            .header-padding { padding: 24px 20px 16px 20px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #051426; color: #f2fbff; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <!-- Hidden Preheader Text for Email Inbox Previews -->
    <div style="display: none; font-size: 1px; color: #051426; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all;">
        @yield('preheader', 'Pickup Puck Hockey League Notification')
        &#847; &zwnj; &nbsp; &#8199; &shy; &#847; &zwnj; &nbsp; &#8199; &shy; &#847; &zwnj; &nbsp; &#8199; &shy;
    </div>

    <!-- Main Wrapper Table -->
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #051426; min-height: 100%;">
        <tr>
            <td align="center" style="padding: 30px 15px 40px 15px;">
                <!-- Centered Content Container (max-width: 580px) -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" class="email-container" style="max-width: 580px; margin: 0 auto;">
                    
                    <!-- Header / Branding -->
                    <tr>
                        <td align="center" class="header-padding" style="padding: 10px 0 24px 0;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <div style="display: inline-block; text-align: center;">
                                            <div style="display: inline-block; width: 44px; height: 44px; background-color: #0d1d30; border: 1px solid rgba(167, 233, 255, 0.35); border-radius: 12px; margin-bottom: 8px; line-height: 44px; text-align: center;">
                                                <span style="font-size: 22px; line-height: 44px;">🏒</span>
                                            </div>
                                            <div style="font-size: 20px; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; color: #ffffff; margin-top: 4px;">
                                                PICKUP <span style="color: #a7e9ff;">PUCK</span>
                                            </div>
                                            <div style="font-size: 10px; font-weight: 600; letter-spacing: 0.18em; text-transform: uppercase; color: #94a3b8; margin-top: 2px;">
                                                Pickup Hockey League
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Main Email Card -->
                    <tr>
                        <td>
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #0d1d30; border: 1px solid #1e3856; border-top: 3px solid #a7e9ff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35); overflow: hidden;">
                                <tr>
                                    <td class="card-padding" style="padding: 36px 32px 32px 32px;">
                                        @yield('content')

                                        @hasSection('actionUrl')
                                            <!-- Fallback Raw URL Section -->
                                            <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #1e3856;">
                                                <p style="margin: 0 0 8px 0; font-size: 12px; line-height: 18px; color: #94a3b8;">
                                                    If you're having trouble clicking the button, copy and paste the URL below into your web browser:
                                                </p>
                                                <p style="margin: 0; font-size: 12px; line-height: 18px; word-break: break-all; color: #a7e9ff; font-family: monospace; background-color: #071322; padding: 10px 12px; border-radius: 6px; border: 1px solid #1a3048;">
                                                    <a href="@yield('actionUrl')" target="_blank" style="color: #a7e9ff; text-decoration: none;">@yield('actionUrl')</a>
                                                </p>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding: 24px 16px 0 16px;">
                            <p style="margin: 0 0 6px 0; font-size: 12px; line-height: 18px; color: #64748b; text-align: center;">
                                Sent with &hearts; from <a href="{{ url('/') }}" target="_blank" style="color: #94a3b8; text-decoration: underline;">Pickup Puck</a>.
                            </p>
                            <p style="margin: 0 0 6px 0; font-size: 11px; line-height: 16px; color: #475569; text-align: center;">
                                This is an automated security message regarding your Pickup Puck account.
                            </p>
                            <p style="margin: 0; font-size: 11px; line-height: 16px; color: #475569; text-align: center;">
                                &copy; {{ date('Y') }} Pickup Puck. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
