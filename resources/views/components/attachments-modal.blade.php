<div class="space-y-4">
    @foreach ($attachments as $file)
        @php
            $path = $file['file_path'] ?? '';
            $url = asset('storage/' . $path);
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        @endphp

        @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
            <div class="border rounded-lg overflow-hidden">
                <img src="{{ $url }}" alt="Attachment" class="w-full h-auto">
            </div>
        @elseif (in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx']))
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                <span class="text-sm text-gray-700">
                    📄 {{ basename($path) }}
                </span>
                <a href="{{ $url }}" target="_blank" download
                   class="text-primary-600 hover:underline font-medium">
                    Download
                </a>
            </div>
        @else
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                <span class="text-sm text-gray-700">
                    📁 {{ basename($path) }}
                </span>
                <a href="{{ $url }}" target="_blank" download
                   class="text-primary-600 hover:underline font-medium">
                    Download
                </a>
            </div>
        @endif
    @endforeach
</div>
