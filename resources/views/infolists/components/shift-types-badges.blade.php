<div class="flex flex-wrap gap-2">
    @if ($getState())
        @foreach ($getState() as $shift)
            <span style="font-size: 11px;box-shadow: rgba(6, 24, 44, 0.18) 0px 0px 0px 2px, rgba(6, 24, 44, 0.65) 0px 4px 6px -1px, rgba(255, 255, 255, 0.08) 0px 1px 0px inset;" 
                  class="inline-flex items-center px-3 py-1 rounded-md bg-gray-100 text-sm font-medium text-gray-800">
                <span class="w-3 h-3 rounded-full mr-2" 
                      style="background-color: {{ $shift->color }};margin-right: 10px;"></span>
                {{ $shift->name }}
            </span>
        @endforeach
    @else
        <p class="text-gray-500 text-sm">No shift types available</p>
    @endif
</div>
