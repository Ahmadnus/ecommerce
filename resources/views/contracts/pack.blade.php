@php
    /**
     * Printable rental contract pack (A4, three pages) — rendered by mPDF.
     * All values come from App\Services\RentalContractService::viewData().
     * mPDF's CSS is a subset of CSS 2.1: tables for layout, no flex/grid.
     */
    $e = fn($v) => e((string) $v);

    // Bilingual label + value. $pairs = how many label/value pairs of the 3-pair grid it spans.
    $cell = function (string $ar, string $en, $value = null, bool $ltr = false, int $pairs = 1) use ($e) {
        $val = trim((string) $value);
        $inner = $val === '' ? '&nbsp;' : ($ltr ? '<span dir="ltr">' . $e($val) . '</span>' : $e($val));
        return '<td class="lab" width="11%">' . $ar . '<br><span class="en">' . $en . '</span></td>'
             . '<td class="val b" width="' . ($pairs * 33 - 11) . '%"' . ($pairs > 1 ? ' colspan="' . ($pairs * 2 - 1) . '"' : '') . '>' . $inner . '</td>';
    };

    // Signature box. Inline styles on purpose: mPDF ignores nested descendant selectors here.
    $sig = function (string $ar, string $en, ?string $sub = null, ?string $name = null, ?string $foot = null, string $h = '27mm') use ($e) {
        return '<td style="width:50%;border:0.3mm solid #d9d3c5;padding:2mm 3mm;vertical-align:top;height:' . $h . '">'
             . '<div style="font-weight:bold;font-size:9pt">' . $ar . ' <span class="en">&nbsp;' . $en . '</span></div>'
             . ($sub ? '<div style="font-size:7pt;color:#8a8478">' . $sub . '</div>' : '')
             . '<table style="margin-top:5mm"><tr><td style="border:0;border-bottom:0.25mm solid #5b564c;height:6mm;padding:0;font-weight:bold;font-size:9.4pt">' . ($name ? $e($name) : '&nbsp;') . '</td></tr></table>'
             . ($foot ? '<div style="font-size:7pt;color:#8a8478;margin-top:0.6mm">' . $foot . '</div>' : '')
             . '</td>';
    };

    $bar = fn(string $ar, string $en) => '<table class="bar"><tr><td class="bar-ar">' . $ar . '</td><td class="bar-en">' . $en . '</td></tr></table>';

    // A dotted blank, optionally pre-filled.
    $fill = fn($v = null, int $pad = 14) => '<span class="fill">' . (trim((string) $v) === ''
        ? str_repeat('&nbsp;', $pad)
        : '&nbsp;&nbsp;' . e((string) $v) . '&nbsp;&nbsp;') . '</span>';

    $header = function (string $titleAr, string $titleEn) use ($d, $e) {
        $contacts = collect([$d['company']['phone'], $d['company']['email'], $d['company']['address']])->filter()->implode('  •  ');
        return '
        <table class="head"><tr>
            <td class="head-logo"><img src="' . $d['logo'] . '" style="width:25mm"></td>
            <td class="head-title">
                <div class="h-en">' . $titleEn . '</div>
                <div class="h-ar">' . $titleAr . '</div>
                <div class="h-co"><b>' . $e($d['company']['name_ar']) . '</b>  ·  <span dir="ltr">' . $e($d['company']['name_en']) . '</span>'
                    . ($contacts ? '<br><span dir="ltr">' . $e($contacts) . '</span>' : '') . '</div>
            </td>
            <td class="head-meta">
                <table class="meta">
                    <tr><td class="m-l">رقم العقد<br><span class="en">Contract No</span></td><td class="m-v" dir="ltr">' . $e($d['contract_no']) . '</td></tr>
                    <tr><td class="m-l">التاريخ<br><span class="en">Date</span></td><td class="m-v" dir="ltr">' . $e($d['issued_at']) . '</td></tr>
                    <tr><td class="m-l">رقم الحجز<br><span class="en">Booking Ref</span></td><td class="m-v" dir="ltr">' . $e($d['reference']) . '</td></tr>
                </table>
            </td>
        </tr></table>
        <div class="rule"></div>';
    };
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<style>
    body { font-family: tajawal; font-size: 8.6pt; color: {{ $d['ink'] }}; direction: rtl; line-height: 1.35; }
    .en { font-size: 6.6pt; color: #8a8478; font-weight: normal; direction: ltr; }
    .b  { font-weight: bold; }
    table { border-collapse: collapse; width: 100%; }

    /* header */
    .head td { vertical-align: middle; }
    .head-logo { width: 29mm; text-align: left; }
    .head-title { text-align: center; }
    .h-en { font-size: 15pt; font-weight: bold; letter-spacing: 1.2pt; direction: ltr; color: {{ $d['ink'] }}; }
    .h-ar { font-size: 12pt; font-weight: bold; color: {{ $d['accent_dark'] }}; margin-top: 1pt; }
    .h-co { font-size: 7.4pt; color: #6b665c; margin-top: 2pt; }
    .head-meta { width: 52mm; }
    .meta td { border: 0.3mm solid {{ $d['line'] }}; padding: 1mm 2mm; }
    .m-l { width: 21mm; background-color: {{ $d['tint'] }}; font-size: 7.2pt; font-weight: bold; }
    .m-v { font-weight: bold; text-align: center; font-size: 8.4pt; }
    .rule { border-top: 0.9mm solid {{ $d['accent'] }}; margin: 1.6mm 0 1.2mm 0; height: 0; }

    /* section bars */
    .bar { margin-top: 2.2mm; }
    .bar td { background-color: {{ $d['ink'] }}; color: #ffffff; padding: 1mm 2.6mm; font-weight: bold; font-size: 8.6pt; }
    .bar-en { text-align: left; direction: ltr; font-size: 7.4pt; letter-spacing: 0.6pt; color: {{ $d['accent_light'] }}; }

    /* data grids: 3 label/value pairs per row */
    .grid td { border: 0.3mm solid {{ $d['line'] }}; padding: 1mm 1.8mm; vertical-align: middle; }
    .lab { width: 11%; background-color: {{ $d['tint'] }}; font-size: 7.4pt; font-weight: bold; }
    .val { width: 22%; font-size: 8.8pt; }

    /* money */
    .money td { border: 0.3mm solid {{ $d['line'] }}; padding: 1.2mm 2mm; text-align: center; }
    .money .mh { background-color: {{ $d['tint'] }}; font-size: 7.4pt; font-weight: bold; }
    .money .mv { font-size: 10pt; font-weight: bold; direction: ltr; }
    .money .due { background-color: {{ $d['ink'] }}; color: #ffffff; }
    .money .due-h { background-color: {{ $d['accent'] }}; color: {{ $d['accent_fg'] }}; font-size: 7.4pt; font-weight: bold; }

    /* conditions */
    .deduct td { background-color: {{ $d['tint'] }}; border: 0.3mm solid {{ $d['accent'] }}; padding: 1.3mm 3mm; text-align: center; font-size: 8.4pt; font-weight: bold; }
    .rules td { border: 0.3mm solid {{ $d['line'] }}; padding: 1.2mm 2.2mm; text-align: center; width: 25%; font-size: 7.4pt; vertical-align: middle; }

    /* legal text */
    .legal { font-size: 7.3pt; line-height: 1.5; text-align: justify; margin-top: 2mm; }
    .legal p { margin: 0 0 1mm 0; }
    .fill { border-bottom: 0.25mm dotted #5b564c; font-weight: bold; }

    /* terms */
    .terms td { vertical-align: top; padding: 1.35mm 2mm; border-bottom: 0.2mm solid #e6e0d4; font-size: 8pt; line-height: 1.5; }
    .terms .t-ar { width: 48%; text-align: justify; }
    .terms .t-en { width: 48%; text-align: left; direction: ltr; font-size: 7.4pt; color: #3d3a34; }
    .terms .t-n  { width: 4%; text-align: center; font-weight: bold; color: {{ $d['accent_dark'] }}; }

    /* inspection */
    .insp td { border: 0.3mm solid {{ $d['line'] }}; padding: 1.7mm 2mm; vertical-align: middle; }
    .insp .lab { width: 17%; }
    .insp .val { width: 33%; }
    .blank-lines div { border-bottom: 0.25mm solid #b9b2a3; height: 8.5mm; }
    .legend td { border: 0.3mm solid {{ $d['line'] }}; padding: 1.2mm 2mm; text-align: center; font-size: 7.6pt; width: 25%; }
</style>
</head>
<body>

{{-- ═══════════════════════════ PAGE 1 — CONTRACT ═══════════════════════════ --}}
{!! $header('عقد إيجار سيارة سياحي وإقرار خطي قابل للتنفيذ', 'RENTAL AGREEMENT') !!}

{!! $bar('بيانات المستأجر', 'RENTER') !!}
<table class="grid">
    <tr>{!! $cell('اسم المستأجر', 'Name', $d['renter']['name'], false, 2) !!}{!! $cell('الجنسية', 'Nationality', $d['renter']['nationality']) !!}</tr>
    <tr>{!! $cell('تاريخ الميلاد', 'Date of Birth', $d['renter']['dob'], true) !!}{!! $cell('مكان الميلاد', 'Place of Birth', $d['renter']['pob']) !!}{!! $cell('رقم الوثيقة', 'Nat. No', $d['renter']['national_id'], true) !!}</tr>
    <tr>{!! $cell('نوع الوثيقة', 'Nat. Type', $d['renter']['id_type']) !!}{!! $cell('تاريخ الانتهاء', 'Expiry Date', $d['renter']['id_expiry'], true) !!}{!! $cell('رقم الهاتف', 'Renter\'s Tel', $d['renter']['phone'], true) !!}</tr>
    <tr>{!! $cell('رقم الرخصة', 'License No', $d['renter']['license_no'], true) !!}{!! $cell('مكان الإصدار', 'Place of Issue', $d['renter']['license_place']) !!}{!! $cell('تاريخ الانتهاء', 'Expiry Date', $d['renter']['license_expiry'], true) !!}</tr>
    <tr>{!! $cell('العنوان', 'Renter\'s Address', $d['renter']['address'], false, 2) !!}{!! $cell('سائق إضافي', 'Add. Driver', $d['renter']['add_driver']) !!}</tr>
    <tr>{!! $cell('رخصة الإضافي', 'Add. License', null) !!}{!! $cell('مكان الإصدار', 'Place of Issue', null) !!}{!! $cell('تاريخ الانتهاء', 'Expiry Date', null) !!}</tr>
</table>

{!! $bar('بيانات السيارة والإيجار', 'VEHICLE & RENTAL') !!}
<table class="grid">
    <tr>{!! $cell('رقم السيارة', 'Car No', $d['car']['plate'], true) !!}{!! $cell('نوع السيارة', 'Car Type', $d['car']['type'], true) !!}{!! $cell('لون السيارة', 'Car Color', $d['car']['color']) !!}</tr>
    <tr>{!! $cell('موديل السيارة', 'Car Model', $d['car']['year'], true) !!}{!! $cell('تاريخ الخروج', 'Date Out', $d['rental']['date_out'], true) !!}{!! $cell('وقت الخروج', 'Time Out', $d['rental']['time_out'], true) !!}</tr>
    <tr>{!! $cell('تاريخ العودة', 'Date of Return', $d['rental']['return_at'], true) !!}{!! $cell('مدة الإيجار', 'No. of Days', $d['rental']['days'], true) !!}{!! $cell('الفرع', 'Branch', $d['rental']['branch']) !!}</tr>
</table>

{!! $bar('البيان المالي', 'FINANCIAL SUMMARY') !!}
<table class="money">
    <tr>
        <td class="mh">الأجرة اليومية<br><span class="en">Daily Rate</span></td>
        <td class="mh">الإجمالي<br><span class="en">Net Total</span></td>
        <td class="mh">المبلغ المدفوع<br><span class="en">Payment</span></td>
        <td class="due-h">المبلغ المتبقي<br>Remainder</td>
    </tr>
    <tr>
        <td class="mv">{{ $d['money']['daily'] }}</td>
        <td class="mv">{{ $d['money']['total'] }}</td>
        <td class="mv">{{ $d['money']['paid'] }}</td>
        <td class="mv due">{{ $d['money']['remaining'] }}</td>
    </tr>
</table>

<table class="deduct" style="margin-top:2mm"><tr>
    <td>شروط التأجير والتأمين &nbsp;|&nbsp; يتحمل المستأجر {{ $d['rules']['deductible_accident'] }} دينار في حالة الحادث &nbsp;|&nbsp; يتحمل المستأجر {{ $d['rules']['deductible_total'] }} دينار في حالة شطب السيارة
        <br><span class="en">Conditions: in case of accident a deductible of {{ $d['rules']['deductible_accident'] }} JD &nbsp;·&nbsp; in case of total damage a deductible of {{ $d['rules']['deductible_total'] }} JD</span></td>
</tr></table>

<table class="rules" style="margin-top:1.6mm"><tr>
    <td>المسموح {{ $d['rules']['km_per_day'] }} كم يومياً<br>ويضاف {{ $d['rules']['extra_km_fee'] }} قرش على كل كيلو زائد</td>
    <td>كل ساعة تأخير {{ $d['rules']['late_hour_fee'] }} دنانير<br>وبعد {{ $d['rules']['late_hours_full_day'] }} ساعات تحسب يوم كامل</td>
    <td>تسليم أو استلام السيارة خارج المكتب<br>يضاف {{ $d['rules']['delivery_fee'] }} دينار بدل تسليم أو استلام</td>
    <td>{{ $d['rules']['wash_fee'] }} دنانير بدل غسيل سيارة</td>
</tr></table>

<div class="legal">
    <p>أنا المستأجر أقر بأنني استلمت السيارة المذكورة تفاصيلها بهذا العقد خالية من العيوب وصالحة وبحالة جيدة جداً، وأتعهد بإعادتها وتسليمها شخصياً للشركة المؤجرة بعد قراءتي للشروط والإقرار الموجودين خلف العقد، وأقر بموافقتي على ما ذُكر في هذا العقد وأتعهد بدفع ما يترتب من أجور مستحقة حسب ما تم الاتفاق عليه بموجب هذا العقد، والبالغة {!! $fill($d['legal']['deposit'], 9) !!} دينار، وأقر بأن المكتب لم يحجز أي أوراق ثبوتية تخصني.</p>
    <p><b>١ -</b> مفوضاً صاحب المكتب أو من يفوضه خطياً أو شفوياً من موظفيه بتعبئة هذا العقد حتى لو بعد استلام المركبة، ويعتبر هذا العقد سنداً تنفيذياً قابلاً للتنفيذ لدى دائرة تنفيذ محكمة {!! $fill($d['legal']['court'], 8) !!} مع حق الفريق الأول إضافة قيمة المخالفات إن وجدت وقيمة فتح ملف الحادث إن حصل.</p>
    <p><b>٢ -</b> يقر الفريق الثاني بأنه سيقوم بقيادة المركبة بنفسه وبكامل قدراته العقلية، وبعكس ذلك يتحمل المسؤولية عن كامل العطل والضرر اللاحق بالمركبة على أن قيمة العطل والضرر هي {!! $fill(null, 12) !!} دينار.</p>
    <p><b>٣ -</b> يحق للفريق الأول إيقاف المركبة حال تجاوز حدود السرعة المقررة أو تجاوز عدد أيام الإيجار أو مخالفة قانون السير ونقل المركبة على حساب الفريق الثاني، ويحق للمؤجر فقط تمديد مدة الإيجار دون حق المستأجر بالاستمرار بالإيجار حال انتهاء العقد.</p>
    <p><b>٤ -</b> يعتبر {!! $fill(null, 12) !!} موطناً لي وعنواناً لأي تبليغ قضائي أو غير قضائي، متعهداً بدفع كامل المبالغ المترتبة والبالغة {!! $fill($d['legal']['due'], 9) !!} دينار بتاريخ {!! $fill($d['legal']['due_date'], 8) !!}</p>
</div>

<table style="margin-top:1.6mm"><tr>
    {!! $sig('الفريق الأول — المؤجر', 'LESSOR', 'المفوض عن صاحب المكتب / Authorised representative', null, 'الاسم والتوقيع والختم · Name, signature &amp; stamp', '30mm') !!}
    {!! $sig('الفريق الثاني — المستلم (المستأجر)', 'LESSEE', 'الاسم / Name', $d['renter']['name'], 'التوقيع · Signature &nbsp;&nbsp; التاريخ · Date: ___ / ___ / ______', '30mm') !!}
</tr></table>

<pagebreak />

{{-- ═══════════════════════════ PAGE 2 — TERMS ═══════════════════════════ --}}
{!! $header('شروط العقد', 'RENTAL AGREEMENT TERMS') !!}

<table class="terms">
    @foreach($d['terms'] as $i => $t)
        <tr>
            <td class="t-en">{{ $t['en'] }}</td>
            <td class="t-n">{{ $i + 1 }}</td>
            <td class="t-ar">{{ $t['ar'] }}</td>
        </tr>
    @endforeach
</table>

<table style="margin-top:5mm"><tr>
    {!! $sig('اسم الموظف المسؤول وتوقيعه', 'Lessor\'s representative', null, null, null, '28mm') !!}
    {!! $sig('اسم المستأجر وتوقيعه', 'Renter name &amp; signature', null, $d['renter']['name'], null, '28mm') !!}
</tr></table>

<pagebreak />

{{-- ═══════════════════════════ PAGE 3 — INSPECTION ═══════════════════════════ --}}
{!! $header('فحص وحالة المركبة عند الاستلام والتسليم', 'VEHICLE CONDITION & INSPECTION') !!}

<table class="insp" style="margin-top:1mm">
    <tr>
        <td class="lab">نوع المركبة<br><span class="en">Model</span></td>
        <td class="val b">{{ $d['inspection']['model'] }}</td>
        <td class="lab">رقم اللوحة<br><span class="en">Plate No</span></td>
        <td class="val b"><span dir="ltr">{{ $d['inspection']['plate'] }}</span></td>
    </tr>
    <tr>
        <td class="lab">عداد الخروج<br><span class="en">Odometer Out</span></td><td class="val">&nbsp;</td>
        <td class="lab">عداد الدخول<br><span class="en">Odometer In</span></td><td class="val">&nbsp;</td>
    </tr>
    <tr>
        <td class="lab">الوقود عند الخروج<br><span class="en">Fuel Out</span></td><td class="val">&nbsp;</td>
        <td class="lab">الوقود عند الدخول<br><span class="en">Fuel In</span></td><td class="val">&nbsp;</td>
    </tr>
    <tr>
        <td class="lab">تاريخ الاستلام<br><span class="en">Date Out</span></td><td class="val b"><span dir="ltr">{{ $d['inspection']['date_out'] }}</span></td>
        <td class="lab">تاريخ التسليم<br><span class="en">Date In</span></td><td class="val b"><span dir="ltr">{{ $d['inspection']['date_in'] }}</span></td>
    </tr>
</table>

{!! $bar('مخطط فحص المركبة', 'INSPECTION DIAGRAM') !!}
<table style="margin-top:1mm; border: 0.3mm solid {{ $d['line'] }}">
    <tr><td style="text-align:center; padding:3mm"><img src="{{ $d['diagram'] }}" style="width:168mm"></td></tr>
</table>

<table class="legend" style="margin-top:2mm"><tr>
    <td><b>(S)</b> خدش / Scratch</td><td><b>(D)</b> انبعاج / Dent</td><td><b>(P)</b> طلاء / Paint</td><td><b>(B)</b> كسر / Broken</td>
</tr></table>

{!! $bar('ملاحظات الفحص', 'INSPECTION NOTES') !!}
<div class="blank-lines" style="margin-top:1mm"><div></div><div></div><div></div></div>

<table style="margin-top:3mm"><tr>
    {!! $sig('توقيع الموظف المسؤول', 'Employee Signature', 'الاسم / Name', null, null, '28mm') !!}
    {!! $sig('توقيع المستأجر', 'Renter Signature', 'الاسم / Name', $d['renter']['name'], null, '28mm') !!}
</tr></table>

</body>
</html>
