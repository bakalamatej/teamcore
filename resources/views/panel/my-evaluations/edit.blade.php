<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <div class="mb-4 pb-4 border-b-2 border-gray-200">
            <h1 class="my-heading text-2xl">{{ __('Edit Evaluation') }}</h1>
        </div>

        <form method="POST" action="{{ route('panel.my-evaluations.update', $evaluation) }}" class="space-y-6">
            @csrf
            @method('PATCH')

            @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="text-sm text-red-600 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <x-input-label :value="__('Coach')" />
                <p class="mt-1 text-gray-700 font-medium">{{ $evaluation->coach?->member?->full_name ?? '-' }}</p>
            </div>

            <div>
                <x-input-label for="rating" :value="__('Rating (1-5)')" />
                <x-text-input
                    id="rating"
                    name="rating"
                    type="number"
                    min="1"
                    max="5"
                    step="0.1"
                    class="mt-1 block w-[70%] lg:w-[50%]"
                    :value="old('rating', $evaluation->rating)"
                />
                <x-input-error :messages="$errors->get('rating')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="comment" :value="__('Comment')" />
                <textarea
                    id="comment"
                    name="comment"
                    rows="4"
                    class="mt-1 block w-[70%] lg:w-[50%] border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                >{{ old('comment', $evaluation->comment) }}</textarea>
                <x-input-error :messages="$errors->get('comment')" class="mt-2" />
            </div>

            <div class="flex gap-4">
                <x-primary-button>{{ __('Save') }}</x-primary-button>
                <x-danger-button :href="route('panel.my-evaluations.index')">
                    {{ __('Discard') }}
                </x-danger-button>
            </div>
        </form>
    </div>
</x-panel-layout>