<?

class Enp_Data_API {
    public $site_url;

    public function __construct() {
        $this->site_url = site_url();
    }

}

//var_dump(new Enp_Data_API());

$data = array("site_url" => site_url());
$data_string = json_encode($data);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://dev/enp-api/api.php');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_string))
);

$result = curl_exec($ch);

curl_close($ch);

var_dump($result);

?>
