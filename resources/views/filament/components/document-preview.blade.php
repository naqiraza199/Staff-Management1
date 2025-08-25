@if(isset($error))
    <div class="p-6 text-center">
        <div class="text-red-500 font-semibold">
            <x-heroicon-s-exclamation-triangle class="w-6 h-6 inline mb-1" />
            {{ $error }}
        </div>
    </div>
@else
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $fileName }}</h3>

        @if($fileExtension === 'pdf')
            <embed src="{{ $fileUrl }}" type="application/pdf" class="w-full h-96" />
        @elseif($fileExtension === 'txt')
            <div class="p-4 bg-gray-100 border rounded max-h-96 overflow-y-auto">
                <pre class="whitespace-pre-wrap text-sm text-gray-800">{{ Storage::disk('public')->get($filePath) }}</pre>
            </div>
        @else
            <div class="text-gray-500 text-center">
                <x-heroicon-s-document class="w-12 h-12 mx-auto" />
                <p class="mt-2">Preview not supported for this file type.</p>
            </div>
        @endif
    </div>
@endif
