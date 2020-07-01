<?php

	include "config.php";

	/*
		@Required 
		name
		email
		contact
		to_country
		to_address
		img (Prescription Image)

	
		@Optional
		from_country & from_address
		prescription_text

	*/


	if(isset(
		$_POST['name'],
		$_POST['email'],
		$_POST['contact'],
		$_POST['to_country'],
		$_POST['to_address']
	)){
		$upload = new \Delight\FileUpload\FileUpload();
		$upload->withTargetDirectory('uploads');
		$upload->withAllowedExtensions([ 'jpeg', 'jpg', 'png', 'pdf' ]);
		$upload->from('img');
		try {
		    $uploadedFile = $upload->save();
		    $filename = $uploadedFile->getFilenameWithExtension();

			$mail->Subject = 'New Order Request at mediship.in';


			$params = Array();

			if(isset($_POST['from_country'],$_POST['from_address'])){
				$params['Method'] = '<h3>Procure & Ship</h3>';
			}
			else{
				$params['Method'] = '<h3>Ship Your Medicine</h3>';				
			}

			$params['Name'] = $_POST['name'];
			$params['Email'] = $_POST['email'];
			$params['Contact'] = $_POST['contact'];

			if(isset($_POST['from_country'],$_POST['from_address'])){
				$params['From Country'] = $_POST['from_country'];
				$params['From Address'] = $_POST['from_address'];				
			}

			$params['Target Country'] = $_POST['to_country'];
			$params['Target Address'] = $_POST['to_address'];

			$params['Uploaded Prescription'] = "<a href='".SITE_URL.'/api/uploads/'.$filename."'>Download</a>";

			if(isset($_POST['prescription_text'])){
				$params['Prescription Text'] = $_POST['prescription_text'];
			}			
    		

		    $mail->addAttachment('uploads/'.$filename,'Prescription.jpg');    // Optional name

    		$mail->Body = generateMailBody($params);



    		try{	
			    $mail->send();
    			die(json_encode(["errors"=>0,"message"=>"Order submitted succesfully"]));
    		}
    		catch(Exception $e){

				die(json_encode(["errors"=>1,"message"=>"Could't send mail","error_detail"=>$e->getMessage()]));	
}



		}
		catch (\Delight\FileUpload\Throwable\InputNotFoundException $e) {
			die(json_encode(["errors"=>1,"message"=>"Prescription image not selected"]));
		}
		catch (\Delight\FileUpload\Throwable\InvalidFilenameException $e) {
			die(json_encode(["errors"=>1,"message"=>"Invalid File Name"]));
		}
		catch (\Delight\FileUpload\Throwable\InvalidExtensionException $e) {
			die(json_encode(["errors"=>1,"message"=>"Invalid File Extension"]));
		}
		catch (\Delight\FileUpload\Throwable\FileTooLargeException $e) {
			die(json_encode(["errors"=>1,"message"=>"File is too large"]));
		}
		catch(Exception $e){
			print_r($e);
			die(json_encode(["errors"=>1,"message"=>"Some error occuredd"]));
		}
	}
	else{
		print_r($_POST);
		die(json_encode(["errors"=>1,"message"=>"Some required fields are not sent"]));
	}
	
?>