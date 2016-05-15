<?php

namespace SocialBeacon;

class GoogleClientFlowController {
  private static $application_name     = "The Social Beacon";
  private static $cli_credentials_path = '~/.credentials/drive-php-quickstart.json';
  private static $cli_secret_path      = __DIR__ . '/../auth/cli_client_secret.json';
  private static $web_secret_path      = __DIR__ . '/../auth/web_client_secret.json';

  /**
   * Returns an authorized API client using the Web server flow
   * @return Google_Client the authorized client object
   */
  public function WebServerFlow() {}

  /**
   * Returns an authorized API client using the CLI flow
   * @return Google_Client the authorized client object
   */
  public function cliFlow() {
    $client = new \Google_Client();
    $client->setApplicationName(static::$application_name);
    $client->setScopes($this->getScopes());
    $client->setAuthConfigFile(static::$cli_secret_path);
    $client->setAccessType('offline');

    // Load previously authorized credentials from a file.
    $credentialsPath = $this->expandHomeDirectory(static::$cli_credentials_path);
    if (file_exists($credentialsPath)) {
      $accessToken = file_get_contents($credentialsPath);
    } else {
      // Request authorization from the user.
      $authUrl = $client->createAuthUrl();
      printf("Open the following link in your browser:\n%s\n", $authUrl);
      print 'Enter verification code: ';
      $authCode = trim(fgets(STDIN));

      // Exchange authorization code for an access token.
      $accessToken = $client->authenticate($authCode);

      // Store the credentials to disk.
      if(!file_exists(dirname($credentialsPath))) {
        mkdir(dirname($credentialsPath), 0700, true);
      }
      file_put_contents($credentialsPath, $accessToken);
      printf("Credentials saved to %s\n", $credentialsPath);
    }
    $client->setAccessToken($accessToken);

    // Refresh the token if it's expired.
    if ($client->isAccessTokenExpired()) {
      $client->refreshToken($client->getRefreshToken());
      file_put_contents($credentialsPath, $client->getAccessToken());
    }
    return $client;
  }

  public function getAccessTokenFromClient($client) {
    return json_decode($client->getAccessToken())->access_token;
  }

  /**
   * Expands the home directory alias '~' to the full path.
   * @param string $path the path to expand.
   * @return string the expanded path.
   */
  private function expandHomeDirectory($path) {
    $homeDirectory = getenv('HOME');
    if (empty($homeDirectory)) {
      $homeDirectory = getenv("HOMEDRIVE") . getenv("HOMEPATH");
    }
    return str_replace('~', realpath($homeDirectory), $path);
  }

  private function getScopes() {
    return implode(' ', array(
      \Google_Service_Drive::DRIVE_METADATA_READONLY,
      "https://spreadsheets.google.com/feeds")
    );
  }
}

?>
