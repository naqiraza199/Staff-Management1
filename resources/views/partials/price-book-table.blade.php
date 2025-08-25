

<div class="rounded-lg border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-3 py-2 text-left">Price Book</th>
                <th class="px-3 py-2 text-center">Default</th>
                <th class="px-3 py-2 text-center">Charge Type</th>
                <th class="px-3 py-2 text-center">Provider Travel</th>
                <th class="px-3 py-2 text-right">Actions</th>
            </tr>
        </thead>
      <tbody>
    @forelse ($getRecord()->priceBooks ?? [] as $book)
        <tr class="border-t">
            <td class="px-3 py-2">{{ $book->name }}</td>
            <td class="px-3 py-2 text-center">
                @if ($book->pivot->is_default ?? false)
                    ✅
                @endif
            </td>
            <td class="px-3 py-2 text-center">
                {{ $book->fixed_price ? 'Fixed Price' : 'Per Hour' }}
            </td>
            <td class="px-3 py-2 text-center">
                {{ $book->provider_travel ? 'Yes' : 'No' }}
            </td>
            <td class="px-3 py-2 text-right text-primary-600">
                <a href="#">Configure</a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="px-3 py-2 text-center text-gray-500">
                No Price Books assigned.
            </td>
        </tr>
    @endforelse
</tbody>

    </table>
</div>
