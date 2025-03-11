<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestCurlCommand extends Command
{
    protected $signature = 'test:mailtrap';
    protected $description = 'Test sending an email using Mailtrap via cURL';

    public function handle()
    {
        $smtp_server = 'sandbox.smtp.mailtrap.io';
        $port = 2525;
        $username = '9f83fa8f5756e6'; // env('MAIL_USERNAME'); // Set in .env
        $password = '********ac95'; //env('MAIL_PASSWORD'); // Set in .env
        $from_email = 'jasonlong.inspire@gmail.com';
        $to_email = 'jasonlong.inspire@gmail.com';

        $email_body = <<<EOF
From: Magic Elves <$from_email>
To: Mailtrap Inbox <$to_email>
Subject: You are awesome!
Content-Type: multipart/alternative; boundary="boundary-string"

--boundary-string
Content-Type: text/plain; charset="utf-8"
Content-Transfer-Encoding: quoted-printable
Content-Disposition: inline

Congrats for sending test email with Mailtrap!

If you are viewing this email in your inbox =E2=80=93 the integration works.
Now send your email using our SMTP server and integration of your choice!

Good luck! Hope it works.

--boundary-string
Content-Type: text/html; charset="utf-8"
Content-Transfer-Encoding: quoted-printable
Content-Disposition: inline

<!doctype html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  </head>
  <body style="font-family: sans-serif;">
    <div style="display: block; margin: auto; max-width: 600px;" class="main">
      <h1 style="font-size: 18px; font-weight: bold; margin-top: 20px">Congrats for sending test email with Mailtrap!</h1>
      <p>If you are viewing this email in your inbox – the integration works.</p>
      <img alt="Inspect with Tabs" src="https://assets-examples.mailtrap.io/integration-examples/welcome.png" style="width: 100%;">
      <p>Now send your email using our SMTP server and integration of your choice!</p>
      <p>Good luck! Hope it works.</p>
    </div>
  </body>
</html>

--boundary-string--
EOF;

        $this->info("Sending test email via Mailtrap...");

        $curl_command = <<<CMD
curl --ssl-reqd \\
--url 'smtp://$smtp_server:$port' \\
--user '$username:$password' \\
--mail-from $from_email \\
--mail-rcpt $to_email \\
--upload-file - <<EOF
$email_body
EOF
CMD;

        exec($curl_command, $output, $return_var);

        if ($return_var !== 0) {
            $this->error("Failed to send email via Mailtrap.");
        } else {
            $this->info("Test email sent successfully via Mailtrap!");
        }
    }
}
