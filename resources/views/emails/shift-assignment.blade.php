<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shift Assignment</title>
    <style>
        /* Reset styles */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
        }
        
        /* Main styles */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px 10px;
        }
        .container {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .header-icon {
            width: 48px;
            height: 48px;
            margin-bottom: 10px;
        }
        .content {
            padding: 25px 20px;
            background-color: #ffffff;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #1F2937;
        }
        .message {
            margin-bottom: 20px;
            color: #4B5563;
            font-size: 15px;
        }
        .message p {
            margin: 0 0 10px 0;
        }
        .shift-details {
            background-color: #f9fafb;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .shift-details h3 {
            margin: 0 0 15px 0;
            color: #4F46E5;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .detail-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #E5E7EB;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #6B7280;
            width: 100px;
            flex-shrink: 0;
            font-size: 14px;
        }
        .detail-value {
            color: #1F2937;
            font-size: 14px;
            font-weight: 500;
        }
        .instructions {
            background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%);
            border-left: 4px solid #4F46E5;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 0 10px 10px 0;
        }
        .instructions h4 {
            margin: 0 0 8px 0;
            color: #1E40AF;
            font-size: 15px;
            font-weight: 600;
        }
        .instructions p {
            margin: 0;
            color: #1E3A8A;
            font-size: 14px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #9CA3AF;
            font-size: 13px;
            background-color: #f9fafb;
            border-top: 1px solid #E5E7EB;
        }
        .footer p {
            margin: 0 0 5px 0;
        }
        .call-to-action {
            text-align: center;
            padding: 20px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            color: white;
            padding: 14px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            box-shadow: 0 4px 6px rgba(79, 70, 229, 0.3);
        }
        
        /* Mobile responsive styles */
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 10px;
            }
            .container {
                border-radius: 8px;
            }
            .header {
                padding: 20px 15px;
            }
            .header h1 {
                font-size: 20px;
            }
            .content {
                padding: 20px 15px;
            }
            .greeting {
                font-size: 16px;
            }
            .message {
                font-size: 14px;
            }
            .shift-details {
                padding: 15px;
            }
            .shift-details h3 {
                font-size: 16px;
            }
            .detail-row {
                flex-direction: column;
                padding: 8px 0;
            }
            .detail-label {
                width: auto;
                margin-bottom: 4px;
                font-size: 13px;
            }
            .detail-value {
                font-size: 14px;
            }
            .instructions {
                padding: 12px 15px;
            }
            .instructions h4 {
                font-size: 14px;
            }
            .instructions p {
                font-size: 13px;
            }
            .footer {
                padding: 15px;
                font-size: 12px;
            }
            .cta-button {
                padding: 12px 24px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="container">
            <div class="header">
                <svg class="header-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12.75 12.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM7.5 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM8.25 17.25a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 0 1.5h-3a.75.75 0 0 1-.75-.75Zm.75 2.25a.75.75 0 0 0 0 1.5h3a.75.75 0 0 0 0-1.5h-3ZM6.75 22.5a.75.75 0 0 1 .75-.75h7.5a.75.75 0 0 1 0 1.5h-7.5a.75.75 0 0 1-.75-.75Zm.75-2.75a.75.75 0 0 0 0 1.5h3a.75.75 0 0 0 0-1.5h-3Z"/>
                    <path fill-rule="evenodd" d="M6.75 2.25A.75.75 0 0 1 7.5 3v1.5h9V3A.75.75 0 0 1 18 3v1.5h.75a3 3 0 0 1 3 3v11.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V7.5a3 3 0 0 1 3-3H6V3a.75.75 0 0 1 .75-.75Zm13.5 9a1.5 1.5 0 0 0-1.5-1.5H5.25a1.5 1.5 0 0 0-1.5 1.5v7.5a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5v-7.5Z" clip-rule="evenodd"/>
                </svg>
                <h1>New Shift Assignment</h1>
            </div>
            
            <div class="content">
                <div class="greeting">Dear {{ $staffName }},</div>
                
                <div class="message">
                    <p><strong>{{ $adminName }}</strong> has assigned a new shift to you. Please review the shift details below and ensure you perform this work properly as scheduled.</p>
                    <p>We trust you to carry out your duties with professionalism and dedication. If you have any questions or concerns about this assignment, please don't hesitate to reach out.</p>
                </div>

                <div class="shift-details">
                    <h3>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                            <line x1="16" x2="16" y1="2" y2="6"/>
                            <line x1="8" x2="8" y1="2" y2="6"/>
                            <line x1="3" x2="21" y1="10" y2="10"/>
                            <path d="M8 14h.01"/>
                            <path d="M12 14h.01"/>
                            <path d="M16 14h.01"/>
                            <path d="M8 18h.01"/>
                            <path d="M12 18h.01"/>
                            <path d="M16 18h.01"/>
                        </svg>
                        Shift Details
                    </h3>
                    
                    <div class="detail-row">
                        <span class="detail-label">Client:</span>
                        <span class="detail-value">{{ $clientName }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Shift Type:</span>
                        <span class="detail-value">{{ $shiftType }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Date:</span>
                        <span class="detail-value">{{ $formattedDate }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Time:</span>
                        <span class="detail-value">{{ $formattedStartTime }} - {{ $formattedEndTime }}</span>
                    </div>
                    
                    @if($address)
                    <div class="detail-row">
                        <span class="detail-label">Address:</span>
                        <span class="detail-value">
                            @if($unitApartmentNumber)
                                {{ $address }}, {{ $unitApartmentNumber }}
                            @else
                                {{ $address }}
                            @endif
                        </span>
                    </div>
                    @endif
                </div>

                @if($description)
                <div class="instructions">
                    <h4>Additional Instructions:</h4>
                    <p>{!! $description !!}</p>
                </div>
                @endif

                <div class="call-to-action">
                    <p style="color: #4B5563; margin-bottom: 15px; font-size: 14px;">Please ensure you arrive on time and are well-prepared for your shift.</p>
                </div>
            </div>

            <div class="footer">
                <p>This is an automated message from the Staff Management System.</p>
                <p>Please do not reply directly to this email.</p>
            </div>
        </div>
    </div>
</body>
</html>
