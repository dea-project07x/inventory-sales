<tr class="item-row">
    <td>
        <select name="items[{{ $index }}][product_id]"
                class="form-select product-select @error('items.' . $index . '.product_id') is-invalid @enderror" required>
            <option value="">-- pilih produk --</option>
            @foreach ($products as $p)
                <option value="{{ $p->id }}" data-cost="{{ $p->purchase_price }}"
                    @selected((string) ($row['product_id'] ?? '') === (string) $p->id)>
                    {{ $p->sku }} — {{ $p->name }} (stok: {{ $p->stock }}@if (! $withCost), Rp {{ number_format($p->selling_price, 0, ',', '.') }}@endif)
                </option>
            @endforeach
        </select>
        @error('items.' . $index . '.product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </td>
    <td>
        <input type="number" min="1" step="1" name="items[{{ $index }}][quantity]"
               value="{{ $row['quantity'] ?? 1 }}"
               class="form-control @error('items.' . $index . '.quantity') is-invalid @enderror" required>
        @error('items.' . $index . '.quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </td>
    @if ($withCost)
        <td>
            <input type="number" min="0" step="0.01" name="items[{{ $index }}][unit_cost]"
                   value="{{ $row['unit_cost'] ?? '' }}"
                   class="form-control cost-input @error('items.' . $index . '.unit_cost') is-invalid @enderror" required>
            @error('items.' . $index . '.unit_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </td>
    @endif
    <td class="text-end">
        <button type="button" class="btn btn-outline-danger btn-sm remove-row" title="Hapus baris">&times;</button>
    </td>
</tr>
