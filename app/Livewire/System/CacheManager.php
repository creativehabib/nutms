<?php

namespace App\Livewire\System;

use Livewire\Component;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Flux\Flux;

class CacheManager extends Component
{
    public string $cacheSize = '0.00 MB';

    public function mount()
    {
        $this->calculateCacheSize();
    }

    // ফোল্ডার থেকে ক্যাশ সাইজ ক্যালকুলেট করার ফাংশন
    public function calculateCacheSize()
    {
        $size = 0;
        $cachePath = storage_path('framework/cache/data');

        if (File::exists($cachePath)) {
            foreach (File::allFiles($cachePath) as $file) {
                $size += $file->getSize();
            }
        }

        $this->cacheSize = number_format($size / 1048576, 2) . ' MB';
    }

    public function clearCmsCache()
    {
        Artisan::call('cache:clear');
        $this->calculateCacheSize();
        Flux::toast(variant: 'success', text: 'CMS cache cleared successfully.');
    }

    public function refreshViews()
    {
        Artisan::call('view:clear');
        Flux::toast(variant: 'success', text: 'Compiled views cleared to make views up to date.');
    }

    public function clearConfig()
    {
        Artisan::call('config:clear');
        Flux::toast(variant: 'success', text: 'Config cache cleared successfully.');
    }

    public function clearRoute()
    {
        Artisan::call('route:clear');
        Flux::toast(variant: 'success', text: 'Route cache cleared successfully.');
    }

    public function clearLogs()
    {
        $logPath = storage_path('logs/laravel.log');
        if (File::exists($logPath)) {
            File::put($logPath, ''); // লগ ফাইল ফাঁকা করে দেওয়া হচ্ছে
        }
        Flux::toast(variant: 'success', text: 'System log files cleared.');
    }

    public function optimizeSystem()
    {
        Artisan::call('optimize');
        Flux::toast(variant: 'success', text: 'Site performance optimized successfully.');
    }

    public function clearOptimization()
    {
        Artisan::call('optimize:clear');
        $this->calculateCacheSize();
        Flux::toast(variant: 'success', text: 'Optimization caches removed.');
    }

    public function render()
    {
        return view('livewire.system.cache-manager');
    }
}
