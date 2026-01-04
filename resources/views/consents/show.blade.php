@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 space-y-4" x-data="signaturePad()">
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
            <canvas x-ref="canvas" class="border w-full h-40"></canvas>
            <div class="flex space-x-2">
                <button type="button" @click="clear" class="px-3 py-1 border rounded">Limpiar</button>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Guardar firma</button>
            </div>
        </form>
    </div>

    <div class="bg-white shadow rounded p-4">
        <h3 class="font-semibold mb-2">Firmas registradas</h3>
        <ul class="space-y-2">
            @foreach($consent->signatures as $signature)
                <li class="flex items-center space-x-2">
                    <img src="{{ Storage::disk('public')->url($signature->signature_image_path) }}" class="h-16 border" alt="firma">
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
    let canvas; let ctx; let drawing=false; let points=[];
    const resize=()=>{if(!canvas) return; const data=canvas.toDataURL(); canvas.width=canvas.clientWidth; canvas.height=160; ctx=lineCtx(); const img=new Image(); img.onload=()=>ctx.drawImage(img,0,0); img.src=data;};
    const lineCtx=()=>{const context=canvas.getContext('2d'); context.lineWidth=2; context.lineCap='round'; return context;};
    return {
        init(){canvas=this.$refs.canvas; ctx=lineCtx(); canvas.addEventListener('mousedown',()=>{drawing=true; ctx.beginPath();}); canvas.addEventListener('mouseup',()=>{drawing=false;}); canvas.addEventListener('mouseleave',()=>{drawing=false;}); canvas.addEventListener('mousemove',(e)=>{if(!drawing) return; ctx.lineTo(e.offsetX,e.offsetY); ctx.stroke();}); resize(); window.addEventListener('resize', resize);},
        clear(){ctx.clearRect(0,0,canvas.width,canvas.height);},
        submitSignature(e){const data=canvas.toDataURL('image/png'); if(data.length < 100){e.preventDefault(); alert('La firma es requerida'); return;} this.$refs.signatureInput.value=data;},
    }
}
</script>
@endpush
@endsection
