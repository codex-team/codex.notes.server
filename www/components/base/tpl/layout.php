<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="language" content="en-us" />
    <title><?= $title ?? 'CodeX Notes' ?></title>
    <meta property="og:title" content="<?= $title ?? 'CodeX Notes' ?>" />
    <meta property="og:site_name" content="CodeX Notes" />

    <link rel="stylesheet" href="/static/css/codex.css?v=<?= filemtime('static/css/codex.css')?>">

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-112240462-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-112240462-1');
    </script>

</head>
<body>

    <?= $content ?>

</body>
</html>
