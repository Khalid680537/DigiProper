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
    class="space-y-6"
>
    @csrf
    @if ($property->exists)
        @method('PATCH')
    @endif

    @if ($errors->any())
        <div class="rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-700 dark:text-red-200">
            <p class="font-medium">Please fix the highlighted fields.</p>
            <ul class="mt-2 list-disc list-inside space-y-1 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-form-section title="Identity" description="What the property is called and who holds the title.">
        <div>
            <x-input-label for="name" value="Name *" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $property->name)" required />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="title_holder" value="Title holder" />
                <x-text-input id="title_holder" name="title_holder" type="text" class="mt-1 block w-full" :value="old('title_holder', $property->title_holder)" placeholder="e.g. Amritsar Bombay Carriers" />
                <x-input-error :messages="$errors->get('title_holder')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="rera_number" value="RERA number" />
                <x-text-input id="rera_number" name="rera_number" type="text" class="mt-1 block w-full" :value="old('rera_number', $property->rera_number)" />
                <x-input-error :messages="$errors->get('rera_number')" class="mt-1" />
            </div>
        </div>
    </x-form-section>

    <x-form-section title="Address" description="Where the property is located.">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <x-input-label for="address_line1" value="Address line 1" />
                <x-text-input id="address_line1" name="address_line1" type="text" class="mt-1 block w-full" :value="old('address_line1', $property->address_line1)" />
                <x-input-error :messages="$errors->get('address_line1')" class="mt-1" />
            </div>
            <div class="sm:col-span-2">
                <x-input-label for="address_line2" value="Address line 2" />
                <x-text-input id="address_line2" name="address_line2" type="text" class="mt-1 block w-full" :value="old('address_line2', $property->address_line2)" />
            </div>
            <div>
                <x-input-label for="city" value="City" />
                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $property->city)" />
            </div>
            <div>
                <x-input-label for="state" value="State / UT" />
                <x-select id="state" name="state" class="mt-1 block w-full" placeholder="—" :options="\App\Support\IndianStates::all()" :selected="old('state', $property->state)" />
                <x-input-error :messages="$errors->get('state')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="pincode" value="Pincode" />
                <x-text-input id="pincode" name="pincode" type="text" inputmode="numeric" maxlength="6" class="mt-1 block w-full" :value="old('pincode', $property->pincode)" placeholder="110042" />
                <x-input-error :messages="$errors->get('pincode')" class="mt-1" />
            </div>
        </div>
    </x-form-section>

    <x-form-section title="Tenure & occupancy" description="How the property is held and who is currently in it.">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="tenure" value="Tenure" />
                <x-select id="tenure" name="tenure" class="mt-1 block w-full" placeholder="—" :options="$tenureOptions" :selected="old('tenure', $property->tenure)" />
            </div>
            <div>
                <x-input-label for="tenure_authority" value="Authority / landlord" />
                <x-text-input id="tenure_authority" name="tenure_authority" type="text" class="mt-1 block w-full" :value="old('tenure_authority', $property->tenure_authority)" placeholder="e.g. DDA" />
            </div>
            <div>
                <x-input-label for="occupancy_status" value="Occupancy status" />
                <x-select id="occupancy_status" name="occupancy_status" class="mt-1 block w-full" placeholder="—" :options="$occupancyOptions" :selected="old('occupancy_status', $property->occupancy_status)" />
            </div>
            <div>
                <x-input-label for="tenant_or_occupant" value="Tenant / occupant" />
                <x-text-input id="tenant_or_occupant" name="tenant_or_occupant" type="text" class="mt-1 block w-full" :value="old('tenant_or_occupant', $property->tenant_or_occupant)" />
            </div>
        </div>
    </x-form-section>

    <x-form-section title="Physical" description="Construction and area.">
        <div>
            <x-input-label for="construction" value="Construction" />
            <x-text-input id="construction" name="construction" type="text" class="mt-1 block w-full" :value="old('construction', $property->construction)" placeholder="e.g. Basement + Ground Floor + First + Second" />
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-2">
                <x-input-label for="area_value" value="Area" />
                <x-text-input id="area_value" name="area_value" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('area_value', $property->area_value)" />
                <x-input-error :messages="$errors->get('area_value')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="area_unit" value="Unit" />
                <x-select id="area_unit" name="area_unit" class="mt-1 block w-full" placeholder="—" :options="$areaUnitOptions" :selected="old('area_unit', $property->area_unit)" />
            </div>
        </div>
    </x-form-section>

    <x-form-section title="Financials" description="Imputed value, yearly rent, and yield.">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <x-input-label for="imputed_value_inr" value="Imputed value (₹)" />
                <x-text-input id="imputed_value_inr" name="imputed_value_inr" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('imputed_value_inr', $property->imputed_value_inr)" />
                <x-input-error :messages="$errors->get('imputed_value_inr')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="rent_yearly_inr" value="Rent / year (₹)" />
                <x-text-input id="rent_yearly_inr" name="rent_yearly_inr" type="number" step="0.01" class="mt-1 block w-full" :value="old('rent_yearly_inr', $property->rent_yearly_inr)" />
                <x-input-error :messages="$errors->get('rent_yearly_inr')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="yield_percent" value="Yield % (override)" />
                <x-text-input id="yield_percent" name="yield_percent" type="number" step="0.01" min="0" max="100" class="mt-1 block w-full" :value="old('yield_percent', $property->yield_percent)" />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank to auto-calc from rent ÷ value.</p>
                <x-input-error :messages="$errors->get('yield_percent')" class="mt-1" />
            </div>
        </div>
    </x-form-section>

    <x-form-section title="Contacts" description="People who manage or are connected with this property.">
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
                <div class="grid grid-cols-12 gap-2 items-start">
                    <input type="text" :name="`contacts[${i}][name]`" x-model="c.name" placeholder="Name"
                        class="col-span-12 sm:col-span-3 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm" />
                    <input type="text" :name="`contacts[${i}][phone]`" x-model="c.phone" placeholder="+91 9811011555"
                        class="col-span-6 sm:col-span-3 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm" />
                    <input type="text" :name="`contacts[${i}][role]`" x-model="c.role" placeholder="Role"
                        class="col-span-6 sm:col-span-2 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm" />
                    <input type="text" :name="`contacts[${i}][notes]`" x-model="c.notes" placeholder="Notes"
                        class="col-span-11 sm:col-span-3 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm" />
                    <button type="button" @click="remove(i)" class="col-span-1 inline-flex items-center justify-center rounded-md text-gray-400 hover:text-red-600 dark:hover:text-red-400 p-2" aria-label="Remove contact">×</button>
                </div>
            </template>
            <button type="button" @click="add()" class="text-sm font-medium text-primary-700 dark:text-primary-300 hover:text-primary-800 dark:hover:text-primary-200">
                + Add contact
            </button>
        </div>
    </x-form-section>

    <x-form-section title="Notes & status" description="Anything else worth remembering.">
        <div>
            <x-input-label for="keys_location" value="Location of keys & documents" />
            <x-text-input id="keys_location" name="keys_location" type="text" class="mt-1 block w-full" :value="old('keys_location', $property->keys_location)" placeholder="e.g. 24 Motia khan" />
        </div>
        <div>
            <x-input-label for="extra_notes" value="Extra notes" />
            <x-textarea id="extra_notes" name="extra_notes" rows="4" class="mt-1">{{ old('extra_notes', $property->extra_notes) }}</x-textarea>
        </div>
        <label class="inline-flex items-center gap-2">
            <input type="hidden" name="is_data_complete" value="0">
            <input type="checkbox" name="is_data_complete" value="1"
                @checked(old('is_data_complete', $property->is_data_complete))
                class="rounded border-gray-300 dark:border-gray-700 text-primary-600 focus:ring-primary-500" />
            <span class="text-sm text-gray-700 dark:text-gray-300">Data entry complete</span>
        </label>
    </x-form-section>

    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
        <a href="{{ $property->exists ? route('properties.show', $property) : route('properties.index') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:underline text-center sm:text-left">Cancel</a>
        <x-primary-button type="submit">
            {{ $property->exists ? 'Save changes' : 'Create property' }}
        </x-primary-button>
    </div>
</form>
