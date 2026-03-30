@props([
    'modelType', // e.g. 'event', 'club', 'member_club'
    'modelId',
    'label' => 'Upload File',
    'categories' => [],
    'canUpload' => true,
])

@if($canUpload)
<div x-data="fileUpload({
    modelType: '{{ $modelType }}',
    modelId: {{ $modelId }},
    uploadUrl: '{{ route('files.upload', [$modelType, $modelId]) }}',
    categories: @js($categories),
})">
    <x-secondary-button type="button" x-on:click="$dispatch('open-modal', 'file-upload-{{ $modelType }}-{{ $modelId }}')">
        {{ $label }}
    </x-secondary-button>

    <x-modal name="file-upload-{{ $modelType }}-{{ $modelId }}" :show="false" focusable>
        <div class="p-6 space-y-4">
            <h2 class="my-heading">{{ __('Upload File') }}</h2>

            <div x-show="error" x-cloak>
                <p class="text-sm text-red-600" x-text="error"></p>
            </div>

            <div x-show="success" x-cloak>
                <p class="text-sm text-green-600">{{ __('File uploaded successfully.') }}</p>
            </div>

            <div>
                <x-input-label :value="__('File')" />
                <input
                    type="file"
                    x-ref="fileInput"
                    x-on:change="handleFileChange($event)"
                    class="mt-1 block w-full text-sm text-gray-700 border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:border-indigo-500 focus:ring-indigo-500"
                    accept=".jpeg,.jpg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx"
                />
                <p class="text-xs text-gray-500 mt-1">{{ __('Max 10MB. Allowed: images, PDF, Word, Excel') }}</p>
            </div>

            <div>
                <x-input-label :value="__('Category')" />
                <select
                    x-model="selectedCategory"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="">{{ __('Select category') }}</option>
                    <template x-for="category in categories" :key="category.file_category_id">
                        <option :value="category.file_category_id" x-text="category.name"></option>
                    </template>
                </select>
            </div>

            <div x-show="selectedFile" x-cloak>
                <p class="text-sm text-gray-600">
                    <span x-text="selectedFile?.name"></span>
                    (<span x-text="formatSize(selectedFile?.size)"></span>)
                </p>
            </div>

            <div class="flex justify-end gap-3 mt-8">
                <x-secondary-button type="button" x-on:click="discard()">
                    {{ __('Close') }}
                </x-secondary-button>
                <x-primary-button
                    type="button"
                    x-on:click="upload()"
                    x-bind:disabled="uploading || !selectedFile || !selectedCategory"
                >
                    <span x-show="!uploading">{{ __('Upload') }}</span>
                    <span x-show="uploading">{{ __('Uploading...') }}</span>
                </x-primary-button>
            </div>
        </div>
    </x-modal>
</div>
@endif