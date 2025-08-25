<?php

function convertOfficeToPdf($relativePath)
{
    $libreOfficePath = '"C:\Program Files\LibreOffice\program\soffice.exe"'; // Adjust if different
    $fullPath = storage_path("app/public/{$relativePath}");
    $outputDir = storage_path("app/public/converted-pdfs");

    if (!file_exists($outputDir)) {
        mkdir($outputDir, 0775, true);
    }

    // Escape paths for Windows
    $escapedInput = escapeshellarg($fullPath);
    $escapedOutput = escapeshellarg($outputDir);

    // Use double quotes around soffice.exe path for Windows
    $command = "{$libreOfficePath} --headless --convert-to pdf --outdir {$escapedOutput} {$escapedInput}";
    exec($command, $output, $status);

    if ($status !== 0) {
        return null;
    }

    $baseName = pathinfo($relativePath, PATHINFO_FILENAME);
    $convertedPath = "converted-pdfs/{$baseName}.pdf";

    return \Illuminate\Support\Facades\Storage::disk('public')->exists($convertedPath) ? $convertedPath : null;
}
