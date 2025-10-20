<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <x-wreckfest-color-input>
        <div class="fi-input-wrp fi-fo-textarea">
            <textarea
                x-ref="input"
                {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
                {!! $isDisabled() ? 'disabled' : null !!}
                {!! ($placeholder = $getPlaceholder()) ? "placeholder=\"{$placeholder}\"" : null !!}
                {!! ($rows = $getRows()) ? "rows=\"{$rows}\"" : 'rows="3"' !!}
                {{ $attributes->merge($getExtraInputAttributes())->class([
                    'fi-input block w-full',
                ]) }}
            ></textarea>
        </div>
    </x-wreckfest-color-input>
</x-dynamic-component>
