<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>AI Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #333;
            line-height: 1.5;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #222;
            margin-bottom: 30px;
        }
        .report-content {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 10px;
        }
        .question {
            font-weight: bold;
            margin-top: 20px;
        }
        .answer {
            margin-top: 5px;
            padding-left: 10px;
        }
        .category {
            font-style: italic;
            color: #555;
            margin-top: 10px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>

    <h1>AI Generated Report</h1>

    <div class="report-content">
        {!! nl2br(e($reportContent)) !!}
    </div>

    <div class="footer">
        Generated on {{ \Carbon\Carbon::now()->format('F j, Y, g:i a') }}
    </div>

</body>
</html>
