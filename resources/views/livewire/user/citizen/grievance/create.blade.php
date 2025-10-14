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

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

            <!-- Grievance Type -->
                <flux:field>
                    <div class="flex flex-col gap-4">
                        <flux:label class="flex gap-2">
                            <flux:icon.squares-2x2 />
                            <span>Grievance Type</span>
                        </flux:label>
                            <flux:radio.group wire:model="grievance_type">
                                <div class="bg-amber-500/20 p-3 rounded-t-lg">
                                    <flux:radio
                                        value="Complaint"
                                        label="Complaint"
                                        description="Reports an issue or dissatisfaction needing corrective action."
                                    />
                                </div>
                                <div class="bg-violet-500/20 p-3">
                                    <flux:radio
                                        value="Inquiry"
                                        label="Inquiry"
                                        description="Seeks clarification or information on HR-related matters."
                                    />
                                </div>
                                <div class="bg-green-500/20 p-3 rounded-b-lg">
                                    <flux:radio
                                        value="Request"
                                        label="Request"
                                        description="Asks for assistance, approval, or support from HR."
                                    />
                                </div>
                            </flux:radio.group>
                    </div>
                    <flux:error name="grievance_type" />
                </flux:field>

                <!-- Priority Level -->
                <flux:field>
                    <div class="flex flex-col gap-4">
                        <flux:label class="flex gap-2">
                            <flux:icon.clock />
                            <span>Priority Level</span>
                        </flux:label>
                            <flux:radio.group wire:model="priority_level">
                                <div class="bg-green-500/20 p-3 rounded-t-lg">
                                    <flux:radio
                                        value="Low"
                                        label="Low"
                                        description="Low priority grievance. May be handled later; urgent attention is not required."
                                    />
                                </div>
                                <div class="bg-mc_primary_color/20 p-3">
                                    <flux:radio
                                        value="Normal"
                                        label="Normal"
                                        description="Normal priority grievance. Standard processing applies."
                                    />
                                </div>
                                <div class="bg-red-500/20 p-3 rounded-b-lg">
                                    <flux:radio
                                        value="High"
                                        label="High"
                                        description="High priority grievance. Requires immediate attention from HR Liaisons."
                                    />
                                </div>
                            </flux:radio.group>
                    </div>
                    <flux:error name="priority_level" />
                </flux:field>
            </div>


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
            <flux:modal.trigger name="confirm-submit">
                <flux:button
                    variant="primary"
                    icon="check"
                    color="blue"
                    type="button"
                    class="w-full bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out"
                >
                    Submit
                </flux:button>
            </flux:modal.trigger>
        </div>

    </div>

    <!-- Confirm Submit Modal -->
    <flux:modal name="confirm-submit" wire:model.self="showConfirmSubmitModal" class="md:w-96">
        <div class="flex flex-col items-center text-center p-6 space-y-4">
            <div class="flex items-center justify-center w-16 h-16 rounded-full bg-mc_primary_color/10">
                <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-mc_primary_color" />
            </div>

            <flux:heading size="lg" class="font-semibold text-gray-800 dark:text-gray-100">
                Confirm Submission
            </flux:heading>

            <flux:text class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">
                Are you sure you want to submit this grievance? <br>
                Once submitted, it will be assigned to the HR Liaison(s).
            </flux:text>
        </div>

        <div class="flex items-center justify-center w-full">
            <div
                wire:loading.remove
                wire:target="submit"
                class="flex justify-end gap-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-4 rounded-b-2xl">
                <flux:modal.close>
                    <flux:button variant="subtle" class="border border-gray-200 dark:border-zinc-800">Cancel</flux:button>
                </flux:modal.close>
                <flux:button
                    variant="primary"
                    color="blue"
                    icon="pencil-square"
                    class="bg-mc_primary_color px-4 py-2 rounded-md"
                    wire:click="submit"
                >
                    Yes, Submit
                </flux:button>
            </div>
            <div wire:loading wire:target="submit">
                <div class="flex items-center justify-center gap-2 w-full">
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                </div>
            </div>
        </div>

    </flux:modal>

    <flux:modal wire:model.self="showConfirmModal" :closable="false">
        <div class="p-6 flex flex-col items-center text-center space-y-4">
            <div class="rounded-full bg-red-100 p-3 text-red-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z" />
                </svg>
            </div>

            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                Missing Required Information
            </h2>

            <p class="text-sm text-gray-600 dark:text-gray-400">
                Some required fields are incomplete or invalid. Please review your input before proceeding.
            </p>

            <div class="flex justify-center gap-3 mt-4">
                <flux:button
                    variant="subtle" class="border border-gray-200 dark:border-zinc-800"
                    @click="$wire.showConfirmModal = false"
                >
                    Close
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
