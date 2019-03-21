<?php
require_once 'vendor/autoload.php';
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=".getenv('ACCOUNT_NAME').";AccountKey=".getenv('ACCOUNT_KEY');
$blobClient = BlobRestProxy::createBlobService($connectionString);


function removeExt($path)
{
    $basename = basename($path);
    return strpos($basename, '.') === false ? $path : substr($path, 0, - strlen($basename) + strlen(explode('.', $basename)[0]));
}

if (!isset($_GET["Cleanup"])) {
	// $fileToUpload = "donuts.png";
	$fileToUpload = basename($_FILES["fileToUpload"]["name"]) ;
	$fileToUplloadWithoutExtension = removeExt($_FILES["fileToUpload"]["name"]);
	// echo "Name File : ".$fileToUplloadWithoutExtension."<br/>";
	$uploadOk = 1;
	$target_dir = "files/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$urlfile = "files/".basename( $_FILES["fileToUpload"]["name"]);

	if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
		// echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
		// echo $urlfile ;
	} else {
		$uploadOk = 0;
		echo "Sorry, there was an error uploading your file.";
	}

	// $myfile = fopen($fileToUpload, "r") or die("Unable to open file!");
	// echo fread($myfile,filesize($fileToUpload));
	// echo $myfile;
	// fclose($myfile);
	if ($uploadOk==1) {
		$createContainerOptions = new CreateContainerOptions();
		$createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);

		$createContainerOptions->addMetaData("key1", "value1");
		$createContainerOptions->addMetaData("key2", "value2");

		$containerName = "blockblobs".generateRandomString();

		try {
			// Create container.
			$blobClient->createContainer($containerName, $createContainerOptions);
			// $myfile = fopen($fileToUpload, "r") or die("Unable to open file!");
			// fclose($myfile);
			
			# Upload file as a block blob
			// echo "Uploading BlockBlob: ".PHP_EOL;
			// echo $fileToUpload;
			// echo "<br />";
			
			$content = fopen($urlfile, "r");

			//Upload blob
			$blobClient->createBlockBlob($containerName, $fileToUpload, $content);

			// List blobs.
			$listBlobsOptions = new ListBlobsOptions();
			$listBlobsOptions->setPrefix($fileToUplloadWithoutExtension);
			echo "<br/>";
			
			echo "These are the blobs present in the container : ";

			do{
				$result = $blobClient->listBlobs($containerName, $listBlobsOptions);
				foreach ($result->getBlobs() as $blob)
				{
					echo "<b>".$blob->getName()."</b> <br/>";
					echo "Url name : <b>".$blob->getUrl()."</b> <br/>";
				}			
				$listBlobsOptions->setContinuationToken($result->getContinuationToken());
			} while($result->getContinuationToken());
			
			echo "Name file : <b>".$fileToUplloadWithoutExtension."</b><br/>";
			echo "<br />";

			// Get blob.
			// echo "This is the content of the blob uploaded: ";
			// $blob = $blobClient->getBlob($containerName, $fileToUpload);
			// fpassthru($blob->getContentStream());
			// echo "<br />";
		}
		catch(ServiceException $e){
			// Handle exception based on error codes and messages.
			// Error codes and messages are here:
			// http://msdn.microsoft.com/library/azure/dd179439.aspx
			$code = $e->getCode();
			$error_message = $e->getMessage();
			echo $code.": ".$error_message."<br />";
		}
		catch(InvalidArgumentTypeException $e){
			// Handle exception based on error codes and messages.
			// Error codes and messages are here:
			// http://msdn.microsoft.com/library/azure/dd179439.aspx
			$code = $e->getCode();
			$error_message = $e->getMessage();
			echo $code.": ".$error_message."<br />";
		}	

	}else{
		echo "Can't upload image";
	}
}else{
	try{
        // Delete container.
        echo "Deleting Container".PHP_EOL;
        echo "<b>".$_GET["containerName"].PHP_EOL."</b>";
        echo "<br />";
        $blobClient->deleteContainer($_GET["containerName"]);
    }
    catch(ServiceException $e){
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179439.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
}
?>

<form method="post" action="uploadsystem.php?Cleanup&containerName=<?php echo $containerName; ?>">
    <button type="submit">Press to clean up all resources created by this sample</button>
</form>
<a href="./"><button>Back</button></a>
