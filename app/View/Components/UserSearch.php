<?php

namespace App\View\Components;

use Illuminate\View\Component;

class UserSearch extends Component
{
    public function __construct(
        public $roles,
        public $search = '',
        public $roleFilter = ''
    ) {}

    public function render()
    {
        return view('components.user-search');
    }
}