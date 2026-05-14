@php
    $tenureOptions = [
        'freehold' => 'Freehold',
        'leasehold' => 'Leasehold',
        'rented_in' => 'Rented in',
        'pagri' => 'Pagri',
        'other' => 'Other',
    ];
    $occupancyOptions = [
        'rented_out' => 'Rented out',
        'self_use' => 'Self use',
        'vacant_plot' => 'Vacant plot',
        'vacant_built' => 'Vacant (built)',
    ];
    $areaUnitOptions = [
        'sqm' => 'sqm',
        'sqft' => 'sqft',
        'sqyd' => 'sqyd',
    ];
    $existingContacts = old('contacts', $property->contacts ?? []);
    if (empty($existingContacts)) {
        $existingContacts = [['name' => '', 'phone' => '', 'role' => '', 'notes' => '']];
    }
@endphp

<form
    method="POST"
    action="{{ $property->exists ? route('properties.update', $property) : route('properties.store') }}"
    class="space-y-5 pb-32 md:pb-0"
>
    @csrf
    @if ($property->exists)
        @method('PATCH')
    @endif

    @if ($errors->any())
        <div class="rounded-2xl bg-red-50 dark:bg-red-900/30 ring-1 ring-red-200 dark:ring-red-800 px-4 py-3 flex items-start gap-3">
            <x-icon name="exclamation-triangle" class="h-5 w-5 text-red-600 dark:text-red-300 shrink-0 mt-0.5" />
            <div class="min-w-0 flex-1 text-sm text-red-700 dark:text-red-200">
                <p class="font-semibold">Please fix the highlighted fields.</p>
                <ul class="mt-1 list-disc list-inside space-y-0.5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <x-form-section title="Identity & ownership" icon="identification" description="What the property is called and who holds the title.">
        <div>
            <x-input-label for="name" value="Name *" />
            <x-text-input id="name" name="name" type="text" class="mt-1.5" :value="old('name', $property->name)" required />
            <x-input-error :messages="$errors->get('name')" />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="title_holder" value="Title holder" />
                <x-text-input id="title_holder" name="title_holder" type="text" class="mt-1.5" :value="old('title_holder', $property->title_holder)" placeholder="e.g. Amritsar Bombay Carriers" />
                <x-input-error :messages="$errors->get('title_holder')" />
            </div>
            <div>
                <x-input-label for="rera_number" value="RERA number" />
                <x-text-input id="rera_number" name="rera_number" type="text" class="mt-1.5" :value="old('rera_number', $property->rera_number)" />
                <p class="mt-1.5 text-xs text-ink-500 dark:text-ink-400">Optional · applies to projects registered after May 2017.</p>
                <x-input-error :messages="$errors->get('rera_number')" />
            </div>
        </div>
    </x-form-section>

    <x-form-section title="Address" icon="map-pin" description="Where the property is located.">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <x-input-label for="address_line1" value="Address line 1" />
                <x-text-input id="address_line1" name="address_line1" type="text" class="mt-1.5" :value="old('address_line1', $property->address_line1)" />
                <x-input-error :messages="$errors->get('address_line1')" />
            </div>
            <div class="sm:col-span-2">
                <x-input-label for="address_line2" value="Address line 2" />
                <x-text-input id="address_line2" name="address_line2" type="text" class="mt-1.5" :value="old('address_line2', $property->address_line2)" />
            </div>
            <div>
                <x-input-label for="city" value="City" />
                <x-text-input id="city" name="city" type="text" class="mt-1.5" :value="old('city', $property->city)" />
            </div>
            <div>
                <x-input-label for="state" value="State / UT" />
                <x-select id="state" name="state" class="mt-1.5" placeholder="—" :options="\App\Support\IndianStates::all()" :selected="old('state', $property->state)" />
                <x-input-error :messages="$errors->get('state')" />
            </div>
            <div>
                <x-input-label for="pincode" value="Pincode" />
                <x-text-input id="pincode" name="pincode" type="text" inputmode="numeric" maxlength="6" class="mt-1.5" :value="old('pincode', $property->pincode)" placeholder="110042" />
                <x-input-error :messages="$errors->get('pincode')" />
            </div>
        </div>
    </x-form-section>

    <x-form-section title="Tenure & occupancy" icon="key" description="How the property is held and who is currently in it.">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="tenure" value="Tenure" />
                <x-select id="tenure" name="tenure" class="mt-1.5" placeholder="—" :options="$tenureOptions" :selected="old('tenure', $property->tenure)" />
            </div>
            <div>
                <x-input-label for="tenure_authority" value="Authority / landlord" />
                <x-text-input id="tenure_authority" name="tenure_authority" type="text" class="mt-1.5" :value="old('tenure_authority', $property->tenure_authority)" placeholder="e.g. DDA" />
            </div>
            <div>
                <x-input-label for="occupancy_status" value="Occupancy status" />
                <x-select id="occupancy_status" name="occupancy_status" class="mt-1.5" placeholder="—" :options="$occupancyOptions" :selected="old('occupancy_status', $property->occupancy_status)" />
            </div>
            <div>
                <x-input-label for="tenant_or_occupant" value="Tenant / occupant" />
                <x-text-input id="tenant_or_occupant" name="tenant_or_occupant" type="text" class="mt-1.5" :value="old('tenant_or_occupant', $property->tenant_or_occupant)" />
            </div>
        </div>
    </x-form-section>

    <x-form-section title="Construction & area" icon="building-office" description="Built form and size.">
        <div>
            <x-input-label for="construction" value="Construction" />
            <x-text-input id="construction" name="construction" type="text" class="mt-1.5" :value="old('construction', $property->construction)" placeholder="e.g. Basement + Ground Floor + First + Second" />
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-2">
                <x-input-label for="area_value" value="Area" />
                <x-text-input id="area_value" name="area_value" type="number" step="0.01" min="0" class="mt-1.5" :value="old('area_value', $property->area_value)" />
                <x-input-error :messages="$errors->get('area_value')" />
            </div>
            <div>
                <x-input-label for="area_unit" value="Unit" />
                <x-select id="area_unit" name="area_unit" class="mt-1.5" placeholder="—" :options="$areaUnitOptions" :selected="old('area_unit', $property->area_unit)" />
            </div>
        </div>
    </x-form-section>

    <x-form-section title="Financials" icon="currency-rupee" description="Imputed value, yearly rent, and yield.">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <x-input-label for="imputed_value_inr" value="Imputed value (₹)" />
                <x-text-input id="imputed_value_inr" name="imputed_value_inr" type="number" step="0.01" min="0" class="mt-1.5" :value="old('imputed_value_inr', $property->imputed_value_inr)" />
                <x-input-error :messages="$errors->get('imputed_value_inr')" />
            </div>
            <div>
                <x-input-label for="rent_yearly_inr" value="Rent / year (₹)" />
                <x-text-input id="rent_yearly_inr" name="rent_yearly_inr" type="number" step="0.01" class="mt-1.5" :value="old('rent_yearly_inr', $property->rent_yearly_inr)" />
                <x-input-error :messages="$errors->get('rent_yearly_inr')" />
            </div>
            <div>
                <x-input-label for="yield_percent" value="Yield % (override)" />
                <x-text-input id="yield_percent" name="yield_percent" type="number" step="0.01" min="0" max="100" class="mt-1.5" :value="old('yield_percent', $property->yield_percent)" />
                <x-input-error :messages="$errors->get('yield_percent')" />
            </div>
        </div>
        <div class="rounded-2xl bg-primary-50 dark:bg-primary-950/30 ring-1 ring-primary-100 dark:ring-primary-900/40 p-3.5 text-xs text-primary-800 dark:text-primary-200 flex items-start gap-2">
            <x-icon name="information-circle" class="h-4 w-4 shrink-0 mt-0.5" />
            <span>Leave yield blank to auto-calc from <span class="font-semibold">rent ÷ value</span>.</span>
        </div>
    </x-form-section>

    <x-form-section title="Contacts" icon="users" description="People who manage or are connected with this property.">
        <div
            x-data="{
                contacts: @js($existingContacts),
                add() { this.contacts.push({name:'',phone:'',role:'',notes:''}); },
                remove(i) {
                    this.contacts.splice(i, 1);
                    if (this.contacts.length === 0) this.add();
                },
            }"
            class="space-y-3"
        >
            <template x-for="(c, i) in contacts" :key="i">
                <div class="rounded-2xl ring-1 ring-ink-100 dark:ring-ink-800 p-4 bg-surface-50 dark:bg-gray-900/40">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-start">
                        <input type="text" :name="`contacts[${i}][name]`" x-model="c.name" placeholder="Name"
                            class="sm:col-span-3 block w-full rounded-xl border-ink-200 dark:border-ink-700 dark:bg-gray-900 dark:text-ink-100 placeholder:text-ink-400 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 px-3.5 py-2 text-sm shadow-sm" />
                        <input type="text" :name="`contacts[${i}][phone]`" x-model="c.phone" placeholder="+91 9811011555"
                            class="sm:col-span-3 block w-full rounded-xl border-ink-200 dark:border-ink-700 dark:bg-gray-900 dark:text-ink-100 placeholder:text-ink-400 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 px-3.5 py-2 text-sm shadow-sm" />
                        <input type="text" :name="`contacts[${i}][role]`" x-model="c.role" placeholder="Role"
                            class="sm:col-span-2 block w-full rounded-xl border-ink-200 dark:border-ink-700 dark:bg-gray-900 dark:text-ink-100 placeholder:text-ink-400 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 px-3.5 py-2 text-sm shadow-sm" />
                        <input type="text" :name="`contacts[${i}][notes]`" x-model="c.notes" placeholder="Notes"
                            class="sm:col-span-3 block w-full rounded-xl border-ink-200 dark:border-ink-700 dark:bg-gray-900 dark:text-ink-100 placeholder:text-ink-400 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 px-3.5 py-2 text-sm shadow-sm" />
                        <button type="button" @click="remove(i)" class="sm:col-span-1 inline-flex items-center justify-center rounded-xl text-ink-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 h-10 transition" aria-label="Remove contact">
                            <x-icon name="trash" class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </template>
            <button type="button" @click="add()"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-primary-200 dark:border-primary-900 text-primary-700 dark:text-primary-200 py-3 text-sm font-semibold hover:bg-primary-50 dark:hover:bg-primary-950/40 transition">
                <x-icon name="plus" class="h-4 w-4" />
                Add contact
            </button>
        </div>
    </x-form-section>

    <x-form-section title="Notes & status" icon="document-text" description="Anything else worth remembering.">
        <div>
            <x-input-label for="keys_location" value="Location of keys & documents" />
            <x-text-input id="keys_location" name="keys_location" type="text" class="mt-1.5" :value="old('keys_location', $property->keys_location)" placeholder="e.g. 24 Motia khan" />
        </div>
        <div>
            <x-input-label for="extra_notes" value="Extra notes" />
            <x-textarea id="extra_notes" name="extra_notes" rows="4" class="mt-1.5">{{ old('extra_notes', $property->extra_notes) }}</x-textarea>
        </div>
        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input type="hidden" name="is_data_complete" value="0">
            <input type="checkbox" name="is_data_complete" value="1"
                @checked(old('is_data_complete', $property->is_data_complete))
                class="rounded border-ink-300 dark:border-ink-700 text-primary-600 focus:ring-primary-500 h-4 w-4" />
            <span class="text-sm font-medium text-ink-700 dark:text-ink-200">Data entry complete</span>
        </label>
    </x-form-section>

    {{-- Action bar: sticky on mobile (above bottom-nav), inline on desktop --}}
    <div class="fixed inset-x-0 bottom-[calc(4.5rem+env(safe-area-inset-bottom))] md:static md:bottom-auto z-30 md:z-auto bg-white/95 dark:bg-gray-900/95 md:bg-transparent dark:md:bg-transparent backdrop-blur md:backdrop-blur-none border-t md:border-0 border-ink-100 dark:border-ink-800 px-4 py-3 sm:px-6 md:p-0 md:pt-2 md:flex md:items-center md:justify-end md:gap-3">
        <a href="{{ $property->exists ? route('properties.show', $property) : route('properties.index') }}"
           class="block text-center md:inline px-4 py-2.5 text-sm font-semibold text-ink-600 dark:text-ink-300 hover:bg-surface-100 dark:hover:bg-gray-800 rounded-xl transition">Cancel</a>
        <x-primary-button type="submit" class="w-full md:w-auto mt-2 md:mt-0">
            <x-icon name="check-circle" class="h-4 w-4" />
            {{ $property->exists ? 'Save changes' : 'Create property' }}
        </x-primary-button>
    </div>
</form>
