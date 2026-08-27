<?php

namespace App\Livewire;

use App\Services\BanglaConverter;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TextConverter extends Component
{
    use WithFileUploads;

    public ?TemporaryUploadedFile $document = null;

    public string $fileConversionMode = 'bijoy_to_unicode';

    public function convertFile(): StreamedResponse
    {
        $validated = $this->validate([
            'document' => ['required', 'file', 'mimes:txt', 'max:10240'],
            'fileConversionMode' => ['required', 'in:bijoy_to_unicode,unicode_to_bijoy'],
        ]);

        $contents = file_get_contents($validated['document']->getRealPath());
        $convertedText = $this->fileConversionMode === 'bijoy_to_unicode'
            ? BanglaConverter::bijoyToUnicode($contents ?: '')
            : BanglaConverter::unicodeToBijoy($contents ?: '');

        $this->reset('document');

        return response()->streamDownload(
            static function () use ($convertedText): void {
                echo $convertedText;
            },
            'converted-'.now()->format('Ymd-His').'.txt',
            ['Content-Type' => 'text/plain; charset=UTF-8'],
        );
    }

    public function render(): View
    {
        return view('livewire.text-converter');
    }
}
