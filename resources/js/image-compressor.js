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

export const calculateCoverCrop = (imageWidth, imageHeight, width, height, positionX = 0.5, positionY = 0.5, zoom = 1) => {
    const scale = Math.max(width / imageWidth, height / imageHeight) * zoom;
    const sourceWidth = width / scale;
    const sourceHeight = height / scale;

    return {
        sourceWidth,
        sourceHeight,
        sourceX: (imageWidth - sourceWidth) * positionX,
        sourceY: (imageHeight - sourceHeight) * positionY,
    };
};

const drawCoverImage = (context, image, width, height, positionX, positionY, zoom) => {
    const { sourceWidth, sourceHeight, sourceX, sourceY } = calculateCoverCrop(
        image.naturalWidth,
        image.naturalHeight,
        width,
        height,
        positionX,
        positionY,
        zoom,
    );

    context.drawImage(image, sourceX, sourceY, sourceWidth, sourceHeight, 0, 0, width, height);
};

export const estimateBackgroundColor = (pixels, width, height) => {
    const sampleSize = Math.max(1, Math.min(8, Math.floor(Math.min(width, height) * 0.03)));
    const corners = [
        [0, 0],
        [width - sampleSize, 0],
        [0, height - sampleSize],
        [width - sampleSize, height - sampleSize],
    ];
    const channels = [[], [], []];

    corners.forEach(([startX, startY]) => {
        for (let y = startY; y < startY + sampleSize; y += 1) {
            for (let x = startX; x < startX + sampleSize; x += 1) {
                const offset = (y * width + x) * 4;
                channels[0].push(pixels[offset]);
                channels[1].push(pixels[offset + 1]);
                channels[2].push(pixels[offset + 2]);
            }
        }
    });

    return channels.map((values) => values.sort((first, second) => first - second)[Math.floor(values.length / 2)]);
};

export const hexToRgb = (hexColor) => {
    const value = Number.parseInt(hexColor.replace('#', ''), 16);

    return [(value >> 16) & 255, (value >> 8) & 255, value & 255];
};

export const createConnectedBackgroundMask = (pixels, width, height, background, tolerance) => {
    const connected = new Uint8Array(width * height);
    const queue = [];
    let queueIndex = 0;
    const enqueueIfBackground = (x, y) => {
        const pixelIndex = (y * width) + x;

        if (connected[pixelIndex]) {
            return;
        }

        const offset = pixelIndex * 4;
        const distance = Math.hypot(
            pixels[offset] - background[0],
            pixels[offset + 1] - background[1],
            pixels[offset + 2] - background[2],
        );

        if (distance <= tolerance + 25) {
            connected[pixelIndex] = 1;
            queue.push([x, y]);
        }
    };

    for (let x = 0; x < width; x += 1) {
        enqueueIfBackground(x, 0);
        enqueueIfBackground(x, height - 1);
    }
    for (let y = 0; y < height; y += 1) {
        enqueueIfBackground(0, y);
        enqueueIfBackground(width - 1, y);
    }

    while (queueIndex < queue.length) {
        const [x, y] = queue[queueIndex];
        queueIndex += 1;

        if (x > 0) {
            enqueueIfBackground(x - 1, y);
        }
        if (x < width - 1) {
            enqueueIfBackground(x + 1, y);
        }
        if (y > 0) {
            enqueueIfBackground(x, y - 1);
        }
        if (y < height - 1) {
            enqueueIfBackground(x, y + 1);
        }
    }

    return { connected };
};

export const removeImageBackground = (imageData, tolerance, solidColor = null) => {
    const pixels = imageData.data;
    const background = estimateBackgroundColor(pixels, imageData.width, imageData.height);
    const replacement = solidColor ? hexToRgb(solidColor) : null;
    const { connected } = createConnectedBackgroundMask(
        pixels,
        imageData.width,
        imageData.height,
        background,
        tolerance,
    );

    for (let offset = 0; offset < pixels.length; offset += 4) {
        const redDifference = pixels[offset] - background[0];
        const greenDifference = pixels[offset + 1] - background[1];
        const blueDifference = pixels[offset + 2] - background[2];
        const distance = Math.sqrt(redDifference ** 2 + greenDifference ** 2 + blueDifference ** 2);
        const pixelIndex = offset / 4;
        const foregroundOpacity = connected[pixelIndex]
            ? Math.max(0, Math.min(1, (distance - tolerance) / 25))
            : 1;
        const originalOpacity = pixels[offset + 3] / 255;
        const opacity = foregroundOpacity * originalOpacity;

        if (replacement) {
            pixels[offset] = Math.round((pixels[offset] * opacity) + (replacement[0] * (1 - opacity)));
            pixels[offset + 1] = Math.round((pixels[offset + 1] * opacity) + (replacement[1] * (1 - opacity)));
            pixels[offset + 2] = Math.round((pixels[offset + 2] * opacity) + (replacement[2] * (1 - opacity)));
            pixels[offset + 3] = 255;
        } else {
            pixels[offset + 3] = Math.round(opacity * 255);
        }
    }

    return imageData;
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
        const previewStage = tool.querySelector('[data-preview-stage]');
        const resetPosition = tool.querySelector('[data-reset-position]');
        const zoomControl = tool.querySelector('[data-zoom]');
        const zoomValue = tool.querySelector('[data-zoom-value]');
        const zoomOutButton = tool.querySelector('[data-zoom-out]');
        const zoomInButton = tool.querySelector('[data-zoom-in]');
        const emptyState = tool.querySelector('[data-empty-state]');
        const result = tool.querySelector('[data-result]');
        const status = tool.querySelector('[data-status]');
        const download = tool.querySelector('[data-download]');
        let selectedFile = null;
        let selectedImage = null;
        let downloadUrl = null;
        let processingSequence = 0;
        let processingTimeout = null;
        let previewFrame = null;
        let positionX = 0.5;
        let positionY = 0.5;
        let zoom = 1;
        let dragStart = null;

        const renderPreview = () => {
            if (! selectedFile || ! selectedImage || ! form.checkValidity()) {
                return null;
            }

            const width = Number(form.elements.width.value);
            const height = Number(form.elements.height.value);
            const backgroundMode = form.elements.backgroundMode.value;
            const requestedType = form.elements.format.value;
            const type = backgroundMode === 'transparent' && requestedType === 'image/jpeg'
                ? 'image/png'
                : requestedType;
            const context = preview.getContext('2d');

            preview.width = width;
            preview.height = height;
            context.clearRect(0, 0, width, height);
            if (backgroundMode === 'keep' && type === 'image/jpeg') {
                context.fillStyle = '#ffffff';
                context.fillRect(0, 0, width, height);
            }
            drawCoverImage(context, selectedImage, width, height, positionX, positionY, zoom);

            if (backgroundMode !== 'keep') {
                const imageData = context.getImageData(0, 0, width, height);
                const solidColor = backgroundMode === 'solid' ? form.elements.backgroundColor.value : null;
                context.putImageData(removeImageBackground(
                    imageData,
                    Number(form.elements.backgroundTolerance.value),
                    solidColor,
                ), 0, 0);
            }

            preview.hidden = false;
            emptyState.hidden = true;

            return { width, height, type, requestedType };
        };

        const processImage = async () => {
            const render = renderPreview();

            if (! render) {
                return;
            }

            const currentSequence = ++processingSequence;
            const maxKilobytes = Number(form.elements.maxSize.value);
            const extension = { 'image/jpeg': 'jpg', 'image/png': 'png', 'image/webp': 'webp' }[render.type];
            const outputCanvas = document.createElement('canvas');
            outputCanvas.width = preview.width;
            outputCanvas.height = preview.height;
            outputCanvas.getContext('2d').drawImage(preview, 0, 0);

            status.textContent = 'ডাউনলোড ফাইল প্রস্তুত হচ্ছে…';

            try {
                const blob = await compressCanvas(outputCanvas, maxKilobytes * 1024, render.type);

                if (currentSequence !== processingSequence) {
                    return;
                }

                if (downloadUrl) {
                    URL.revokeObjectURL(downloadUrl);
                }

                downloadUrl = URL.createObjectURL(blob);
                download.href = downloadUrl;
                download.download = `resized-${render.width}x${render.height}.${extension}`;
                result.querySelector('[data-result-size]').textContent = `${(blob.size / 1024).toFixed(1)} KB`;
                result.querySelector('[data-result-dimensions]').textContent = `${render.width} × ${render.height} px`;
                result.hidden = false;
                status.textContent = blob.size <= maxKilobytes * 1024
                    ? render.type === 'image/png' && render.requestedType === 'image/jpeg'
                        ? 'স্বচ্ছতা রাখতে আউটপুট স্বয়ংক্রিয়ভাবে PNG করা হয়েছে।'
                        : 'পরিবর্তন অনুযায়ী লাইভ প্রিভিউ প্রস্তুত।'
                    : 'এই মাত্রায় সর্বোচ্চ কম্প্রেশনের পরও ফাইলটি নির্ধারিত সাইজের চেয়ে বড়।';
            } catch (error) {
                status.textContent = error.message;
            }
        };

        const scheduleLivePreview = () => {
            processingSequence += 1;
            window.cancelAnimationFrame(previewFrame);
            previewFrame = window.requestAnimationFrame(renderPreview);
            window.clearTimeout(processingTimeout);
            processingTimeout = window.setTimeout(processImage, 250);
        };

        const updatePosition = (horizontalChange, verticalChange) => {
            positionX = Math.max(0, Math.min(1, positionX - horizontalChange));
            positionY = Math.max(0, Math.min(1, positionY - verticalChange));
            scheduleLivePreview();
        };

        previewStage.addEventListener('pointerdown', (event) => {
            if (! selectedImage) {
                return;
            }

            dragStart = { x: event.clientX, y: event.clientY };
            previewStage.setPointerCapture(event.pointerId);
        });
        previewStage.addEventListener('pointermove', (event) => {
            if (! dragStart) {
                return;
            }

            const bounds = previewStage.getBoundingClientRect();
            updatePosition((event.clientX - dragStart.x) / bounds.width, (event.clientY - dragStart.y) / bounds.height);
            dragStart = { x: event.clientX, y: event.clientY };
        });
        previewStage.addEventListener('pointerup', () => {
            dragStart = null;
        });
        previewStage.addEventListener('pointercancel', () => {
            dragStart = null;
        });
        previewStage.addEventListener('keydown', (event) => {
            const movements = {
                ArrowLeft: [-0.025, 0],
                ArrowRight: [0.025, 0],
                ArrowUp: [0, -0.025],
                ArrowDown: [0, 0.025],
            };

            if (! selectedImage || ! movements[event.key]) {
                return;
            }

            event.preventDefault();
            updatePosition(...movements[event.key]);
        });
        resetPosition.addEventListener('click', () => {
            positionX = 0.5;
            positionY = 0.5;
            zoom = 1;
            zoomControl.value = '1';
            zoomValue.textContent = '১০০%';
            scheduleLivePreview();
        });

        const updateZoom = (nextZoom) => {
            zoom = Math.max(1, Math.min(3, nextZoom));
            zoomControl.value = zoom.toFixed(2);
            zoomValue.textContent = `${Math.round(zoom * 100).toLocaleString('bn-BD')}%`;
            scheduleLivePreview();
        };

        zoomControl.addEventListener('input', () => updateZoom(Number(zoomControl.value)));
        zoomOutButton.addEventListener('click', () => updateZoom(zoom - 0.1));
        zoomInButton.addEventListener('click', () => updateZoom(zoom + 0.1));

        tool.querySelectorAll('[data-size-preset]').forEach((button) => {
            button.addEventListener('click', () => {
                form.elements.width.value = button.dataset.width;
                form.elements.height.value = button.dataset.height;
                form.elements.maxSize.value = button.dataset.maxSize;
                scheduleLivePreview();
            });
        });

        form.querySelectorAll('input, select').forEach((control) => {
            control.addEventListener('input', scheduleLivePreview);
            control.addEventListener('change', scheduleLivePreview);
        });

        const backgroundOptions = form.querySelector('[data-background-options]');
        const solidColorControl = form.querySelector('[data-solid-color]');
        const toleranceValue = form.querySelector('[data-tolerance-value]');

        form.elements.backgroundMode.forEach((control) => {
            control.addEventListener('change', () => {
                backgroundOptions.hidden = control.value === 'keep';
                solidColorControl.hidden = control.value !== 'solid';
            });
        });
        form.elements.backgroundTolerance.addEventListener('input', () => {
            toleranceValue.textContent = form.elements.backgroundTolerance.value;
        });

        input.addEventListener('change', async () => {
            selectedFile = input.files?.[0] ?? null;
            selectedImage = null;
            positionX = 0.5;
            positionY = 0.5;
            zoom = 1;
            zoomControl.value = '1';
            zoomValue.textContent = '১০০%';
            processingSequence += 1;

            if (! selectedFile) {
                preview.hidden = true;
                emptyState.hidden = false;
                result.hidden = true;
                resetPosition.hidden = true;
                zoomControl.disabled = true;
                zoomOutButton.disabled = true;
                zoomInButton.disabled = true;
                return;
            }

            result.hidden = true;
            status.textContent = `${selectedFile.name} পড়া হচ্ছে…`;

            try {
                selectedImage = await loadImage(selectedFile);
                resetPosition.hidden = false;
                zoomControl.disabled = false;
                zoomOutButton.disabled = false;
                zoomInButton.disabled = false;
                status.textContent = `${selectedFile.name} (${(selectedFile.size / 1024).toFixed(1)} KB)`;
                await processImage();
            } catch (error) {
                status.textContent = error.message;
            }
        });

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (! selectedFile) {
                status.textContent = 'প্রথমে একটি ছবি নির্বাচন করুন।';
                return;
            }

            const submitButton = form.querySelector('[type="submit"]');

            submitButton.disabled = true;
            await processImage();
            submitButton.disabled = false;
        });

        tool.dataset.initialized = 'true';
    });
};
