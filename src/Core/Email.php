<?php

namespace Wepesi\Core;

class Email
{
    private $url;
    private string $from;
    private string $contact;
    private string $subject;
    private Validate $validation;
    private string $default_lang;
    private string $to;

    function __construct()
    {
        $this->subject = "Welcome to Wepesi";
        $this->from = "no-reply@" . APP_DOMAIN;
        $this->contact = "contact@" . APP_DOMAIN;
        $this->url = DEFAULT_DOMAIN;
        $this->default_lang = LANG;
    }

    /**
     * @param array $data
     * @return array|bool
     */
    function welcom(array $data)
    {
        $subject = isset($data["subject"]) ?? $this->subject;
        $body = <<<EOF
            <table style="border-spacing:0!important;border-collapse:collapse!important;table-layout: fixed !important;margin: 0" >
                <tr>
                    <td style="width:50%;text-align: center;color:#3e3e33;" >
                        <div>Bienvenues chez <b style="font-size:25px;color:#f0c807">Wepesi</b> <br>
                            <span>Your simple and light weight framework to devellop simple wep application</span>
                        </div>                
                    </td>
                    <td style="width:50%">
                        <H1 style="text-align:center;color:#f0c807">Wepesi</h1>
                        <div style="color:#3e3e33">.</div>
                    </td>            
                </tr>
                <tr>
                    <td style="width:50%;text-align: center;color:#3e3e33;" >
                        <div><a href="#"> Unsubscribe</a></div>                
                    </td>
                    <td style="width:50%">
                        <H1 style="text-align:center;color:#f0c807">Wepesi</h1>
                        <div style="color:#3e3e33">votre assistant, de prise de rendez-vous.</div>
                    </td>            
                </tr>      
            </table>
        EOF;
        try {
            $body = $this->template($body);
            return $this->sendMail($data, $body);
        } catch (\Exception $ex) {
            return ["exception" => $ex->getMessage()];
        }
    }

    /**
     * @param string $message_body
     * @return string
     * Wepesi model format email template
     */
    private function template(string $message_body): string
    {
        try {
            $thisYear = date("Y", time());
            return <<< html_body
            <!DOCTYPE html>
            <html lang="$this->default_lang" xmlns="http://www.w3.org/1999/xhtml" style="margin: 0 auto !important;padding: 0 !important;height: 100% !important;width: 100% !important;background: #b8b8b8;">
            <head>
                <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
                <meta name="viewport" content="width=device-width">
                <meta http-equiv="X-UA-Compatible" content="ie=edge">
                <meta name="x-apple-disable-message-reformatting" content="framework,library,validation,ORM,routing,php">
                <title>$this->subject</title>
                <link href="https://fonts.googleapis.com/css?family=Ubuntu:200,300,400,600,700,800,900" rel="stylesheet">
            </head>
            <body style="width:100%;font-family: ubuntu;font-weight: 400;font-size: 15px;line-height: 1.8;color: rgba(0,0,0,.4);  text-decoration: none;background: #e6e6e6;">
                <div style="max-width: 600px; margin: 0 auto;" >
                    <table style="margin: auto; border-spacing: 0 !important;border-collapse: collapse !important; table-layout: fixed !important;">
                        <tr style="background: #009688;">
                            <td style="padding: 1em 2.5em;font-weight: 600;position:relative;">                   
                               <h1><a href="$this->url" style="font-family: Ubuntu; text-decoration: none;font-weight: 600;color:#e3e3e3!important;position:absolute;top:50%;left:20%;transform: translate(-50%,-50%);">Wepesi</a></h1>
                            </td>
                        </tr>                        
                        <tr>
                            <td style="background-color: #fff;height: 280px;position:relative; padding:15px;">                        
                                $message_body
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align:center;padding:2.5em;background:#009688;">
                                <div style="color: rgba(255,255,255,.8);" >
                                    <h2 style="color: #ffffff;font-size: 28px;margin-top: 0;line-height: 1.4;font-weight: 400;font-family: 'Lato', sans-serif;">Bienvenu chez <a href="$this->url" style="font-weight: 600;color:#e3e3e3!important;text-decoration:none;">Wepesi</a></h2>
                                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Suscipit harum, voluptatum quaerat voluptate numquam officia optio. Consequatur quos eligendi molestiae suscipit facilis aperiam fuga ut? Fuga cumque sit eos amet!</p>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:1.5em;background: #e3e3e3;color: #3e3e3e !important;">
                                <table style=" border-spacing: 0 !important;border-collapse: collapse !important;table-layout: fixed !important;margin: 0" >
                                    <tr>
                                        <td style="width:50%" >
                                            <h3 style="text-align: center;">Apropos de  nous</h3>
                                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Itaque repudiandae, iure quod ex dolores velit officiis aliquam exercitationem.</p>
                                        </td>
                                        <td style="width:50%" >
                                            <h3 style="text-align: center;">Contact Infos</h3>
                                            <ul>
                                                <li>Adress: Goma, Congo DRC</li>
                                                <li>Email: $this->contact</li>
                                                <li>Phone:+243 000-000-000</li>
                                            </ul>
                                        </td>
                                    </tr>
                                    
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="footer " style="text-align:center; background: #009688;color:#fff">
                                <p >copyright &copy; $thisYear Wepesi. all right reserved.</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </body>
            </html>
        html_body;
        } catch (\Exception $ex) {
            return ["exception" => $ex->getMessage()];
        }
    }

    private function sendMail(array $data, string $body)
    {
        try {
            $this->from = $data["from"];
            $this->to = $data["to"];
            $this->subject = $data["subject"];
            $check_email = $this->checkConfig(["from" => $this->from, "to" => $this->to]);
            if (isset($check_email['exception'])) return $check_email;
            $header = [
                "MIME_Version:1.0",
                "Content-type:text/html;charset=iso-8859",
                "From:$this->from"
            ];
            return mail($this->to, $this->subject, $body, implode("\r\n", $header));
        } catch (\Exception $ex) {
            return ["exception" => $ex->getMessage()];
        }
    }

    /**
     * @return array|void
     */
    private function checkConfig(array $config_data)
    {
        try {
            $validat = new Validate($config_data);
            $schema = [
                "from" => $validat->string("from")->required()->email()->check(),
                "to" => $validat->string("to")->required()->email()->check(),
            ];
            $validat->check($config_data, $schema);
            if (!$validat->passed()) {
                throw new \Exception($validat->errors());
            }
        } catch (\Exception $ex) {
            return ["exception" => $ex->getMessage()];
        }
    }
}