<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Firmar consentimiento</title>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100" x-data="signaturePad()" x-init="init()">
    <div class="max-w-3xl mx-auto py-8 space-y-4">
        <h1 class="text-2xl font-bold">Consentimiento {{ $consent->code }}</h1>
        <div class="bg-white shadow rounded p-4">
            <div class="prose max-w-none">{!! $consent->merged_body_html !!}</div>
        </div>
        <div class="bg-white shadow rounded p-4 space-y-2">
            <form method="POST" action="{{ route('public.consents.sign', $token) }}" @submit="submitSignature" enctype="multipart/form-data" class="space-y-2">
                @csrf
                <input type="hidden" name="signature_base64" x-ref="signatureInput">
                <label class="block text-sm">Nombre completo
                    <input type="text" name="signer_name" class="mt-1 w-full border rounded px-3 py-2" required>
                </label>
                <label class="block text-sm">Documento
                    <input type="text" name="signer_document" class="mt-1 w-full border rounded px-3 py-2">
                </label>
                <canvas x-ref="canvas" class="border w-full h-40" style="touch-action: none;"></canvas>
                <div class="flex space-x-2">
                    <button type="button" @click="clear" class="px-3 py-1 border rounded">Limpiar</button>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Firmar</button>
                </div>
            </form>
        </div>
    </div>
<script>
function signaturePad(){
    let canvas; let ctx; let drawing=false; let hasStroke=false; const dpr = window.devicePixelRatio || 1;

    const lineCtx = ()=>{
        const context = canvas.getContext('2d');
        context.scale(dpr, dpr);
        context.lineWidth = 2;
        context.lineCap = 'round';
        context.strokeStyle = '#1f2937';
        return context;
    };

    const resize = ()=>{
        if(!canvas) return;
        const data = canvas.toDataURL();
        canvas.width = canvas.clientWidth * dpr;
        canvas.height = 160 * dpr;
        canvas.style.width = '100%';
        canvas.style.height = '160px';
        ctx = lineCtx();
        if(data){
            const img = new Image();
            img.onload = ()=>ctx.drawImage(img,0,0,canvas.width,canvas.height);
            img.src = data;
        }
    };

    const start = (x, y)=>{
        drawing = true;
        hasStroke = true;
        ctx.beginPath();
        ctx.moveTo(x, y);
    };

    const draw = (x, y)=>{
        if(!drawing) return;
        ctx.lineTo(x, y);
        ctx.stroke();
    };

    const end = ()=>{ drawing = false; };

    return {
        init(){
            canvas = this.$refs.canvas;
            ctx = lineCtx();

            const getPos = (e)=>{
                const rect = canvas.getBoundingClientRect();
                const point = e.touches ? e.touches[0] : e;
                const scaleX = canvas.width / rect.width;
                const scaleY = canvas.height / rect.height;
                return {
                    x: (point.clientX - rect.left) * scaleX / dpr,
                    y: (point.clientY - rect.top) * scaleY / dpr,
                };
            };

            canvas.addEventListener('pointerdown', (e)=>{
                e.preventDefault();
                canvas.setPointerCapture(e.pointerId);
                const {x,y} = getPos(e);
                start(x,y);
            });

            canvas.addEventListener('pointermove', (e)=>{
                if(!drawing) return;
                e.preventDefault();
                const {x,y} = getPos(e);
                draw(x,y);
            });

            ['pointerup','pointerleave','pointercancel'].forEach(evt=>canvas.addEventListener(evt, (e)=>{
                e.preventDefault();
                end();
            }));

            resize();
            window.addEventListener('resize', resize);
        },
        clear(){
            if(!ctx || !canvas) return;
            ctx.clearRect(0,0,canvas.width,canvas.height);
            hasStroke = false;
        },
        submitSignature(e){
            if(!hasStroke){
                e.preventDefault();
                alert('La firma es requerida');
                return;
            }
            this.$refs.signatureInput.value = canvas.toDataURL('image/png');
        }
    }
}
</script>
</body>
</html>
