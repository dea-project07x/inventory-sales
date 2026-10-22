@php
    $rows = old('items', [['product_id' => '', 'quantity' => 1, 'unit_cost' => '']]);
    $nextIndex = (collect(array_keys($rows))->max() ?? -1) + 1;
@endphp

@error('items')<div class="alert alert-danger py-2">{{ $message }}</div>@enderror

<div class="table-responsive">
    <table class="table align-middle">
        <thead class="table-light">
            <tr>
                <th>Produk</th>
                <th style="width:140px">Kuantitas</th>
                @if ($withCost)<th style="width:200px">Harga Beli / unit</th>@endif
                <th style="width:60px"></th>
            </tr>
        </thead>
        <tbody id="items-body">
            @foreach ($rows as $i => $row)
                @include('partials.item-row', ['index' => $i, 'row' => $row, 'products' => $products, 'withCost' => $withCost])
            @endforeach
        </tbody>
    </table>
</div>
<button type="button" id="add-row" class="btn btn-outline-secondary btn-sm">+ Tambah item</button>

<template id="row-template">
    @include('partials.item-row', ['index' => '__INDEX__', 'row' => [], 'products' => $products, 'withCost' => $withCost])
</template>

@push('scripts')
<script>
(function () {
    const body = document.getElementById('items-body');
    const tpl = document.getElementById('row-template').innerHTML;
    let next = {{ $nextIndex }};

    document.getElementById('add-row').addEventListener('click', function () {
        body.insertAdjacentHTML('beforeend', tpl.replaceAll('__INDEX__', next++));
    });

    body.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-row');
        if (!btn) return;
        if (body.querySelectorAll('.item-row').length > 1) {
            btn.closest('.item-row').remove();
        }
    });

    // Pembelian: isi otomatis harga beli dari data produk bila kolom masih kosong.
    body.addEventListener('change', function (e) {
        if (!e.target.classList.contains('product-select')) return;
        const row = e.target.closest('.item-row');
        const cost = row.querySelector('.cost-input');
        const opt = e.target.selectedOptions[0];
        if (cost && opt && opt.dataset.cost && cost.value === '') {
            cost.value = opt.dataset.cost;
        }
    });
})();
</script>
@endpush
