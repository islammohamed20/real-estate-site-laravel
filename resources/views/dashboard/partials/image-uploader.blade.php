{{--
    Reusable drag & drop image uploader for dashboard forms.

    Usage:
        @include('dashboard.partials.image-uploader', [
            'label'      => __('Upload Images'),      // optional, defaults to __('Upload Images')
            'existing'   => $project?->images,        // optional, array of stored image paths
            'selectable' => true,                     // optional, adds a radio to choose a main image
            'selected'   => $project?->cover_image_path, // optional, currently selected main image
            'inputName'  => 'main_image',             // optional, radio input name for the main image
        ])
--}}
<div
    x-data="{
        files: [],
        dragging: false,
        addFiles(fileList) {
            if (! fileList) return;
            for (const file of fileList) {
                if (file.type.startsWith('image/')) {
                    this.files.push(file);
                }
            }
            this.syncInput();
        },
        removeFile(index) {
            this.files.splice(index, 1);
            this.syncInput();
        },
        previewUrl(file) {
            return URL.createObjectURL(file);
        },
        syncInput() {
            const dt = new DataTransfer();
            for (const file of this.files) {
                dt.items.add(file);
            }
            this.$refs.input.files = dt.files;
        },
    }"
    class="{{ ($compact ?? false) ? 'space-y-3' : 'space-y-4' }}"
>
    <div>
        <label class="mb-2 block text-sm font-medium text-slate-300">{{ $label ?? __('Upload Images') }}</label>

        <input type="file" name="images[]" accept="image/*" multiple
               x-ref="input" class="hidden"
               @change="addFiles($refs.input.files)">

        <div
            @dragover.prevent="dragging = true"
            @dragleave.prevent="dragging = false"
            @drop.prevent="addFiles($event.dataTransfer.files)"
            @click="$refs.input.click()"
            :class="dragging ? 'border-brand-500/70 bg-brand-500/10' : 'border-white/15 bg-white/5 hover:border-brand-500/40 hover:bg-white/10'"
            class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed text-center transition {{ ($compact ?? false) ? 'gap-2 px-4 py-5' : 'gap-3 px-6 py-10' }}"
        >
            <svg class="{{ ($compact ?? false) ? 'h-8 w-8' : 'h-10 w-10' }} text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <div>
                <p class="text-sm font-semibold text-white">{{ __('Drag & drop images here') }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ __('or click to browse — multiple files allowed') }}</p>
                <p class="mt-1 text-[10px] text-slate-600">{{ __('JPG, PNG, WEBP, GIF — max 8MB each') }}</p>
            </div>
        </div>

        @error('images')
            <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
        @enderror

        @if ($errors->has('images.*'))
            <ul class="mt-1 space-y-1">
                @foreach ($errors->get('images.*') as $key => $messages)
                    @foreach ($messages as $message)
                        <li class="text-xs text-rose-400">{{ $key }}: {{ $message }}</li>
                    @endforeach
                @endforeach
            </ul>
        @endif
    </div>

    {{-- New files preview --}}
    <template x-if="files.length > 0">
        <div>
            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('New images') }}</p>
            <div class="grid grid-cols-3 gap-3">
                <template x-for="(file, index) in files" :key="index">
                    <div class="relative block overflow-hidden rounded-2xl border border-brand-500/30">
                        @if ($selectable ?? false)
                            <label class="absolute left-0 top-0 z-10 flex cursor-pointer items-center gap-1 bg-slate-950/70 px-2 py-1 text-[10px] font-semibold text-brand-300">
                                <input type="radio" name="{{ $inputName ?? 'main_image' }}" :value="'new:' + index" class="h-3 w-3 border-slate-500 text-brand-600 focus:ring-brand-500/20"> {{ __('Main') }}
                            </label>
                        @endif
                        <img :src="previewUrl(file)" alt="" class="h-24 w-full object-cover">
                        <button type="button" @click="removeFile(index)" class="absolute right-1.5 top-1.5 flex h-6 w-6 items-center justify-center rounded-full bg-slate-950/70 text-xs text-rose-300 transition hover:bg-rose-500/30">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 6l12 12M18 6L6 18" stroke-width="2" stroke-linecap="round"/></svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </template>

    {{-- Existing stored images --}}
    @if (! empty($existing))
        <div>
            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Current images') }}</p>
            <div class="grid grid-cols-3 gap-3">
                @foreach ($existing as $img)
                    @if (is_string($img))
                        <label class="relative block overflow-hidden rounded-2xl border border-white/10">
                            @if ($selectable ?? false)
                                <span class="absolute left-0 top-0 z-10 flex items-center gap-1 bg-slate-950/70 px-2 py-1 text-[10px] font-semibold text-brand-300">
                                    <input type="radio" name="{{ $inputName ?? 'main_image' }}" value="{{ $img }}" @checked(($selected ?? null) === $img) class="h-3 w-3 border-slate-500 text-brand-600 focus:ring-brand-500/20"> {{ __('Main') }}
                                </span>
                            @endif
                            <img src="{{ asset('storage/'.$img) }}" alt="" class="h-24 w-full object-cover">
                            <span class="absolute inset-x-0 bottom-0 flex items-center justify-center bg-slate-950/70 py-1 text-[10px] font-semibold text-rose-300">
                                <input type="checkbox" name="remove_images[]" value="{{ $img }}" class="mr-1 h-3 w-3 rounded border-slate-500"> {{ __('Remove') }}
                            </span>
                        </label>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</div>
