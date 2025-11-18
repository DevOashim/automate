<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>UPLOAD HTML FILES</title>
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
        <!-- jQuery -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            body {
                background: linear-gradient(135deg, #fffde4, #f4d03f 80%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .custom-card {
                background: #fffde4;
                border-radius: 18px;
                box-shadow: 0 4px 24px rgba(60,60,100,0.10);
                padding: 40px 32px 32px 32px;
                max-width: 70%;
                border: 2px solid #f4d03f;
                margin: 40px auto;
            }
            .custom-title {
                margin-bottom: 24px;
                text-align:center;
                font-weight: 700;
                background: linear-gradient(90deg, #f4d03f 60%, #fffde4 100%);
                color: #333;
                border-radius: 8px;
                padding: 16px 0 12px 0;
                box-shadow: 0 2px 8px rgba(244,208,63,0.10);
                letter-spacing: 1px;
                font-size: 2rem;
            }
            .custom-label {
                font-weight: 600;
                color: #3949ab;
                background: #f4d03f;
                padding: 6px 18px;
                border-radius: 6px;
                box-shadow: 0 1px 4px rgba(244,208,63,0.10);
                display: inline-block;
                margin-bottom: 8px;
            }
            .custom-input {
                padding: 10px;
                border: 1.5px solid #f4d03f;
                border-radius: 6px;
                width: 100%;
                max-width: 350px;
                background: #fff;
                font-size: 1em;
                transition: border 0.2s;
            }
            .custom-input:focus {
                border: 1.5px solid #3949ab;
                outline: none;
                box-shadow: 0 0 0 2px #f4d03f33;
            }
            .custom-btn {
                width: 100%;
                max-width: 200px;
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
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
            .custom-btn:hover {
                background: linear-gradient(90deg, #f7ca18 80%, #fffde4 100%);
                box-shadow: 0 4px 16px rgba(244,208,63,0.18);
                transform: translateY(-2px) scale(1.03);
            }
            .custom-preview {
                background: #272822;
                color: #f8f8f2;
                padding: 20px;
                border-radius: 10px;
                overflow-x: auto;
                white-space: pre-wrap;
                margin-top: 18px;
                font-size: 1.05em;
            }
            .swal2-border-radius {
                border-radius: 16px !important;
            }
        </style>
    </head>

    <body>
        <div class="custom-card">
            <h1 class="custom-title">Upload Your HTML Files <i class="fa-solid fa-file-lines"></i></h1>
            <form id="uploadForm" action="{{ route('htmlToBlade.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div>
                    <label for="html_file" class="custom-label"><i class="fa-solid fa-cloud-arrow-up"></i> Select Files :</label>
                    <input id="html_file" name="html_file[]" type="file" accept=".html,.htm,.txt" class="custom-input" required multiple>
                </div>
                <div id="filePreview" class="hidden custom-preview" style="max-height: 300px; overflow-y: auto; margin-top: 16px;"></div>
                <button type="submit" id="submitBtn" class="custom-btn"><i class="fa-solid fa-arrow-up-from-bracket fa-bounce"></i> UPLOAD</button>
            </form>
        </div>

        <!-- SweetAlert2 Notification -->
        <script>
            @if (isset($success))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: "{{ $success }}",
                    background: '#fffde4',
                    color: '#333',
                    confirmButtonColor: '#f4d03f',
                    customClass: {
                        popup: 'swal2-border-radius'
                    }
                });
            @endif
        </script>

        <script>
            $(document).ready(function() {
                $('#html_file').on('change', function() {
                    const files = this.files;
                    const previewContainer = $('#filePreview');
                    previewContainer.empty().removeClass('hidden');

                    if (files.length === 0) {
                        previewContainer.addClass('hidden');
                        return;
                    }

                    Array.from(files).forEach(file => {
                        if (!file.name.match(/\.(html?|txt)$/i)) return;

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const escaped = $('<div>').text(e.target.result).html();
                            const block = $(`
                        <div class="bg-gray-900 rounded-xl shadow-lg overflow-hidden border border-indigo-300">
                            <div class="bg-indigo-700 text-white px-4 py-2 text-sm font-semibold">${file.name}</div>
                            <pre class="text-sm text-green-200 font-mono p-4 overflow-auto max-h-80 leading-snug whitespace-pre-wrap">${escaped}</pre>
                        </div>
                    `);
                            previewContainer.append(block);
                        };
                        reader.readAsText(file);
                    });
                });
            });
        </script>



    </body>

</html>
