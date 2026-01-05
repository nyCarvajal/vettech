@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 space-y-4" x-data="signaturePad()" x-init="init()">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold">{{ $consent->code }}</h1>
            <p class="text-gray-600">Estado: {{ $consent->status }}</p>
        </div>
        <div class="space-x-2">
            <a href="{{ route('consents.pdf', $consent) }}" class="text-indigo-600">PDF</a>
            <form method="POST" action="{{ route('consents.public-link', $consent) }}" class="inline">
                @csrf
                <button class="text-indigo-600" type="submit">Generar link público</button>
            </form>
        </div>
    </div>
    @if(session('public_link'))
    <div class="p-3 bg-green-100 rounded">Link público: <input class="w-full" value="{{ session('public_link') }}" readonly></div>
    @endif
    <div class="bg-white shadow rounded p-4">
        <div class="prose max-w-none">{!! $consent->merged_body_html !!}</div>
    </div>

    <div class="bg-white shadow rounded p-4 space-y-2">
        <h2 class="font-semibold">Firmar internamente</h2>
        <form method="POST" action="{{ route('consents.sign', $consent) }}" @submit="submitSignature" class="space-y-2">
            @csrf
            <input type="hidden" name="signature_base64" x-ref="signatureInput">
            <div class="grid grid-cols-2 gap-2">
                <label class="block text-sm">Nombre
                    <input type="text" name="signer_name" class="mt-1 w-full border rounded px-3 py-2" value="{{ $consent->owner_snapshot['full_name'] ?? $consent->owner_snapshot['first_name'] ?? '' }}" required>
                </label>
                <label class="block text-sm">Documento
                    <input type="text" name="signer_document" class="mt-1 w-full border rounded px-3 py-2">
                </label>
            </div>
            <input type="hidden" name="signer_role" value="owner">
            <canvas x-ref="canvas" class="border w-full h-40" style="touch-action: none;"></canvas>
            <div class="flex space-x-2">
                <button type="button" @click="clear" class="px-3 py-1 border rounded">Limpiar</button>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Firmar</button>
            </div>
        </form>
    </div>

    <div class="bg-white shadow rounded p-4">
        <h3 class="font-semibold mb-2">Firmas registradas</h3>
        <ul class="space-y-2">
            @foreach($consent->signatures as $signature)
                <li class="flex items-center space-x-2">
                        <img src="{{ Storage::disk('consents')->url($signature->signature_image_path) }}" class="h-16 border" alt="firma">
                    <div>
                        <p>{{ $signature->signer_name }} ({{ $signature->signer_role }})</p>
                        <p class="text-xs text-gray-600">{{ $signature->signed_at }} · {{ $signature->method }} · {{ $signature->ip_address }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>

@push('scripts')
<script>
function signaturePad(){
    let canvas; let ctx; let drawing=false; let hasStroke=false; let dpr=window.devicePixelRatio || 1;

    const resize = ()=>{
        if(!canvas) return;
        const data = canvas.toDataURL();
        canvas.width = canvas.clientWidth * dpr;
        canvas.height = 160 * dpr;
        canvas.style.width = '100%';
        canvas.style.height = '160px';
        ctx = lineCtx();
        if(data){
            const img=new Image();
            img.onload=()=>ctx.drawImage(img,0,0,canvas.width,canvas.height);
            img.src=data;
        }
    };

    const lineCtx = ()=>{
        const context = canvas.getContext('2d');
        context.scale(dpr, dpr);
        context.lineWidth = 2;
        context.lineCap = 'round';
        context.strokeStyle = '#1f2937';
        return context;
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
                const {x,y}=getPos(e);
                start(x,y);
            });

            canvas.addEventListener('pointermove', (e)=>{
                if (!drawing) return;
                e.preventDefault();
                const {x,y}=getPos(e);
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
        },
    }
}
</script>
@endpush
@endsection
