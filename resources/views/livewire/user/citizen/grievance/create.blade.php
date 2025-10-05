<div class="px-10 py-5 flex flex-col justify-center items-center w-full">
    <div class="p-6 bg-gray-200/20 dark:bg-zinc-800/50 rounded-lg shadow-md w-full">

        <div class="flex flex-col gap-4 space-y-4">

            <!-- Is Anonymous (Flux Blade) -->
            <flux:field>
                <div class="flex flex-col gap-2">
                    <flux:label class="flex gap-2">
                        <flux:icon.question-mark-circle />
                        <span>Is Anonymous</span>
                    </flux:label>

                    <flux:radio.group wire:model="is_anonymous" variant="segmented">
                        <flux:radio :value="1" label="Yes" icon="eye" description="Your identity will be hidden from HR Liaisons." />
                        <flux:radio :value="0" label="No" icon="eye-slash" description="Your name will be visible to assigned HR Liaisons." />
                    </flux:radio.group>

                </div>
                <flux:error name="is_anonymous" />
            </flux:field>

            <!-- Grievance Type -->
            <flux:field>
                <div class="flex flex-col gap-2">
                    <flux:label class="flex gap-2">
                        <flux:icon.squares-2x2 />
                        <span>Grievance Type</span>
                    </flux:label>
                    <flux:input.group>
                        <x-searchable-select
                            name="grievance_type"
                            placeholder="Select a grievance type"
                            :options="['Complaints', 'Inquiry', 'Request', 'Suggestion/Feedback']"
                            :selected="$grievance_type"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="grievance_type" />
            </flux:field>

            <!-- Priority Level -->
            <flux:field>
                <div class="flex flex-col gap-2">
                    <flux:label class="flex gap-2">
                        <flux:icon.clock />
                        <span>Priority Level</span>
                    </flux:label>

                    <flux:radio.group wire:model="priority_level">
                        <flux:radio
                            value="Low"
                            label="Low"
                            description="Low priority grievance. May be handled later; urgent attention is not required."
                        />
                        <flux:radio
                            value="Normal"
                            label="Normal"
                            description="Normal priority grievance. Standard processing applies."
                        />
                        <flux:radio
                            value="High"
                            label="High"
                            description="High priority grievance. Requires immediate attention from HR Liaisons."
                        />
                    </flux:radio.group>
                </div>
                <flux:error name="priority_level" />
            </flux:field>

            <!-- Department, Title, Details, Files remain unchanged -->
            <flux:field>
                <div class="flex flex-col gap-2">
                    <flux:label class="flex gap-2">
                        <flux:icon.building-office />
                        <span>Department</span>
                    </flux:label>
                    <x-multiple-select
                        name="department"
                        placeholder="Select department(s)"
                        :options="$departmentOptions"
                    />
                </div>
                <flux:error name="department" />
            </flux:field>

            <flux:field>
                <div class="flex flex-col gap-2">
                    <flux:label class="flex gap-2">
                        <flux:icon.tag />
                        <span>Grievance Title</span>
                    </flux:label>
                    <flux:description>The title should summarize your grievance briefly.</flux:description>
                    <flux:input.group>
                        <flux:input
                            wire:model="grievance_title"
                            type="text"
                            name="grievance_title"
                            placeholder="Enter your grievance title"
                            clearable
                        />
                    </flux:input.group>
                </div>
                <flux:error name="grievance_title" />
            </flux:field>

            <flux:field>
                <div class="flex flex-col gap-2 w-full">
                    <flux:label class="flex gap-2">
                        <flux:icon.document-magnifying-glass />
                        <span>Grievance Details</span>
                    </flux:label>
                    {{ $this->form->getComponent('grievance_details') }}
                </div>
                <flux:error name="grievance_details" />
            </flux:field>

            <flux:field>
                <div class="flex flex-col gap-2 w-full">
                    <flux:label class="flex gap-2">
                        <flux:icon.folder />
                        <span>Grievance Files</span>
                    </flux:label>
                    {{ $this->form->getComponent('grievance_files') }}
                </div>
                <flux:error name="grievance_files" />
            </flux:field>

        </div>

        <!-- Submit Button -->
        <div class="mt-4 flex justify-end w-full">
            <flux:button
                variant="primary"
                icon="check"
                color="blue"
                type="button"
                class="w-full bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out"
                x-on:click="$dispatch('open-modal', 'confirm-submit')"
            >
                Submit
            </flux:button>
        </div>

    </div>

    <!-- Confirm Submit Modal -->
    <x-modal name="confirm-submit" class="p-6">
        <div class="flex items-center space-x-3 p-4">
            <div class="flex-shrink-0">
                <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-mc_primary_color" />
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Confirm Submission
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Are you sure you want to submit this grievance? <br>
                    Once submitted, it will be assigned to the HR Liaison(s).
                </p>
            </div>
        </div>
        <div class="mt-6 flex justify-end space-x-3 p-4">
            <flux:button
                variant="primary"
                color="zinc"
                class="px-4 py-2 rounded-md"
                x-on:click="$dispatch('close-modal', 'confirm-submit')"
            >
                Cancel
            </flux:button>
            <flux:button
                variant="primary"
                color="blue"
                icon="pencil-square"
                class="bg-mc_primary_color dark:bg-blue-500 px-4 py-2 rounded-md"
                wire:click="submit"
                x-on:click="$dispatch('close-modal', 'confirm-submit')"
            >
                Yes, Submit
            </flux:button>
        </div>
    </x-modal>
</div>
