<div class="p-6 bg-white rounded-lg shadow-md max-w-lg mx-auto mt-10">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Teacher Data Import</h2>

    @if($message)
        <div @class([
            'mb-4 rounded p-3',
            'bg-green-100 text-green-700' => $messageType === 'success',
            'bg-amber-100 text-amber-800' => $messageType === 'warning',
            'bg-red-100 text-red-700' => $messageType === 'error',
        ]) role="alert">
            {{ $message }}
        </div>
    @endif

    <form wire:submit.prevent="import" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Excel বা CSV ফাইল নির্বাচন করুন</label>
            <input type="file" wire:model="file" class="mt-1 block w-full text-sm text-gray-500
                file:mr-4 file:py-2 file:px-4
                file:rounded-md file:border-0
                file:text-sm file:font-semibold
                file:bg-indigo-50 file:text-indigo-700
                hover:file:bg-indigo-100" accept=".csv, .xlsx, .xls">
            @error('file') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <span wire:loading wire:target="import" class="mr-2">Uploading...</span>
            Import Data
        </button>
    </form>
</div>
