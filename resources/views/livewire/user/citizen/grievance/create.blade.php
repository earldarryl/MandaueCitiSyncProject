<div class="w-full h-full z-20 flex flex-col">
    <header class="p-3">
        <h1 class="text-3xl font-medium flex items-center gap-2">
            <flux:icon.document-plus />
            Grievance Application Form
        </h1>
    </header>

    <div class="relative w-full overflow-y-auto">
        <div class="w-full mx-auto p-3 shadow-lg max-w-4xl">
            <form wire:submit.prevent="submit" class="flex flex-col gap-4" enctype="multipart/form-data">
                <div class="w-full flex flex-col lg:flex-row gap-4">
                    <div class="w-full flex gap-2 rounded-md">
                        <flux:field class="w-full">
                                <flux:label>Grievance Type</flux:label>
                                <flux:description>The type/nature of your grievance</flux:description>

                                <flux:input.group>
                                    <x-select
                                        name="grievance_type"
                                        placeholder="Choose grievance type"
                                        :options="['Complaints', 'Inquiry', 'Request', 'Suggestion/Feedback']"
                                        wire:model="grievance_type"
                                    />
                                </flux:input.group>

                            <flux:error name="grievance_type" />
                        </flux:field>
                    </div>
                    <div class="w-full flex gap-2 rounded-md">
                        <flux:field class="w-full">
                                <flux:label>Department</flux:label>
                                <flux:description>The appropriate department that handles/manages your grievance</flux:description>

                                <flux:input.group>
                                    <x-multiple-select
                                        name="department"
                                        placeholder="Choose department type"
                                        :options="$departmentList"
                                        wire:model="department"
                                    />
                                </flux:input.group>

                            <flux:error name="department" />
                        </flux:field>
                    </div>
                </div>

                <flux:field>
                    <flux:label>Grievance Title</flux:label>
                    <flux:description>The specified title of your grievance</flux:description>
                    <flux:input
                        wire:model="grievance_title"
                    />
                    <flux:error name="grievance_title" />
                </flux:field>

                <flux:field>
                    <flux:label>Grievance Details</flux:label>
                    <flux:description>The specified details/brief summary of your grievance</flux:description>
                    <flux:textarea
                        wire:model="grievance_details"
                        resize="none"
                    />
                    <flux:error name="grievance_details" />
                </flux:field>

              <flux:field>
                    <flux:label>Upload Attachment</flux:label>
                    <flux:description>The additional files/documents serve as evidence</flux:description>

                    <div x-data="{ progress: 0, isUploading: false, isComplete: false }"
                        x-on:livewire-upload-start="isUploading = true; isComplete = false; progress = 0"
                        x-on:livewire-upload-finish="isUploading = false; progress = 100; isComplete = true"
                        x-on:livewire-upload-error="isUploading = false; isComplete = false; progress = 0"
                        x-on:livewire-upload-progress="progress = $event.detail.progress"
                        x-on:form-submitted.window="isComplete = false">  <!-- ✅ hide complete msg on submit -->

                        <!-- File input (disabled while uploading/submitting) -->
                        <flux:input type="file"
                            wire:model="grievance_files"
                            multiple
                            x-bind:disabled="isUploading"
                            wire:loading.attr="disabled"
                            wire:target="submit,grievance_files"
                        />

                        <!-- Progress bar -->
                        <template x-if="isUploading">
                            <div class="mt-2">
                                <div class="w-full bg-gray-200 rounded h-2">
                                    <div class="bg-blue-600 h-2 rounded transition-all duration-200"
                                        :style="'width: ' + progress + '%'"></div>
                                </div>
                                <div class="text-sm text-gray-600 mt-1">
                                    Uploading... <span x-text="progress"></span>%
                                </div>
                            </div>
                        </template>

                        <!-- Completed message -->
                        <template x-if="isComplete && !isUploading">
                            <div class="mt-2 text-sm text-green-600 font-medium">
                                ✅ Uploading complete
                            </div>
                        </template>
                    </div>

                    <flux:error name="grievance_files" />
                </flux:field>



                <div class="flex justify-end">
                    <flux:button
                        type="submit"
                        variant="primary"
                        class="w-full group bg-mc_primary_color text-white dark:bg-blue-700
                            hover:bg-mc_primary_color dark:hover:bg-blue-700 dark:hover:text-white
                            transition duration-300 ease-in-out"
                        wire:loading.attr="disabled"
                        wire:loading.class="cursor-not-allowed"
                        wire:target="submit,grievance_files"
                    >
                        <span class="flex items-center justify-center gap-2">
                            <span>
                                <flux:icon.arrow-up-tray variant="mini"/>
                            </span>
                            <span>{{ __('Submit') }}</span>
                        </span>
                    </flux:button>
                </div>

            </form>
        </div>
    </div>
</div>
