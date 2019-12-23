<?php
$samlfile = '/var/simplesaml/lib/_autoload.php';
$landesverband = 0;
$user = "generic";


if (file_exists($samlfile) AND $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
    require_once($samlfile);
    $as = new SimpleSAML_Auth_Simple('default-sp');
    $as->requireAuth();
    $user =$samlattributes['urn:oid:0.9.2342.19200300.100.1.1'][0];

    require_once('inc/versionswitch.php');
}

if (file_exists('log/do.php')){
    require_once('log/do.php');
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8"/>
    <title>Sharepicgenerator</title>
    <link rel="stylesheet" type="text/css" href="./assets/css/styles.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
       
        <div class="col-12 col-lg-9">
            <div class="col-12 text-center pt-4 pb-3">
                <h1 class="text-uppercase h6">Sharepicgenerator</h1>
            </div>
            <div class="col-12">
                <div id="canvas"></div>
            </div>
            <div class="col-12 mt-3 mb-3">
                <div id="message" class="bg-danger text-white p-4" style="display:none"></div>
            </div>

            <div class="col-12 text-center mb-5">
                <button class="btn btn-secondary btn-lg" id="download">
                    <i class="fas fa-download"></i> Herunterladen
                </button>
            </div>


        </div>
        <div class="col-12 col-lg-3 mt-3 mb-5 cockpit">
            <?php require_once('inc/cockpit.php'); ?>
        </div>
    </div>
</div>

<footer class="row bg-primary p-2 text-white">
    <div class="col-12 col-lg-6">
        <?php
        $countSharepicsFile = 'log/countsharepics.txt';
        if(!file_exists($countSharepicsFile) OR time() - filemtime($countSharepicsFile) > 60 * 60){
            $countDownloads = 0;
            $lines = file('log/log.txt');
            foreach( $lines AS $line ){
                list( $time, $payload, $action ) = explode("\t", trim($line) );
                if($action == 'download') {
                    $countDownloads++;
                }
            }
            file_put_contents( $countSharepicsFile, (string) $countDownloads );
        }
        printf("%s erstellte Sharepics |", number_format( file_get_contents( $countSharepicsFile ), 0, ',', '.'));
        ?>
          <a href="bayern">
            Kommunalwahl Bayern</a>
    </div>

    <div class="col-12 col-lg-6 text-lg-right">
        <a href="https://chatbegruenung.de/channel/sharepicgenerator" target="_blank">Feedback im Chat-Channel</a> |
        <a href="https://github.com/codeispoetry/sharepicgenerator" target="_blank">Quellcode auf github.com</a> |
        Programmiert mit <i class="fas fa-heart text-yellow"></i> von Tom Rose.
    </div>
</footer>


<div class="overlays">
    <?php
        require_once('inc/overlays/pixabay.php');
        require_once('inc/overlays/icons.php');
        require_once('inc/overlays/waiting.php');
        require_once('inc/overlays/templates.php');

    ?>
</div>

<script>
    <?php echo 'var config ='; @readfile('config.json') || readfile('config-sample.json'); echo ';'?>
    <?php printf('config.landesverband = %d;', $landesverband); ?>
    <?php printf('config.user="%s";', $user); ?>
    if (/Edge/.test(navigator.userAgent)) {
        alert('Falls der Sharepicgenerator hier nicht funktioniert, probiere bitte einen anderen Browser, z.B. Firefox.');
    }
</script>
<script src="./vendor/jquery-3.4.1.min.js"></script>
<script src="./vendor/svg.min.js"></script>
<script src="./vendor/svg.draggable.min.js"></script>
<script src="./assets/js/main.min.js"></script>
</body>
</html>
<?php

deleteOldFiles();
function deleteOldFiles()
{
    $files = glob("tmp/shpic*\.{png,jpg,svg}", GLOB_BRACE );
    $now = time();

    foreach($files AS $file){
        if (is_file($file) AND $now - filemtime($file) >= 60 * 60 * 24 * 7){
            unlink($file);
        }
    }
}
?>