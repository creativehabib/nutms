<?php

namespace App\Livewire\DocumentEditor;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class DocumentEditor extends Component
{
    public string $documentContent = '';

    public function saveDocument(): void
    {
        $this->validate([
            'documentContent' => 'required|string',
        ]);

        $this->dispatch('media-toast', type: 'success', message: 'ডকুমেন্ট সফলভাবে সেভ হয়েছে!');
    }

    public function render(): Factory|View
    {
        return view('livewire.document-editor.document-editor')->layout('layouts.frontend',['title'=> 'Document Editor']);
    }
}
