<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agreement Signature Template</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
                background: linear-gradient(135deg, #2d3030 0%, #9b9ca5 100%);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow-x: hidden;
        }
        .container {
            background-color: #ffffff;
            width: 900px;
            max-width: 95%;
            padding: 60px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            margin: 30px;
            position: relative;
            overflow: hidden;
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 10px;
            background: linear-gradient(to right, #ff6b6b, #4ecdc4);
        }
        h1 {
            text-align: center;
            color: #1a202c;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 50px;
            letter-spacing: 1.2px;
            position: relative;
        }
        h1::after {
            content: '';
            width: 100px;
            height: 4px;
            background: linear-gradient(to right, #ff6b6b, #4ecdc4);
            display: block;
            margin: 15px auto;
            border-radius: 2px;
        }
        .agreement-text {
            color: #2d3748;
            line-height: 1.9;
            font-size: 16px;
            margin-bottom: 60px;
            background-color: #f7fafc;
            padding: 25px;
            border-radius: 10px;
            border-left: 5px solid #4ecdc4;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .agreement-text p {
            margin: 0 0 20px;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 30px;
        }
        .signature-box {
            flex: 1;
            min-width: 300px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .signature-box label {
            display: block;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 15px;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .signature-box canvas {
            border: 2px solid #e2e8f0;
            transition: border-color 0.3s ease;
        }
        .signature-box canvas:hover {
            border-color: #4ecdc4;
        }
        .date-field {
            margin-top: 20px;
        }
        .date-field label {
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 10px;
            display: block;
            font-size: 14px;
        }
        .date-field input {
            padding: 12px;
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .date-field input:focus {
            outline: none;
            border-color: #4ecdc4;
            box-shadow: 0 0 10px rgba(78, 205, 196, 0.3);
        }
        .clear-button {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #ff6b6b;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .clear-button:hover {
            background-color: #e53e3e;
            transform: scale(1.05);
        }

        .submit-button {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #3abbbdff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .submit-button:hover {
            background-color: #5be3cfff;
            transform: scale(1.05);
        }
        .footer {
            text-align: center;
            margin-top: 60px;
            color: #718096;
            font-size: 14px;
            border-top: 1px solid #edf2f7;
            padding-top: 25px;
        }
        .footer a {
            color: #4ecdc4;
            text-decoration: none;
            font-weight: 500;
        }
        .footer a:hover {
            color: #ff6b6b;
            text-decoration: underline;
        }
        @media (max-width: 600px) {
            .container {
                padding: 30px;
            }
            .signature-box {
                min-width: 100%;
            }
            h1 {
                font-size: 28px;
            }
            .agreement-text {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
  <div class="container">
    <h1>Agreement Signature Document</h1>
    <div class="agreement-text">
        <p>{{ $document->details }}</p>
    </div>
    <div class="signature-section">
        <div class="signature-box">
            @if(!$document->is_verified)
                <form method="POST" action="{{ route('documents.sign.store', $document->signature_token) }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block mb-2 font-semibold text-gray-700">Sign below:</label>
                        <canvas id="signature-pad-1" class="border w-full h-48 rounded-lg shadow-sm bg-gray-50" style="width: 100%;"></canvas>
                        <input type="hidden" name="signature" id="signature-input-1">
                        <div style="float: right; padding: 10px;">
                            <button type="button" id="clear-1" class="clear-button px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow">Clear Signature</button>
                            <button type="submit" onclick="saveSignature()" class="submit-button px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow">Submit Signature</button>
                        </div>
                    </div>
                </form>
            @else
                <div class="bg-green-50 border border-green-200 rounded-xl p-6 shadow-md">
                    <p class="text-green-700 font-semibold text-lg mb-4">
                        ✅ Document signed on: {{ \Carbon\Carbon::parse($document->signed_at)->format('F j, Y') }}
                    </p>
                    <div class="grid md:grid-cols-2 gap-6 items-start">
                        <div class="flex flex-col items-center">
                            <p class="text-gray-600 font-medium mb-2">Signature:</p>
                            <img src="{{ $document->signature }}" 
                                 alt="Signature" 
                                 class="border rounded-lg shadow bg-white w-72">
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
    function initializeSignaturePad(canvasId, inputId, clearId) {
        const canvas = document.getElementById(canvasId);
        if (canvas) {
            const signaturePad = new SignaturePad(canvas);

            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                signaturePad.clear();
            }

            window.addEventListener("resize", resizeCanvas);
            resizeCanvas();

            window.saveSignature = function () {
                if (!signaturePad.isEmpty()) {
                    document.getElementById(inputId).value = signaturePad.toDataURL();
                }
            }

            document.getElementById(clearId).addEventListener('click', () => {
                signaturePad.clear();
            });
        }
    }

    initializeSignaturePad('signature-pad-1', 'signature-input-1', 'clear-1');
</script>