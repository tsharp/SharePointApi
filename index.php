<html>
<body>
<?php
    error_reporting(E_ALL);
	ini_set("error_log", "c:/temp/php-error.log");
	
    require_once('SharePoint\SharePointApi.php');
    require_once('SharePoint\Service\QueryObjectService.php');
    require_once('SharePoint\Service\ListService.php');
    require_once('SharePoint\Auth\SoapClientAuth.php');
    require_once('SharePoint\Auth\SharePointOnlineAuth.php');
    require_once('SharePoint\Auth\StreamWrapperHttpAuth.php');

    echo 'Before SharePoint ...';
    $sp = new \SharePoint\SharePointApi('', '','', 'NTLM');
    echo 'After SharePoint';
    
    $results = $sp->read('Applicants');
    
    // $file = $sp->readFileMeta('Applicants', '', 'Test Document.pdf');
    
    
    $queried = $sp->query('Applicants')->where('ServerUrl', '=', '/Applicants/Test Folder/MARBIBM.TIF')->get();
    $queried2 = $sp->query('Applicants')->where('EncodedAbsUrl', '=', '')->get();
    
    
    foreach ($results as &$doc) {
    	echo '<h2>' . $doc['fileref'] . '</h2>';
    	echo '<div>';
    	// ITERATE THROUGH VALUES
    	foreach($doc as $key => $value) {
    		echo $key . ' = ' . $value;
    		echo '<br />';
    	}
    	echo '</div>';
	}
    
?>
</body>
</html>