<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Laravel Automation</title>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            body {
                font-family: 'Arial', sans-serif;
                margin: 0;
                padding: 0;
                background: linear-gradient(135deg, #f4f4f9, #e0e0e0);
                color: #333;
                animation: fadeIn 1s ease-in-out;
                overflow-x: hidden;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            header {
                background: linear-gradient(135deg, rgb(192, 185, 70), #f4d03f);
                color: #333333;
                padding: 1.5rem 0;
                text-align: center;
                margin-bottom: 60px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                animation: slideDown 1s ease-in-out;
            }

            @keyframes slideDown {
                from {
                    transform: translateY(-100%);
                }

                to {
                    transform: translateY(0);
                }
            }

            h1 {
                padding: 0 0 30px;
            }

            a:hover,
            a:focus {
                text-decoration: none;
                outline: none;
            }

            #accordion .panel {
                border: none;
                border-radius: 0;
                box-shadow: none;
                margin-bottom: 15px;
                position: relative;
            }

            #accordion .panel:before {
                content: "";
                display: block;
                width: 1px;
                height: 100%;
                border: 1px dashed #6e8898;
                top: 25px;
                left: 18px;
                position: absolute;
            }

            #accordion .panel:last-child:before {
                display: none;
            }

            #accordion .panel-heading {
                padding: 0;
                border: none;
                border-radius: 0;
                position: relative;
            }

            #accordion .panel-title a {
                display: block;
                padding: 10px 30px 10px 60px;
                margin: 0;
                background: #fff;
                font-size: 18px;
                font-weight: 700;
                color: #1d3557;
                letter-spacing: 1px;
                border-radius: 0;
                overflow: hidden;
                position: relative;
            }

            #accordion .panel-title a:before,
            #accordion .panel-title a.collapsed:before {
                content: "\f107";
                font-family: "Font Awesome 5 Free";
                font-weight: 900;
                width: 40px;
                height: 100%;
                line-height: 40px;
                font-size: 17px;
                color: #fff;
                text-align: center;
                background: #f4d03f;
                border-radius: 3px;
                border: 1px solid rgb(249, 200, 8);
                position: absolute;
                top: 0;
                left: 0;
                transition: all 0.3s ease 0s;
            }

            #accordion .panel-title a.collapsed:before {
                content: "\f105";
                color: #000;
                background: #fff;
                border: 1px solid #6e8898;
            }

            #accordion .panel-body {
                padding: 40px 20px 30px;
                margin-left: 40px;
                border-top: none;
                background: #f4d03f;
                font-size: 15px;
                line-height: 28px;
                letter-spacing: 1px;
                -webkit-clip-path: polygon(0% 15%, 15% 15%, 15% 0%, 85% 0%, 85% 15%, 100% 15%, 100% 85%, 85% 85%, 85% 100%, 15% 100%, 15% 85%, 0% 85%);
                clip-path: polygon(0% 15%, 15% 15%, 15% 0%, 85% 0%, 85% 15%, 100% 15%, 100% 85%, 85% 85%, 85% 100%, 15% 100%, 15% 85%, 0% 85%);
                text-align: center;
            }

            #accordion .panel-body a.btn {
                font-size: 25px;
                /* Adjust the size to make the button small */
                padding: 5px;
                /* Adjust padding for smaller size */
                border-radius: 25px;
                width: auto;
                /* Adjust width to make it smaller */
                max-width: 55px;
                /* Set a maximum width */
            }
            header p {
                font-size: 18px;
                margin: 0;
                padding: 0 0 20px;
            }
            footer {
                background: #f4d03f;
                color: #333;
                text-align: center;
                padding: 60px 0 100px 0;
                margin-top: 40px;
                border-top: 2px solid #e0c200;
                font-size: 16px;
                box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.04);
                letter-spacing: 1px;
            }

            footer a {
                color: #1d3557;
                font-weight: bold;
                transition: color 0.2s;
            }

            footer a:hover {
                color: #b7950b;
                text-decoration: underline;
            }

            .swal2-popup {
                background: #fffde4 !important;
                color: #333 !important;
                border-radius: 18px !important;
                box-shadow: 0 4px 24px rgba(244,208,63,0.15) !important;
                font-family: 'Arial', sans-serif;
                width: 30em !important;
                max-width: 90vw !important;
                padding: 2.5em 2.5em 2em 2.5em !important;
                font-size: 1.35em !important;
            }
            .swal2-confirm {
                background: #f4d03f !important;
                color: #333 !important;
                font-weight: bold;
                border-radius: 8px !important;
                box-shadow: 0 2px 8px rgba(244,208,63,0.10);
                border: none;
            }
            .swal2-title {
                color: #b7950b !important;
                font-weight: 700;
            }

            @media only screen and (max-width: 480px) {
                #accordion .panel-body {
                    padding: 70px 10px;
                }
            }
        </style>
    </head>

    <body>
        <header>
            <h1>Laravel Automation Steps</h1>
            <p>Follow the steps below to automate your Laravel project.Click the buttons to perform the actions.</p>
        </header>
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: "{{ session('success') }}",
                    background: '#fffde4',
                    color: '#333',
                    confirmButtonColor: '#f4d03f',
                });
            </script>
        @endif
        @if (session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Waning',
                    text: "{{ session('error') }}",
                    background: 'red',
                    color: '#333',
                    confirmButtonColor: '#f4d03f',
                });
            </script>
        @endif
        <div class="row">
            <div class="col-md-offset-3 col-md-6">
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingOne">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                                    aria-expanded="true" aria-controls="collapseOne">
                                    Step 1
                                </a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel"
                            aria-labelledby="headingOne">
                            <div class="panel-body" style="text-align: center;">
                                <p>Select the file you want to convert into blade template.</p>
                                <a target="_blank" href="{{ route('htmlToBlade') }}" class="btn btn-success"
                                    style="display: block; margin: 0 auto;"><i class="fa-brands fa-golang"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingTwo">
                            <h4 class="panel-title">
                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                                    href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Step 2
                                </a>
                            </h4>
                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel"
                            aria-labelledby="headingTwo">
                            <div class="panel-body" style="text-align: center;">
                                <p>Click the button below to generate components from your blade template.</p>
                                <a href="{{ route('make-components') }}" class="btn btn-success"
                                    style="display: block; margin: 0 auto;"><i class="fa-brands fa-golang"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingThree">
                            <h4 class="panel-title">
                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                                    href="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                    Step 3
                                </a>
                            </h4>
                        </div>
                        <div id="collapse3" class="panel-collapse collapse" role="tabpanel"
                            aria-labelledby="headingThree">
                            <div class="panel-body" style="text-align: center;">
                                <p>Click the button below to generate routes and controller methods automatically.</p>
                                <a href="{{ route('bladeToRouteAuto') }}" class="btn btn-success"
                                    style="display: block; margin: 0 auto;"><i class="fa-brands fa-golang"></i></a>
                                    <hr>
                                <p>Or click the button below to generate routes and controller methods manually.</p>
                                <a target="_blank" href="{{ route('bladeToRoute') }}" class="btn btn-success"
                                    style="display: block; margin: 0 auto;"><i class="fa-brands fa-golang"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="heading4">
                            <h4 class="panel-title">
                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                                    href="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                    Step 4
                                </a>
                            </h4>
                        </div>
                        <div id="collapse4" class="panel-collapse collapse" role="tabpanel"
                            aria-labelledby="heading4">
                            <div class="panel-body" style="text-align: center;">
                                <p>Click the button below to generate views.</p>
                                <a href="{{ route('make-views') }}" class="btn btn-success"
                                    style="display: block; margin: 0 auto;"><i class="fa-brands fa-golang"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer>
            <p>Note: This is a demo version. Please use it responsibly.</p>
            <p>For any issues, please contact <a target="_blank" href="https://facebook.com/DevOashim">Washim Akram</a></p>
            <p>Thank you for using Laravel Automation.</p>
        </footer>

    </body>

</html>
