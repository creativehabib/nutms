const loadImage = (file) => new Promise((resolve, reject) => {
    const image = new Image();
    const objectUrl = URL.createObjectURL(file);

    image.onload = () => {
        URL.revokeObjectURL(objectUrl);
        resolve(image);
    };
    image.onerror = () => {
        URL.revokeObjectURL(objectUrl);
        reject(new Error('ছবিটি পড়া যায়নি।'));
    };
    image.src = objectUrl;
});

const canvasToBlob = (canvas, type, quality) => new Promise((resolve, reject) => {
    canvas.toBlob((blob) => blob ? resolve(blob) : reject(new Error('ছবিটি তৈরি করা যায়নি।')), type, quality);
});

const drawCoverImage = (context, image, width, height) => {
    const scale = Math.max(width / image.naturalWidth, height / image.naturalHeight);
    const sourceWidth = width / scale;
    const sourceHeight = height / scale;
    const sourceX = (image.naturalWidth - sourceWidth) / 2;
    const sourceY = (image.naturalHeight - sourceHeight) / 2;

    context.drawImage(image, sourceX, sourceY, sourceWidth, sourceHeight, 0, 0, width, height);
};

const compressCanvas = async (canvas, maxBytes, type) => {
    let lowestQualityBlob = null;
    let bestMatchingBlob = null;
    let minimumQuality = 0.1;
    let maximumQuality = 0.95;

    for (let attempt = 0; attempt < 8; attempt += 1) {
        const quality = (minimumQuality + maximumQuality) / 2;
        const blob = await canvasToBlob(canvas, type, quality);

        lowestQualityBlob = blob;
        if (blob.size <= maxBytes) {
            bestMatchingBlob = blob;
            minimumQuality = quality;
        } else {
            maximumQuality = quality;
        }
    }

    if (bestMatchingBlob) {
        return bestMatchingBlob;
    }

    if (lowestQualityBlob.size > maxBytes) {
        lowestQualityBlob = await canvasToBlob(canvas, type, 0.05);
    }

    return lowestQualityBlob;
};

export const initializeImageCompressors = () => {
    document.querySelectorAll('[data-image-compressor]').forEach((tool) => {
        if (tool.dataset.initialized === 'true') {
            return;
        }

        const input = tool.querySelector('[data-image-input]');
        const form = tool.querySelector('form');
        const preview = tool.querySelector('[data-image-preview]');
        const emptyState = tool.querySelector('[data-empty-state]');
        const result = tool.querySelector('[data-result]');
        const status = tool.querySelector('[data-status]');
        const download = tool.querySelector('[data-download]');
        let selectedFile = null;
        let previewUrl = null;
        let downloadUrl = null;

        tool.querySelectorAll('[data-size-preset]').forEach((button) => {
            button.addEventListener('click', () => {
                form.elements.width.value = button.dataset.width;
                form.elements.height.value = button.dataset.height;
                form.elements.maxSize.value = button.dataset.maxSize;
            });
        });

        input.addEventListener('change', () => {
            selectedFile = input.files?.[0] ?? null;

            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
            }

            if (! selectedFile) {
                preview.hidden = true;
                emptyState.hidden = false;
                return;
            }

            previewUrl = URL.createObjectURL(selectedFile);
            preview.src = previewUrl;
            preview.hidden = false;
            emptyState.hidden = true;
            result.hidden = true;
            status.textContent = `${selectedFile.name} (${(selectedFile.size / 1024).toFixed(1)} KB)`;
        });

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (! selectedFile) {
                status.textContent = 'প্রথমে একটি ছবি নির্বাচন করুন।';
                return;
            }

            const width = Number(form.elements.width.value);
            const height = Number(form.elements.height.value);
            const maxKilobytes = Number(form.elements.maxSize.value);
            const type = form.elements.format.value;
            const extension = type === 'image/webp' ? 'webp' : 'jpg';
            const submitButton = form.querySelector('[type="submit"]');

            submitButton.disabled = true;
            status.textContent = 'ছবি প্রস্তুত হচ্ছে…';

            try {
                const image = await loadImage(selectedFile);
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.width = width;
                canvas.height = height;
                context.fillStyle = '#ffffff';
                context.fillRect(0, 0, width, height);
                drawCoverImage(context, image, width, height);

                const blob = await compressCanvas(canvas, maxKilobytes * 1024, type);

                if (downloadUrl) {
                    URL.revokeObjectURL(downloadUrl);
                }

                downloadUrl = URL.createObjectURL(blob);
                download.href = downloadUrl;
                download.download = `resized-${width}x${height}.${extension}`;
                result.querySelector('[data-result-size]').textContent = `${(blob.size / 1024).toFixed(1)} KB`;
                result.querySelector('[data-result-dimensions]').textContent = `${width} × ${height} px`;
                result.hidden = false;
                status.textContent = blob.size <= maxKilobytes * 1024
                    ? 'ছবি সফলভাবে প্রস্তুত হয়েছে।'
                    : 'নির্ধারিত মাত্রায় সর্বোচ্চ কম্প্রেশন করা হয়েছে; জটিল ছবির আকার কিছুটা বেশি হতে পারে।';
            } catch (error) {
                status.textContent = error.message;
            } finally {
                submitButton.disabled = false;
            }
        });

        tool.dataset.initialized = 'true';
    });
};
