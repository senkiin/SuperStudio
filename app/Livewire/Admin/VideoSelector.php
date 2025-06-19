<?php

namespace App\Livewire\Admin;

use App\Models\Video;
use Livewire\Component;

class VideoSelector extends Component
{
    public function selectVideo($videoUrl)
    {
        $this->dispatch('videoSelected', videoUrl: $videoUrl);
    }

    public function render()
    {
        return view('livewire.admin.video-selector', [
            'videos' => Video::latest()->get()
        ]);
    }
}
