<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <x-wreckfest-color-input>
        <textarea
            x-ref="input"
            {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
            {!! $isDisabled() ? 'disabled' : null !!}
            {!! ($placeholder = $getPlaceholder()) ? "placeholder=\"{$placeholder}\"" : null !!}
            {!! ($rows = $getRows()) ? "rows=\"{$rows}\"" : 'rows="3"' !!}
            {{ $attributes->merge($getExtraInputAttributes())->class([
                'fi-input block w-full border-none py-1.5 text-base text-gray-950 transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6 bg-white dark:bg-white/5 px-3 rounded-lg shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-white/10',
            ]) }}
        ></textarea>
    </x-wreckfest-color-input>
</x-dynamic-component>
