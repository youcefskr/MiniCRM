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



