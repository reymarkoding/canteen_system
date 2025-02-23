<?php

include "../conn.php";
session_start();

$ID_NUMBER = $_SESSION['ID_NUMBER'];
$fullName = $_SESSION['fullName'];
$balance = $_SESSION['balance'];

if (!isset($_SESSION['student_status']) || $_SESSION['student_status'] !== 'login') {
    header("Location: student_login.php");
    exit();
}

$sql = "SELECT * FROM stud_info WHERE ID_NUMBER = '$ID_NUMBER'";
$result = mysqli_query($conn, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $STUD_ID = $row['STUD_ID'];
    $stud_name = $row['LASTNAME'] . ", " . $row['FIRSTNAME'] . " " . $row['MIDDLENAME'][0] . ".";
    $ID_NUMBER = $row['ID_NUMBER'];
    $bal = $row['BALANCE'];
    $dp = $row['PROFILE_IMAGE'];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="./css/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/bootstrap.css">
    <link rel="stylesheet" href="./css/student_profile.css">
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/fontawesome.min.css">
    <script src="./css/css/js/bootstrap.bundle.js"></script>
</head>

<body>
    <div class="main">
        <div class="canteen_id">

            <div class="header p-1">
                <?= "<img src='profile/newDJEMCLOGO.png' style='width:60px;height:60px;border-radius: 50%;'>"; ?>
                <span>D</span>
                <span>J</span>
                <span>E</span>
                <span>M</span>
                <span>C</span>
            </div>

            <div class="body">
                <div class="header">
                    <span>Don Jose Ecleo Memorial College</span>
                </div>
                <div id="qrcode" class="qrcode">

                </div>
                <div class="prof_pic">
                    <?= "<img src='$dp' class='p-2' style='width:50%;height:50%;border: 1px solid #347928;'>"; ?>
                    <?= "<span style='font-weight: bold;'>" . $fullName . "</span>"; ?>
                    <?= "<span style='font-weight: bold;opacity: 50%;'>" . $ID_NUMBER . "</span>"; ?>
                </div>
            </div>

        </div><br>
        <div class="btn-wrapper mt-2">
            <a href="student_dashboard.php" class="btn btn-danger">Back</a>
            <!-- <button id="save-btn" class="btn btn-primary" onclick="saveAsImage()">Save QR Code</button>	 -->
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.0.0-rc.7/dist/html2canvas.min.js"></script>

    <script src="js/qrcode.min.js"></script>
    <script>
        function generateQRCode() {
            var student_data = {
                stud_id: "<?php echo $STUD_ID; ?>",
                name: "<?php echo $stud_name; ?>",
                id_number: "<?php echo $ID_NUMBER; ?>",
                balance: "<?php echo $bal; ?>"
            };

            var qrText = JSON.stringify(student_data);

            var qrcode = new QRCode(document.getElementById("qrcode"), {
                text: qrText,
                width: 200,
                height: 200
            });
        }
        generateQRCode();


        function saveAsImage() {
            var canteenIdDiv = document.querySelector('.canteen_id');

            // Ensure the entire element is visible
            canteenIdDiv.scrollIntoView();

            // Capture the element with a higher scale for better resolution
            html2canvas(canteenIdDiv, {
                scale: 2, // Increase scale for better quality
                useCORS: true // Enable CORS if needed for external resources
            }).then(function(canvas) {
                var imgData = canvas.toDataURL("image/png");
                var link = document.createElement('a');
                link.href = imgData;
                link.download = "<?php echo $fullName; ?>.png";
                link.click();
            });
        }

        const clickMe = document.querySelector('#clickMe');
        const click = document.querySelector('#click');
        const menuContainer = document.querySelector('.header_menu');

        clickMe.addEventListener('click', () => {
            if (click.classList.contains('fa-bars')) {
                click.classList.remove('fa-bars');
                click.classList.add('fa-close');

                // menu
                menuContainer.classList.add('show');
                menuContainer.classList.remove('hide');
            } else {
                click.classList.add('fa-bars');
                click.classList.remove('fa-close');

                // menu
                menuContainer.classList.remove('show');
                menuContainer.classList.add('hide');
            }
        })
    </script>
    <script>

    </script>

</body>

</html>