<?php
include "../conn.php";

// if (isset($_POST['submit'])) {
//     if ($_FILES['excel_file']['error'] == 0) {
//         // Open the uploaded file
//         $file = fopen($_FILES['excel_file']['tmp_name'], 'r');

//         // Skip the first row (assumes it's the header row)
//         fgetcsv($file);

//         $find = mysqli_query($conn, "SELECT * FROM stud_info");
//         $finded = mysqli_fetch_assoc($find);
//         $id_number = $finded['ID_NUMBER'];

//         // Read each row and insert into the database
//         while (($row = fgetcsv($file, 1000, ",")) !== false) {
//             // Modify these to match your table columns
//             $ID_NUMBER = $conn->real_escape_string($row[0]);
//             $FIRSTNAME = $conn->real_escape_string($row[1]);
//             $LASTNAME = $conn->real_escape_string($row[2]);
//             $MIDDLENAME = $conn->real_escape_string($row[3]);
//             $CNUMBER = $conn->real_escape_string($row[4]);

//             if($ID_NUMBER)

//             // Insert query
//             $sql = "INSERT INTO stud_info (STUD_ID, FIRSTNAME, LASTNAME, MIDDLENAME, CNUMBER, STATUS)
//                  VALUES ('$ID_NUMBER', '$FIRSTNAME', '$LASTNAME', '$MIDDLENAME', '$CNUMBER','unregistered')";

//             // Execute the query
//             if (!$conn->query($sql)) {
//                 echo "Error: " . $conn->error;
//             }
//         }

//         fclose($file);
//         echo "<script>alert('Data imported successfully!')
//                 window.location.href='students.php'</script>";
//     } else {
//         echo "Error uploading file.";
//     }
// }

if (isset($_POST['submit'])) {
    if ($_FILES['excel_file']['error'] == 0) {
        // Open the uploaded file
        $file = fopen($_FILES['excel_file']['tmp_name'], 'r');

        // Skip the first row (assumes it's the header row)
        fgetcsv($file);

        // Fetch all existing ID_NUMBER values from the database
        $existingIds = [];
        $find = mysqli_query($conn, "SELECT ID_NUMBER FROM stud_info");
        while ($row = mysqli_fetch_assoc($find)) {
            $existingIds[] = $row['ID_NUMBER'];
        }

        // Create an array to track duplicates in the file
        $fileIds = [];

        // Read each row from the Excel file
        while (($row = fgetcsv($file, 1000, ",")) !== false) {
            // Extract columns
            $ID_NUMBER = $conn->real_escape_string($row[0]);
            $FIRSTNAME = $conn->real_escape_string($row[1]);
            $LASTNAME = $conn->real_escape_string($row[2]);
            $MIDDLENAME = $conn->real_escape_string($row[3]);
            $CNUMBER = $conn->real_escape_string($row[4]);

            // Check for duplicates within the file
            if (in_array($ID_NUMBER, $fileIds)) {
                echo "<script>alert('Duplicate ID_NUMBER in file: $ID_NUMBER'); window.location.href='students.php';</script>";
                // echo "Duplicate ID_NUMBER in file: $ID_NUMBER<br>";
                continue; // Skip this row
            }

            // Check if the ID_NUMBER already exists in the database
            if (in_array($ID_NUMBER, $existingIds)) {
                echo "<script>alert('Skipping existing ID_NUMBER in database: $ID_NUMBER'); window.location.href='students.php';</script>";
                // echo "Skipping existing ID_NUMBER in database: $ID_NUMBER<br>";
                continue; // Skip this row
            }

            // Add the ID_NUMBER to the fileIds array to track it
            $fileIds[] = $ID_NUMBER;

            // Insert query for unique and new ID_NUMBER
            $sql = "INSERT INTO stud_info (ID_NUMBER, FIRSTNAME, LASTNAME, MIDDLENAME, CNUMBER, STATUS)
                    VALUES ('$ID_NUMBER', '$FIRSTNAME', '$LASTNAME', '$MIDDLENAME', '$CNUMBER', 'unregistered')";

            if (!$conn->query($sql)) {
                echo "Error inserting ID_NUMBER $ID_NUMBER: " . $conn->error . "<br>";
            } else {
                echo "Added new ID_NUMBER: $ID_NUMBER<br>";
            }
        }

        fclose($file);
        echo "<script>alert('Data import completed!'); window.location.href='students.php';</script>";
    } else {
        echo "Error uploading file.";
    }
}
