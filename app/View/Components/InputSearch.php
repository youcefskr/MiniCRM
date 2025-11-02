<?php


namespace App\View\Components;

use Illuminate\View\Component;

class InputSearch extends Component
{
    public $name;
    public $value;
    public $placeholder;

    public function __construct($name = 'search', $value = '', $placeholder = 'Search...')
    {
        $this->name = $name;
        $this->value = $value;
        $this->placeholder = $placeholder;
    }

    public function render()
    {
        return view('components.input-search');
    }
}