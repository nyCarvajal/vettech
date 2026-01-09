@csrf
<div class="space-y-4">
    <div>
        <label class="text-sm text-gray-600">Categoría</label>
        <input type="text" name="category" value="{{ old('category', $expense->category ?? '') }}" class="w-full border border-gray-300 rounded px-3 py-2" required />
    </div>
    <div>
        <label class="text-sm text-gray-600">Descripción</label>
        <input type="text" name="description" value="{{ old('description', $expense->description ?? '') }}" class="w-full border border-gray-300 rounded px-3 py-2" />
    </div>
    <div>
        <label class="text-sm text-gray-600">Monto</label>
        <input type="number" step="0.01" name="amount" value="{{ old('amount', $expense->amount ?? '') }}" class="w-full border border-gray-300 rounded px-3 py-2" required />
    </div>
    <div>
        <label class="text-sm text-gray-600">Fecha de pago</label>
        <input type="datetime-local" name="paid_at" value="{{ old('paid_at', isset($expense) ? $expense->paid_at?->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" class="w-full border border-gray-300 rounded px-3 py-2" required />
    </div>
    <div>
        <label class="text-sm text-gray-600">Método de pago</label>
        <input type="text" name="payment_method" value="{{ old('payment_method', $expense->payment_method ?? '') }}" class="w-full border border-gray-300 rounded px-3 py-2" />
    </div>
    <div>
        <label class="text-sm text-gray-600">Usuario</label>
        <select name="user_id" class="w-full border border-gray-300 rounded px-3 py-2">
            <option value="">Automático</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" @selected(old('user_id', $expense->user_id ?? '') == $user->id)>{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-sm text-gray-600">Cliente</label>
        <select name="owner_id" class="w-full border border-gray-300 rounded px-3 py-2">
            <option value="">Sin cliente</option>
            @foreach($owners as $owner)
                <option value="{{ $owner->id }}" @selected(old('owner_id', $expense->owner_id ?? '') == $owner->id)>{{ $owner->name }}</option>
            @endforeach
        </select>
    </div>
</div>
