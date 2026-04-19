<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="utf-8">

<style>

/* ================= FONT ================= */

@font-face {
    font-family: "thsarabun";
    font-style: normal;
    font-weight: normal;
    src: url("{{ storage_path('fonts/THSarabunNew.ttf') }}") format("truetype");
}

@font-face {
    font-family: "thsarabun";
    font-style: normal;
    font-weight: bold;
    src: url("{{ storage_path('fonts/THSarabunNew-Bold.ttf') }}") format("truetype");
}

body {
    font-family: "thsarabun";
    font-size: 16pt;
    line-height: 1.2;
    color: #000;
}

/* ================= Utility ================= */

.bg-mint {
    background-color: #26d0a8;
    color: white;
}

.text-mint {
    color: #26d0a8;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th,
.table td {
    border: 1px solid #000;
    padding: 6px;
}

.no-border {
    border: none !important;
}

.text-right {
    text-align: right;
}

.text-center {
    text-align: center;
}

.bold {
    font-weight: bold;
}

/* ================= Layout ================= */

.header-box {
    border: 1px solid #000;
    margin-top: 15px;
}

.header-box td {
    border: 1px solid #000;
    padding: 10px;
}

.signature-table {
    margin-top: 40px;
}

.signature-table td {
    border: 1px solid #000;
    height: 100px;
    text-align: center;
    vertical-align: bottom;
}

.empty-row td {
    border-top: none;
    border-bottom: none;
    height: 25px;
}

</style>

</head>

<body>

<table class="table no-border">

<tr>

<td class="no-border" width="60%">

<h1 class="text-mint"
style="margin:0; font-size: 28pt;">

SME MOVE

</h1>

<div style="font-size:14pt;">

<strong>
บริษัท ของคุณ จำกัด (สำนักงานใหญ่)
</strong>

<br>

เลขประจำตัวผู้เสียภาษี:
01055XXXXXXXX

</div>

</td>

<td class="no-border text-right"
width="40%">

<div class="bg-mint"
style="padding:12px;
width:230px;
text-align:center;
display:inline-block;">

<strong style="font-size:18pt;">

Tax Invoice / Invoice

</strong>

<br>

ใบกำกับภาษี / ใบวางบิล

</div>

<div style="margin-top:10px;">

<strong>

เลขที่:

{{ $sale->doc_no }}

</strong>

</div>

</td>

</tr>

</table>

<table class="table header-box">

<tr>

<td width="55%">

<strong class="text-mint">

ชื่อลูกค้า:

</strong>

{{ $sale->customer_name ?? 'ลูกค้าทั่วไป' }}

<br>

<strong>

ที่อยู่:

</strong>

{{ $sale->address ?? '-' }}

</td>

<td width="45%">

<strong>

วันที่:

</strong>

{{ date('d/m/Y', strtotime($sale->doc_date)) }}

<br>

<strong>

ครบกำหนด:

</strong>

{{ date('d/m/Y', strtotime($sale->due_date)) }}

</td>

</tr>

</table>

<table class="table"
style="margin-top:20px;">

<thead>

<tr class="bg-mint">

<th width="8%">ลำดับ</th>

<th width="52%">รายละเอียด</th>

<th width="10%">จำนวน</th>

<th width="15%">ราคา/หน่วย</th>

<th width="15%">รวมเงิน</th>

</tr>

</thead>

<tbody>

@foreach($sale->items as $index => $item)

<tr>

<td class="text-center">

{{ $index + 1 }}

</td>

<td>

{{ $item->description }}

</td>

<td class="text-center">

{{ number_format($item->quantity, 0) }}

</td>

<td class="text-right">

{{ number_format($item->unit_price, 2) }}

</td>

<td class="text-right">

{{ number_format($item->total, 2) }}

</td>

</tr>

@endforeach

{{-- เติมแถวว่าง --}}

@for ($i = count($sale->items); $i < 8; $i++)

<tr class="empty-row">

<td>&nbsp;</td>
<td></td>
<td></td>
<td></td>
<td></td>

</tr>

@endfor

</tbody>

</table>

<table class="table no-border"
style="margin-top:10px;">

<tr>

<td class="no-border"
width="60%">

<div style="background:#eee;
padding:10px;">

<strong>

ตัวอักษร:

</strong>

({{ $sale->total_text ?? '-' }})

</div>

</td>

<td class="no-border"
width="40%"
style="padding:0;">

<table class="table">

<tr>

<td class="text-right no-border">

รวมเงิน:

</td>

<td class="text-right">

{{ number_format($sale->subtotal, 2) }}

</td>

</tr>

<tr>

<td class="text-right no-border">

ภาษี 7%:

</td>

<td class="text-right">

{{ number_format($sale->vat, 2) }}

</td>

</tr>

<tr class="bg-mint">

<td class="text-right no-border">

<strong>

ยอดสุทธิ:

</strong>

</td>

<td class="text-right">

<strong>

{{ number_format($sale->total, 2) }}

</strong>

</td>

</tr>

</table>

</td>

</tr>

</table>

<table class="table signature-table">

<tr>

<td width="50%">

ผู้รับสินค้า

<br><br>

......../......../........

</td>

<td width="50%">

ผู้อนุมัติ

<br><br>

......../......../........

</td>

</tr>

</table>

</body>
ddddddd
</html>
