<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- ড্যাশবোর্ড হেডার -->
    <div class="mb-8 border-b border-slate-200 pb-5 flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-bold text-slate-800">জরিপ অ্যানালিটিক্স রিপোর্ট</h2>
            <p class="text-slate-500 mt-1">জাতীয় বিশ্ববিদ্যালয়ের প্রথম বর্ষের আইসিটি কোর্স বিষয়ক ডেটা</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('survey.teacher') }}" class="bg-indigo-50 text-indigo-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-100 transition">শিক্ষক ফর্ম দেখুন</a>
            <a href="{{ route('survey.student') }}" class="bg-teal-50 text-teal-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-teal-100 transition">শিক্ষার্থী ফর্ম দেখুন</a>
        </div>
    </div>

    <!-- সামারি কার্ডস (Summary Cards) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <!-- শিক্ষক কার্ড -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center justify-between border-l-4 border-l-indigo-600">
            <div>
                <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-1">মোট শিক্ষক মতামত</p>
                <h3 class="text-4xl font-extrabold text-slate-800">{{ $totalTeachers }}</h3>
            </div>
            <div class="w-14 h-14 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>

        <!-- শিক্ষার্থী কার্ড -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center justify-between border-l-4 border-l-teal-600">
            <div>
                <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-1">মোট শিক্ষার্থী মতামত</p>
                <h3 class="text-4xl font-extrabold text-slate-800">{{ $totalStudents }}</h3>
            </div>
            <div class="w-14 h-14 bg-teal-50 rounded-full flex items-center justify-center text-teal-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v6"></path></svg>
            </div>
        </div>
    </div>

    <!-- চার্ট সেকশন (Charts Section) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- শিক্ষক চার্ট: Q6 -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                <h4 class="font-bold text-slate-700">শিক্ষকদের সামগ্রিক সন্তুষ্টি (Q6)</h4>
            </div>
            <div class="p-6 flex justify-center items-center h-80">
                @if($totalTeachers > 0)
                    <canvas id="teacherChart"></canvas>
                @else
                    <p class="text-slate-400">পর্যাপ্ত ডেটা নেই</p>
                @endif
            </div>
        </div>

        <!-- শিক্ষার্থী চার্ট: SQ3 -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                <h4 class="font-bold text-slate-700">কোর্সটি ডিজিটাল জ্ঞান বৃদ্ধি করেছে (SQ3)</h4>
            </div>
            <div class="p-6 flex justify-center items-center h-80">
                @if($totalStudents > 0)
                    <canvas id="studentConfidenceChart"></canvas>
                @else
                    <p class="text-slate-400">পর্যাপ্ত ডেটা নেই</p>
                @endif
            </div>
        </div>

        <!-- শিক্ষার্থী চার্ট: SQ4 -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden lg:col-span-2">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                <h4 class="font-bold text-slate-700">ভবিষ্যৎ কর্মজীবনের জন্য সহায়ক (SQ4)</h4>
            </div>
            <div class="p-6 flex justify-center items-center h-96">
                @if($totalStudents > 0)
                    <canvas id="studentFutureChart"></canvas>
                @else
                    <p class="text-slate-400">পর্যাপ্ত ডেটা নেই</p>
                @endif
            </div>
        </div>

    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('livewire:initialized', () => {

        // কালার প্যালেট
        const colors = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444'];
        const barColors = ['#0d9488', '#14b8a6', '#2dd4bf', '#5eead4', '#ccfbf1'];

        // 1. Teacher Satisfaction (Pie Chart)
        if(document.getElementById('teacherChart')) {
            new Chart(document.getElementById('teacherChart'), {
                type: 'doughnut',
                data: {
                    labels: @json($tSatisfactionLabels),
                    datasets: [{
                        data: @json($tSatisfactionData),
                        backgroundColor: colors,
                        borderWidth: 2,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'right' } }
                }
            });
        }

        // 2. Student Confidence (Bar Chart)
        if(document.getElementById('studentConfidenceChart')) {
            new Chart(document.getElementById('studentConfidenceChart'), {
                type: 'bar',
                data: {
                    labels: @json($sConfidenceLabels),
                    datasets: [{
                        label: 'শিক্ষার্থীর সংখ্যা',
                        data: @json($sConfidenceData),
                        backgroundColor: '#10b981',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                    plugins: { legend: { display: false } }
                }
            });
        }

        // 3. Student Future Career (Bar Chart - Horizontal)
        if(document.getElementById('studentFutureChart')) {
            new Chart(document.getElementById('studentFutureChart'), {
                type: 'bar',
                data: {
                    labels: @json($sFutureCareerLabels),
                    datasets: [{
                        label: 'মতামত সংখ্যা',
                        data: @json($sFutureCareerData),
                        backgroundColor: '#6366f1',
                        borderRadius: 6
                    }]
                },
                options: {
                    indexAxis: 'y', // এটা বার চার্টকে হরিজন্টাল করবে
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
                    plugins: { legend: { display: false } }
                }
            });
        }
    });
</script>
