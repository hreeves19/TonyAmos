<?php
/**
 * Created by PhpStorm.
 * User: hreeves
 * Date: 9/13/2018
 * Time: 12:00 PM
 */
?>

<!doctype html>
<!--suppress ALL -->
<html lang="en">
<!-- HTML HEADER -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Tony Amos Collection</title>

    <!-- Importing JS files -->
    <script type="text/javascript" src="../../TonyAmos/Scripts/tonyAmosManager.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>

    <!-- Importing CSS files -->
    <link rel="stylesheet" type="text/css" href="../../TonyAmos/Master/CSS/master.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
</head>
<!-- END HEADER -->
<!-- HTML BODY -->
<body>
<div class="flex-container">
    <div id="title">
        <h1>Tony Amos San Jose Observations</h1>
    </div>
    <div id="divMenu" style="margin: 0px;">
        <?php include '../../TonyAmos/Master/navbar.php'; ?>
    </div>
</div>
<?php include '../../TonyAmos/Master/footer.php'; ?>
</body>
</html>
