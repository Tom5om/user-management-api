<h3>Click the Link To Verify Your Email</h3>
<p>
    Click the following link to verify your email <a href="{{ env('APP_URL').'/verify-email/' . $email_token }}">{{ env('APP_URL').'/verify-email/' . $email_token }}</a>
</p>
Or copy the following link in your browser: <br />
{{ env('APP_URL').'/verify-email/' . $email_token }}