<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print QR Label 112 (A4 Layout) - {{ config('app.name') }}</title>
    <style>
        :root {
            --top-margin: 3.8mm;
            --side-margin: 5.5mm;
            --v-pitch: 10mm;
            --h-pitch: 22mm;
            --label-width: 20mm;
            --label-height: 8mm;
        }

        @page {
            size: A4;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #f3f3f3;
            font-family: Arial, sans-serif;
        }

        .sheet {
            width: 210mm;
            height: 297mm;
            background: white;
            margin: 10mm auto;
            display: block;
            position: relative;
            box-sizing: border-box;
            page-break-after: always;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .label {
            position: absolute;
            width: var(--label-width);
            height: var(--label-height);
            display: flex;
            align-items: center;
            justify-content: flex-start;
            box-sizing: border-box;
            padding: 0.5mm 1mm;
            border: 0.05mm solid rgba(0, 0, 0, 0.05);
            /* Very light guide for screen */
        }

        .qr-box {
            width: 7mm;
            height: 7mm;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .qr-box svg {
            width: 100%;
            height: 100%;
        }

        .text-box {
            flex-grow: 1;
            font-size: 3.5pt;
            line-height: 1;
            margin-left: 1mm;
            font-weight: bold;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            word-break: break-all;
        }

        @media print {
            body {
                background: transparent;
            }

            .sheet {
                margin: 0;
                border: none;
                box-shadow: none;
            }

            .label {
                border: none !important;
            }

            .no-print {
                display: none !important;
            }
        }

        .controls {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 260px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            z-index: 999;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 12px;
            margin-bottom: 8px;
            border-radius: 6px;
            border: none;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }

        .btn-primary {
            background: #4f46e5;
            color: white;
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }

        .setting-group {
            margin-bottom: 10px;
            font-size: 12px;
        }

        .setting-group label {
            display: block;
            margin-bottom: 2px;
            color: #666;
        }

        .setting-group input {
            width: 100%;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .grid-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
    </style>
</head>

<body>
    <div class="controls no-print">
        <h3 style="margin-top:0; font-size:18px">A4 Label Settings</h3>
        <p style="font-size:11px; color:#666; margin-bottom:15px">Based on Combo No. 112 (20x8mm) specs</p>

        <div class="grid-inputs">
            <div class="setting-group">
                <label>Top Margin (cm)</label>
                <input type="number" id="in-top" value="0.38" step="0.05" oninput="updateCSS()">
            </div>
            <div class="setting-group">
                <label>Side Margin (cm)</label>
                <input type="number" id="in-side" value="0.55" step="0.05" oninput="updateCSS()">
            </div>
        </div>

        <div class="grid-inputs">
            <div class="setting-group">
                <label>Vert Pitch (cm)</label>
                <input type="number" id="in-vPitch" value="1" step="0.05" oninput="updateCSS()">
            </div>
            <div class="setting-group">
                <label>Horiz Pitch (cm)</label>
                <input type="number" id="in-hPitch" value="2.2" step="0.05" oninput="updateCSS()">
            </div>
        </div>

        <div class="grid-inputs">
            <div class="setting-group">
                <label>L-Width (cm)</label>
                <input type="number" id="in-width" value="2.0" step="0.1" oninput="updateCSS()">
            </div>
            <div class="setting-group">
                <label>L-Height (cm)</label>
                <input type="number" id="in-height" value="0.8" step="0.1" oninput="updateCSS()">
            </div>
        </div>

        <button onclick="window.print()" class="btn btn-primary">Print Now</button>
        <a href="{{ route('items.index') }}" class="btn btn-secondary">Back</a>

        <div style="font-size:10px; color:#d97706; margin-top:10px">
            * Check printer: A4, Scale 100%, Margins: None.
        </div>
    </div>

    @php
    $cols = 9;
    $rows = 14;
    $labelsPerSheet = $cols * $rows;
    $chunks = $items->chunk($labelsPerSheet);
    @endphp

    @forelse($chunks as $chunk)
    <div class="sheet">
        @foreach($chunk as $index => $item)
        @php
        $row = floor($index / $cols);
        $col = $index % $cols;
        @endphp
        <div class="label" style="
                top: calc(var(--top-margin) + ({{ $row }} * var(--v-pitch)));
                left: calc(var(--side-margin) + ({{ $col }} * var(--h-pitch)));
            ">
            <div class="qr-box">
                {!! QrCode::size(50)->margin(0)->generate($item->item_id . '-' . $item->user_id) !!}
            </div>
            <div class="text-box">
                {{ $item->nama_item }}
            </div>
        </div>
        @endforeach
    </div>
    @empty
    <div style="text-align: center; padding: 50px;">
        <p>No products available to print.</p>
        <a href="{{ route('items.index') }}" class="btn btn-primary">Go Back</a>
    </div>
    @endforelse

    <script>
        function updateCSS() {
            const root = document.documentElement;
            root.style.setProperty('--top-margin', (document.getElementById('in-top').value * 10) + 'mm');
            root.style.setProperty('--side-margin', (document.getElementById('in-side').value * 10) + 'mm');
            root.style.setProperty('--v-pitch', (document.getElementById('in-vPitch').value * 10) + 'mm');
            root.style.setProperty('--h-pitch', (document.getElementById('in-hPitch').value * 10) + 'mm');
            root.style.setProperty('--label-width', (document.getElementById('in-width').value * 10) + 'mm');
            root.style.setProperty('--label-height', (document.getElementById('in-height').value * 10) + 'mm');
        }
        updateCSS();
    </script>
</body>

</html>