<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel Route Generator</title>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #f4f4f9, #e0e0e0);
            color: #333;
            animation: fadeIn 1s ease-in-out;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        .upload-card {
            margin-top: 60px;
            background: #fffde4;
            box-shadow: 0 4px 24px rgba(60,60,100,0.10);
            border-radius: 18px;
            padding: 40px 32px 32px 32px;
            max-width: 70%;
            border: 2px solid #f4d03f;
        }

        .upload-title {
            margin-bottom: 24px;
            text-align:center;
            font-weight: 700;
            background: linear-gradient(90deg, #f4d03f 60%, #fffde4 100%);
            color: #333;
            border-radius: 8px;
            padding: 16px 0 12px 0;
            box-shadow: 0 2px 8px rgba(244,208,63,0.10);
            letter-spacing: 1px;
        }

        .upload-form {
            display: flex;
            flex-direction: column;
            gap: 22px;
            align-items: center;
        }

        .upload-label {
            font-weight: 600;
            color: #3949ab;
            background: #f4d03f;
            padding: 6px 18px;
            border-radius: 6px;
            box-shadow: 0 1px 4px rgba(244,208,63,0.10);
        }

        .upload-input {
            padding: 10px;
            border: 1.5px solid #f4d03f;
            border-radius: 6px;
            width: 100%;
            max-width: 350px;
            background: #fff;
            font-size: 1em;
            transition: border 0.2s;
        }

        .upload-input:focus {
            border: 1.5px solid #3949ab;
            outline: none;
            box-shadow: 0 0 0 2px #f4d03f33;
        }

        .upload-btn {
            width: 100%;
            max-width: 200px;
        }

        pre {
            background: #272822;
            color: #f8f8f2;
            padding: 20px;
            border-radius: 10px;
            overflow-x: auto;
            white-space: pre-wrap;
            margin-top: 18px;
            font-size: 1.05em;
        }

        .submit-btn,
        .copy-btn {
            padding: 12px 24px;
            background: linear-gradient(90deg, #f4d03f 80%, #fffde4 100%);
            color: #333;
            border: none;
            border-radius: 6px;
            margin-top: 10px;
            cursor: pointer;
            font-weight: 700;
            font-size: 1.1em;
            box-shadow: 0 2px 8px rgba(244,208,63,0.10);
            transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
        }

        .submit-btn:hover,
        .copy-btn:hover {
            background: linear-gradient(90deg, #f7ca18 80%, #fffde4 100%);
            box-shadow: 0 4px 16px rgba(244,208,63,0.18);
            transform: translateY(-2px) scale(1.03);
        }

        h3 i {
            margin-right: 8px;
            color:rgb(0, 0, 0) !important;
            filter: drop-shadow(0 1px 2px #fffde4);
        }

        .fa-upload, .fa-file-code, .fa-magic {
            color:rgb(0, 0, 0) !important;
            filter: drop-shadow(0 1px 2px #fffde4);
        }

        .fa-copy {
            color: #3949ab !important;
        }

        .swal2-border-radius {
            border-radius: 16px !important;
        }

    </style>
</head>

<body>
    <div class="container upload-card">
        <h2 class="upload-title"><i class="fa-solid fa-upload"></i> Upload Blade Files to Generate Routes and Controller Methods</h2>

        <form action="{{ route('upload.blade') }}" method="POST" enctype="multipart/form-data" class="upload-form">
            @csrf
            <label for="blade_files" class="upload-label"><i class="fa-regular fa-file-code"></i> Select Blade Files</label>
            <input type="file" id="blade_files" name="blade_files[]" multiple required class="upload-input">
            <button type="submit" class="submit-btn upload-btn"><i class="fa-solid fa-magic"></i> Generate</button>
        </form>

        @isset($routeDefinitions)
        <h3><i class="fa-solid fa-route"></i> Route Definitions</h3>
        <button class="copy-btn" onclick="copyText('routes-block')"><i class="fa-regular fa-copy"></i> Copy Routes</button>
        <pre id="routes-block">
@foreach ($useStatements as $use)
{{ $use }}
@endforeach

@foreach ($routeDefinitions as $line)
{!! $line !!}
@endforeach
            </pre>

        <h3><i class="fa-solid fa-code"></i> Controller Methods</h3>
        <button class="copy-btn" onclick="copyText('controllers-block')"><i class="fa-regular fa-copy"></i> Copy Controller Methods</button>
        <pre id="controllers-block">
@foreach ($methodSnippets as $controller => $methods)
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class {{ $controller }} extends Controller
{
@foreach ($methods as $m)
{!! "\n$m\n" !!}
@endforeach
}
@endforeach
            </pre>
        @endisset
    </div>

    <script>
        function copyText(id) {
            const text = document.getElementById(id).innerText;
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Copied!',
                    text: 'Copied to clipboard!',
                    background: '#fffde4',
                    color: '#333',
                    confirmButtonColor: '#f4d03f',
                    customClass: {
                        popup: 'swal2-border-radius'
                    }
                });
            });
        }
    </script>
</body>

</html>
