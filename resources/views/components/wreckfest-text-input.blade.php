<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <x-wreckfest-color-input>
        <div class="fi-input-wrp fi-fo-text-input">
            <input
                x-ref="input"
                {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
                type="text"
                @keydown.enter.prevent
                {!! $isDisabled() ? 'disabled' : null !!}
                {!! ($placeholder = $getPlaceholder()) ? "placeholder=\"{$placeholder}\"" : null !!}
                {!! ($maxLength = $getMaxLength()) ? "maxlength=\"{$maxLength}\"" : null !!}
                {{ $attributes->merge($getExtraInputAttributes())->class([
                    'fi-input block w-full',
                ]) }}
            />
        </div>
    </x-wreckfest-color-input>
</x-dynamic-component>
