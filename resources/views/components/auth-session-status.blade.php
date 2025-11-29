@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-neon-lime-600 bg-neon-lime-50 border border-neon-lime-200 rounded-lg p-3']) }}>
        {{ $status }}
    </div>
@endif
