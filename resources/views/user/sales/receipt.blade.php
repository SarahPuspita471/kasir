<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Struk {{ $sale->code }}</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body{font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Arial; padding:16px; color:#111;}
  .box{max-width:560px;margin:0 auto;border:1px solid #eee;border-radius:12px; padding:16px;}
  h1{font-size:18px;margin:0 0 8px}
  table{width:100%;border-collapse:collapse;margin-top:8px}
  th,td{font-size:13px;padding:6px 0;border-bottom:1px dashed #ddd;text-align:left}
  tfoot td{border:0;padding-top:6px}
  .right{text-align:right}
  .muted{color:#666}
  .btn{display:inline-block;padding:8px 12px;border-radius:8px;border:1px solid #ddd;text-decoration:none;color:#111}
  @media print {.no-print{display:none}}
</style>
</head>
<body>
<div class="box">
  <div class="no-print" style="text-align:right;margin-bottom:8px">
    <a href="javascript:window.print()" class="btn">Unduh / Cetak</a>
  </div>

  <h1>Kasirku</h1>
  <div class="muted" style="font-size:13px">
    <div>Kode: <b>{{ $sale->code }}</b></div>
    <div>Tanggal: {{ $sale->created_at->format('d/m/Y H:i') }}</div>
    <div>Kasir: {{ $sale->user->name ?? '—' }}</div>
    <div>Pembeli: {{ $sale->customer_name ?: 'Umum' }}</div>
  </div>

  <table>
    <thead>
      <tr><th>Item</th><th class="right">Harga</th><th class="right">Qty</th><th class="right">Total</th></tr>
    </thead>
    <tbody>
      @foreach($sale->items as $it)
      <tr>
        <td>{{ $it->nama_barang }}</td>
        <td class="right">Rp {{ number_format($it->harga_jual,0,',','.') }}</td>
        <td class="right">{{ $it->qty }}</td>
        <td class="right">Rp {{ number_format($it->line_total,0,',','.') }}</td>
      </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr><td colspan="3" class="right">Subtotal</td><td class="right">Rp {{ number_format($sale->subtotal,0,',','.') }}</td></tr>
      <tr><td colspan="3" class="right">Diskon Otomatis</td><td class="right">- Rp {{ number_format($sale->auto_discount,0,',','.') }}</td></tr>
      <tr><td colspan="3" class="right">Voucher {{ $sale->voucher_code ? '(' . $sale->voucher_code . ')' : '' }}</td><td class="right">- Rp {{ number_format($sale->voucher_discount,0,',','.') }}</td></tr>
      <tr><td colspan="3" class="right"><b>Total</b></td><td class="right"><b>Rp {{ number_format($sale->total,0,',','.') }}</b></td></tr>
      <tr><td colspan="3" class="right">Tunai</td><td class="right">Rp {{ number_format($sale->cash_paid,0,',','.') }}</td></tr>
      <tr><td colspan="3" class="right">Kembalian</td><td class="right">Rp {{ number_format($sale->change_due,0,',','.') }}</td></tr>
    </tfoot>
  </table>

  @if($sale->discount_snapshot)
    <div class="muted" style="margin-top:10px; font-size:12px">
      <div>Rincian Diskon:</div>
      @if(!empty($sale->discount_snapshot['auto']))
        <div>- Auto: {{ $sale->discount_snapshot['auto']['scheme'] ?? '—' }} ({{ $sale->discount_snapshot['auto']['tier']['type'] ?? '' }} {{ $sale->discount_snapshot['auto']['tier']['value'] ?? '' }})</div>
      @endif
      @if(!empty($sale->discount_snapshot['voucher']))
        <div>- Voucher: {{ $sale->discount_snapshot['voucher']['code'] ?? '' }}</div>
      @endif
    </div>
  @endif
</div>
</body>
</html>
