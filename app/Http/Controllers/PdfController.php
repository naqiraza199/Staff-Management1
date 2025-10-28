<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PdfCoService;

use Illuminate\Support\Facades\Http;


class PdfController extends Controller
{
protected PdfCoService $service;

    public function __construct(PdfCoService $service)
    {
        $this->service = $service;
    }

    /**
     * Use the PDF.co example payload exactly as in the tester.
     * Visit: GET /pdfco/test-edit
     */
    public function testEdit()
    {
        // Exact payload from PDF.co tester (adapted to PHP array)
       $payload = [
                'url' => 'https://pdfco-test-files.s3.us-west-2.amazonaws.com/pdf-edit/sample.pdf',
                'annotations' => [
                    [
                        'text' => 'sample prefilled text',
                        'x' => 10,
                        'y' => 30,
                        'size' => 12,
                        'pages' => '0-',
                        'type' => 'TextField',
                        'id' => 'textfield1'
                    ],
                    [
                        'x' => 100,
                        'y' => 150,
                        'size' => 12,
                        'pages' => '0-',
                        'type' => 'Checkbox',
                        'id' => 'checkbox2'
                    ],
                    [
                        'x' => 100,
                        'y' => 170,
                        'size' => 12,
                        'pages' => '0-',
                        'link' => 'https://pdfco-test-files.s3.us-west-2.amazonaws.com/pdf-edit/logo.png',
                        'type' => 'CheckboxChecked',
                        'id' => 'checkbox3'
                    ]
                ],
                'images' => [
                    [
                        'url' => 'https://pdfco-test-files.s3.us-west-2.amazonaws.com/pdf-edit/logo.png',
                        'x' => 200,
                        'y' => 250,
                        'pages' => '0',
                        'link' => 'https://www.pdf.co',
                        'keepAspectRatio' => true // ✅ Fix here (or remove completely)
                    ]
                ],
                'name' => 'newDocument',
                'expiration' => 60,
            ];


        try {
            $result = $this->service->editAdd($payload);
            // Return full JSON result to browser for inspection
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Example: edit a custom PDF URL (you can pass url, text, coords via request)
     * POST /pdfco/edit-custom
     */
    public function editCustom(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'text' => 'required|string',
            'x' => 'required|integer',
            'y' => 'required|integer',
        ]);

        $annotations = [
            [
                'text' => $request->input('text'),
                'x' => (int)$request->input('x'),
                'y' => (int)$request->input('y'),
                'size' => 14,
                'pages' => '0',
                'type' => 'TextField',
                'id' => 'field_' . uniqid()
            ]
        ];

        $payload = [
            'url' => $request->input('url'),
            'annotations' => $annotations,
            'name' => 'edited_custom.pdf',
            'expiration' => 60
        ];

        try {
            $res = $this->service->editAdd($payload);
            return response()->json($res);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }


}
