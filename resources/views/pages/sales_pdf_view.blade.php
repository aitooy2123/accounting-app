<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: normal;
            src: url("{{ public_path('fonts/THSarabunNew.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: bold;
            src: url("{{ public_path('fonts/THSarabunNew-Bold.ttf') }}") format('truetype');
        }
        body {
            font-family: "THSarabunNew";
            font-size: 16px;
            line-height: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .header { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header text-center">
        <h1>ใบแจ้งหนี้ (Invoice)</h1>
        <p>เลขที่เอกสาร: {{ $sale->doc_no }}</p>
    </div>

    <table>
        <thead>
            <tr style="background: #eee;">
                <th style="border: 1px solid #000; padding: 5px;">วันที่</th>
                <th style="border: 1px solid #000; padding: 5px;">รายละเอียด</th>
                <th style="border: 1px solid #000; padding: 5px;" class="text-right">จำนวนเงิน</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #000; padding: 5px;">{{ $sale->doc_date }}</td>
                <td style="border: 1px solid #000; padding: 5px;">รายการขายสินค้า/บริการ</td>
                <td style="border: 1px solid #000; padding: 5px;" class="text-right">฿{{ number_format($sale->total, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
