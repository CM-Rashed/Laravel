<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AI Workspace</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        pre, code { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col items-center justify-start p-4 sm:p-8">

    <div class="w-full max-w-3xl space-y-8">
        
        <!-- Header -->
        <header class="text-center space-y-2">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-semibold uppercase tracking-wider">
                <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                AI Generation Console
            </div>
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900">
                Prompt Playground
            </h1>
            <p class="text-sm text-slate-500 max-w-md mx-auto">
                Submit custom instructions to generate AI responses in real-time.
            </p>
        </header>

        <!-- Main Form Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
            <form action="{{ route('ai.generate') }}" method="POST" class="space-y-5">
                @csrf
                
                <div>
                    <label for="prompt" class="block text-sm font-medium text-slate-700 mb-2">
                        Prompt Instruction
                    </label>
                    <textarea 
                        id="prompt" 
                        name="prompt" 
                        rows="4" 
                        required
                        placeholder="e.g., Write a 3-paragraph summary of quantum computing for beginners..."
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-slate-800 placeholder-slate-400 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 outline-none transition duration-150 resize-y text-sm leading-relaxed"
                    >{{ old('prompt') }}</textarea>
                </div>

                <div class="flex items-center justify-end">
                    <button 
                        type="submit" 
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-100 transition active:scale-[0.98]"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Generate Response
                    </button>
                </div>
            </form>
        </div>

        <!-- Response Display -->
        @if(isset($response))
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden animate-fade-in">
                <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-3.5 flex items-center justify-between">
                    <span class="text-xs font-semibold tracking-wider text-slate-500 uppercase flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Generated Output
                    </span>
                    <button 
                        type="button" 
                        onclick="navigator.clipboard.writeText(`{{ addslashes($response) }}`)"
                        class="text-xs text-slate-500 hover:text-indigo-600 font-medium transition"
                    >
                        Copy text
                    </button>
                </div>
                <div class="p-6 text-sm leading-relaxed text-slate-800 whitespace-pre-wrap">
                    {{ $response }}
                </div>
            </div>
        @endif

    </div>

</body>
</html>