<?php
$err = false;

function customAPICall($url, $method = "GET", $fields = ""){

    $curl = curl_init();
        
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        // CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 300,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_POSTFIELDS => $fields,
        CURLOPT_HTTPHEADER => array(
            "content-type: application/json"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    $response = json_decode($response);
    return $response;
}

function getPost()
{
    if(!empty($_POST))
    {
        // when using application/x-www-form-urlencoded or multipart/form-data as the HTTP Content-Type in the request
        // NOTE: if this is the case and $_POST is empty, check the variables_order in php.ini! - it must contain the letter P
        return $_POST;
    }

    // when using application/json as the HTTP Content-Type in the request 
    $post = json_decode(file_get_contents('php://input'), true);
    if(json_last_error() == JSON_ERROR_NONE)
    {
        return $post;
    }

    return [];
}
if (isset($_GET['type'])) {
    $callType = $_GET['type'];

    if ($callType == 'email') {

        if (isset(getPost()["lead"])) {

            $lead = json_encode(getPost()["lead"]);
            $lead = json_decode($lead);
            $email = $lead->Email;
        
            $dataToCollect = new stdClass();
            $dataToCollect->Email = $email;
            $dataToCollect->First_Name = isset($lead->First_Name) ? $lead->First_Name : '';
            $dataToCollect->Last_Name = isset($lead->Last_Name) ? $lead->Last_Name : '';
            $dataToCollect->Company = isset($lead->Company) ? $lead->Company : '';
            $dataToCollect->Job_Title = isset($lead->Job_Title) ? $lead->Job_Title : '';
            // $dataToCollect->Phone_Number = isset($lead->Phone_Number) ? $lead->Phone_Number : '';
            $dataToCollect->Industry = isset($lead->Industry) ? $lead->Industry : '';
            $dataToCollect->Size = isset($lead->Size) ? $lead->Size : '';
            $dataToCollect->Country = isset($lead->Country) ? $lead->Country : '';
        
            $response = customAPICall('https://api.cognism.com/api/prospector/people/email/'.$email.'?api_key=API-TOKEN','GET',"{}");
            

            // $dataToCollect = $response;
            if (isset($response->hit)) {

                if($dataToCollect->First_Name == ''){
                    $dataToCollect->First_Name = isset($response->hit->first) ? $response->hit->first : '';
                }
                if($dataToCollect->Last_Name == '' || $dataToCollect->Last_Name == 'Undefined' || $dataToCollect->Last_Name == 'undefined'){
                    $dataToCollect->Last_Name = isset($response->hit->last) ? $response->hit->last : $dataToCollect->Last_Name;
                }
                if($dataToCollect->Company == '' || $dataToCollect->Company == 'your company'){
                    $dataToCollect->Company = isset($response->hit->com[0]) ? isset($response->hit->com[0]->name) ? $response->hit->com[0]->name : $dataToCollect->Company : $dataToCollect->Company;
                }
                if($dataToCollect->Job_Title == ''){
                    $dataToCollect->Job_Title = isset($response->hit->com[0]) ? isset($response->hit->com[0]->title) ? $response->hit->com[0]->title : '' : '';
                }
                if($dataToCollect->Industry == ''){
                    $dataToCollect->Industry = isset($response->hit->com[0]) ? isset($response->hit->com[0]->industry[0]) ? $response->hit->com[0]->industry[0] : '' : '';
                }
                if($dataToCollect->Size == ''){
                    $dataToCollect->Size = isset($response->hit->com[0]) ? isset($response->hit->com[0]->size_from) ? $response->hit->com[0]->size_from : '' : '';
                    $dataToCollect->Size .= isset($response->hit->com[0]) ? isset($response->hit->com[0]->size_to) ? ' - '.$response->hit->com[0]->size_to : '' : '';
                }
                if($dataToCollect->Country == ''){
                    $dataToCollect->Country = isset($response->hit->com[0]) ? isset($response->hit->com[0]->loc->country) ? $response->hit->com[0]->loc->country : '' : '';
                }

            }
            else{
                $error = new stdClass();
                $error->error=true;
                $error->msg='No information about that lead.';
                echo json_encode($error);
                exit;
            }

            if ($err) {
                $error = new stdClass();
                $error->error=true;
                $error->msg='cURL Error #: '. $err;
                echo json_encode($error);
            } 
            else {
                echo json_encode($dataToCollect);
            }
        
        }
        else{
            $error = new stdClass();
            $error->error=true;
            $error->msg='Expected token "email" not found.';
            echo json_encode($error);
        }

    }

    else if ($callType == 'domain') {

        if (isset(getPost()["lead"])) {

            $lead = json_encode(getPost()["lead"]);
            $lead = json_decode($lead);

            $company_name = $lead->domain;
            
        
            $dataToCollect = new stdClass();

            $dataToCollect->Company_name = $company_name;
            $dataToCollect->Domain = isset($lead->domain) ? $lead->domain : '';
            $dataToCollect->Industry = '';
            $dataToCollect->Size = '';
            $dataToCollect->Domain = '';
            $dataToCollect->Country = isset($lead->country) ? $lead->country : '';
            $dataToCollect->Region = isset($lead->region) ? $lead->region : '';
            $dataToCollect->Description = '';
            $dataToCollect->Founded_year = '';
            $dataToCollect->Revenue_dollars = '';

            $response = customAPICall('https://api.cognism.com/api/prospector/company/search?pageSize=1&page=1&api_key=API-TOKEN','POST',"{\"names\": [\"".$company_name."\"]}");  

            if (isset($response->results) && count($response->results)>0) {

                $dataToCollect->Industry = isset($response->results[0]->industry) ? isset($response->results[0]->industry[0]) ? $response->results[0]->industry[0] : '' : '';
                $dataToCollect->Size = isset($response->results[0]->size_from) ? strval($response->results[0]->size_from) : '';
                $dataToCollect->Size .= isset($response->results[0]->size_to) ? ' - '.strval($response->results[0]->size_to) : '';
                $dataToCollect->Country = isset($response->results[0]->loc) ? isset($response->results[0]->loc->country) ? $response->results[0]->loc->country : '' : '';
                $dataToCollect->domain = isset($response->results[0]->domain) ? $response->results[0]->domain : '';
                $dataToCollect->Description = isset($response->results[0]->desc) ? $response->results[0]->desc : '';
                $dataToCollect->Founded_year = isset($response->results[0]->founded) ? strval($response->results[0]->founded) : '';
                $dataToCollect->Revenue_dollars = isset($response->results[0]->revenue) ? strval($response->results[0]->revenue) : '';

            }
            else{
                $error = new stdClass();
                $error->error=true;
                $error->msg='No information about that lead.';
                echo json_encode($error);
                exit;
            }

            if ($err) {
                $error = new stdClass();
                $error->error=true;
                $error->msg='cURL Error #: '. $err;
                echo json_encode($error);
            } 
            else {
                echo json_encode($dataToCollect);
            }
        
        }
        else{
            $error = new stdClass();
            $error->error=true;
            $error->msg='Expected token "email" not found.';
            echo json_encode($error);
        }

    }
    else{
        $error = new stdClass();
        $error->error=true;
        $error->msg='Contact support team.';
        echo json_encode($error);

    }
}
else{
    $error = new stdClass();
    $error->error=true;
    $error->msg='Expected token "type" not found.';


}
