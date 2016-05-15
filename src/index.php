<?php

require __DIR__ . '/../vendor/autoload.php';

$config = new SocialBeacon\SocialBeaconConfig();

$client_access_flow = new SocialBeacon\GoogleClientFlowController();
$google_client = $client_access_flow->cliFlow();
$access_token  = $client_access_flow->getAccessTokenFromClient($google_client);

$serviceRequest = new Google\Spreadsheet\DefaultServiceRequest($access_token);
Google\Spreadsheet\ServiceRequestFactory::setInstance($serviceRequest);

$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
$master_spreadsheet = $config->getMasterSpreadsheet();

$ss = $spreadsheetService->getResourceById("Google\\Spreadsheet\\Spreadsheet", $master_spreadsheet);

$worksheetFeed = $ss->getWorksheetFeed();
$worksheet = $worksheetFeed->getByTitle('Sheet1');

$listFeed = $worksheet->getListFeed();

print "\n";
foreach ($listFeed->getEntries() as $entry) {
    $values = $entry->getValues();
    print implode("\t", $values);
    print "\n";
}

?>
