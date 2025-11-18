<?php

namespace DevOashim\Automate\src\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;




class AutomateController extends Controller
{
    public $page_header_title = 'h2';
    public $allowCurrent = false;

    public function htmlToBlade(Request $request)
    {
        $request->validate([
            'html_file.*' => 'required|file|mimes:html,htm,txt',
        ]);

        $savedFiles = [];

        foreach ($request->file('html_file') as $file) {
            $htmlContent = file_get_contents($file->getRealPath());

            // Replace href="*.html" with route(...)
            $htmlContent = preg_replace_callback(
                '/href="([^"]+\.html)"/i',
                function ($matches) {
                    $filename = pathinfo($matches[1], PATHINFO_FILENAME);
                    return 'href="{{ route(\'' . $filename . '\') }}"';
                },
                $htmlContent
            );

            // Replace src="assets/..."
            $htmlContent = preg_replace_callback(
                '/src="(assets\/[^"]+)"/i',
                fn($matches) => 'src="{{ asset(\'' . $matches[1] . '\') }}"',
                $htmlContent
            );

            // Replace url(assets/...)
            $htmlContent = preg_replace_callback(
                '/url\((["\']?)(assets\/[^)\'"]+)\1\)/i',
                fn($matches) => "url({{ asset('{$matches[2]}') }})",
                $htmlContent
            );

            // Replace href="assets/*.css"
            $htmlContent = preg_replace_callback(
                '/href="(assets\/[^"]+\.css)"/i',
                fn($matches) => 'href="{{ asset(\'' . $matches[1] . '\') }}"',
                $htmlContent
            );

            // Replace href="assets/*.png"
            $htmlContent = preg_replace_callback(
                '/href="(assets\/[^"]+\.png)"/i',
                fn($matches) => 'href="{{ asset(\'' . $matches[1] . '\') }}"',
                $htmlContent
            );
            // Replace href="assets/*.img"
            $htmlContent = preg_replace_callback(
                '/href="(assets\/[^"]+\.img)"/i',
                fn($matches) => 'href="{{ asset(\'' . $matches[1] . '\') }}"',
                $htmlContent
            );
            // Replace href="assets/*.jpg"
            $htmlContent = preg_replace_callback(
                '/href="(assets\/[^"]+\.jpg)"/i',
                fn($matches) => 'href="{{ asset(\'' . $matches[1] . '\') }}"',
                $htmlContent
            );
            // Replace href="assets/*.jpeg"
            $htmlContent = preg_replace_callback(
                '/href="(assets\/[^"]+\.jpeg)"/i',
                fn($matches) => 'href="{{ asset(\'' . $matches[1] . '\') }}"',
                $htmlContent
            );
            // action="something.html" → action="{{ route('something') }}"
            $htmlContent = preg_replace_callback(
                '/action="([^"]+\.html)"/i',
                function ($matches) {
                    $filename = pathinfo($matches[1], PATHINFO_FILENAME);
                    return 'action="{{ route(\'' . $filename . '\') }}"';
                },
                $htmlContent
            );
            // href="assets/*.webmanifest" → href="{{ asset('assets/...') }}"
            $htmlContent = preg_replace_callback(
                '/href="(assets\/[^"]+\.webmanifest)"/i',
                function ($matches) {
                    return 'href="{{ asset(\'' . $matches[1] . '\') }}"';
                },
                $htmlContent
            );
            $htmlContent = preg_replace_callback(
                '/<title>(.*?)<\/title>/i',
                function ($matches) {
                    $updated = preg_replace('/html\s*5?/i', 'Laravel ', $matches[1]);
                    return "<title>$updated</title>";
                },
                $htmlContent
            );
            $htmlContent = preg_replace_callback(
                '/<meta\s+name="description"\s+content="([^"]*)"/i',
                function ($matches) {
                    $updated = preg_replace('/html\s*5?/i', 'Laravel ', $matches[1]);
                    return '<meta name="description" content="' . $updated . '"';
                },
                $htmlContent
            );

            // Save to Blade
            $filenameOnly = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $bladePath = resource_path("views/htmlToBlade/{$filenameOnly}.blade.php");

            if (!file_exists(dirname($bladePath))) {
                mkdir(dirname($bladePath), 0755, true);
            }

            file_put_contents($bladePath, $htmlContent);
            $savedFiles[] = "{$filenameOnly}.blade.php";
        }

        // Return success message with file names

        //$msg = count($savedFiles) . "টি ফাইল সফলভাবে সেভ হয়েছে 💛💛" . implode(', ', $savedFiles);

        // Return success message without file names
        $msg = count($savedFiles) . " files saved successfully 💛💛";

        return back()->with(['msg' => $msg, 'type' => 'success']);
    }
    public function bladeToRoute(Request $request)
    {
        $request->validate([
            'blade_files.*' => 'required|mimes:php,txt,html',
        ]);

        $allRoutes = [];

        foreach ($request->file('blade_files') as $file) {
            $content = file_get_contents($file->getRealPath());

            preg_match_all("/href\s*=\s*\"{{\s*route\(\s*'([^']+)'\s*\)\s*}}\"/", $content, $matches);

            if (!empty($matches[1])) {
                $allRoutes = array_merge($allRoutes, $matches[1]);
            }
        }

        $uniqueRoutes = array_values(array_unique($allRoutes));

        $routeDefinitions = [];
        $methodSnippets = [
            'PagesController' => [],
            'HomeController' => [],
        ];

        $usedControllers = [];

        foreach ($uniqueRoutes as $route) {
            $isHome = str_contains($route, 'index');
            $controller = $isHome ? 'HomeController' : 'PagesController';
            $usedControllers[] = $controller;

            $method = str_replace('-', '_', $route);
            $view = ($isHome ? 'home' : 'pages') . '.' . $route;

            // Route definition
            $routeDefinitions[] = "Route::get('/$route', [$controller::class, '$method'])\n    ->name('$route');";

            // Method definition
            $methodSnippets[$controller][] =
                "    public function $method()\n    {\n        return view('$view');\n    }";
        }

        $useStatements = array_unique(array_map(fn($ctrl) => "use App\\Http\\Controllers\\$ctrl;", array_unique($usedControllers)));

        return view('auto::uploadBlade', [
            'routeDefinitions' => $routeDefinitions,
            'useStatements' => $useStatements,
            'methodSnippets' => $methodSnippets,
        ]);
    }
    public function bladeToRouteAuto()
    {
        $path = resource_path('views/components/menuList.blade.php');

        if (!File::exists($path)) {
            return redirect()->back()->with('error', 'menuList.blade.php not found!');
        }

        $content = File::get($path);
        preg_match_all("/href\s*=\s*\"{{\s*route\(\s*'([^']+)'\s*\)\s*}}\"/", $content, $matches);

        $uniqueRoutes = array_values(array_unique($matches[1]));

        if (empty($uniqueRoutes)) {
            return redirect()->back()->with('error', 'No routes found in menuList.blade.php!');
        }

        $webRoutes = base_path('routes/web.php');
        $webFileContent = File::exists($webRoutes) ? File::get($webRoutes) : "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n";
        $controllerBasePath = app_path('Http/Controllers');

        // Ensure "<?php\n\nuse Illuminate\Support\Facades\Route;" exists
        if (!Str::startsWith($webFileContent, "<?php")) {
            $webFileContent = "<?php\n\n" . $webFileContent;
        }

        if (!Str::contains($webFileContent, "use Illuminate\\Support\\Facades\\Route;")) {
            $webFileContent = preg_replace(
                '/<\?php\s*/',
                "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n",
                $webFileContent,
                1
            );
        }

        foreach ($uniqueRoutes as $route) {
            $isHome = Str::contains($route, 'index');
            $controllerName = $isHome ? 'HomeController' : 'PagesController';
            $methodName = Str::replace('-', '_', $route);
            $viewPath = ($isHome ? 'home' : 'pages') . '.' . $route;

            // Add controller use statement if missing
            $useStatement = "use App\\Http\\Controllers\\$controllerName;\n";
            if (!Str::contains($webFileContent, $useStatement)) {
                $webFileContent = preg_replace(
                    '/use Illuminate\\\\Support\\\\Facades\\\\Route;\s*/i',
                    "use Illuminate\\Support\\Facades\\Route;\n$useStatement",
                    $webFileContent,
                    1
                );
            }

            // Add route definition if missing
            $routeSnippet = "Route::get('/$route', [{$controllerName}::class, '$methodName'])\n    ->name('$route');";
            if (!Str::contains($webFileContent, "->name('$route');")) {
                $webFileContent .= "\n\n" . $routeSnippet;
            }

            // Create or update Controller
            $controllerPath = $controllerBasePath . "/$controllerName.php";

            if (!File::exists($controllerPath)) {
                File::put($controllerPath, <<<PHP
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class $controllerName extends Controller
{
    public function $methodName(){
        return view('$viewPath');
    }
}
PHP);
            } else {
                $controllerContent = File::get($controllerPath);
                if (!Str::contains($controllerContent, "function $methodName(")) {
                    $methodCode = <<<PHP

    public function $methodName(){
        return view('$viewPath');
    }
PHP;
                    $controllerContent = preg_replace('/}\s*$/', $methodCode . "\n}", $controllerContent);
                    File::put($controllerPath, $controllerContent);
                }
            }
        }

        File::put($webRoutes, $webFileContent);

        return redirect()->back()->with('success', 'Route and Controller Created 😊');
    }
    public function scanAndCreateViews()
    {
        $controllersPath = app_path('Http/Controllers');
        $excludedFiles = ['Controller.php', 'autoController.php'];
        $bladeFilesCreated = 0;
        $controllerFiles = File::allFiles($controllersPath);

        // ✅ STEP 1: head.blade.php থেকে CSS asset path collect
        $baseCssAssets = [];
        $baseHeadPath = resource_path('views/components/head.blade.php');
        if (file_exists($baseHeadPath)) {
            $baseHeadContent = File::get($baseHeadPath);
            preg_match_all('/{{\s*asset\([\'"]([^\'"]+\.css)[\'"]\)\s*}}/', $baseHeadContent, $baseMatches);
            $baseCssAssets = array_unique($baseMatches[1]);
        }

        // ✅ STEP 1: head.blade.php থেকে js asset path collect
        $baseScriptsAssets = [];
        $baseScriptsPath = resource_path('views/components/scripts.blade.php');
        if (file_exists($baseScriptsPath)) {
            $baseJsContent = File::get($baseScriptsPath);
            preg_match_all('/{{\s*asset\([\'"]([^\'"]+\.js)[\'"]\)\s*}}/', $baseJsContent, $baseMatchesJs);
            $baseScriptsAssets = array_unique($baseMatchesJs[1]);
        }




        foreach ($controllerFiles as $file) {
            if (in_array($file->getFilename(), $excludedFiles))
                continue;

            $code = File::get($file->getRealPath());
            preg_match_all('/return\s+view\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/', $code, $matches);

            foreach ($matches[1] as $viewPath) {
                $bladePath = resource_path('views/' . str_replace('.', '/', $viewPath) . '.blade.php');

                if (!file_exists($bladePath)) {
                    $dir = dirname($bladePath);
                    if (!is_dir($dir))
                        File::makeDirectory($dir, 0755, true);

                    $fileName = last(explode('.', $viewPath)) . '.blade.php';
                    $sourceFile = resource_path('views/htmlToBlade/' . $fileName);
                    if (!file_exists($sourceFile))
                        continue;

                    $htmlContent = File::get($sourceFile);



                    // ✅ STEP 2: HTML ফাইলের head tag থেকে CSS asset গুলো বের করো
                    $fileCssAssets = [];
                    if (preg_match('/<head[^>]*>(.*?)<\/head>/is', $htmlContent, $headMatch)) {
                        $fileHeadContent = $headMatch[1];
                        preg_match_all('/{{\s*asset\([\'"]([^\'"]+\.css)[\'"]\)\s*}}/', $fileHeadContent, $fileMatches);
                        $fileCssAssets = array_unique($fileMatches[1]);
                    }

                    // ✅ STEP 3: নতুন লিংকগুলো খুঁজে বের করা যেগুলো base এ নাই
                    // $uniqueCssAssets = array_diff($fileCssAssets, $baseCssAssets);
                    $uniqueCssAssets = [];
                    foreach ($fileCssAssets as $fileCssAsset) {
                        if (!in_array($fileCssAsset, $baseCssAssets)) {
                            $uniqueCssAssets[] = $fileCssAsset;
                        }

                    }

                    $cssLinks = '';
                    foreach ($uniqueCssAssets as $cssPath) {
                        $cssLinks .= '<link rel="stylesheet" href="{{ asset(\'' . $cssPath . '\') }}">' . "\n";
                    }


                    // ✅ STEP 2: HTML ফাইলের head tag থেকে js asset গুলো বের করো
                    $fileJsAssets = [];
                    if (preg_match('/<\/footer[^>]*>(.*?)<\/body>/is', $htmlContent, $headMatchJs)) {
                        $fileJsContent = $headMatchJs[1];
                        preg_match_all('/{{\s*asset\([\'"]([^\'"]+\.js)[\'"]\)\s*}}/', $fileJsContent, $fileMatchesJs);
                        $fileJsAssets = array_unique($fileMatchesJs[1]);
                    }

                    // ✅ STEP 3: নতুন লিংকগুলো খুঁজে বের করা যেগুলো base এ নাই
                    $uniqueJsAssets = [];
                    foreach ($fileJsAssets as $fileJsAsset) {
                        if (!in_array($fileJsAsset, $baseScriptsAssets)) {
                            $uniqueJsAssets[] = $fileJsAsset;
                        }

                    }

                    $JsLinks = '';
                    foreach ($uniqueJsAssets as $jsPath) {
                        $JsLinks .= '<script src="{{ asset(\'' . $jsPath . '\') }}"></script>' . "\n";
                    }



                    // Blade ডিরেক্টিভ গুলা এনকোড করে ফেলো
                    $htmlContent = str_replace(
                        ['{{', '}}', '@'],
                        ['___BLADE_OPEN___', '___BLADE_CLOSE___', '___BLADE_AT___'],
                        $htmlContent
                    );

                    // Title বের করো
                    $titleText = 'Untitled';
                    if (preg_match('/<title>(.*?)<\/title>/si', $htmlContent, $titleMatch)) {
                        $titleText = preg_replace('/HTML\s*5?/i', 'Laravel', trim($titleMatch[1]));
                    }

                    $middleContent = '';
                    $pageHeaderBlade = '';

                    if (str_starts_with($viewPath, 'pages.')) {
                        if (preg_match('/<section[^>]*class="[^"]*page-header[^"]*"[^>]*>(.*?)<\/section>/si', $htmlContent, $sectionMatch)) {
                            $headerSection = $sectionMatch[1];
                            $pattern = '/<'.$this->page_header_title.'[^>]*>(.*?)<\/' . $this->page_header_title . '>/si';
                            preg_match($pattern, $headerSection, $h2Match);
                            $headerTitle = trim(strip_tags($h2Match[1] ?? ''));
                            preg_match_all('/<li[^>]*>(.*?)<\/li>/si', $headerSection, $liMatches);
                            $headerSubtitle = trim(strip_tags(end($liMatches[1]) ?? ''));

                            $startPos = strpos($htmlContent, $sectionMatch[0]) + strlen($sectionMatch[0]);
                            if (preg_match('/<section[^>]*class="[^"]*newsletter[^"]*"[^>]*>/si', $htmlContent, $newsletterMatch, PREG_OFFSET_CAPTURE, $startPos)) {
                                $endPos = $newsletterMatch[0][1];
                            } else {
                                $endPos = strlen($htmlContent);
                            }

                            $middleContent = substr($htmlContent, $startPos, $endPos - $startPos);

                            if ($headerTitle && $headerSubtitle) {
                                $pageHeaderBlade = "<x-page-header title=\"$headerTitle\" subtitle=\"$headerSubtitle\" />";
                            }
                        }

                        $middleContent = str_replace(
                            ['___BLADE_OPEN___', '___BLADE_CLOSE___', '___BLADE_AT___'],
                            ['{{', '}}', '@'],
                            $middleContent
                        );
                        $middleContent = urldecode(html_entity_decode($middleContent));
                        if (!empty($cssLinks)) {
                            $pushCss = 
"@push('styles') 
    \n$cssLinks 
@endpush";
                        } else {
                            $pushCss = '';
                        }
                        if (!empty($JsLinks)) {
$pushJs = 
"@push('scripts') 
   \n$JsLinks 
@endpush";
                        } else {
                            $pushJs = '';
                        }
                        $finalContent = <<<BLADE
@extends('layouts.layoutCommon')
@section('title', '$titleText')
$pushCss
$pushJs
@section('content')

$pageHeaderBlade

$middleContent

@endsection
BLADE;
                    } elseif (str_starts_with($viewPath, 'home.')) {
                        libxml_use_internal_errors(true);
                        $dom = new \DOMDocument();
                        $dom->loadHTML($htmlContent);
                        $xpath = new \DOMXPath($dom);
                        $nodes = $xpath->query('//*[contains(@class, "stricky-header")]');
                        if ($nodes->length === 0)
                            $nodes = $xpath->query('//header');

                        if ($nodes->length > 0) {
                            $node = $nodes->item(0);
                            $sibling = $node->nextSibling;

                            while ($sibling) {
                                if ($sibling->nodeType === XML_ELEMENT_NODE) {
                                    $classAttr = $sibling->attributes?->getNamedItem("class")?->nodeValue ?? '';
                                    if (preg_match('/newsletter|footer/i', $classAttr))
                                        break;
                                }
                                $middleContent .= $dom->saveHTML($sibling);
                                $sibling = $sibling->nextSibling;
                            }
                        }

                        $middleContent = str_replace(
                            ['___BLADE_OPEN___', '___BLADE_CLOSE___', '___BLADE_AT___'],
                            ['{{', '}}', '@'],
                            $middleContent
                        );
                        $middleContent = urldecode(html_entity_decode($middleContent));
                        if (!empty($cssLinks)) {
                            $pushCss = 
"@push('styles')  
   \n$cssLinks 
@endpush";
                        } else {
                            $pushCss = '';
                        }
                        if (!empty($JsLinks)) {
                            $pushJs = 
"@push('scripts') 
   \n$JsLinks 
@endpush";
                        } else {
                            $pushJs = '';
                        }
                        $finalContent = <<<BLADE
@extends('layouts.layoutStyleOne')
@section('title', '$titleText')
$pushCss
$pushJs
@section('content')

$middleContent

@endsection
BLADE;
                    }

                    File::put($bladePath, $finalContent);
                    $bladeFilesCreated++;
                }
            }
        }

        return back()->with('success', "$bladeFilesCreated Blade files generated successfully 😊");
    }

    public function extractAndGenerateMenuList(): bool
    {
        $inputPath = resource_path('views/htmlToBlade/index.blade.php');
        $outputPath = resource_path('views/components/menuList.blade.php');

        // Create components directory if it doesn't exist
        $componentsDir = dirname($outputPath);
        if (!File::exists($componentsDir)) {
            File::makeDirectory($componentsDir, 0755, true);
        }

        // If input file doesn't exist, create an empty output file and return false
        if (!File::exists($inputPath)) {
            File::put($outputPath, '<!-- Menu list will be generated here -->');
            return false;
        }

        $html = File::get($inputPath);

        libxml_use_internal_errors(true);

        // Blade কোড সঠিক রাখতে xml header সহ HTML লোড
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?><div>' . $html . '</div>');
        $xpath = new \DOMXPath($dom);

        // main-menu__list ul খোঁজা হচ্ছে
        $ulNodes = $xpath->query('//ul[contains(@class, "main-menu__list")]');

        if ($ulNodes->length === 0) {
            // Create empty output file if no UL found
            File::put($outputPath, '<!-- No menu list found in source file -->');
            return false;
        }

        $ul = $ulNodes[0];
        $newUl = '<ul class="main-menu__list">' . PHP_EOL;

        foreach ($ul->childNodes as $li) {
            if ($li->nodeType !== XML_ELEMENT_NODE || $li->nodeName !== 'li')
                continue;

            $liHtml = $dom->saveHTML($li);

            // Blade এর route() ট্যাগ ঠিক রাখতে ডিকোড করা হচ্ছে
            $liHtml = html_entity_decode($liHtml);
            $liHtml = urldecode($liHtml);

            // li এর ভিতরে যতগুলো route() আছে তা বের করা
            preg_match_all('/route\(\'([^)]+)\'\)/', $liHtml, $matches);

            if ($this->allowCurrent) {
                 $routes = $matches[1];
            } else {
               $routes = [];  
            }
            
            if (count($routes)) {
                $routeList = implode("','", $routes);
                $isDropdown = strpos($liHtml, '<ul') !== false;

                if ($isDropdown) {
                    // ড্রপডাউন হলে class থেকে আগের current বাদ দিয়ে নতুনভাবে যুক্ত করা
                    $modifiedLi = preg_replace_callback(
                        '/<li([^>]*)class="([^"]*dropdown[^"]*)"([^>]*)>/i',
                        function ($match) use ($routeList) {
                            $existingClasses = preg_replace('/\bcurrent\b/', '', trim($match[2]));
                            $existingClasses = trim(preg_replace('/\s+/', ' ', $existingClasses));
                            return '<li' . $match[1] . 'class="' . $existingClasses . ' @if (request()->is([\'' . $routeList . '\'])) current @endif"' . $match[3] . '>';
                        },
                        $liHtml
                    );

                    // fallback: যদি class="dropdown..." না থাকে
                    if ($modifiedLi === $liHtml) {
                        $modifiedLi = preg_replace(
                            '/<li([^>]*)>/i',
                            '<li class="dropdown @if (request()->is([\'' . $routeList . '\'])) current @endif">',
                            $liHtml
                        );
                    }
                } else {
                    // সাধারণ li এর class থেকে আগের current বাদ দিয়ে নতুন current যুক্ত করা
                    $modifiedLi = preg_replace_callback(
                        '/<li([^>]*)class="([^"]*)"([^>]*)>/i',
                        function ($match) use ($routeList) {
                            $existingClasses = preg_replace('/\bcurrent\b/', '', trim($match[2]));
                            $existingClasses = trim(preg_replace('/\s+/', ' ', $existingClasses));
                            return '<li' . $match[1] . 'class="' . $existingClasses . ' @if (request()->is([\'' . $routeList . '\'])) current @endif"' . $match[3] . '>';
                        },
                        $liHtml
                    );

                    // fallback: যদি class="" না থাকে
                    if ($modifiedLi === $liHtml) {
                        $modifiedLi = preg_replace(
                            '/<li([^>]*)>/i',
                            '<li @if (request()->is([\'' . $routeList . '\'])) class="current" @endif>',
                            $liHtml
                        );
                    }
                }

                $newUl .= "    " . $modifiedLi . PHP_EOL;
            } else {
                // যদি route() না থাকে, li অপরিবর্তিত রাখা
                $newUl .= "    " . $liHtml . PHP_EOL;
            }
        }

        $newUl .= '</ul>';

        // ফাইনাল UL ফাইল আকারে সংরক্ষণ
        File::put($outputPath, $newUl);

        return true;
    }

    public function extractAndSaveHeadComponent(): bool
    {
        // সোর্স ফাইলের পথ
        $sourcePath = resource_path('views/htmlToBlade/index.blade.php');

        // গন্তব্য ফাইলের পথ
        $destinationPath = resource_path('views/components/head.blade.php');

        // সোর্স ফাইল আছে কিনা চেক করি
        if (!File::exists($sourcePath)) {
            return false;
        }

        // সোর্স ফাইল থেকে কন্টেন্ট নেই
        $html = File::get($sourcePath);

        // <head> ট্যাগ খুঁজে বের করি
        if (!preg_match('/<head\b[^>]*>(.*?)<\/head>/is', $html, $matches)) {
            return false;
        }

        // <head> এর ভিতরের কন্টেন্ট নেই
        $headContent = $matches[1];

        // <title> ট্যাগ রূপান্তর করি @yield দিয়ে
        $headContent = preg_replace_callback('/<title>(.*?)<\/title>/is', function ($match) {
            $defaultTitle = trim($match[1]);
            return "<title>@yield('title', '{$defaultTitle}')</title>";
        }, $headContent);

        // @stack('styles') যোগ করি যদি না থাকে
        $additionalStack = "\n    {{-- Additional Styles --}}\n    @stack('styles')\n";
        if (!str_contains($headContent, '@stack(\'styles\')')) {
            $headContent .= $additionalStack;
        }

        // <head> ট্যাগ সহ চূড়ান্ত কন্টেন্ট তৈরি করি
        $finalHead = "<head>\n" . trim($headContent) . "\n</head>\n";

        // গন্তব্য ফোল্ডার তৈরি করি যদি না থাকে
        File::ensureDirectoryExists(dirname($destinationPath));

        // ফাইল সংরক্ষণ করি
        File::put($destinationPath, $finalHead);

        // সফল হলে true রিটার্ন করি
        return true;
    }
    public function extractAndSaveScriptComponent(): bool
    {
        $sourcePath = resource_path('views/htmlToBlade/index.blade.php');

        // গন্তব্য ফাইলের পথ
        $destinationPath = resource_path('views/components/scripts.blade.php');

        // ফাইল আছে কিনা যাচাই করি
        if (!File::exists($sourcePath)) {
            return false;
        }

        // ফাইলের কন্টেন্ট নেই
        $html = File::get($sourcePath);

        // </footer> থেকে </body> এর আগ পর্যন্ত অংশ নেই
        if (!preg_match('/<\/footer>(.*?)<\/body>/is', $html, $matches)) {
            return false;
        }

        // কাঙ্খিত অংশ নেই
        $section = $matches[1];

        // ঐ অংশ থেকে সব <script> ট্যাগ বের করি
        preg_match_all('/<script\b[^>]*>.*?<\/script>/is', $section, $scriptMatches);

        if (empty($scriptMatches[0])) {
            return false;
        }

        // সব স্ক্রিপ্ট একসাথে যোগ করি
        $scripts = implode("\n", $scriptMatches[0]);

        // @stack('scripts') যোগ করি
        $scripts .= "\n\n{{-- Additional Scripts --}}\n@stack('scripts')\n";

        // গন্তব্য ফোল্ডার তৈরি করি যদি না থাকে
        File::ensureDirectoryExists(dirname($destinationPath));

        // ফাইল সংরক্ষণ করি
        File::put($destinationPath, $scripts);

        // সফল হলে true রিটার্ন করি
        return true;
    }
    public function make_components()
    {
        $this->extractAndSaveHeadComponent();
        $this->extractAndGenerateMenuList();
        $this->extractAndSaveScriptComponent();
        return redirect()->back()->with('success', 'Components Created 😊');
    }
}
