<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Congratulations on Achieving Your Target!</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; color: #333;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <tr>
            <td style="background-color: #4CAF50; padding: 20px 30px; color: white; text-align: center; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                <h2>🎉 Congratulations on Achieving Your Target! 🎉</h2>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px;">
                <p>Hi <strong>{{ $name }}</strong>,</p>
                <p>We’re thrilled to celebrate Our amazing achievement!</p>
                <p><strong>Congratulations on hitting our target for {{$month}}</strong> — your hard work and dedication truly paid off.</p>
                <p>At <strong>Quick Phone Fix and More</strong>, we’re proud to have such a driven and talented individual as part of the team.</p>
                <p>Keep up the great work — the future looks even brighter!</p>
                <br>
                <p style="font-style: italic;">Warm regards,<br>Team Quick Phone Fix Nnd More</p>
            </td>
        </tr>
        <tr>
            <td style="background-color: #f0f0f0; text-align: center; padding: 15px; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
                <small>© {{ date('Y') }} Quick Phone Fix and More. All rights reserved.</small>
            </td>
        </tr>
    </table>
</body>
</html>