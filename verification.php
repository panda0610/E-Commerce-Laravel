<?php
// System : StoreGo Saas
// System Version : 3.6


function getPhpVersionInfo()
{
    $currentVersionFull = PHP_VERSION;
    preg_match("#^\d+(\.\d+)*#", $currentVersionFull, $filtered);
    $currentVersion = $filtered[0];

    return [
        'full' => $currentVersionFull,
        'version' => $currentVersion,
    ];
}

$minPhpVersion                                 = '7.2';
$arrPermissions                                = [];
$arrPermissions['storage/framework/']          = __DIR__ . "/storage/framework/";
$arrPermissions['storage/framework/cache/']    = __DIR__ . "/storage/framework/cache/";
$arrPermissions['storage/framework/sessions/'] = __DIR__ . "/storage/framework/sessions/";
$arrPermissions['storage/framework/views/']    = __DIR__ . "/storage/framework/views/";
$arrPermissions['storage/logs/']               = __DIR__ . "/storage/logs/";
$arrPermissions['bootstrap/cache/']            = __DIR__ . "/bootstrap/cache/";
$arrPermissions['public/']                     = __DIR__ . "/public/";
$arrPermissions['resources/lang/']             = __DIR__ . "/resources/lang/";
// $arrPermissions['.env']                        = __DIR__ . "/.env";
$arrPer = [];
$err    = 0;

foreach($arrPermissions as $key => $value)
{
    $permission   = ltrim(substr(sprintf('%o', fileperms($value)), -4), 0);
    $arrPer[$key] = ltrim($permission, 0);

    if($permission < '777')
    {
        $err = 1;
    }
}

// Check PHP Version
$allowed_version = version_compare(getPhpVersionInfo()['version'], $minPhpVersion, ">=");


if(!$allowed_version)
{
    $err = 1;
}

// If all directory has and .env file has permission then redirect to main File
if($err != 1)
{

    require_once __DIR__ . '/public/index.php';
    die;
}
// end
?>

<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Permissions | Installer</title>
    <link rel="icon" type="image/png" href="public/installer/img/favicon/favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="public/installer/img/favicon/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="public/installer/img/favicon/favicon-96x96.png" sizes="96x96">
    <link href="public/installer/css/style.min.css" rel="stylesheet">
</head>
<body data-gr-c-s-loaded="true">
<div class="master">
    <div class="box" style="width: 550px">
        <div class="header">
            <h1 class="header__title"><i class="fa fa-key fa-fw" aria-hidden="true"></i>
                Permissions
            </h1>
        </div>
        <div class="main">
            <p>Please set below directory and file permission to <b>777</b> and reload page.</p>
            <ul class="list">
                <li class="list__item list__title <?php if($allowed_version)
                {
                    echo 'success';
                }
                else
                {
                    echo 'error';
                } ?>">
                    <strong>Php
                        <small>(version <?php echo $minPhpVersion; ?> required)</small>
                    </strong>
                    <span class="float-right">
                        <?php if($allowed_version) { ?>
                            <i class="fa fa-fw fa-check-circle-o"></i><strong> <?php echo getPhpVersionInfo()['version']; ?></strong>
                        <?php } else { ?>
                            <i class="fa fa-fw fa-exclamation-circle"></i><strong> <?php echo getPhpVersionInfo()['version']; ?></strong>
                        <?php } ?>
                    </span>

                </li>
                <?php
                foreach($arrPer as $key => $val)
                { ?>

                    <li class="list__item list__item--permissions <?php if($val == '777')
                    {
                        echo 'success';
                    }
                    else
                    {
                        echo 'error';
                    } ?>">
                        <?php echo $key ?>
                        <?php if($val < '777')
                        {
                            echo "<small>(Required Permission is 777)</small>";
                        } ?>
                        <span>
                            <?php if($val == '777') { ?>
                                <i class="fa fa-fw fa-check-circle-o"></i>
                            <?php } else { ?>
                                <i class="fa fa-fw fa-exclamation-circle"></i>
                            <?php } ?>
                            <?php echo $val; ?>
                        </span>
                    </li>
                <?php } ?>
            </ul>
            <?php if($err != 1) { ?>
                <div class="buttons">
                    <a href="." class="button">
                        Install
                        <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
</body>
</html>


