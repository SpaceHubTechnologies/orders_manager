<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>TRA Microservices</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <style>
        /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */
        html {
            line-height: 1.15;
            -webkit-text-size-adjust: 100%
        }

        body {
            margin: 0
        }

        a {
            background-color: transparent
        }

        [hidden] {
            display: none
        }

        html {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
            line-height: 1.5
        }

        *, :after, :before {
            box-sizing: border-box;
            border: 0 solid #e2e8f0
        }

        a {
            color: inherit;
            text-decoration: inherit
        }

        svg, video {
            display: block;
            vertical-align: middle
        }

        video {
            max-width: 100%;
            height: auto
        }

        .bg-white {
            --bg-opacity: 1;
            background-color: #fff;
            background-color: rgba(255, 255, 255, var(--bg-opacity))
        }

        .bg-gray-100 {
            --bg-opacity: 1;
            background-color: #f7fafc;
            background-color: rgba(247, 250, 252, var(--bg-opacity))
        }

        .border-gray-200 {
            --border-opacity: 1;
            border-color: #edf2f7;
            border-color: rgba(237, 242, 247, var(--border-opacity))
        }

        .border-t {
            border-top-width: 1px
        }

        .flex {
            display: flex
        }

        .grid {
            display: grid
        }

        .hidden {
            display: none
        }

        .items-center {
            align-items: center
        }

        .justify-center {
            justify-content: center
        }

        .font-semibold {
            font-weight: 600
        }

        .h-5 {
            height: 1.25rem
        }

        .h-8 {
            height: 2rem
        }

        .h-16 {
            height: 4rem
        }

        .text-sm {
            font-size: .875rem
        }

        .text-lg {
            font-size: 1.125rem
        }

        .leading-7 {
            line-height: 1.75rem
        }

        .mx-auto {
            margin-left: auto;
            margin-right: auto
        }

        .ml-1 {
            margin-left: .25rem
        }

        .mt-2 {
            margin-top: .5rem
        }

        .mr-2 {
            margin-right: .5rem
        }

        .ml-2 {
            margin-left: .5rem
        }

        .mt-4 {
            margin-top: 1rem
        }

        .ml-4 {
            margin-left: 1rem
        }

        .mt-8 {
            margin-top: 2rem
        }

        .ml-12 {
            margin-left: 3rem
        }

        .-mt-px {
            margin-top: -1px
        }

        .max-w-6xl {
            max-width: 72rem
        }

        .min-h-screen {
            min-height: 100vh
        }

        .overflow-hidden {
            overflow: hidden
        }

        .p-6 {
            padding: 1.5rem
        }

        .py-4 {
            padding-top: 1rem;
            padding-bottom: 1rem
        }

        .px-6 {
            padding-left: 1.5rem;
            padding-right: 1.5rem
        }

        .pt-8 {
            padding-top: 2rem
        }

        .fixed {
            position: fixed
        }

        .relative {
            position: relative
        }

        .top-0 {
            top: 0
        }

        .right-0 {
            right: 0
        }

        .shadow {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, .1), 0 1px 2px 0 rgba(0, 0, 0, .06)
        }

        .text-center {
            text-align: center
        }

        .text-gray-200 {
            --text-opacity: 1;
            color: #edf2f7;
            color: rgba(237, 242, 247, var(--text-opacity))
        }

        .text-gray-300 {
            --text-opacity: 1;
            color: #e2e8f0;
            color: rgba(226, 232, 240, var(--text-opacity))
        }

        .text-gray-400 {
            --text-opacity: 1;
            color: #cbd5e0;
            color: rgba(203, 213, 224, var(--text-opacity))
        }

        .text-gray-500 {
            --text-opacity: 1;
            color: #a0aec0;
            color: rgba(160, 174, 192, var(--text-opacity))
        }

        .text-gray-600 {
            --text-opacity: 1;
            color: #718096;
            color: rgba(113, 128, 150, var(--text-opacity))
        }

        .text-gray-700 {
            --text-opacity: 1;
            color: #4a5568;
            color: rgba(74, 85, 104, var(--text-opacity))
        }

        .text-gray-900 {
            --text-opacity: 1;
            color: #1a202c;
            color: rgba(26, 32, 44, var(--text-opacity))
        }

        .underline {
            text-decoration: underline
        }

        .antialiased {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale
        }

        .w-5 {
            width: 1.25rem
        }

        .w-8 {
            width: 2rem
        }

        .w-auto {
            width: auto
        }

        .grid-cols-1 {
            grid-template-columns:repeat(1, minmax(0, 1fr))
        }

        @media (min-width: 640px) {
            .sm\:rounded-lg {
                border-radius: .5rem
            }

            .sm\:block {
                display: block
            }

            .sm\:items-center {
                align-items: center
            }

            .sm\:justify-start {
                justify-content: flex-start
            }

            .sm\:justify-between {
                justify-content: space-between
            }

            .sm\:h-20 {
                height: 5rem
            }

            .sm\:ml-0 {
                margin-left: 0
            }

            .sm\:px-6 {
                padding-left: 1.5rem;
                padding-right: 1.5rem
            }

            .sm\:pt-0 {
                padding-top: 0
            }

            .sm\:text-left {
                text-align: left
            }

            .sm\:text-right {
                text-align: right
            }
        }

        @media (min-width: 768px) {
            .md\:border-t-0 {
                border-top-width: 0
            }

            .md\:border-l {
                border-left-width: 1px
            }

            .md\:grid-cols-2 {
                grid-template-columns:repeat(2, minmax(0, 1fr))
            }
        }

        @media (min-width: 1024px) {
            .lg\:px-8 {
                padding-left: 2rem;
                padding-right: 2rem
            }
        }

        @media (prefers-color-scheme: dark) {
            .dark\:bg-gray-800 {
                --bg-opacity: 1;
                background-color: #2d3748;
                background-color: rgba(45, 55, 72, var(--bg-opacity))
            }

            .dark\:bg-gray-900 {
                --bg-opacity: 1;
                background-color: #1a202c;
                background-color: rgba(26, 32, 44, var(--bg-opacity))
            }

            .dark\:border-gray-700 {
                --border-opacity: 1;
                border-color: #4a5568;
                border-color: rgba(74, 85, 104, var(--border-opacity))
            }

            .dark\:text-white {
                --text-opacity: 1;
                color: #fff;
                color: rgba(255, 255, 255, var(--text-opacity))
            }

            .dark\:text-gray-400 {
                --text-opacity: 1;
                color: #cbd5e0;
                color: rgba(203, 213, 224, var(--text-opacity))
            }

            .dark\:text-gray-500 {
                --tw-text-opacity: 1;
                color: #6b7280;
                color: rgba(107, 114, 128, var(--tw-text-opacity))
            }
        }
    </style>

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>
<body class="antialiased">

<div class="ml-12 relative  items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">

<h1 id="php-laravel-simple-ads-manager">TRA MICROSERVICE</h1>
<ol>
    <li><p>commands</p>
        <p><code>$ php artisan block</code></p>
        <p><code>$ php artisan unblock</code></p>
        <p><code>$ php artisan rct:code</code></p>
        <p><code>$ php artisan enable:vat</code></p>
        <p><code>$ php artisan disable:vat</code></p>
    </li>
</ol>
<h2 id="get-list-of-providers">Post Receipt</h2>
<h3 id="request">Request</h3>

<code>Post /transaction/</code></p>
<pre><code>http:<span class="hljs-regexp">//</span><span class="hljs-number">127.0</span>.<span
            class="hljs-number">0.1</span>:<span class="hljs-number">8000</span><span class="hljs-regexp">/api/</span>v1<span
            class="hljs-regexp">/post-transaction</span>

<span class="json"> {
    <span class="hljs-attr">"customer_id"</span>: <span class="hljs-number">3</span>,
    <span class="hljs-attr">"code_sale_master"</span>: <span class="hljs-string">"78788898H99"</span>,
    <span class="hljs-attr">"status"</span>: <span class="hljs-string">200</span>,
    <span class="hljs-attr">"last_update"</span>: <span class="hljs-string">"20/12/2020"</span>,
    <span class="hljs-attr">"date_sale"</span>: <span class="hljs-string">"20/12/2020"</span>,
    <span class="hljs-attr">"date_created"</span>: <span class="hljs-string">"Feb 14, 2022 5:13 AM"</span>
    <span class="hljs-attr">"total_value"</span>: <span class="hljs-string">800</span>
    <span class="hljs-attr">"total_paid"</span>: <span class="hljs-string">800</span>
    <span class="hljs-attr">"description"</span>: <span class="hljs-string">"Test Sales is a test"</span>
}</span>
<h3 id="response">Response</h3>
<pre><code>HTTP/1.1 <span class="hljs-number">200</span> OK
<span class="hljs-attribute">Date</span>: Thu, 24 Feb 2011 12:36:30 GMT
<span class="hljs-attribute">Status</span>: 200 OK
<span class="hljs-attribute">Connection</span>: close
<span class="hljs-attribute">Content-Type</span>: application/json
<span class="hljs-attribute">Content-Length</span>: 2

<span class="json"> {
<span class="hljs-attr">"error"</span>: <span class="hljs-literal">false</span>,
<span class="hljs-attr">"message"</span>: <span class="hljs-string">"Transaction Created Successfully"</span>,
<span class="hljs-attr">"transaction"</span>: {
    <span class="hljs-attr">"id"</span>: <span class="hljs-number">4</span>,
    <span class="hljs-attr">"reference"</span>: <span class="hljs-string">"lQF4w0WWOe7029424"</span>,
    <span class="hljs-attr">"code_sale_master"</span>: <span class="hljs-string">"doeeqwewqeqw"</span>,
    <span class="hljs-attr">"total_value"</span>: <span class="hljs-string">800</span>,
    <span class="hljs-attr">"total_paid"</span>: <span class="hljs-string">800</span>,
    <span class="hljs-attr">"date_sale"</span>: <span class="hljs-string">"Feb 14, 2022 5:45 AM"</span>
    <span class="hljs-attr">"last_update"</span>: <span class="hljs-string">"Feb 14, 2022 5:45 AM"</span>
    <span class="hljs-attr">"description"</span>: <span class="hljs-string">"Test Sales is a test"</span>
    }</span></code></pre>

    </code>
</pre>

</div>
</body>
</html>
