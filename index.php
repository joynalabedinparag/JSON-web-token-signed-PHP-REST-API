<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/constants.php';
require __DIR__ . '/library/JWT.php';

$settings = require __DIR__ . '/src/settings.php';
$app = new \Slim\App($settings);

/**
 * API for creating a new json web token for the payload.
 *
 * @param `api_access_key` is the api secret key
 * @param `bundle_version` is app bundle version 
 * @param `status` is app status
 *
 * @return string   A json object containing a signed JWT.
*/
$app->get('/createJWT', function (Request $request, Response $response) use ($app) {

     $allGetVars = $request->getQueryParams();
     $bundle_version = $allGetVars['bundle_version'];
     $status = $allGetVars['status'];
     $api_access_key = $allGetVars['api_access_key'];

     if ($api_access_key == API_ACCESS_KEY) {

          $jwt = new JWT;
          $api_secret_key = API_SECRET_KEY;

          $payload = array (
              "iss" => "Phoenix IT",
              "sub" => "Modify Bundle Version Status",
              "bundle_version" => $bundle_version,
              "status" => $status,
          );

          $token = $jwt::encode($payload, $api_secret_key, $algo = 'HS256');

          $msg = ($token) ? $token : FAILURE_MSG;
          $response_status = ($token) ? SUCCESS_STATUS : FAILURE_STATUS;

     } else {
          $msg = INVALID_API_ACCESS_KEY;
          $response_status = INVALID_API_ACCESS_KEY_STATUS ;
     }
     /* Response */
     $response_status = (int)$response_status;
     $data = array('status'=>$response_status,'data' => $msg);
     $newResponse = $response->withStatus($response_status);
     $newResponse = $response->withHeader('Content-type', 'application/json');
     $newResponse = $response->withAddedHeader('Allow', 'PUT');
     $newResponse = $response->withJson($data, $response_status);
     return $newResponse;
});


/**
 * API to Modify Bundle Version .
 *
 * @param `api_access_key` is the api secret key
 * @param `token` is a jwt.
 *
 * @return string A json object.
*/
$app->put('/ModifyBundleVersion', function (Request $request, Response $response) use ($app) {

     $allGetVars = $request->getQueryParams();
     $token = $allGetVars['token'];

     $api_access_key = $allGetVars['api_access_key'];

     if ($api_access_key == API_ACCESS_KEY) {

          $jwt = new JWT;
          $api_secret_key = API_SECRET_KEY;
          $payload = $jwt::decode($token, $api_secret_key, $verify = true);

          $bundle_version = $payload->bundle_version;
          $status = $payload->status;
          $con = $this->get('settings')['db']['con'];
          $sql = "update sm_project set status='".$status."' where project_number='".$bundle_version."'";
          $result = $con->query($sql);

          $msg = ($result)? SUCCESS_MSG : FAILURE_MSG;
          $response_status = ($result) ? SUCCESS_STATUS : FAILURE_STATUS;

     } else {
          $msg = INVALID_API_ACCESS_KEY;
          $response_status = INVALID_API_ACCESS_KEY_STATUS ;
     }
     /* Response */
     $response_status = (int)$response_status;
     $data = array('status'=>$response_status,'data' => $msg);
     $newResponse = $response->withStatus($response_status);
     $newResponse = $response->withHeader('Content-type', 'application/json');
     $newResponse = $response->withAddedHeader('Allow', 'PUT');
     $newResponse = $response->withJson($data, $response_status);
     return $newResponse;
});

$app->run();