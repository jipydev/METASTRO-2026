<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page - METASTRO 2026</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&family=Libre+Caslon+Text:wght@400;700&family=Open+Sans:wght@700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        .bg-landing {
            background-image: url('{{ asset("images/background-metastro.webp") }}');
            background-size: cover;
            background-position: center bottom; 
            background-repeat: no-repeat;
        }
    </style>
</head>
<body class="bg-landing min-h-screen w-full flex flex-col justify-between overflow-x-hidden relative">

    <div class="w-full text-center pt-[10vh] md:pt-[12vh] px-4 z-10">
        
        <h1 style="font-family: 'Oswald', sans-serif;" 
            class="text-[#136175] text-[13vw] sm:text-[10vw] md:text-[6vw] lg:text-[5.5rem] font-bold tracking-tight drop-shadow-md leading-none mb-2 md:mb-4">
            METASTRO 2026.
        </h1>
        
        <p style="font-family: 'Libre Caslon Text', serif;" 
           class="text-white text-[3.5vw] sm:text-[2.5vw] md:text-[1.5vw] lg:text-[1.25rem] font-bold drop-shadow-lg tracking-widest md:tracking-[0.2em]">
            “SPIRIT OF HIRO, HEART OF SOLDER”
        </p>
    </div>

    <div class="w-full px-4 sm:px-8 md:px-12 pb-[6vh] md:pb-[8vh] z-10 flex justify-center">
        
        <div class="w-full max-w-4xl bg-[#f28e2b]/85 backdrop-blur-[3px] rounded-3xl md:rounded-[3rem] py-10 md:py-16 lg:py-20 flex flex-col items-center text-center shadow-2xl border border-white/20">
            
            <h2 style="font-family: 'Open Sans', sans-serif;" 
                class="text-white text-[7vw] sm:text-[5vw] md:text-[3.5vw] lg:text-[3rem] font-bold mb-6 md:mb-8 tracking-widest drop-shadow-md leading-tight">
                LOGIN PANITIA
            </h2>
            
            <a href="{{ route('login') }}" 
               class="inline-flex items-center justify-center bg-[#f7ede3] text-[#713f17] hover:bg-white hover:-translate-y-1 transition-all duration-300 font-bold text-sm sm:text-base md:text-xl px-8 py-3 md:px-12 md:py-4 rounded-xl shadow-lg">
                LOGIN <span class="ml-2 font-black text-lg md:text-2xl leading-none">&rarr;</span>
            </a>
            
        </div>
        
    </div>

</body>
</html>