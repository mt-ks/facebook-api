<?php

namespace facebookAPI;

class Facebook
{

    protected $restServerURL  = "https://api.facebook.com/restserver.php";
    protected $graphServerURL = "https://graph.facebook.com/v4.0/";
    protected $serverURL      = 'graph';
    protected $apiKey         = "882a8490361da98702bf97a021ddc14d";
    protected $apiSecret      = "62f8ce9f74b12f84c123cc23437a4a32";
    protected $signed_data    = false;

    /**
     * @param @string $username            facebook kullanıcı adınız, e-posta adresiniz veya telefon numaranız olabilir.
     *                                     herhangi bir tanesini yazıp giriş yapmaya çalışmanız yeterli olacaktır.
     *
     * @param @string $password            facebook şifreniz. Facebook rest server üzerinde bir token oluşturma işlemi yapılacaksa
     *                                     şifrenizi girmeniz gereklidir.
     *
     * @param bool $browserLogin           $browserLogin = true değeri verilirse sizin için bir login url'si oluşturacaktır.
     *                                     tarayıcı adresine yapıştırıp access_token oluşturabilirsiniz.
     * @return bool|string
     */

    public function login($username,$password,$browserLogin = false)
    {
        $this->serverURL    = 'rest';
        $this->signed_data  = true;
        $data = array(
            "api_key"                  => $this->apiKey,
            "credentials_type"         => "password",
            "email"                    => $username,
            "format"                   => "JSON",
            "generate_machine_id"      => "1",
            "generate_session_cookies" => "1",
            "locale"                   => "en_US",
            "method"                   => "auth.login",
            "password"                 => $password,
            "return_ssl_resources"     => "0",
            "v"                        => "1.0"
        );
        if($browserLogin):
            return $this->restServerURL.'?'.http_build_query(self::sign_data($data));
        endif;
        return $this->request('',$data);
    }

    /**
     * @param $id                    beğeni işlemi yapılacak olan id'dir yanlızca id değil versiyon 4.0'da userid_postid
     *                               şeklinde belirlenmesi gerekiyor. örneğin 1235_2135135463464545
     *
     * @param $token                 Giriş işlemini tamamladıktan sonra aldığınız access_token'dir.
     *
     * @param string $reactionType   reaksiyon tipleridir 'LIKE','LOVE','HAHA','WOW','SAD','ANGRY' herhangi bir tanesi gelebilir
     * @return bool|string
     */

    public function like($id,$token,$reactionType = 'LIKE')
    {
        $this->serverURL = 'graph';
        return $this->request("{$id}/reactions",[
            'type'         => $reactionType,
            'method'       => 'POST',
            'access_token' => $token
        ]);
    }

    public function userInfo($token)
    {
        $this->serverURL = 'graph';
        return $this->request('me?fields=id,name,picture&access_token='.$token);
    }


    protected function reactionTypes()
    {
        return ['LIKE','LOVE','HAHA','WOW','SAD','ANGRY'];
    }

    protected function request($endpoint,$postFields = NULL)
    {
        $set_url = ($this->serverURL == 'graph') ? $this->graphServerURL : $this->restServerURL;
        $curl       = curl_init();
        $options    = [
            CURLOPT_URL             => $set_url.$endpoint,
            CURLOPT_RETURNTRANSFER  => TRUE,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_USERAGENT       => self::UserAgent()
        ];
        if(is_array($postFields)):
            $options[CURLOPT_POST] = true;
            $postType = ($this->signed_data == true) ? self::sign_data($postFields)  : http_build_query($postFields);
            $options[CURLOPT_POSTFIELDS] = $postType;
        endif;
        curl_setopt_array($curl,$options);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
    protected function sign_data($data)
    {
        $sig = "";
        foreach($data as $key => $value){
            $sig .= "$key=$value";
        }
        $sig .= $this->apiSecret;
        $sig = md5($sig);
        $data['sig'] = $sig;
        return $data;
    }

    protected function UserAgent()
    {
        $user_agents = array(
            "Mozilla/5.0 (Linux; Android 5.0.2; Andromax C46B2G Build/LRX22G) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/37.0.0.0 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/60.0.0.16.76;]",
            "Mozilla/5.0 (Linux; Android 5.1.1; SM-N9208 Build/LMY47X) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.81 Mobile Safari/537.36",
            "Mozilla/5.0 (Linux; U; Android 5.0; en-US; ASUS_Z008 Build/LRX21V) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 UCBrowser/10.8.0.718 U3/0.8.0 Mobile Safari/534.30",
            "Mozilla/5.0 (Linux; U; Android 5.1; en-US; E5563 Build/29.1.B.0.101) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 UCBrowser/10.10.0.796 U3/0.8.0 Mobile Safari/534.30",
            "Mozilla/5.0 (Linux; U; Android 4.4.2; en-us; Celkon A406 Build/MocorDroid2.3.5) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1"
        );
        $useragent = $user_agents[array_rand($user_agents)];
        return $useragent;
    }

}