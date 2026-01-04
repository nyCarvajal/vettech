<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Firmar consentimiento</title>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100" x-data="signaturePad()">
    <div class="max-w-3xl mx-auto py-8 space-y-4">
        <h1 class="text-2xl font-bold">Consentimiento {{ $consent->code }}</h1>
        <div class="bg-white shadow rounded p-4">
            <div class="prose max-w-none">{!! $consent->merged_body_html !!}</div>
        </div>
        <div class="bg-white shadow rounded p-4 space-y-2">
            <form method="POST" action="{{ route('public.consents.sign', $token) }}" @submit="submitSignature" class="space-y-2">
                @csrf
                <input type="hidden" name="signature_base64" x-ref="signatureInput">
                <label class="block text-sm">Nombre completo
                    <input type="text" name="signer_name" class="mt-1 w-full border rounded px-3 py-2" required>
                </label>
                <label class="block text-sm">Documento
                    <input type="text" name="signer_document" class="mt-1 w-full border rounded px-3 py-2">
                </label>
                <canvas x-ref="canvas" class="border w-full h-40"></canvas>
                <div class="flex space-x-2">
                    <button type="button" @click="clear" class="px-3 py-1 border rounded">Limpiar</button>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Firmar</button>
                </div>
            </form>
        </div>
    </div>
<script>
function signaturePad(){
    let canvas; let ctx; let drawing=false;
    const setup=()=>{canvas=this.$refs.canvas; ctx=canvas.getContext('2d'); ctx.lineWidth=2; ctx.lineCap='round'; canvas.addEventListener('mousedown',()=>{drawing=true; ctx.beginPath();}); canvas.addEventListener('mouseup',()=>drawing=false); canvas.addEventListener('mouseleave',()=>drawing=false); canvas.addEventListener('mousemove',(e)=>{if(!drawing) return; ctx.lineTo(e.offsetX,e.offsetY); ctx.stroke();}); canvas.width=canvas.clientWidth; canvas.height=160;};
    return {init(){setup(); window.addEventListener('resize', setup);}, clear(){ctx.clearRect(0,0,canvas.width,canvas.height);}, submitSignature(e){const data=canvas.toDataURL('image/png'); if(data.length < 100){e.preventDefault(); alert('La firma es requerida'); return;} this.$refs.signatureInput.value=data;}}
}
</script>
</body>
</html>
