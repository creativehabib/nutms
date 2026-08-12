<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="850" viewBox="0 0 1200 850" role="img" aria-labelledby="title description">
    <title id="title">Certificate of Completion</title>
    <desc id="description">Training completion certificate for {{ $participant->name }}</desc>
    <rect width="1200" height="850" fill="#fafafa" />
    <rect x="28" y="28" width="1144" height="794" rx="12" fill="none" stroke="#4338ca" stroke-width="6" />
    <rect x="48" y="48" width="1104" height="754" rx="8" fill="none" stroke="#0ea5e9" stroke-width="2" />
    <circle cx="600" cy="145" r="58" fill="#4338ca" />
    <text x="600" y="158" text-anchor="middle" fill="white" font-family="sans-serif" font-size="38" font-weight="700">NU</text>
    <text x="600" y="240" text-anchor="middle" fill="#18181b" font-family="sans-serif" font-size="26" font-weight="700">NATIONAL UNIVERSITY</text>
    <text x="600" y="280" text-anchor="middle" fill="#52525b" font-family="sans-serif" font-size="18">Teacher Training Management System</text>
    <text x="600" y="360" text-anchor="middle" fill="#4338ca" font-family="serif" font-size="50" font-weight="700">Certificate of Completion</text>
    <text x="600" y="415" text-anchor="middle" fill="#52525b" font-family="sans-serif" font-size="20">This certificate is proudly presented to</text>
    <text x="600" y="485" text-anchor="middle" fill="#18181b" font-family="serif" font-size="44" font-weight="700">{{ $participant->name }}</text>
    <line x1="300" y1="505" x2="900" y2="505" stroke="#a1a1aa" stroke-width="1" />
    <text x="600" y="560" text-anchor="middle" fill="#52525b" font-family="sans-serif" font-size="20">for successfully completing</text>
    <text x="600" y="615" text-anchor="middle" fill="#18181b" font-family="sans-serif" font-size="28" font-weight="700">{{ $training->title }}</text>
    <text x="600" y="660" text-anchor="middle" fill="#52525b" font-family="sans-serif" font-size="17">{{ $training->start_date->format('d F Y') }} — {{ $training->end_date->format('d F Y') }}</text>
    <text x="100" y="755" fill="#71717a" font-family="sans-serif" font-size="15">Certificate No: {{ $certificateNumber }}</text>
    <text x="1100" y="755" text-anchor="end" fill="#71717a" font-family="sans-serif" font-size="15">Issued: {{ $completedAt }}</text>
</svg>
