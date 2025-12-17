<flux:navlist.item 
    :href="route('contacts.index')" 
    :current="request()->routeIs('contacts.index')"
    icon="list-bullet" 
    class="text-sm"
>
    {{ __('Liste des contacts') }}
</flux:navlist.item>



