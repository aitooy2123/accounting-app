<?php
namespace App\View\Components;

use Illuminate\View\Component;

class StatsCard extends Component
{
    public $title;
    public $amount;
    public $color;

    /**
     * สร้าง Component Instance ใหม่
     */
    public function __construct($title, $amount, $color = 'blue')
    {
        $this->title = $title;
        $this->amount = $amount;
        $this->color = $color;
    }

    /**
     * ดึง View ที่จะใช้แสดงผล
     */
    public function render()
    {
        return view('components.stats-card');
    }
}
