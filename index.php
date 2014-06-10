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

    $sp = new \SharePoint\SharePointApi('http://sharepointsite', 'domain\\usernname','password', 'NTLM');
    
    $doc = $sp->getItemByAbsUrl('Applicants', 'http://sp2010dev.webfortis.com/Applicants/Test%20Document.pdf', 
    	array( 
    		'DOCHASH',
    		'include_x0020_in_x0020_bundle',
    		'EncodedAbsUrl'
    	));   
    
    echo '<h2>' . $doc['fileref'] . '</h2>';
    // ITERATE THROUGH VALUES
    echo '<table>';
    echo '<th><tr>';
    echo '<td>Field</td>';
    echo '<td>Value</td>';
    echo '</tr></th>';
    foreach($doc as $key => $value) {
    	echo '<tr>';
    	echo '<td>' . $key . '</td>';
    	echo '<td>' . $value . '</td>';
    	echo '</tr>';
    }
    echo '</table>';
    
?>
</body>
</html>