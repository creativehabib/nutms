<x-layouts::app.welcome
    :title="$title ?? null"
    :description="$description ?? null"
    :keywords="$keywords ?? null"
    :image="$image ?? null"
>
    {{ $slot }}
</x-layouts::app.welcome>
