<?php
    // echo "Data";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Page Title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <h1>Hello World</h1>
    <button onclick="sayhi()">Hi</button>
    <br>
    <form action="uploadsystem.php" method="post" enctype="multipart/form-data">
        Select image to upload:
        <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="submit" value="Upload Image" name="submit">
    </form>

    <script>
        function sayhi() {  
            alert('Hi !');
        }
    </script>
</body>
</html>



