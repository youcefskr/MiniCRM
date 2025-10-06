<flux:navlist.item 
    :href="route('contacts.index')" 
    :current="request()->routeIs('contacts.index')"
    icon="list-bullet" 
    class="text-sm"
>
    {{ __('Liste des contacts') }}
</flux:navlist.item>

<flux:navlist.item 
    :href="route('contacts.information')"
    :current="request()->routeIs('contacts.information')"
    icon="chart-bar" 
    class="text-sm cursor-pointer"
>
    {{ __('Information contacts') }}
</flux:navlist.item>

<flux:navlist.item 
    @click="$dispatch('show-create-modal')"
    icon="document-plus" 
    class="text-sm cursor-pointer"
>
    {{ __('Ajouter un contact') }}
</flux:navlist.item>

<flux:navlist.item 
    @click="$dispatch('export-contacts')"
    icon="document-arrow-down" 
    class="text-sm cursor-pointer"
>
    {{ __('Exporter') }}
</flux:navlist.item>