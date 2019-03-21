<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Dicoding</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
</head>
<body>
    <h1>Final Project Class Azure</h1>
    <form action="uploadsystem.php?" method="post" enctype="multipart/form-data">
       <h5>Select file and upload to Blob Storage</h5>
        <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="submit" value="Upload Image" name="submit">
    </form>
    <hr>
    <h5>Enter the URL to an image, then click the button. </h5>
    Image to analyze:
    <input type="text" name="inputImage" id="inputImage"
        value="http://upload.wikimedia.org/wikipedia/commons/3/3c/Shaki_waterfall.jpg" />
    <button onclick="processImage()">Analyze image</button>
    <br>
    Name image from response vision : <input type="text" id="idresponse" disabled >
    <br>
    <div id="wrapper" style="width:1020px; display:table;">
        <div id="imageDiv" style="width:420px; display:table-cell;">
            Source image:
            <br><br>
            <img id="sourceImage" width="400" height="250px" src="files/background.png"/>
        </div>
        <div id="jsonOutput" style="width:600px; display:table-cell;">
            Response:
            <br><br>
            <textarea id="responseTextArea" class="UIInput"
                    style="width:420px; height:250px;"></textarea>
        </div>
       
    </div>

    <script type="text/javascript">
        function processImage() {
            document.getElementById('idresponse').value = "";
            var subscriptionKey = "db429da0b80a4760959c428f7af9fb96";
            var uriBase ="https://southeastasia.api.cognitive.microsoft.com/vision/v2.0/analyze";
            var params = {
                "visualFeatures": "Categories,Description,Color",
                "details": "",
                "language": "en",
            };
    
            // Display the image.
            var sourceImageUrl = document.getElementById("inputImage").value;
            document.querySelector("#sourceImage").src = sourceImageUrl;
    
            // Make the REST API call.
            $.ajax({
                url: uriBase + "?" + $.param(params),
                // Request headers.
                beforeSend: function(xhrObj){
                    xhrObj.setRequestHeader("Content-Type","application/json");
                    xhrObj.setRequestHeader("Ocp-Apim-Subscription-Key", subscriptionKey);
                },
                type: "POST",
                data: '{"url": ' + '"' + sourceImageUrl + '"}',
            }).done(function(data) {
                // Show formatted JSON on webpage.
                $("#responseTextArea").val(JSON.stringify(data.categories[0], null, 2));
                getNameImageVision(data);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                // Display error message.
                var errorString = (errorThrown === "") ? "Error. " :
                errorThrown + " (" + jqXHR.status + "): ";
                errorString += (jqXHR.responseText === "") ? "" :
                jQuery.parseJSON(jqXHR.responseText).message;
                alert(errorString);
            });
        };

        function getNameImageVision(obj) {  
            document.getElementById('idresponse').value = obj.categories[0].name;
        }
    </script>
</body>
</html>



