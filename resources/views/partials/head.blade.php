<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<!-- ========================================== -->
<!-- Dynamic SEO Meta Tags -->
<!-- ========================================== -->
@php
    $defaultTitle = config('app.name', 'National University Teacher Training Department');
    $pageTitle = filled($title ?? null) ? $title . ' - ' . $defaultTitle : $defaultTitle;

    // Fallback description and keywords based on your project
    $pageDescription = $description ?? __('A modern digital system for securely storing, updating, and analyzing teacher information, ICT training, and computer lab data for National University affiliated colleges.');
    $pageKeywords = $keywords ?? 'National University, Teacher Training, ICT Course, Education Management, Teacher Portal, Bangladesh';

    // Default Open Graph image (Make sure you have an image at public/images/og-image.jpg or change the path)
    $pageImage = $image ?? asset('images/og-image.jpg');
    $currentUrl = url()->current();
@endphp

<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDescription }}" />
<meta name="keywords" content="{{ $pageKeywords }}" />
<meta name="author" content="National University Bangladesh" />
<link rel="canonical" href="{{ $currentUrl }}" />

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website" />
<meta property="og:url" content="{{ $currentUrl }}" />
<meta property="og:title" content="{{ $pageTitle }}" />
<meta property="og:description" content="{{ $pageDescription }}" />
<meta property="og:image" content="{{ $pageImage }}" />

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:url" content="{{ $currentUrl }}" />
<meta name="twitter:title" content="{{ $pageTitle }}" />
<meta name="twitter:description" content="{{ $pageDescription }}" />
<meta name="twitter:image" content="{{ $pageImage }}" />

<!-- ========================================== -->
<!-- Light / Dark Mode & Theme Colors -->
<!-- ========================================== -->
<meta name="color-scheme" content="light dark" />
<!-- Light mode theme color (matches bg-slate-50) -->
<meta name="theme-color" content="#f8fafc" media="(prefers-color-scheme: light)" />
<!-- Dark mode theme color (matches dark:bg-slate-950) -->
<meta name="theme-color" content="#020617" media="(prefers-color-scheme: dark)" />

<!-- ========================================== -->
<!-- Favicon & Assets -->
<!-- ========================================== -->
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
@stack('styles')
